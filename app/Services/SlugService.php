<?php

namespace App\Services;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class SlugService
{
    /**
     * Menghasilkan slug unik untuk model tertentu.
     *
     * @param string|Model $modelClass Nama kelas model Eloquent
     * @param string $sourceText Teks sumber untuk slug (misal name)
     * @param string|null $customSlug Slug kustom dari input admin (opsional)
     * @param int|null $ignoreId ID record yang sedang diupdate (opsional)
     */
    public static function createUniqueSlug(string|Model $modelClass, string $sourceText, ?string $customSlug = null, ?int $ignoreId = null): string
    {
        $baseText = !empty($customSlug) ? $customSlug : $sourceText;
        $slug = Str::slug($baseText);

        if (empty($slug)) {
            $slug = 'item-' . Str::random(6);
        }

        $query = is_string($modelClass) ? $modelClass::query() : $modelClass->newQuery();

        $originalSlug = $slug;
        $count = 1;

        while ($query->clone()
            ->where('slug', $slug)
            ->when($ignoreId, fn($q) => $q->where('id', '!=', $ignoreId))
            ->exists()) {
            $slug = $originalSlug . '-' . $count;
            $count++;
        }

        return $slug;
    }
}
