<?php

namespace App\Http\Requests;

use App\Models\Destination;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class BookingRequest extends FormRequest
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
            'customer_name' => ['required', 'string', 'max:100'],
            'customer_phone' => ['required', 'string', 'max:25'],
            'tour_date' => ['required', 'date', 'after_or_equal:today'],
            'passenger_count' => ['required', 'integer', 'min:1', 'max:12'],
            'notes' => ['nullable', 'string', 'max:500'],
            'pickup' => ['required', 'array'],
            'pickup.name' => ['required', 'string', 'max:255'],
            'pickup.latitude' => ['required', 'numeric', 'between:-90,90'],
            'pickup.longitude' => ['required', 'numeric', 'between:-180,180'],
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
            'customer_name' => 'Nama Pemesan',
            'customer_phone' => 'Nomor WhatsApp Pemesan',
            'tour_date' => 'Tanggal Perjalanan',
            'passenger_count' => 'Jumlah Penumpang',
            'notes' => 'Catatan Tambahan',
            'pickup' => 'Titik Jemput',
            'pickup.name' => 'Nama Titik Jemput',
            'pickup.latitude' => 'Latitude Titik Jemput',
            'pickup.longitude' => 'Longitude Titik Jemput',
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
            'customer_name.required' => 'Nama lengkap pemesan wajib diisi.',
            'customer_phone.required' => 'Nomor WhatsApp pemesan wajib diisi.',
            'tour_date.required' => 'Tanggal jadwal tour wajib ditentukan.',
            'tour_date.after_or_equal' => 'Tanggal tour tidak boleh di masa lalu.',
            'passenger_count.required' => 'Jumlah penumpang wajib ditentukan.',
            'passenger_count.min' => 'Jumlah penumpang minimal 1 orang.',
            'passenger_count.max' => 'Jumlah penumpang maksimal 12 orang per armada tour.',
            'destinations.required' => 'Pilih minimal satu tempat wisata tujuan.',
            'destinations.min' => 'Rencana perjalanan memerlukan minimal satu tempat wisata.',
            'destinations.max' => 'Maksimal 5 tempat wisata yang dapat dipilih dalam satu rencana perjalanan.',
        ];
    }

    /**
     * Validasi lanjutan: cegah duplikasi destinasi dan pastikan destinasi aktif berkoordinat valid.
     */
    public function withValidator(Validator $validator): void
    {
        $validator->after(function ($validator) {
            $destinationsInput = $this->input('destinations', []);
            if (!is_array($destinationsInput) || empty($destinationsInput)) {
                return;
            }

            $ids = array_column($destinationsInput, 'id');
            if (count($ids) !== count(array_unique($ids))) {
                $validator->errors()->add('destinations', 'Terdapat tempat wisata yang duplikat dalam daftar rute Anda.');
                return;
            }

            $validCount = Destination::whereIn('id', $ids)
                ->where('is_active', true)
                ->whereNotNull('latitude')
                ->whereNotNull('longitude')
                ->count();

            if ($validCount !== count($ids)) {
                $validator->errors()->add(
                    'destinations',
                    'Salah satu tempat wisata tidak aktif atau belum memiliki titik koordinat yang valid.'
                );
            }
        });
    }
}
