<?php
namespace App\Http\Controllers;

use App\Jobs\AnalyzeTradeJob;
use App\Models\Trades;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/* 
pasang ini di browser untuk setting telegram bot agar terhubung di web
https://api.telegram.org/bot<TOKEN_BOT_KAMU>/setWebhook?url=https://riza.freehosting.dev/api/telegram/webhook

ini untuk mereset antrian telegram agar menjadi 0
https://api.telegram.org/bot<TOKEN_BOT_KAMU>/setWebhook?url=https://riza.freehosting.dev/api/telegram/webhook&drop_pending_updates=true
*/
class TelegramWebhookController extends Controller
{
    public function handle(Request $request)
    {
        $botToken = env('TELEGRAM_BOT_TOKEN');
        $data = $request->all();

        if (!isset($data['message'])) {
            return response()->json(['status' => 'ignored'], 200);
        }

        $message = $data['message'];
        $caption = $message['caption'] ?? $message['text'] ?? null;
        $mediaGroupId = $message['media_group_id'] ?? null; // ID unik jika dikirim sebagai album

        // 1. Cek Apakah Request Memiliki Foto
        if (isset($message['photo'])) {
            // Ambil resolusi terbesar dari foto pada request saat ini
            $photos = $message['photo'];
            $largestPhoto = end($photos);
            $fileId = $largestPhoto['file_id'];

            // Dapatkan URL File dari Telegram API
            $response = Http::get("https://api.telegram.org/bot{$botToken}/getFile?file_id={$fileId}");

            if ($response->successful()) {
                $filePathOnTelegram = $response->json()['result']['file_path'];
                $fileContent = Http::get("https://api.telegram.org/file/bot{$botToken}/{$filePathOnTelegram}")->body();

                // Simpan Gambar ke Storage Lokal
                $fileName = 'images/' . Str::uuid() . '.jpg';
                Storage::disk('public')->put($fileName, $fileContent);

                // 2. Logika Penanganan Album (Multiple Photos) vs Single Photo
                if ($mediaGroupId) {
                DB::transaction(function () use ($mediaGroupId, $fileName, $caption) {
                    $trade = Trades::where('media_group_id', $mediaGroupId)->lockForUpdate()->first();

                    if ($trade) {
                        $images = $trade->chart_images ?? [];
                        if (!in_array($fileName, $images)) {
                            $images[] = $fileName;
                        }
                        $trade->update([
                            'chart_images' => $images,
                            'note_transcript' => $trade->notes ?: $caption,
                        ]);
                    } else {
                        $trade = Trades::create([
                            'media_group_id' => $mediaGroupId,
                            'chart_images'   => [$fileName],
                            'note_transcript' => $caption,
                        ]);
                    }
                    
                    // Langsung jalankan Job seketika (Sync)
                    AnalyzeTradeJob::dispatchSync($trade);
                });
            } else {
                $trade = Trades::create([
                    'chart_images' => [$fileName],
                    'note_transcript' => $caption,
                ]);

                // Langsung jalankan Job seketika (Sync)
                AnalyzeTradeJob::dispatchSync($trade);
            }
            }
        }

        return response()->json(['status' => 'success'], 200);
    }
}