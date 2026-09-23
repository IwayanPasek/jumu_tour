<?php

namespace App\Http\Requests;

use App\Models\Destination;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class CalculateRouteRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'pickup' => ['required', 'array'],
            'pickup.name' => ['required', 'string', 'max:255'],
            'pickup.latitude' => ['required', 'numeric', 'between:-90,90'],
            'pickup.longitude' => ['required', 'numeric', 'between:-180,180'],
            'pickup.place_id' => ['nullable', 'string', 'max:255'],
            'passenger_count' => ['nullable', 'integer', 'min:1', 'max:12'],
            'destinations' => ['required', 'array', 'min:1', 'max:5'],
            'destinations.*.id' => ['required', 'integer', 'exists:destinations,id'],
        ];
    }

    /**
     * Custom validation attribute names.
     */
    public function attributes(): array
    {
        return [
            'pickup' => 'Titik Penjemputan',
            'pickup.name' => 'Nama Titik Jemput',
            'pickup.latitude' => 'Latitude Titik Jemput',
            'pickup.longitude' => 'Longitude Titik Jemput',
            'passenger_count' => 'Jumlah Penumpang',
            'destinations' => 'Daftar Tempat Wisata',
            'destinations.*.id' => 'ID Tempat Wisata',
        ];
    }

    /**
     * Custom validation messages.
     */
    public function messages(): array
    {
        return [
            'pickup.required' => 'Titik penjemputan wajib ditentukan.',
            'pickup.name.required' => 'Nama titik penjemputan wajib diisi.',
            'pickup.latitude.between' => 'Koordinat latitude titik jemput harus berada di antara -90 dan 90.',
            'pickup.longitude.between' => 'Koordinat longitude titik jemput harus berada di antara -180 dan 180.',
            'passenger_count.integer' => 'Jumlah penumpang harus berupa angka bulat.',
            'passenger_count.min' => 'Jumlah penumpang minimal 1 orang.',
            'passenger_count.max' => 'Jumlah penumpang maksimal 12 orang per armada tour.',
            'destinations.required' => 'Pilih minimal satu tempat wisata tujuan.',
            'destinations.min' => 'Rencana perjalanan memerlukan minimal satu tempat wisata.',
            'destinations.max' => 'Maksimal 5 tempat wisata yang dapat dipilih dalam satu rute.',
            'destinations.*.id.exists' => 'Salah satu tempat wisata yang dipilih tidak ditemukan dalam sistem.',
        ];
    }

    /**
     * Validasi lanjutan: cek duplikasi dan pastikan destinasi aktif & memiliki koordinat.
     */
    public function withValidator(Validator $validator): void
    {
        $validator->after(function ($validator) {
            $destinationsInput = $this->input('destinations', []);
            if (!is_array($destinationsInput) || empty($destinationsInput)) {
                return;
            }

            // 1. Cek Duplikasi Tempat Wisata
            $ids = array_column($destinationsInput, 'id');
            if (count($ids) !== count(array_unique($ids))) {
                $validator->errors()->add('destinations', 'Terdapat tempat wisata yang duplikat dalam daftar rute Anda.');
                return;
            }

            // 2. Ambil data asli dari database untuk validasi status dan kelengkapan koordinat
            $existingDestinations = Destination::whereIn('id', $ids)
                ->where('is_active', true)
                ->whereNotNull('latitude')
                ->whereNotNull('longitude')
                ->whereBetween('latitude', [-90, 90])
                ->whereBetween('longitude', [-180, 180])
                ->get()
                ->keyBy('id');

            foreach ($ids as $id) {
                if (!isset($existingDestinations[$id])) {
                    $validator->errors()->add(
                        'destinations',
                        'Salah satu tempat wisata tidak aktif atau belum memiliki titik koordinat yang valid.'
                    );
                    break;
                }
            }
        });
    }
}
