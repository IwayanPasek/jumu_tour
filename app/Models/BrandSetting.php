<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BrandSetting extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'brand_name',
        'tagline',
        'logo_path',
        'primary_color',
        'secondary_color',
        'whatsapp_number',
        'contact_email',
        'about_text',
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
     * Scope untuk konfigurasi brand yang aktif.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * Mendapatkan konfigurasi brand aktif yang berlaku (prioritas id terbaru).
     */
    public static function getActive(): ?self
    {
        return static::active()->latest('id')->first();
    }
}
