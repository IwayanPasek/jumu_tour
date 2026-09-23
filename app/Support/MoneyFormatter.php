<?php

namespace App\Support;

class MoneyFormatter
{
    /**
     * Format nilai angka menjadi string mata uang Rupiah standar.
     * Contoh: 872000 -> "Rp872.000"
     */
    public static function formatIdr(float|int|string|null $amount): string
    {
        if ($amount === null || !is_numeric($amount)) {
            return 'Rp0';
        }

        $numericAmount = (float) $amount;

        // Hilangkan desimal untuk rupiah di tampilan publik
        return 'Rp' . number_format($numericAmount, 0, ',', '.');
    }
}
