<?php

namespace App\Services;

use App\Models\GatewaySetting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GatewayIntegrationService
{
    protected function getGatewayUrl(): string
    {
        return config('services.gateway.url', 'https://salesflowi.cloud');
    }

    /**
     * Connect to the Gateway and retrieve an API Key by registering the project.
     * Or update if already exists.
     */
    public function connect(string $username, string $password): array
    {
        $setting = GatewaySetting::first() ?? new GatewaySetting();
        $url = rtrim($this->getGatewayUrl(), '/');
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

        $url = rtrim($this->getGatewayUrl(), '/');
        
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

            // Fast Upsert Governorates
            $govsInsert = [];
            foreach ($citiesData as $city) {
                if (empty($city['id'])) continue;
                $govsInsert[] = [
                    'id' => $city['id'],
                    'name' => $city['city_name'] ?? $city['name'] ?? 'Unknown',
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now()
                ];
            }
            if (!empty($govsInsert)) {
                \App\Models\Governorate::upsert($govsInsert, ['id'], ['name', 'is_active', 'updated_at']);
            }
            $syncedGovCount = count($govsInsert);

            // Fetch Regions Concurrently for ALL cities to avoid timeouts
            $headers = [
                'Project' => $setting->project_name,
                'X-API-KEY' => $setting->api_key,
            ];

            $responses = Http::pool(function (\Illuminate\Http\Client\Pool $pool) use ($citiesData, $headers, $url) {
                return collect($citiesData)->filter(fn($c) => !empty($c['id']))->map(function ($city) use ($pool, $headers, $url) {
                    return $pool->as('city_' . $city['id'])
                        ->timeout(30)
                        ->withHeaders($headers)
                        ->get("{$url}/api/gateway/regions", ['city_id' => $city['id']]);
                });
            });

            // Gather and format all districts for Fast Upsert
            $distsInsert = [];
            $syncedDistCount = 0;
            foreach ($responses as $key => $response) {
                if ($response instanceof \Illuminate\Http\Client\Response && $response->successful()) {
                    $regionsData = $response->json()['data'] ?? [];
                    $cityId = str_replace('city_', '', $key);
                    
                    foreach ($regionsData as $region) {
                        $distsInsert[] = [
                            'id' => $region['id'],
                            'governorate_id' => (int) $cityId,
                            'name' => $region['region_name'] ?? $region['name'] ?? 'Unknown',
                            'delivery_fee' => 0.00,
                            'is_active' => true,
                            'created_at' => now(),
                            'updated_at' => now()
                        ];
                    }
                }
            }

            // Insert in chunks to prevent MySQL "packet too large" errors
            if (!empty($distsInsert)) {
                $chunks = array_chunk($distsInsert, 250); // Smaller chunks for safer upsert
                foreach ($chunks as $chunk) {
                    \App\Models\District::upsert(
                        $chunk, 
                        ['id'], 
                        ['governorate_id', 'name', 'delivery_fee', 'is_active', 'updated_at']
                    );
                    $syncedDistCount += count($chunk);
                }
            }

            // Update Sync timestamp
            $setting->last_sync_at = now();
            $setting->save();

            return [
                'success' => true, 
                'message' => "تمت المزامنة البطلة بنجاح! ($syncedGovCount محافظة و $syncedDistCount منقطة في ثوانٍ)"
            ];

        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("Gateway Sync Error: " . $e->getMessage());
            // Truncate message to avoid UI overflows in mobile apps
            $errorMsg = \Illuminate\Support\Str::limit($e->getMessage(), 150);
            return ['success' => false, 'message' => 'حدث خطأ أثناء المزامنة: ' . $errorMsg];
        }
    }
    /**
     * AlWaseet API V2.3 Integration
     */
    protected string $waseetBaseUrl = 'https://api.alwaseet-iq.net';

    /**
     * Login to Waseet and get token
     */
    public function loginToWaseet(): ?string
    {
        $setting = GatewaySetting::first();
        if (!$setting || !$setting->waseet_username || !$setting->waseet_password) {
            return null;
        }

        // Cache token for 24 hours
        return \Illuminate\Support\Facades\Cache::remember('waseet_token', 86400, function () use ($setting) {
            try {
                $response = Http::asMultipart()->post("{$this->waseetBaseUrl}/v1/merchant/login", [
                    'username' => $setting->waseet_username,
                    'password' => $setting->waseet_password,
                ]);

                $data = $response->json();
                if ($response->successful() && ($data['status'] ?? false) === true) {
                    return $data['data']['token'];
                }
            } catch (\Exception $e) {
                Log::error("Waseet Login Error: " . $e->getMessage());
            }
            return null;
        });
    }

    /**
     * Send Order to Waseet via Gateway Bridge (SalesFlowi)
     */
    public function sendToWaseet(\App\Models\Order $order): array
    {
        $setting = GatewaySetting::first();
        if (!$setting || !$setting->is_connected || !$setting->api_key) {
            return ['success' => false, 'message' => 'يجب الاتصال ببوابة الوسيط أولاً من الإعدادات.'];
        }

        try {
            $order->load(['governorate', 'district', 'orderItems.product']);

            // Generate content description from product names
            $productNames = $order->orderItems->map(function($item) {
                return $item->product ? $item->product->name : 'منتج غير معروف';
            })->unique()->join('، ');

            $typeName = \Illuminate\Support\Str::limit($productNames, 90, '...');
            
            $payload = [
                'client_name' => $order->customer_name,
                'client_mobile' => $this->formatIraqiPhone($order->customer_phone),
                'city_id' => $order->governorate_id,
                'region_id' => $order->district_id,
                'location' => $order->customer_address,
                'type_name' => $typeName ?: 'منتجات منوعة',
                'items_number' => $order->orderItems->sum('quantity'),
                'price' => $order->is_paid ? 0 : (int) $order->total_amount,
                'package_size' => 1, // Default size
                'merchant_notes' => $order->customer_notes ?? '',
                'replacement' => $order->is_replacement ? 1 : 0,
            ];

            if ($order->customer_phone_2) {
                $payload['client_mobile2'] = $this->formatIraqiPhone($order->customer_phone_2);
            }

            // Call the SalesFlowi Gateway Bridge
            $url = rtrim($this->getGatewayUrl(), '/') . '/api/gateway/create-order';
            $response = Http::timeout(30)
                ->withHeaders([
                    'Project' => $setting->project_name,
                    'X-API-KEY' => $setting->api_key,
                ])
                ->asMultipart()
                ->post($url, $payload);

            $data = $response->json();

            if ($response->successful() && ($data['status'] ?? false) === true) {
                // Success from Al-Waseet via Gateway
                $order->update([
                    'waseet_order_id' => $data['data']['qr_id'] ?? $data['data']['order_id'],
                    'waseet_tracking_url' => $data['data']['qr_link'] ?? $data['data']['tracking_url'],
                    'waseet_status' => 'قيد المعالجة',
                    'status' => \App\Enums\OrderStatus::SENT_TO_GATEWAY,
                ]);

                // Create initial history log (First Movement)
                \App\Models\OrderStatusLog::create([
                    'order_id' => $order->id,
                    'status' => $order->status,
                    'waseet_status' => 'قيد المعالجة (تم الإرسال للوسيط)',
                    'notes' => 'تم رفع الطلب بنجاح لشركة الوسيط وحجز رقم تتبع.',
                ]);

                return ['success' => true, 'message' => 'تم إرسال الطلب وحجز رقم تتبع بنجاح!'];
            }

            return ['success' => false, 'message' => $data['msg'] ?? $data['message'] ?? 'فشل إرسال الطلب للوسيط.'];

        } catch (\Exception $e) {
            Log::error("Waseet Gateway Send Error: " . $e->getMessage());
            return ['success' => false, 'message' => 'خطأ في الاتصال بالبوابة: ' . $e->getMessage()];
        }
    }

    /**
     * Format Iraqi phone number to +964...
     */
    protected function formatIraqiPhone(string $phone): string
    {
        // Remove spaces and non-digits
        $phone = preg_replace('/\D/', '', $phone);
        
        // If it starts with 07, replace 0 with +964
        if (str_starts_with($phone, '07')) {
            return '+964' . substr($phone, 1);
        }
        
        // If it starts with 7 and has 10 digits, prepend +964
        if (str_starts_with($phone, '7') && strlen($phone) === 10) {
            return '+964' . $phone;
        }

        // Already has 964?
        if (str_starts_with($phone, '964')) {
            return '+' . $phone;
        }

        return '+' . $phone;
    }

    /**
     * Get dynamic statuses from Al-Waseet via Gateway.
     */
    public function getWaseetStatuses(): array
    {
        $setting = GatewaySetting::first();
        if (!$setting || !$setting->is_connected) return [];

        return \Illuminate\Support\Facades\Cache::remember('waseet_statuses_list', 86400, function () use ($setting) {
            try {
                $response = Http::withHeaders([
                    'Project' => $setting->project_name,
                    'X-API-KEY' => $setting->api_key,
                ])->get(rtrim($this->getGatewayUrl(), '/') . '/api/gateway/statuses');

                if ($response->successful()) {
                    return $response->json()['data'] ?? [];
                }
            } catch (\Exception $e) {
                Log::error("Failed to fetch Waseet statuses: " . $e->getMessage());
            }
            return [];
        });
    }

    /**
     * Update an existing order on Waseet via Gateway
     */
    public function updateOrderOnGateway(\App\Models\Order $order): array
    {
        $setting = GatewaySetting::first();
        if (!$setting || !$setting->is_connected || !$setting->api_key || !$order->waseet_order_id) {
            return ['success' => false, 'message' => 'الطلب غير مربوط بالوسيط أو الإعدادات غير مكتملة.'];
        }

        try {
            $order->load(['governorate', 'district', 'orderItems.product']);

            // Generate content description from product names
            $productNames = $order->orderItems->map(fn($item) => $item->product?->name ?? 'منتج')->unique()->join('، ');
            $typeName = \Illuminate\Support\Str::limit($productNames, 90, '...');

            $payload = [
                'qr_id' => $order->waseet_order_id,
                'client_name' => $order->customer_name,
                'client_mobile' => $this->formatIraqiPhone($order->customer_phone),
                'city_id' => $order->governorate_id,
                'region_id' => $order->district_id,
                'location' => $order->customer_address,
                'type_name' => $typeName ?: 'منتجات منوعة',
                'items_number' => $order->orderItems->sum('quantity'),
                'price' => $order->is_paid ? 0 : (int) $order->total_amount,
                'package_size' => 1,
                'merchant_notes' => $order->customer_notes ?? '',
                'replacement' => $order->is_replacement ? 1 : 0,
            ];

            if ($order->customer_phone_2) {
                $payload['client_mobile2'] = $this->formatIraqiPhone($order->customer_phone_2);
            }

            $url = rtrim($this->getGatewayUrl(), '/') . '/api/gateway/edit-order';
            $response = Http::timeout(30)
                ->withHeaders([
                    'Project' => $setting->project_name,
                    'X-API-KEY' => $setting->api_key,
                ])
                ->asMultipart()
                ->post($url, $payload);

            $data = $response->json();

            if ($response->successful() && ($data['status'] ?? false) === true) {
                \App\Models\OrderStatusLog::create([
                    'order_id' => $order->id,
                    'status' => $order->status,
                    'waseet_status' => 'تعديل بيانات الطلب',
                    'notes' => 'تم تحديث بيانات الطلب بنجاح في نظام الوسيط.',
                ]);
                return ['success' => true, 'message' => 'تم تحديث البيانات في الوسيط بنجاح!'];
            }

            return ['success' => false, 'message' => $data['msg'] ?? 'فشل تحديث الطلب في الوسيط.'];
        } catch (\Exception $e) {
            Log::error("Waseet Gateway Update Error: " . $e->getMessage());
            return ['success' => false, 'message' => 'خطأ في الاتصال بالبوابة: ' . $e->getMessage()];
        }
    }

    /**
     * Cancel an order on Waseet via Gateway
     */
    public function cancelOrderOnGateway(\App\Models\Order $order): array
    {
        $setting = GatewaySetting::first();
        if (!$setting || !$setting->is_connected || !$order->waseet_order_id) {
            return ['success' => false, 'message' => 'الطلب غير مربوط بالوسيط.'];
        }

        try {
            $url = rtrim($this->getGatewayUrl(), '/') . '/api/gateway/delete-order';
            $response = Http::timeout(30)
                ->withHeaders([
                    'Project' => $setting->project_name,
                    'X-API-KEY' => $setting->api_key,
                ])
                ->asMultipart()
                ->post($url, ['id' => $order->waseet_order_id]);

            $data = $response->json();

            if ($response->successful() && ($data['status'] ?? false) === true) {
                \App\Models\OrderStatusLog::create([
                    'order_id' => $order->id,
                    'status' => $order->status,
                    'waseet_status' => 'إلغاء الطلب',
                    'notes' => 'تم إرسال طلب إلغاء بنجاح لنظام الوسيط.',
                ]);
                return ['success' => true, 'message' => 'تم إرسال طلب الإلغاء للوسيط.'];
            }

            return ['success' => false, 'message' => $data['msg'] ?? 'فشل إرسال طلب الإلغاء. قد لا يدعم هذا الطلب الإلغاء حالياً.'];
        } catch (\Exception $e) {
            Log::error("Waseet Gateway Cancel Error: " . $e->getMessage());
            return ['success' => false, 'message' => 'خطأ في الاتصال بالبوابة.'];
        }
    }

    /**
     * Fetch full order details including history from Al-Waseet via Gateway.
     */
    public function getWaseetOrderDetails(string $waseetOrderId): array
    {
        $setting = GatewaySetting::first();
        if (!$setting || !$setting->is_connected) return [];

        try {
            $response = Http::withHeaders([
                'Project' => $setting->project_name,
                'X-API-KEY' => $setting->api_key,
            ])->get(rtrim($this->getGatewayUrl(), '/') . "/api/gateway/order-status/{$waseetOrderId}");

            if ($response->successful()) {
                $data = $response->json();
                // Usually returns an array since it's a bulk/ids lookup
                return $data['data'][0] ?? $data['data'] ?? [];
            }
        } catch (\Exception $e) {
            Log::error("Failed to fetch Waseet order details: " . $e->getMessage());
        }
        return [];
    }
}

