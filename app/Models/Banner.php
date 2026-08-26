<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Banner extends Model
{
    protected $fillable = [
        'title',
        'subtitle',
        'image_url',
        'mobile_image_url',
        'prompt',
        'active',
    ];

    protected function casts(): array
    {
        return [
            'active' => 'boolean',
        ];
    }

    public function desktopImage(): string
    {
        return $this->image_url;
    }

    public function mobileImage(): string
    {
        return $this->mobile_image_url ?: $this->image_url;
    }
}
