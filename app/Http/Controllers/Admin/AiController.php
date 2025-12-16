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
            'Authorization' => 'Bearer ' . $apiKey,
            'Content-Type' => 'application/json',
        ])->post('https://api.openai.com/v1/chat/completions', [
            'model' => $model,
            'messages' => [['role' => 'user', 'content' => 'Hello']],
            'max_tokens' => 10
        ]);
        
        if ($response->successful()) {
            return response()->json(['success' => true, 'message' => '✓ Kết nối thành công! Model: ' . $model]);
        }
        
        return response()->json(['success' => false, 'message' => 'Lỗi: ' . $response->json()['error']['message'] ?? 'Unknown error']);
    }
    

    
    private function testGemini($apiKey, $model)
    {
        $response = Http::post("https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key={$apiKey}", [
            'contents' => [['parts' => [['text' => 'Hello']]]]
        ]);
        
        if ($response->successful()) {
            return response()->json(['success' => true, 'message' => '✓ Kết nối thành công! Model: ' . $model]);
        }
        
        return response()->json(['success' => false, 'message' => 'Lỗi: ' . $response->json()['error']['message'] ?? 'Unknown error']);
    }
    
    public function generate(Request $request)
    {
        $ai = json_decode(setting('ai', '{}'), true);
        
        if (!($ai['enabled'] ?? false)) {
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
            $model = 'gemini-pro';
        } else {
            return response()->json(['error' => 'Chưa cấu hình API key']);
        }
        $temperature = $ai['temperature'] ?? 0.7;
        $maxTokens = $ai['max_tokens'] ?? 2000;
        
        try {
            switch ($provider) {
                case 'openai':
                    return $this->generateOpenAI($apiKey, $model, $request->prompt, $temperature, $maxTokens);

                case 'gemini':
                    return $this->generateGemini($apiKey, $model, $request->prompt, $temperature, $maxTokens);
            }
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()]);
        }
    }
    
    private function generateOpenAI($apiKey, $model, $prompt, $temperature, $maxTokens)
    {
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $apiKey,
            'Content-Type' => 'application/json',
        ])->post('https://api.openai.com/v1/chat/completions', [
            'model' => $model,
            'messages' => [['role' => 'user', 'content' => $prompt]],
            'temperature' => (float) $temperature,
            'max_tokens' => (int) $maxTokens
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
        $response = Http::post("https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key={$apiKey}", [
            'contents' => [['parts' => [['text' => $prompt]]]],
            'generationConfig' => [
                'temperature' => (float) $temperature,
                'maxOutputTokens' => (int) $maxTokens
            ]
        ]);
        
        if ($response->successful()) {
            $content = $response->json()['candidates'][0]['content']['parts'][0]['text'];
            return response()->json(['content' => $content]);
        }
        
        return response()->json(['error' => $response->json()['error']['message'] ?? 'Unknown error']);
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
}

