<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

#[Fillable([
    'user_id', 'title', 'description', 'price', 'total_numbers',
    'status', 'prize_name', 'prize_description', 'image_url', 'images',
    'youtube_url', 'draw_date', 'live_url',
])]
class Raffle extends Model
{
    protected function casts(): array
    {
        return [
            'draw_date' => 'datetime',
            'price' => 'decimal:2',
            'total_numbers' => 'integer',
            'images' => 'array',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }

    public function packages(): HasMany
    {
        return $this->hasMany(RafflePackage::class)->orderBy('sort_order');
    }

    public function draw(): HasOne
    {
        return $this->hasOne(Draw::class);
    }

    public function startingPrice(): float
    {
        $cheapestPackage = $this->relationLoaded('packages')
            ? $this->packages->sortBy('price')->first()
            : $this->packages()->orderBy('price')->first();

        if ($cheapestPackage) {
            return (float) $cheapestPackage->price;
        }

        return (float) $this->price;
    }

    public function syncPackages(array $packages): void
    {
        $this->packages()->delete();

        foreach (array_values($packages) as $index => $package) {
            if (empty($package['name']) || empty($package['numbers_qty']) || empty($package['price'])) {
                continue;
            }

            $this->packages()->create([
                'name' => $package['name'],
                'numbers_qty' => (int) $package['numbers_qty'],
                'price' => $package['price'],
                'highlight' => $package['highlight'] ?? null,
                'is_featured' => ! empty($package['is_featured']),
                'sort_order' => $package['sort_order'] ?? ($index + 1),
            ]);
        }

        $cheapest = $this->packages()->orderBy('price')->first();
        if ($cheapest) {
            $this->update(['price' => $cheapest->price]);
        }
    }

    public function seedDefaultPackages(): void
    {
        foreach (RafflePackage::defaultDefinitions() as $package) {
            $this->packages()->create($package);
        }

        $cheapest = $this->packages()->orderBy('price')->first();
        if ($cheapest) {
            $this->update(['price' => $cheapest->price]);
        }
    }
}
