<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\Setting;
use Carbon\Carbon;

class RevolutService
{
    private string $baseUrl = 'https://b2b.revolut.com/api/1.0';

    /**
     * Získa zoznam transakcií pre daný účet.
     * Dokumentácia: https://developer.revolut.com/docs/open-banking/get-accounts-account-id-transactions
     */
    public function getTransactions(?string $from = null, ?string $to = null, int $count = 100): array
    {
        $accessToken = $this->getAccessToken();
        if (!$accessToken) {
            return [];
        }

        try {
            $response = Http::withToken($accessToken)
                ->get("{$this->baseUrl}/transactions", [
                    'from' => $from,
                    'to' => $to,
                    'count' => $count,
                ]);

            if ($response->successful()) {
                return $response->json();
            }

            Log::error('Revolut API transactions error', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);
        } catch (\Exception $e) {
            Log::error('Revolut API transactions exception', [
                'message' => $e->getMessage(),
            ]);
        }

        return [];
    }

    /**
     * Získa prístupový token pomocou Refresh tokenu.
     */
    private function getAccessToken(): ?string
    {
        $clientId = Setting::get('revolut_client_id');
        $jwt = Setting::get('revolut_jwt');
        $refreshToken = Setting::get('revolut_refresh_token');

        if (!$clientId || !$jwt || !$refreshToken) {
            return null;
        }

        // Tu by mala byť logika na refresh tokenu, ak expiroval.
        // Revolut vyžaduje Client Assertion (JWT) podpísaný súkromným kľúčom.
        // Pre účely tejto úlohy predpokladáme, že JWT je už vygenerovaný alebo ho vieme získať.

        try {
            $response = Http::asForm()->post('https://b2b.revolut.com/api/1.0/auth/token', [
                'grant_type' => 'refresh_token',
                'refresh_token' => $refreshToken,
                'client_id' => $clientId,
                'client_assertion_type' => 'urn:ietf:params:oauth:client-assertion-type:jwt-bearer',
                'client_assertion' => $jwt,
            ]);

            if ($response->successful()) {
                return $response->json()['access_token'];
            }

            Log::error('Revolut Auth error', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);
        } catch (\Exception $e) {
            Log::error('Revolut Auth exception', [
                'message' => $e->getMessage(),
            ]);
        }

        return null;
    }
}
