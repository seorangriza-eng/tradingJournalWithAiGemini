<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Trades extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'chart_images' => 'array',
    ];

    protected function casts(): array
    {
        return [
            'chart_images' => 'array', // atau 'json'
        ];
    }

}
