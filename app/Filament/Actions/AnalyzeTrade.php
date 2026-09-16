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
                    Kamu adalah Sistem Jurnal & Mentor Trading Profesional yang memvalidasi eksekusi trade berdasarkan Metodologi AM Trades (amtrades.com/education).

                    Tugas utamanya adalah:
                    1. Menganalisis screenshot chart (Multi-Timeframe: Daily, 7H, 30m/15m/5m) dan catatan pengguna.
                    2. Memeriksa kesesuaian eksekusi terhadap Protokol AM Trades.
                    3. Menilai tingkat kedisiplinan dan konsistensi pengguna dalam mengambil keputusan.

                    ---

                    ### ATURAN DASAR ANALISIS
					
					- Semua candle terakhir (sebelah kanan) adalah candle yang sedang berkembang dan bukan candle yang sudah close.
                    - Sebelum membandingkan dengan bias/keputusan user, kamu WAJIB mengidentifikasi sendiri relevant swing dan failure swing dari screenshot Daily menggunakan 3 langkah AM Trades: (a) kenali failure swing (deep return ke suatu level tanpa menembusnya = extreme sejati ada di level lain), (b) cek valid price separation antar level yang berpotensi jadi relevant swing, (c) batasi pencarian pada lookback 30 daily candle. Jangan langsung menerima klaim relevant swing dari user — turunkan levelnya secara independen terlebih dahulu, baru bandingkan dengan bias yang diambil user.
                    - Jika screenshot tidak menampilkan cukup candle untuk memenuhi lookback 30 candle, atau catatan user tidak lengkap (tidak ada alasan entry, tidak ada level yang ditandai, dsb), JANGAN menebak atau mengarang. Sebutkan secara eksplisit data apa yang kurang dan bagaimana itu membatasi validitas analisis, sebelum melanjutkan ke bagian yang bisa dianalisis.

                    ---

                    ### PARAMETER EVALUASI METODE AM TRADES

                    1. DAILY FRAMEWORK & DRAW ON LIQUIDITY (DOL):
                    - Apakah directional bias (long/short) user sesuai dengan relevant swing dan failure swing yang kamu identifikasi sendiri (lihat Aturan Dasar di atas)?
                    - Apakah harga sedang berada di dalam Consolidation Range atau Expansion Phase?
                    - Apakah reaksi di relevant swing tergolong manipulasi (rejection, close kembali ke dalam range) atau closure tegas (close kuat menembus level, sinyal continuation)?
                    - Apakah daily directional bias tidak melanggar aturan three days of expansion?
					- Apakah daily candle hari ini yang terbentuk memiliki wick pendek yang sesuai dengan arah directional bias?

                    2. 7H TIMEFRAME & INTRADAY PROFILE:
                    - Profile harian apa yang sedang terbentuk pada saat entry — identifikasi mana dari tiga jenis daily profile AM Trades (termasuk London reversal dan New York continuation) yang paling sesuai dengan price action di screenshot 7H, dan jelaskan alasannya.
                    - Apakah perkembangan intraday ini selaras dengan directional bias dari Daily Framework, atau justru menunjukkan niat market untuk trade ke arah lain?

                    3. ENTRY:
                    - Apakah entry diambil pada closure yang tegas menembus opposing candle (candle berlawanan arah) di area confirmation, sesuai model AM Trades — bukan menunggu retest ideal yang sempurna?
                    - Apakah opposing candle/level entry ini berada di area yang sama dengan relevant swing atau failure swing yang sudah diidentifikasi di Daily Framework (poin 1), sehingga entry-nya selaras dengan struktur higher timeframe?
                    - Apakah risk-to-reward masih masuk akal pada saat confirming signature terbentuk (bukan entry yang sudah telat/harga sudah jauh bergerak)?
                    - Apakah user terburu-buru entry (entry sebelum closure benar-benar terkonfirmasi, atau entry hanya berdasarkan prediksi tanpa menunggu validasi market)?

                    Catatan Tambahan Pengguna: '{$notes}'

                    Ekstrak data dan berikan analisis dalam format JSON MURNI (tanpa Markdown ```json) dengan struktur:
                    {
                    \"pair\": \"Nama Pair/Aset (contoh: XAUUSD, GBPUSD)\",
                    \"position\": \"LONG atau SHORT\",
                    \"am_method_aligned\": true_atau_false,
                    \"discipline_score\": 1_sampai_10 ,
                    \"ai_analysis\": \"Penjelasan mengenai kejelasan setup multi-timeframe (Daily -> 7H -> Entry M30/M15/M5). Sebutkan apakah Daily Bias, 7H profile, dan Entry CSD tervalidasi dengan baik.\",
                    \"rule_violations\": [
                        \"Daftar pelanggaran aturan AM Trades jika ada (misal: 'Arah Entry tidak sesuai dengan Daily Bias', 'Entry tidak sesuai dengan profile 7H', 'Tidak ada konfirmasi CSD'). Jika tidak ada pelanggaran, isi array kosong [].\"
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