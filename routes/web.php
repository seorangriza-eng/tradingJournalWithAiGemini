<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Http;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/gemini', function () {
    $apiKey = env('GEMINI_API_KEY');

    if (!$apiKey) {
        return response()->json(['error' => 'GEMINI_API_KEY belum diset di .env'], 500);
    }

    // Menggunakan model gemini-3.6-flash
    $response = Http::post("https://generativelanguage.googleapis.com/v1beta/models/gemini-3.6-flash:generateContent?key={$apiKey}", [
        'contents' => [
            [
                'parts' => [
                    ['text' => 'Halo Gemini, jika kamu menerima pesan ini, jawab dengan kalimat: Koneksi Gemini Berhasil!']
                ]
            ]
        ]
    ]);

    if ($response->successful()) {
        $data = $response->json();
        $text = $data['candidates'][0]['content']['parts'][0]['text'] ?? 'Tidak ada respon teks';
        
        return response()->json([
            'status' => 'SUCCESS',
            'gemini_response' => $text
        ]);
    }

    return response()->json([
        'status' => 'FAILED',
        'http_code' => $response->status(),
        'error_details' => $response->json()
    ], $response->status());
});