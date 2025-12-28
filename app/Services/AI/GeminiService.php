<?php

namespace App\Services\AI;

use Gemini\Laravel\Facades\Gemini;
use Illuminate\Support\Facades\Log;

class GeminiService
{
    public function __construct()
    {
        // No initialization needed - Gemini facade handles configuration
    }

    /**
     * Generate product description (short and long)
     */
    public function generateProductDescription(array $productData, string $type = 'both'): array
    {
        try {
            $prompt = $this->buildPrompt($productData, $type);

            Log::info('Generating product description with Gemini', [
                'model' => 'gemini-2.0-flash',
                'type' => $type,
                'product_name' => $productData['name'] ?? 'N/A',
            ]);

            $response = $this->callGeminiAPI($prompt);

            return $this->parseResponse($response, $type);
        } catch (\Exception $e) {
            Log::error('Error generating product description', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            throw $e;
        }
    }

    /**
     * Build prompt from product data
     */
    protected function buildPrompt(array $data, string $type = 'both'): string
    {
        $prompt = "أنت خبير في كتابة وصف المنتجات. قم بكتابة وصف جذاب ومهني بالعربية الفصحى للمنتج التالي:\n\n";

        // Product basic info
        $prompt .= "اسم المنتج: " . ($data['name'] ?? 'غير محدد') . "\n";

        if (!empty($data['author'])) {
            $prompt .= "المؤلف: " . $data['author'] . "\n";
        }

        if (!empty($data['publisher'])) {
            $prompt .= "دار النشر: " . $data['publisher'] . "\n";
        }

        if (!empty($data['product_type'])) {
            $prompt .= "نوع المنتج: " . $data['product_type'] . "\n";
        }

        if (!empty($data['category_name'])) {
            $prompt .= "الفئة: " . $data['category_name'] . "\n";
        }

        if (!empty($data['tags']) && is_array($data['tags'])) {
            $prompt .= "التاغات: " . implode(', ', $data['tags']) . "\n";
        }

        if (!empty($data['color'])) {
            $prompt .= "اللون: " . $data['color'] . "\n";
        }

        if (!empty($data['sku'])) {
            $prompt .= "كود المنتج: " . $data['sku'] . "\n";
        }

        // Price info (optional context)
        if (!empty($data['retail_price'])) {
            $prompt .= "سعر البيع: " . $data['retail_price'] . " دينار\n";
        }

        $prompt .= "\n";

        // Request based on type
        if ($type === 'short') {
            $prompt .= "المطلوب: اكتب وصفاً قصيراً وجذاباً للمنتج (2-3 جمل فقط).\n";
            $prompt .= "يجب أن يكون الوصف:\n";
            $prompt .= "- مختصراً ومفيداً (2-3 جمل)\n";
            $prompt .= "- جذاباً ويبرز المميزات الرئيسية\n";
            $prompt .= "- باللغة العربية الفصحى\n";
            $prompt .= "- مناسباً للاستخدام في بطاقات المنتج وقوائم المتاجر\n\n";
            $prompt .= "الرجاء كتابة الوصف القصير فقط بدون أي مقدمة أو تعليقات:";
        } elseif ($type === 'long') {
            $prompt .= "المطلوب: اكتب وصفاً تفصيلياً وشاملاً للمنتج (5-7 جمل).\n";
            $prompt .= "يجب أن يكون الوصف:\n";
            $prompt .= "- تفصيلياً ومعلوماتياً (5-7 جمل)\n";
            $prompt .= "- يتضمن جميع المميزات والتفاصيل المهمة\n";
            $prompt .= "- باللغة العربية الفصحى\n";
            $prompt .= "- مناسباً للصفحة التفصيلية للمنتج\n\n";
            $prompt .= "الرجاء كتابة الوصف الطويل فقط بدون أي مقدمة أو تعليقات:";
        } else {
            $prompt .= "المطلوب: اكتب وصفين للمنتج:\n";
            $prompt .= "1. وصف قصير (2-3 جمل) - مناسب لبطاقات المنتج\n";
            $prompt .= "2. وصف طويل (5-7 جمل) - مناسب للصفحة التفصيلية\n\n";
            $prompt .= "يجب أن تكون الأوصاف:\n";
            $prompt .= "- جذابة ومهنية\n";
            $prompt .= "- باللغة العربية الفصحى\n";
            $prompt .= "- تبرز مميزات المنتج\n\n";
            $prompt .= "الرجاء كتابة النص بالشكل التالي:\n";
            $prompt .= "=== الوصف القصير ===\n";
            $prompt .= "[الوصف القصير هنا]\n\n";
            $prompt .= "=== الوصف الطويل ===\n";
            $prompt .= "[الوصف الطويل هنا]";
        }

        return $prompt;
    }

    /**
     * Call Gemini API using the package
     */
    protected function callGeminiAPI(string $prompt): array
    {
        try {
            // Use gemini-2.0-flash (free tier, recommended replacement for gemini-1.5-flash)
            $response = Gemini::generativeModel(model: 'gemini-2.0-flash')->generateContent($prompt);

            $text = $response->text();

            if (empty($text)) {
                throw new \Exception('لم يتم إرجاع نص من خدمة الذكاء الاصطناعي');
            }

            return ['text' => $text];
        } catch (\Exception $e) {
            Log::error('Gemini API error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            throw new \Exception('فشل في التواصل مع خدمة الذكاء الاصطناعي: ' . $e->getMessage());
        }
    }

    /**
     * Parse Gemini API response
     */
    protected function parseResponse(array $response, string $type = 'both'): array
    {
        try {
            $text = $response['text'] ?? '';

            if (empty($text)) {
                throw new \Exception('لم يتم إرجاع نص من خدمة الذكاء الاصطناعي');
            }

            if ($type === 'short') {
                return [
                    'short' => trim($text),
                    'long' => null,
                ];
            } elseif ($type === 'long') {
                return [
                    'short' => null,
                    'long' => trim($text),
                ];
            } else {
                // Parse both descriptions
                return $this->parseBothDescriptions($text);
            }
        } catch (\Exception $e) {
            Log::error('Error parsing Gemini response', [
                'error' => $e->getMessage(),
                'response' => $response,
            ]);

            throw new \Exception('فشل في معالجة الرد من خدمة الذكاء الاصطناعي');
        }
    }

    /**
     * Parse text to extract both short and long descriptions
     */
    protected function parseBothDescriptions(string $text): array
    {
        $short = '';
        $long = '';

        // Try to split by markers
        if (preg_match('/=== الوصف القصير ===\s*(.*?)(?=\n=== الوصف الطويل ===|\n===|$)/s', $text, $shortMatch)) {
            $short = trim($shortMatch[1]);
        }

        if (preg_match('/=== الوصف الطويل ===\s*(.*?)(?=\n===|$)/s', $text, $longMatch)) {
            $long = trim($longMatch[1]);
        }

        // If markers not found, try to split by first paragraph break
        if (empty($short) && empty($long)) {
            $parts = preg_split('/\n\n+/', trim($text), 2);
            $short = trim($parts[0] ?? '');
            $long = trim($parts[1] ?? '');
        }

        // If still empty, use full text for both
        if (empty($short) && empty($long)) {
            $short = trim($text);
            $long = trim($text);
        }

        return [
            'short' => $short,
            'long' => $long,
        ];
    }
}
