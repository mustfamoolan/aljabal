<?php

namespace App\Services;

use App\Models\GatewaySetting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GatewayIntegrationService
{
    protected string $defaultGatewayUrl = 'https://salesflowi.cloud';

    /**
     * Connect to the Gateway and retrieve an API Key by registering the project.
     * Or update if already exists.
     */
    public function connect(string $username, string $password): array
    {
        $setting = GatewaySetting::first() ?? new GatewaySetting();
        $url = rtrim($this->defaultGatewayUrl, '/');
        $projectName = 'Al Jabal ' . config('app.env');

        try {
            // Fast timeout to avoid freezing admin panel
            $response = Http::timeout(10)->post("{$url}/api/gateway/register", [
                'project_name' => $projectName,
                'username' => $username,
                'password' => $password,
            ]);

            $data = $response->json();

            if ($response->successful() && ($data['status'] ?? false) === true) {
                // Save it locally
                $setting->project_name = $projectName;
                $setting->waseet_username = $username;
                $setting->waseet_password = $password; // Mutator will encrypt it
                $setting->api_key = $data['data']['api_key'];
                $setting->is_connected = true;
                $setting->save();

                return ['success' => true, 'message' => 'تم الاتصال بالبوابة بنجاح!'];
            }

            // Could be already registered with that project name. Let's try to just connect instead of register!
            // If the Gateway returned 422 because of unique project name, we should try `connect-waseet`.
            // BUT wait, `connect-waseet` requires `X-API-KEY`. So if we lost it locally but it's registered on Gateway, we might be stuck.
            // We append a random hash to the project name to ensure uniqueness if needed.
            if ($response->status() === 422 && str_contains(json_encode($data['errors'] ?? []), 'unique')) {
                 $response = Http::timeout(10)->post("{$url}/api/gateway/register", [
                    'project_name' => $projectName . ' ' . uniqid(),
                    'username' => $username,
                    'password' => $password,
                ]);
                $data = $response->json();
                if ($response->successful() && ($data['status'] ?? false) === true) {
                    $setting->project_name = $projectName;
                    $setting->waseet_username = $username;
                    $setting->waseet_password = $password;
                    $setting->api_key = $data['data']['api_key'];
                    $setting->is_connected = true;
                    $setting->save();
                    return ['success' => true, 'message' => 'تم الاتصال بالبوابة بنجاح!'];
                }
            }

            return ['success' => false, 'message' => $data['msg'] ?? 'فشل الاتصال بـ API البوابة.'];
        } catch (\Exception $e) {
            Log::error("Gateway Connection Error: " . $e->getMessage());
            return ['success' => false, 'message' => 'خطأ في الشبكة أو البوابة لا تستجيب.'];
        }
    }

    /**
     * Synchronize Governors and Districts from Gateway
     */
    public function syncLocations(): array
    {
        $setting = GatewaySetting::first();
        if (!$setting || !$setting->is_connected || !$setting->api_key) {
            return ['success' => false, 'message' => 'يجب الاتصال بالبوابة أولاً!'];
        }

        $url = rtrim($this->defaultGatewayUrl, '/');
        
        try {
            // 1. Fetch Governorates (Cities in Waseet)
            $citiesResponse = Http::timeout(20)->withHeaders([
                'Project' => $setting->project_name,
                'X-API-KEY' => $setting->api_key,
            ])->get("{$url}/api/gateway/cities");

            if (!$citiesResponse->successful()) {
                return ['success' => false, 'message' => 'فشل في جلب قائمة المحافظات من البوابة.'];
            }

            $citiesData = $citiesResponse->json()['data'] ?? [];
            if (empty($citiesData)) {
                return ['success' => false, 'message' => 'قائمة المحافظات من الوسيط فارغة.'];
            }

            // Start Syncing Governorates
            $syncedGovCount = 0;
            $syncedDistCount = 0;

            foreach ($citiesData as $city) {
                // Determine the ID strategy
                // Since user chose Option 2 (Sync), we will map Waseet's City ID to Governorate.
                // We will use updateOrCreate to retain existing matching IDs or create new ones.
                $govId = $city['id']; // Expecting Waseet's ID
                $govName = $city['city_name'] ?? $city['name'] ?? 'Unknown';
                
                $governorate = \App\Models\Governorate::updateOrCreate(
                    ['id' => $govId],
                    ['name' => $govName, 'is_active' => true]
                );
                $syncedGovCount++;

                // 2. Fetch Districts (Regions in Waseet) for this City
                $regionsResponse = Http::timeout(10)->withHeaders([
                    'Project' => $setting->project_name,
                    'X-API-KEY' => $setting->api_key,
                ])->get("{$url}/api/gateway/regions", [
                    'city_id' => $govId
                ]);

                if ($regionsResponse->successful()) {
                    $regionsData = $regionsResponse->json()['data'] ?? [];
                    foreach ($regionsData as $region) {
                        \App\Models\District::updateOrCreate(
                            ['id' => $region['id']], // Use Waseet's Region ID
                            [
                                'governorate_id' => $govId,
                                'name' => $region['region_name'] ?? $region['name'] ?? 'Unknown',
                                'is_active' => true,
                                'delivery_fee' => 0 // Default to zero or extract if available
                            ]
                        );
                        $syncedDistCount++;
                    }
                }
            }

            // Update Sync timestamp
            $setting->last_sync_at = now();
            $setting->save();

            return [
                'success' => true, 
                'message' => "تمت المزامنة بنجاح! ($syncedGovCount محافظة و $syncedDistCount منقطة)"
            ];

        } catch (\Exception $e) {
            Log::error("Gateway Sync Error: " . $e->getMessage());
            return ['success' => false, 'message' => 'حدث خطأ أثناء المزامنة: ' . $e->getMessage()];
        }
    }
}
