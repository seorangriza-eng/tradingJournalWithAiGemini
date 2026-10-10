<?php

namespace App\Filament\Widgets;

use App\Models\Trades;
use Saade\FilamentFullCalendar\Widgets\FullCalendarWidget;

class TradeCalendarWidget extends FullCalendarWidget
{
    protected static ?int $sort = 4;
    
    public function fetchEvents(array $fetchInfo): array
    {
        return Trades::query()
            ->where('created_at', '>=', $fetchInfo['start'])
            ->where('created_at', '<=', $fetchInfo['end'])
            ->get()
            ->map(function (Trades $trade) {
                // Tentukan warna latar belakang berdasarkan Win / Loss / Discipline
                $color = '#3b82f6'; // Default Biru
                if ($trade->result !== null) {
                    if($trade->result === "Win"){
                        $color = '#22c55e';
                    } else if($trade->result === "Lose"){
                        $color = '#ef4444';
                    } else {
                        $color = '#646464';
                    }
                }

                // Format teks judul di kalender: "XAUUSD | Score: 8/10"
                $title = "{$trade->pair} ";

                return [
                    'id'    => $trade->id,
                    'title' => $title,
                    'start' => $trade->created_at->toIso8601String(),
                    'end'   => $trade->created_at->toIso8601String(),
                    'backgroundColor' => $color,
                    'borderColor'     => $color,
                    'textColor'       => '#ffffff',
                ];
            })
            ->toArray();
    }

    /**
     * Opsi klik event untuk melihat detail/pindah ke halaman view trade
     */
    protected function headerActions(): array
    {
        return [
            // Tambahkan tombol action global jika diperlukan
        ];
    }

    /**
     * Konfigurasi tampilan FullCalendar
     */
    public function config(): array
    {
        return [
            'initialView' => 'dayGridMonth',
            'displayEventTime' => false,
            'eventDisplay' => 'block',
            'headerToolbar' => [
                'right'   => 'prev,next today',
                'center' => 'title',
                'left'  => null,
            ],
        ];
    }
}
