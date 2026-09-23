<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PricingConfiguration extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'base_price',
        'price_per_kilometer',
        'extra_passenger_price',
        'parking_fee',
        'toll_fee',
        'night_service_fee',
        'minimum_price',
        'is_active',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'base_price' => 'decimal:2',
            'price_per_kilometer' => 'decimal:2',
            'extra_passenger_price' => 'decimal:2',
            'parking_fee' => 'decimal:2',
            'toll_fee' => 'decimal:2',
            'night_service_fee' => 'decimal:2',
            'minimum_price' => 'decimal:2',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Scope untuk konfigurasi tarif yang aktif.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * Mendapatkan satu konfigurasi tarif aktif yang berlaku saat ini (prioritas id terbaru).
     */
    public static function getActive(): ?self
    {
        return static::active()->latest('id')->first();
    }
}
