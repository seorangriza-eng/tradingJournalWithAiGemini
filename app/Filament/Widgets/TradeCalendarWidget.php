<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\Trades\TradesResource;
use App\Models\Trades;
use Override;
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
                        $color = '#179d48';
                    } else if($trade->result === "Lose"){
                        $color = '#a22121';
                    } else {
                        $color = '#646464';
                    }
                }

                $title = "{$trade->pair} - {$trade->discipline_score} ";

                return [
                    'id'    => $trade->id,
                    'title' => $title,
                    'start' => $trade->created_at->toIso8601String(),
                    'end'   => $trade->created_at->toIso8601String(),
                    'backgroundColor' => $color,
                    'borderColor'     => $color,
                    'textColor'       => '#ffffff',
                    'url'             => TradesResource::getUrl('view', ['record' => $trade->id]),
                ];
            })
            ->toArray();
    }

    #[Override]
    protected function headerActions(): array
    {
        return [
            //
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
