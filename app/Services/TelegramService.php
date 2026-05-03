<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class TelegramService
{
    private string $baseUrl;

    public function __construct()
    {
        $token = config('services.telegram.bot_token');
        $this->baseUrl = "https://api.telegram.org/bot{$token}";
    }

    public function sendMessage(string $message): void
    {
        $this->client()
            ->post("{$this->baseUrl}/sendMessage", [
                'chat_id'    => config('services.telegram.chat_id'),
                'text'       => $message,
                'parse_mode' => 'HTML',
            ])
            ->throw();
    }

    public function sendDocument(string $filePath, string $caption = ''): void
    {
        $absolutePath = Storage::path($filePath);
        $fileName     = basename($filePath);

        $this->client()
            ->attach('document', file_get_contents($absolutePath), $fileName)
            ->post("{$this->baseUrl}/sendDocument", [
                'chat_id' => config('services.telegram.chat_id'),
                'caption' => $caption,
            ])
            ->throw();
    }

    private function client(): \Illuminate\Http\Client\PendingRequest
    {
        $client = Http::timeout(30);

        if (app()->isLocal()) {
            $client = $client->withoutVerifying();
        }

        return $client;
    }
}
