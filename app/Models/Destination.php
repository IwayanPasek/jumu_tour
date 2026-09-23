<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Destination extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'region_id',
        'category_id',
        'name',
        'slug',
        'description',
        'address',
        'latitude',
        'longitude',
        'image_path',
        'is_active',
        'display_order',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'latitude' => 'decimal:7',
            'longitude' => 'decimal:7',
            'is_active' => 'boolean',
            'display_order' => 'integer',
        ];
    }

    /**
     * Relasi ke Region (kabupaten/daerah).
     */
    public function region(): BelongsTo
    {
        return $this->belongsTo(Region::class);
    }

    /**
     * Relasi ke Category (kategori wisata, nullable).
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Scope untuk memfilter destinasi yang aktif.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope untuk mengurutkan destinasi berdasarkan display_order, lalu nama.
     */
    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('display_order', 'asc')->orderBy('name', 'asc');
    }
}
