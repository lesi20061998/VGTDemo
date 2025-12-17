<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class AiController extends Controller
{
    public function test(Request $request)
    {
        $provider = $request->provider;
        $apiKey = $request->api_key;
        $model = $request->model;

        try {
            switch ($provider) {
                case 'openai':
                    return $this->testOpenAI($apiKey, $model);

                case 'gemini':
                    return $this->testGemini($apiKey, $model);
                default:
                    return response()->json(['success' => false, 'message' => 'Provider không hợp lệ']);
            }
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    private function testOpenAI($apiKey, $model)
    {
        $response = Http::withHeaders([
            'Authorization' => 'Bearer '.$apiKey,
            'Content-Type' => 'application/json',
        ])->post('https://api.openai.com/v1/chat/completions', [
            'model' => $model,
            'messages' => [['role' => 'user', 'content' => 'Hello']],
            'max_tokens' => 10,
        ]);

        if ($response->successful()) {
            return response()->json(['success' => true, 'message' => '✓ Kết nối thành công! Model: '.$model]);
        }

        return response()->json(['success' => false, 'message' => 'Lỗi: '.$response->json()['error']['message'] ?? 'Unknown error']);
    }

    private function testGemini($apiKey, $model)
    {
        // Use the correct model name for Gemini
        $validModel = $this->getValidGeminiModel($model);

        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'X-goog-api-key' => $apiKey,
        ])->post("https://generativelanguage.googleapis.com/v1beta/models/{$validModel}:generateContent", [
            'contents' => [['parts' => [['text' => 'Hello']]]],
        ]);

        if ($response->successful()) {
            return response()->json(['success' => true, 'message' => '✓ Kết nối thành công! Model: '.$validModel]);
        }

        $errorMessage = 'Unknown error';
        if ($response->json() && isset($response->json()['error']['message'])) {
            $errorMessage = $response->json()['error']['message'];

            // Handle common errors with user-friendly messages
            if (str_contains($errorMessage, 'expired') || str_contains($errorMessage, 'invalid')) {
                $errorMessage = '🔑 API key đã hết hạn hoặc không hợp lệ! Vui lòng tạo API key mới';
            } elseif (str_contains($errorMessage, 'not found') || str_contains($errorMessage, 'model')) {
                $errorMessage = '🤖 Model không tồn tại! Hãy click "Models" để xem danh sách model có sẵn';
            }
        }

        return response()->json(['success' => false, 'message' => 'Lỗi: '.$errorMessage]);
    }

    public function generate(Request $request)
    {
        try {
            $ai = json_decode(setting('ai', '{}'), true);

            if (! ($ai['enabled'] ?? false)) {
                return response()->json(['error' => 'AI chưa được bật']);
            }

            // Auto select provider based on available API key
            $openaiKey = $ai['openai_key'] ?? '';
            $geminiKey = $ai['gemini_key'] ?? '';

            if ($openaiKey) {
                $provider = 'openai';
                $apiKey = $openaiKey;
                $model = 'gpt-3.5-turbo';
            } elseif ($geminiKey) {
                $provider = 'gemini';
                $apiKey = $geminiKey;
                $model = $ai['gemini_model'] ?? 'gemini-1.5-flash';
            } else {
                return response()->json(['error' => 'Chưa cấu hình API key']);
            }

            $temperature = $ai['temperature'] ?? 0.7;
            $maxTokens = $ai['max_tokens'] ?? 2000;

            \Log::info('AI Generate Request', [
                'provider' => $provider,
                'model' => $model,
                'prompt' => $request->prompt,
            ]);

            switch ($provider) {
                case 'openai':
                    return $this->generateOpenAI($apiKey, $model, $request->prompt, $temperature, $maxTokens);

                case 'gemini':
                    return $this->generateGemini($apiKey, $model, $request->prompt, $temperature, $maxTokens);
            }
        } catch (\Exception $e) {
            \Log::error('AI Generate Error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json(['error' => $e->getMessage()]);
        }
    }

    private function generateOpenAI($apiKey, $model, $prompt, $temperature, $maxTokens)
    {
        $response = Http::withHeaders([
            'Authorization' => 'Bearer '.$apiKey,
            'Content-Type' => 'application/json',
        ])->post('https://api.openai.com/v1/chat/completions', [
            'model' => $model,
            'messages' => [['role' => 'user', 'content' => $prompt]],
            'temperature' => (float) $temperature,
            'max_tokens' => (int) $maxTokens,
        ]);

        if ($response->successful()) {
            $content = $response->json()['choices'][0]['message']['content'];
            $this->updateStats($response->json()['usage']);

            return response()->json(['content' => $content]);
        }

        return response()->json(['error' => $response->json()['error']['message'] ?? 'Unknown error']);
    }

    private function generateGemini($apiKey, $model, $prompt, $temperature, $maxTokens)
    {
        // Use the correct model name for Gemini
        $validModel = $this->getValidGeminiModel($model);

        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
            'X-goog-api-key' => $apiKey,
        ])->post("https://generativelanguage.googleapis.com/v1beta/models/{$validModel}:generateContent", [
            'contents' => [['parts' => [['text' => $prompt]]]],
            'generationConfig' => [
                'temperature' => (float) $temperature,
                'maxOutputTokens' => (int) $maxTokens,
            ],
        ]);

        if ($response->successful()) {
            $responseData = $response->json();
            if (isset($responseData['candidates'][0]['content']['parts'][0]['text'])) {
                $content = $responseData['candidates'][0]['content']['parts'][0]['text'];

                return response()->json(['content' => $content]);
            }
        }

        $errorMessage = 'Unknown error';
        if ($response->json() && isset($response->json()['error']['message'])) {
            $errorMessage = $response->json()['error']['message'];

            // Handle common errors with user-friendly messages
            if (str_contains($errorMessage, 'quota') || str_contains($errorMessage, 'Quota')) {
                $errorMessage = '⚠️ Đã hết quota miễn phí! Vui lòng đợi 24h để reset hoặc kiểm tra usage tại ai.dev/usage';
            } elseif (str_contains($errorMessage, 'expired') || str_contains($errorMessage, 'invalid')) {
                $errorMessage = '🔑 API key đã hết hạn hoặc không hợp lệ! Vui lòng tạo API key mới tại aistudio.google.com';
            } elseif (str_contains($errorMessage, 'not found') || str_contains($errorMessage, 'model')) {
                $errorMessage = '🤖 Model không tồn tại! Hãy click "Models" để xem danh sách model có sẵn';
            }
        }

        return response()->json(['error' => $errorMessage]);
    }

    private function updateStats($usage)
    {
        $ai = json_decode(setting('ai', '{}'), true);
        $stats = $ai['stats'] ?? ['total_requests' => 0, 'total_tokens' => 0, 'estimated_cost' => 0];

        $stats['total_requests']++;
        $stats['total_tokens'] += $usage['total_tokens'] ?? 0;
        $stats['estimated_cost'] += ($usage['total_tokens'] ?? 0) * 0.00002; // Rough estimate

        $ai['stats'] = $stats;
        setting(['ai' => json_encode($ai, JSON_UNESCAPED_UNICODE)]);
    }

    /**
     * List available Gemini models
     */
    public function listModels(Request $request)
    {
        $apiKey = $request->api_key;

        try {
            $response = Http::withHeaders([
                'X-goog-api-key' => $apiKey,
            ])->get('https://generativelanguage.googleapis.com/v1beta/models');

            if ($response->successful()) {
                $models = collect($response->json()['models'] ?? [])
                    ->filter(function ($model) {
                        return str_contains($model['name'], 'gemini') &&
                               in_array('generateContent', $model['supportedGenerationMethods'] ?? []);
                    })
                    ->map(function ($model) {
                        return [
                            'name' => str_replace('models/', '', $model['name']),
                            'displayName' => $model['displayName'] ?? $model['name'],
                            'description' => $model['description'] ?? '',
                        ];
                    })
                    ->values();

                return response()->json(['success' => true, 'models' => $models]);
            }

            $errorMessage = 'Không thể lấy danh sách models';
            if ($response->json() && isset($response->json()['error']['message'])) {
                $errorMessage = $response->json()['error']['message'];

                if (str_contains($errorMessage, 'expired') || str_contains($errorMessage, 'invalid')) {
                    $errorMessage = '🔑 API key đã hết hạn hoặc không hợp lệ! Vui lòng tạo API key mới';
                }
            }

            return response()->json(['success' => false, 'message' => $errorMessage]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    /**
     * Get valid Gemini model name - Only free models that actually work
     */
    private function getValidGeminiModel($model)
    {
        // Only use confirmed working FREE models
        $freeWorkingModels = [
            'gemini-1.5-flash',
            'gemini-1.5-flash-latest',
            'gemini-1.5-pro',
            'gemini-1.5-pro-latest',
        ];

        // If the requested model is in our confirmed working list, use it
        if (in_array($model, $freeWorkingModels)) {
            return $model;
        }

        // Otherwise, return the most reliable free one
        return 'gemini-1.5-flash';
    }
}
