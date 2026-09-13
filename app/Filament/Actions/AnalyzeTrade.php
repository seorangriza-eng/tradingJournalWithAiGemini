<?php

namespace App\Filament\Actions;

use App\Models\Trades;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AnalyzeTrade extends Action
{
    public static function make(?string $name = "AiReflection"): static
    {
        return parent::make($name)
            ->label("Analyze Trade")
            ->icon('heroicon-m-sparkles')
            ->color('info')
            ->requiresConfirmation()
            ->modalHeading('Analisis Chart dengan Gemini')
            ->modalDescription('Apakah kamu yakin ingin menganalisis chart trade ini menggunakan AI?')
            ->modalSubmitActionLabel('Ya, Analisis Sekarang')
            ->action(function (Trades $record): void {
                try {
                    $apiKey = env('GEMINI_API_KEY');
                    if (!$apiKey) {
                        Notification::make()
                            ->title('API Key Error')
                            ->body('GEMINI_API_KEY belum diset di file .env')
                            ->danger()
                            ->send();
                        return;
                    }

                    // 1. Ambil data dari $record (seperti bayanganmu)
                    $notes = $record->note_transcript ?? 'Tidak ada catatan tambahan.';
                    $chartImages =$record->chart_images ?? [];

                    if (empty($chartImages)) {
                        Notification::make()
                            ->title('Tidak Ada Gambar')
                            ->body('Unggah minimal 1 gambar chart terlebih dahulu.')
                            ->warning()
                            ->send();
                        return;
                    }

                    // 2. Susun Prompt AM Trades
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

                    Catatan Tambahan Pengguna: '{$notes}'

                    Ekstrak data dan berikan analisis dalam format JSON MURNI (tanpa Markdown ```json) dengan struktur:
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
                        ['text' => $prompt]
                    ];

                    // 3. Ambil file gambar dari $record->chart_images
                    foreach ($chartImages as $imagePath) {
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

                    // 4. Kirim Request ke Gemini 3.6 Flash Direct HTTP
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
                            // 5. Lakukan $record->update() sesuai bayanganmu!
                            $record->update([
                                'pair'         => $parsedData['pair'] ?? $record->pair,
                                'position'     => $parsedData['position'] ?? null,
                                'ai_analysis'  => $parsedData['ai_analysis'] ?? null,
                                'am_method_aligned' => $parsedData['am_method_aligned'] ?? null,
                                'discipline_score' => $parsedData['discipline_score'] ?? null,
                                'rule_violations' => $parsedData['rule_violations'] ?? null, 
                                'consistency_eval' => $parsedData['consistency_eval'] ?? null,
                            ]);

                            Notification::make()
                                ->title('Analisis Berhasil!')
                                ->body("Trade ID #{$record->id} berhasil dianalisis oleh AI.")
                                ->success()
                                ->send();
                        } else {
                            Log::error("JSON Parsing Error pada Trade ID #{$record->id}: " . $responseText);
                            Notification::make()->title('Gagal Membaca Respons JSON AI')->danger()->send();
                        }
                    } else {
                        Log::error('Gemini API Error: ' . $response->body());
                        Notification::make()->title('Gagal Menghubungi Server Gemini')->danger()->send();
                    }

                } catch (\Exception $e) {
                    Log::error('Analyze Action Gemini Exception: ' . $e->getMessage());
                }
            });
    }
}