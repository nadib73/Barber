<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FonnteService
{
    protected ?string $apiKey;
    protected string $apiUrl = 'https://api.fonnte.com/send';

    public function __construct()
    {
        $this->apiKey = config('services.fonnte.token', env('FONNTE_API_KEY'));
    }

    /**
     * Send WhatsApp message via Fonnte API
     */
    public function sendMessage(string $targetPhone, string $message): bool
    {
        if (empty($this->apiKey)) {
            Log::info("Fonnte API Token not set. Simulated sending WA to {$targetPhone}: \n{$message}");
            return true;
        }

        try {
            $formattedPhone = $this->formatPhoneNumber($targetPhone);

            $response = Http::withHeaders([
                'Authorization' => $this->apiKey,
            ])->post($this->apiUrl, [
                'target' => $formattedPhone,
                'message' => $message,
                'countryCode' => '62',
            ]);

            if ($response->successful()) {
                Log::info("WhatsApp sent to {$formattedPhone}: {$response->body()}");
                return true;
            }

            Log::error("Fonnte API error ({$response->status()}): {$response->body()}");
            return false;
        } catch (\Exception $e) {
            Log::error("Fonnte exception: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Format phone number to Indonesian format (628xxx or 08xxx)
     */
    protected function formatPhoneNumber(string $phone): string
    {
        $cleaned = preg_replace('/[^0-9]/', '', $phone);
        if (str_starts_with($cleaned, '0')) {
            $cleaned = '62' . substr($cleaned, 1);
        } elseif (str_starts_with($cleaned, '8')) {
            $cleaned = '62' . $cleaned;
        }
        return $cleaned;
    }
}
