<?php

namespace App\Filament\Actions;

use App\Models\Trades;
use App\Models\WeeklyReflection;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AiReflection extends Action
{
    public static function make(?string $name = "AiReflection"): static
    {
        return parent::make($name)
            ->label("Ai Reflection")
            ->icon('heroicon-m-sparkles')
            ->color('info')
            ->action(function () : void {
                try {
                    $apiKey = env('GEMINI_API_KEY');
                    if (!$apiKey) {
                        Log::error('AiReflection Error: GEMINI_API_KEY belum diset di .env');
                        Notification::make()->title('API Key Tidak Ditemukan')->danger()->send();
                        return;
                    }

                    // 1. LOGIKA AMBIL DATA TRADES PERIODE MINGGU INI (Senin - Minggu)
                    $startOfWeek = Carbon::now()->startOfWeek(); // Senin 00:00:00
                    $endOfWeek   = Carbon::now()->endOfWeek();   // Minggu 23:59:59

                    $trades = Trades::whereBetween('created_at', [$startOfWeek, $endOfWeek])->get();

                    if ($trades->isEmpty()) {
                        Notification::make()
                            ->title('Tidak Ada Data Trade')
                            ->body('Belum ada transaksi yang tercatat untuk minggu ini.')
                            ->warning()
                            ->send();
                        return;
                    }

                    // Format ringkas data transaksi minggu ini untuk dikirim ke Gemini
                    $tradesSummary = $trades->map(function ($t, $index) {
                        return [
                            'no' => $index + 1,
                            'tanggal' => $t->created_at->format('Y-m-d H:i'),
                            'pair' => $t->pair,
                            'position' => $t->position,
                            'rule_violations' => $t->rule_violations ?? null,
                            'am_method_aligned' => $t->am_method_aligned ?? null,
                            'discipline_score' => $t->discipline_score ?? null,
							'result' => $t->result ?? null,
                            'notes' => $t->note_transcript ?? null,
                            'ai_analysis_item' => $t->ai_analysis,
                        ];
                    })->toArray();

                    $tradesJsonText = json_encode($tradesSummary, JSON_PRETTY_PRINT);

                    // 2. PROMPT AM TRADES MINGGUAN (DISESUAIKAN & DIPERBAIKI)
                    $prompt = "Kamu adalah Mentor Trading Profesional berbasis Metodologi AM Trades (SMC/ICT Framework).
                    Tugasmu adalah melakukan evaluasi mendalam terhadap seluruh eksekusi trade pengguna selama MINGGU INI.

                    Berikut adalah daftar transaksi pengguna minggu ini:
                    {$tradesJsonText}

                    ---

                    ### INSTRUKSI EVALUASI:
                    1. Hitung dan evaluasi tingkat kedisiplinan (Discipline Score) dan kepatuhan metode AM Trades.
                    2. Identifikasi pola positif yang konsisten (misal: penantian CSD yang baik, manajemen risiko yang tepat).
                    3. Identifikasi kesalahan emosional/teknis yang berulang (misal: Sell di Discount area, FOMO saat High-Impact News, melanggar Stop Loss).
                    4. Berikan saran taktis untuk perbaikan minggu depan.

                    ---

                    ### FORMAT OUTPUT (MUST BE VALID JSON ONLY, TANPA BACKTICK/MARKDOWN):
                    {
                        \"periode\": \"Week {$startOfWeek->weekOfYear} ({$startOfWeek->format('d')} - {$endOfWeek->format('d F Y')})\",
                        \"perlu_dipertahankan\": \"Penjelasan detail berupa poin-poin mengenai kebiasaan baik dan eksekusi yang sudah sesuai metode AM Trades minggu ini.\",
                        \"saran_perbaikan\": \"Penjelasan detail berupa poin perbaikan psikologis, kedisiplinan, dan eksekusi teknis untuk minggu depan.\"
                    }";

                    $contentsParts = [
                        ['text' => $prompt]
                    ];

                    // 3. EKSEKUSI KE GEMINI 3.6 FLASH API
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
                            WeeklyReflection::create([
                                'periode'             => $parsedData['periode'],
                                'perlu_dipertahankan' => $parsedData['perlu_dipertahankan'],
                                'saran_perbaikan'     => $parsedData['saran_perbaikan']
                            ]);

                            Log::info("Gemini Analysis Completed for period: {$parsedData['periode']}");
                            
                            Notification::make()
                                ->title('Refleksi Mingguan Berhasil Dibuat!')
                                ->success()
                                ->send();
                        } else {
                            Log::error('Json Decode Error: String JSON Gemini tidak valid: ' . $responseText);
                        }
                    } else {
                        Log::error('Gemini API Error Response: ' . $response->body());
                    }
                    
                } catch (\Exception $e) {
                    Log::error('AiReflectionAction Exception: ' . $e->getMessage());
                }
            });
    }
}