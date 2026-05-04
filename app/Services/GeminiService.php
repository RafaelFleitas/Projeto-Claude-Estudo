<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class GeminiService
{
    public function __construct(
        private readonly string $apiKey,
        private readonly string $model,
    ) {}

    public function analyze(string $filePath, string $prompt): string
    {
        $content = Storage::disk('local')->get($filePath);
        $encoded = base64_encode($content);

        $extension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));

        $mimeType = match ($extension) {
            'jpg', 'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            'gif' => 'image/gif',
            'webp' => 'image/webp',
            default => 'application/pdf',
        };

        $url = "https://generativelanguage.googleapis.com/v1beta/models/{$this->model}:generateContent?key={$this->apiKey}";

        $response = Http::withHeaders(['Content-Type' => 'application/json'])
            ->post($url, [
                'contents' => [
                    [
                        'parts' => [
                            ['inline_data' => ['mime_type' => $mimeType, 'data' => $encoded]],
                            ['text' => $prompt],
                        ],
                    ],
                ],
            ]);

        if ($response->failed()) {
            throw new \RuntimeException('Gemini API error: '.$response->body());
        }

        return $response->json('candidates.0.content.parts.0.text', 'Não foi possível gerar análise.');
    }

    public static function make(): static
    {
        return new static(
            config('services.gemini.key'),
            config('services.gemini.model'),
        );
    }
}
