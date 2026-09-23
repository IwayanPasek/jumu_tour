<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Region extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'slug',
        'regency',
        'description',
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
            'is_active' => 'boolean',
        ];
    }

    /**
     * Relasi one-to-many ke Destination.
     */
    public function destinations(): HasMany
    {
        return $this->hasMany(Destination::class);
    }

    /**
     * Scope untuk memfilter daerah yang aktif.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }
}
