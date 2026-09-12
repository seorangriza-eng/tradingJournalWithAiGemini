<?php

namespace App\Jobs;

use App\Models\Trades;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AnalyzeTradeJob implements ShouldQueue
{
    use Queueable;

    public Trades $trade;

    /**
     * Create a new job instance.
     */
    public function __construct(Trades $trade)
    {
        $this->trade = $trade;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {   
            $this->trade->refresh();

            $apiKey = env('GEMINI_API_KEY');
            if (!$apiKey) {
                Log::error('Gemini Job Error: GEMINI_API_KEY belum diset di .env');
                return;
            }

            // Tambahkan Prompt Instruksi di bagian awal
            $prompt = "
            Kamu adalah Sistem Jurnal & Mentor Trading Profesional yang memvalidasi eksekusi trade berdasarkan Metodologi AM Trades (SMC/ICT Framework).

            Tugas utamanya adalah:
            1. Menganalisis screenshot chart (Multi-Timeframe: Daily, 7H, 30m/15m/5m) dan catatan pengguna.
            2. Memeriksa kesesuaian eksekusi terhadap Protokol AM Trades.
            3. Menilai tingkat kedisiplinan dan konsistensi pengguna dalam mengambil keputusan.

            ---

            ### PARAMETER EVALUASI METODE AM TRADES

            Saat mengevaluasi data trade, periksa poin-poin krusial berikut:

            1. DAILY FRAMEWORK & DRAW ON LIQUIDITY (DOL):
            - Apakah directional bias (Long/Short) sesuai dengan arah Draw on Liquidity (BSL/SSL) pada Daily Chart?
            - Apakah harga sedang berada di dalam Consolidation Range atau Expansion Phase?
            - Peringatan: Jangan pernah Sell di area Discount/Low pada kondisi konsolidasi (misal: selling persis saat sweep PDL), atau Buy di area Premium/High!

            2. 7H TIMEFRAME & INTRADAY PROFILE:
            - Apakah entry dilakukan dengan memperhitungkan posisi harga terhadap Daily Open / Midnight Open (mencari area Premium/Discount)?[cite: 1]
            - Apakah terdapat konfirmasi Change in State of Delivery (CSD) sebelum eksekusi dilakukan?[cite: 1]

            3. PROTOKOL HIGH-IMPACT NEWS DRIVER:
            - Apakah trade dieksekusi terlalu dekat / persis saat rilis berita Red Folder (seperti CPI/PPI)?[cite: 1]
            - Peringatan: AM Trades melarang entry di tengah riak spicing berita tanpa menunggu kestabilan struktur baru.[cite: 1]

            ---

            ### FORMAT OUTPUT (MUST BE VALID JSON ONLY)

            Kembalikan respons HANYA dalam format JSON terstruktur tanpa tanda backtick atau markdown tambahan, dengan skema berikut:

            {
            \"pair\": \"Nama Pair/Aset (contoh: XAUUSD, GBPUSD)\",
            \"position\": \"LONG atau SHORT\",
            \"am_method_aligned\": true_atau_false,
            \"discipline_score\": 1_sampai_10 ,
            \"ai_analysis\": \"Penjelasan 2-3 kalimat mengenai kejelasan setup multi-timeframe (Daily -> 7H -> Entry M15/M5). Sebutkan apakah DOL dan CSD tervalidasi dengan baik.\",
            \"rule_violations\": [
                \"Daftar pelanggaran aturan AM Trades jika ada (misal: 'Entry Sell di area Discount/PDL', 'Trading saat rilis High-Impact News', 'Tidak ada konfirmasi CSD'). Jika tidak ada pelanggaran, isi array kosong [].\"
            ],
            \"consistency_eval\": \"Catatan ringkas 1-2 kalimat untuk melatih mentalitas dan konsistensi trader berdasarkan keputusan ini.\"
            }
            ";

            $contentsParts = [
                ['text' => $prompt] // Prompt AM Trades kamu
            ];

            // Ambil SEMUA gambar yang sudah terkumpul di kolom chart_images
            if (!empty($this->trade->chart_images)) {
                foreach ($this->trade->chart_images as $imagePath) {
                    $fullPath = storage_path('app/public/' . $imagePath);
                    if (file_exists($fullPath)) {
                        $contentsParts[] = [
                            'inline_data' => [
                                'mime_type' => 'image/jpeg',
                                'data'      => base64_encode(file_get_contents($fullPath))
                            ]
                        ];
                    }
                }
            }

            // Gunakan model gemini-3.6-flash
            $url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-3.6-flash:generateContent?key={$apiKey}";

            $response = Http::post($url, [
                'contents' => [
                    ['parts' => $contentsParts]
                ]
            ]);

            if ($response->successful()) {
                $responseText = $response->json()['candidates'][0]['content']['parts'][0]['text'] ?? '';
                
                $cleanJson = preg_replace('/```(?:json)?|\n```/i', '', trim($responseText));
                $parsedData = json_decode($cleanJson, true);

                if ($parsedData) {
                    $this->trade->update([
                        'pair'         => $parsedData['pair'] ?? $this->trade->pair,
                        'position'     => $parsedData['position'] ?? null,
                        'ai_analysis'  => $parsedData['ai_analysis'] ?? null,
                        'am_method_aligned' => $parsedData['am_method_aligned'] ?? null,
                        'discipline_score' => $parsedData['discipline_score'] ?? null,
                        'rule_violations' => $parsedData['rule_violations'] ?? null, 
                        'consistency_eval' => $parsedData['consistency_eval'] ?? null,
                    ]);
                    Log::info("Gemini Analysis Completed for Trade ID: {$this->trade->id}");
                }
            } else {
                Log::error('Gemini API Response Error: ' . $response->body());
            }
        } catch (\Exception $e) {
            Log::error('AnalyzeTradeJob Error: ' . $e->getMessage());
        }
    }
}
