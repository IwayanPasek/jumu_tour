<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateDestinationRequest extends FormRequest
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
        $destinationId = $this->route('destination') instanceof \App\Models\Destination
            ? $this->route('destination')->id
            : $this->route('destination');

        return [
            'name' => ['required', 'string', 'max:180'],
            'slug' => [
                'nullable',
                'string',
                'max:200',
                Rule::unique('destinations', 'slug')->ignore($destinationId),
            ],
            'region_id' => [
                'required',
                'integer',
                'exists:regions,id',
            ],
            'category_id' => [
                'nullable',
                'integer',
                'exists:categories,id',
            ],
            'description' => ['nullable', 'string'],
            'address' => ['nullable', 'string', 'max:500'],
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
            'image' => [
                'nullable',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:5120',
            ],
            'is_active' => ['nullable', 'boolean'],
            'display_order' => ['nullable', 'integer', 'min:0', 'max:999999'],
        ];
    }

    /**
     * Custom validation attribute names.
     */
    public function attributes(): array
    {
        return [
            'name' => 'Nama Tempat Wisata',
            'slug' => 'Slug URL',
            'region_id' => 'Daerah Wisata',
            'category_id' => 'Kategori Wisata',
            'description' => 'Deskripsi Tempat Wisata',
            'address' => 'Alamat Lokasi',
            'latitude' => 'Titik Latitude',
            'longitude' => 'Titik Longitude',
            'image' => 'Foto Utama Destinasi',
            'is_active' => 'Status Aktif',
            'display_order' => 'Urutan Tampil',
        ];
    }

    /**
     * Custom validation messages.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Nama destinasi wisata wajib diisi.',
            'name.max' => 'Nama destinasi wisata tidak boleh melebihi 180 karakter.',
            'slug.unique' => 'Slug URL ini sudah digunakan oleh destinasi lain.',
            'region_id.required' => 'Silakan pilih daerah wisata yang valid.',
            'region_id.exists' => 'Daerah yang dipilih tidak valid.',
            'category_id.exists' => 'Kategori yang dipilih tidak valid.',
            'latitude.required' => 'Koordinat latitude wajib diisi.',
            'latitude.between' => 'Nilai latitude harus berada di antara -90 hingga 90 derajat.',
            'longitude.required' => 'Koordinat longitude wajib diisi.',
            'longitude.between' => 'Nilai longitude harus berada di antara -180 hingga 180 derajat.',
            'image.image' => 'File yang diunggah harus berupa gambar yang valid.',
            'image.mimes' => 'Format gambar harus berupa JPG, JPEG, PNG, atau WEBP.',
            'image.max' => 'Ukuran file gambar tidak boleh melebihi 5 MB.',
        ];
    }
}
