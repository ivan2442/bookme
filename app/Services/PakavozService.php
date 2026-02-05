<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PakavozService
{
    private string $baseUrl = 'https://pakavoz.sk/api';

    /**
     * Zistí dostupnosť termínov pre daný dátum.
     */
    public function getAvailability(string $apiKey, ?string $date = null): array
    {
        try {
            $response = Http::withHeaders([
                'X-API-KEY' => $apiKey,
                'Accept' => 'application/json',
            ])->get("{$this->baseUrl}/v1/availability", [
                'date' => $date,
            ]);

            if ($response->successful()) {
                return $response->json();
            }

            Log::error('Pakavoz API availability error', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);
        } catch (\Exception $e) {
            Log::error('Pakavoz API exception', [
                'message' => $e->getMessage(),
            ]);
        }

        return ['availability' => []];
    }

    /**
     * Vytvorí rezerváciu v systéme Pakavoz.
     */
    public function createReservation(string $apiKey, array $data): array
    {
        try {
            $response = Http::withHeaders([
                'X-API-KEY' => $apiKey,
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ])->post("{$this->baseUrl}/v1/reservation", [
                'name' => $data['name'],
                'phone' => $data['phone'],
                'email' => $data['email'] ?? null,
                'evc' => $data['evc'],
                'date' => $data['date'], // YYYY-MM-DD
                'time' => $data['time'], // HH:mm
                'model' => $data['model'] ?? null,
                'note' => $data['note'] ?? null,
            ]);

            if ($response->status() === 201) {
                return [
                    'success' => true,
                    'data' => $response->json(),
                ];
            }

            return [
                'success' => false,
                'message' => $response->json()['message'] ?? 'Chyba pri vytváraní rezervácie v systéme Pakavoz.',
                'status' => $response->status(),
            ];
        } catch (\Exception $e) {
            Log::error('Pakavoz API reservation exception', [
                'message' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'message' => 'Nepodarilo sa spojiť so systémom Pakavoz.',
            ];
        }
    }
}
