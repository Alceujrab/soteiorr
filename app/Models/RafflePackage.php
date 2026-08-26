<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'raffle_id',
    'name',
    'numbers_qty',
    'price',
    'highlight',
    'is_featured',
    'sort_order',
])]
class RafflePackage extends Model
{
    protected function casts(): array
    {
        return [
            'numbers_qty' => 'integer',
            'price' => 'decimal:2',
            'is_featured' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    public function raffle(): BelongsTo
    {
        return $this->belongsTo(Raffle::class);
    }

    public function effectiveCostPerNumber(): float
    {
        if ($this->numbers_qty <= 0) {
            return 0.0;
        }

        return round(((float) $this->price) / $this->numbers_qty, 4);
    }

    /**
     * @return list<array{name: string, numbers_qty: int, price: float, highlight: string, is_featured: bool, sort_order: int}>
     */
    public static function defaultDefinitions(): array
    {
        return [
            [
                'name' => 'Essencial',
                'numbers_qty' => 20,
                'price' => 9.90,
                'highlight' => 'Para começar',
                'is_featured' => false,
                'sort_order' => 1,
            ],
            [
                'name' => 'Popular',
                'numbers_qty' => 50,
                'price' => 21.90,
                'highlight' => 'Mais chances',
                'is_featured' => false,
                'sort_order' => 2,
            ],
            [
                'name' => 'Avançado',
                'numbers_qty' => 120,
                'price' => 44.90,
                'highlight' => 'Mais escolhido',
                'is_featured' => true,
                'sort_order' => 3,
            ],
            [
                'name' => 'Premium',
                'numbers_qty' => 200,
                'price' => 69.90,
                'highlight' => 'Melhor custo-benefício',
                'is_featured' => false,
                'sort_order' => 4,
            ],
        ];
    }
}
