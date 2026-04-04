<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Banner extends Model
{
    protected $fillable = [
        'image_path',
        'title',
        'subtitle',
        'button_text',
        'button_link',
        'order',
        'is_active',
        'title_color',
        'subtitle_color',
        'title_size',
        'subtitle_size',
        'button_color',
        'button_text_color',
        'buttons',
    ];

    protected $casts = [
        'buttons' => 'array',
    ];
}
