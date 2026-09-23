<?php

namespace App\Http\View\Composers;

use App\Models\BrandSetting;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class BrandViewComposer
{
    /**
     * Bind data brand ke view menggunakan request-scoped binding.
     */
    public function compose(View $view): void
    {
        $brandData = app()->has('current_brand_data')
            ? app('current_brand_data')
            : tap($this->resolveBrandData(), fn($data) => app()->instance('current_brand_data', $data));

        $view->with('brand', $brandData);
    }


    /**
     * Mengambil dan mensanitasi data brand aktif dengan fallback aman.
     */
    protected function resolveBrandData(): array
    {
        $setting = null;

        try {
            if (Schema::hasTable('brand_settings')) {
                $setting = BrandSetting::getActive();
            }
        } catch (\Throwable $e) {
            // Graceful fallback jika database belum siap
            $setting = null;
        }

        // 1. Brand Name & Tagline
        $brandName = !empty($setting?->brand_name) ? trim($setting->brand_name) : 'Bali Tour Service';
        $tagline = !empty($setting?->tagline) ? trim($setting->tagline) : 'Jelajahi Bali dengan lebih mudah';
        $aboutText = !empty($setting?->about_text) ? trim($setting->about_text) : 'Layanan transportasi dan private tour terpercaya di Bali dengan rute fleksibel dan driver berpengalaman.';
        $contactEmail = !empty($setting?->contact_email) ? trim($setting->contact_email) : null;

        // 2. Sanitasi Warna Brand (HEX Regex: #RRGGBB)
        $primaryColor = $this->sanitizeHexColor($setting?->primary_color, '#0f172a');
        $secondaryColor = $this->sanitizeHexColor($setting?->secondary_color, '#f59e0b');
        $accentColor = '#0d9488'; // Emerald accent
        $textColor = '#1e293b';
        $mutedColor = '#64748b';
        $surfaceColor = '#ffffff';

        // 3. Resolusi Logo (pastikan file benar-benar ada sebelum digunakan)
        $logoUrl = null;
        if (!empty($setting?->logo_path)) {
            if (Storage::disk('public')->exists($setting->logo_path)) {
                $logoUrl = Storage::disk('public')->url($setting->logo_path);
            } elseif (file_exists(public_path($setting->logo_path))) {
                $logoUrl = asset($setting->logo_path);
            }
        }

        // 4. Validasi WhatsApp: Cegah nomor placeholder (misal 6281234567890) menjadi link aktif
        $rawWhatsapp = !empty($setting?->whatsapp_number) ? preg_replace('/[^0-9]/', '', $setting->whatsapp_number) : '';
        $isPlaceholderWa = in_array($rawWhatsapp, ['', '6281234567890', '081234567890', '1234567890']);
        $isValidWhatsapp = !$isPlaceholderWa && strlen($rawWhatsapp) >= 10 && strlen($rawWhatsapp) <= 15;

        $whatsappUrl = $isValidWhatsapp 
            ? 'https://wa.me/' . $rawWhatsapp . '?text=' . urlencode('Halo ' . $brandName . ', saya ingin konsultasi layanan tour di Bali.')
            : null;

        return [
            'name' => $brandName,
            'tagline' => $tagline,
            'about' => $aboutText,
            'email' => $contactEmail,
            'logo_url' => $logoUrl,
            'primary_color' => $primaryColor,
            'secondary_color' => $secondaryColor,
            'accent_color' => $accentColor,
            'text_color' => $textColor,
            'muted_color' => $mutedColor,
            'surface_color' => $surfaceColor,
            'whatsapp_number' => $isValidWhatsapp ? $rawWhatsapp : null,
            'whatsapp_url' => $whatsappUrl,
            'has_valid_whatsapp' => $isValidWhatsapp,
        ];
    }

    /**
     * Sanitasi format warna HEX (#RRGGBB).
     */
    protected function sanitizeHexColor(?string $color, string $fallback): string
    {
        if (!empty($color) && preg_match('/^#[a-fA-F0-9]{6}$/', trim($color))) {
            return trim($color);
        }

        return $fallback;
    }
}
