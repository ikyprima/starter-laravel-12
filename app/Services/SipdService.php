<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class SipdService
{
    protected string $baseUrl;
    protected string $clientId;
    protected string $clientSecret;

    public function __construct()
    {
        $this->baseUrl = rtrim(config('services.sipd.base_url'), '/');
        $this->clientId = config('services.sipd.client_id');
        $this->clientSecret = config('services.sipd.client_secret');
    }

    /**
     * Get the access token using client_credentials grant.
     */
    public function getAccessToken()
    {
        return Cache::remember('sipd.access_token', now()->addMinutes(55), function () {
            try {
                $response = Http::asForm()
                    ->timeout(10)
                    ->withoutVerifying()
                    ->withHeaders(['Accept' => 'application/json'])
                    ->post($this->baseUrl . '/oauth/token', [
                        'grant_type' => 'client_credentials',
                        'client_id' => $this->clientId,
                        'client_secret' => $this->clientSecret,
                        'scope' => '',
                    ]);

                if ($response->failed()) {
                    Log::error('SIPD Auth Failed', [
                        'url' => $this->baseUrl . '/oauth/token',
                        'status' => $response->status(),
                        'body' => $response->body()
                    ]);
                    return null;
                }

                return $response->json('access_token');
            } catch (\Exception $e) {
                Log::error('SIPD Auth Exception: ' . $e->getMessage());
                return null;
            }
        });
    }

    /**
     * Fetch Sub SKPD data from the external API.
     */
    public function getSubSkpd($tahun = 2026)
    {
        try {
       
            $token = $this->getAccessToken();
            
            if (!$token) {
                Log::error('IKD API Cancelled: No Access Token');
                return null;
            }

            $response = Http::withToken($token)
                ->timeout(30)
                ->withoutVerifying()
                ->get($this->baseUrl . '/api/master/sub-skpd', [
                    'tahun' => $tahun
                ]);

            if ($response->failed()) {
                Log::error('IKD API Failed', [
                    'url' => $this->baseUrl . '/api/master/sub-skpd',
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);
                return null;
            }

            return $response->json();
        } catch (\Exception $e) {
            Log::error('SIPD API Exception: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Fetch SKPD data from the external API.
     */
    public function getSkpd($tahun = 2026)
    {
        try {
            $token = $this->getAccessToken();
            
            if (!$token) {
                Log::error('IKD API Cancelled: No Access Token (SKPD)');
                return null;
            }

            $response = Http::withToken($token)
                ->timeout(30)
                ->withoutVerifying()
                ->get($this->baseUrl . '/api/master/skpd', [
                    'tahun' => $tahun
                ]);

            if ($response->failed()) {
                Log::error('IKD API Failed (SKPD)', [
                    'url' => $this->baseUrl . '/api/master/skpd',
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);
                return null;
            }

            return $response->json();
        } catch (\Exception $e) {
            Log::error('SIPD API Exception (SKPD): ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Get a specific Sub SKPD by its code.
     */
    public function findSubSkpdByKode($kode, $tahun = 2026)
    {
        $data = $this->getSubSkpd($tahun);
        
        if (!$data || !isset($data['result'])) {
            return null;
        }

        return collect($data['result'])->firstWhere('kode_sub_skpd', $kode);
    }

    /**
     * Fetch realization data from external API.
     */
    public function getRealisasi($idskpd, $bulan)
    {
        try {
            $token = $this->getAccessToken();
            
            if (!$token) {
                Log::error('IKD API Cancelled: No Access Token (Realisasi)');
                return null;
            }

            $response = Http::withToken($token)
                ->timeout(60)
                ->withoutVerifying()
                ->get($this->baseUrl . '/api/laporan/realisasi', [
                    'idskpd' => $idskpd,
                    'bulan' => $bulan
                ]);

            if ($response->failed()) {
                Log::error('IKD API Failed (Realisasi)', [
                    'url' => $this->baseUrl . '/api/laporan/realisasi',
                    'idskpd' => $idskpd,
                    'bulan' => $bulan,
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);
                return $response->json(); // Return json anyway to catch error message
            }

            return $response->json();
        } catch (\Exception $e) {
            Log::error('SIPD API Exception (Realisasi): ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Fetch BKU Pajak data from external API.
     */
    public function getBkuPajak($idskpd, $bulan, $tahun)
    {
        try {
            $token = $this->getAccessToken();
            
            if (!$token) {
                Log::error('IKD API Cancelled: No Access Token (BKU Pajak)');
                return null;
            }

            $response = Http::withToken($token)
                ->timeout(60)
                ->withoutVerifying()
                ->get($this->baseUrl . '/api/bku/pajak', [
                    'idskpd' => $idskpd,
                    'bulan' => $bulan,
                    'tahun' => $tahun
                ]);

            if ($response->failed()) {
                Log::error('IKD API Failed (BKU Pajak)', [
                    'url' => $this->baseUrl . '/api/bku/pajak',
                    'idskpd' => $idskpd,
                    'bulan' => $bulan,
                    'tahun' => $tahun,
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);
                return $response->json();
            }

            return $response->json();
        } catch (\Exception $e) {
            Log::error('SIPD API Exception (BKU Pajak): ' . $e->getMessage());
            return null;
        }
    }
    /**
     * Fetch BKU data from external API.
     */
    public function getBku($idskpd, $bulan, $tahun)
    {
        try {
            $token = $this->getAccessToken();
            
            if (!$token) {
                Log::error('IKD API Cancelled: No Access Token (BKU)');
                return null;
            }

            $response = Http::withToken($token)
                ->timeout(60)
                ->withoutVerifying()
                ->get($this->baseUrl . '/api/bku/skpd', [
                    'idskpd' => $idskpd,
                    'bulan' => $bulan,
                    'tahun' => $tahun
                ]);

            if ($response->failed()) {
                Log::error('IKD API Failed (BKU)', [
                    'url' => $this->baseUrl . '/api/bku/skpd',
                    'idskpd' => $idskpd,
                    'bulan' => $bulan,
                    'tahun' => $tahun,
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);
                return $response->json();
            }

            return $response->json();
        } catch (\Exception $e) {
            Log::error('SIPD API Exception (BKU): ' . $e->getMessage());
            return null;
        }
    }

    public function checkConnection()
    {

    //    respon error kode 500 jika session hapus
    //     {
    //         "status": false,
    //         "message": "response unseccesful",
    //         "data": {
    //             "error": true,
    //             "status": 400,
    //             "message": "",
    //             "data": {
    //                 "error": true,
    //                 "msg": "Missing or malformed JWT"
    //             },
    //             "hint": "Session mungkin expired. Silakan login ulang via /api/scrape"
    //         },
    //         "statusCodeSipd": 400
    //     }


    //    respon error kode 500 jika server bridge  hapus
    // {
    //     "status": false,
    //     "message": "response unseccesful",
    //     "data": {
    //         "error": true,
    //         "message": "Attempted to use detached Frame '1AB5D3C915C619614F2A4CA7636E59B8'.",
    //         "hint": "Jika error terus terjadi, coba restart server dan login ulang"
    //     },
    //     "statusCodeSipd": 500
    // }
        try {
            $token = $this->getAccessToken();
            if (!$token) {
                return [
                    'success' => false,
                    'message' => 'Gagal mendapatkan Access Token. Cek konfigurasi Client ID/Secret.'
                ];
            }

            $response = Http::withToken($token)
                ->timeout(10)
                ->withoutVerifying()
                ->get($this->baseUrl . '/api/webservice/cek-koneksi');

            if ($response->successful()) {
                return [
                    'success' => true,
                    'message' => 'Koneksi Berhasil'
                ];
            }

            $data = $response->json();
            $hint = $data['data']['hint'] ?? $data['message'] ?? 'Webservice Mati';

            return [
                'success' => false,
                'message' => $hint
            ];
        } catch (\Exception $e) {
            Log::error('SIPD Connection Check Exception: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Terjadi kesalahan sistem: ' . $e->getMessage()
            ];
        }
    }
}
