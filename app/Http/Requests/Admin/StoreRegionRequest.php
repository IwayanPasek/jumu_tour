<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreRegionRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:150'],
            'slug' => ['nullable', 'string', 'max:180', 'unique:regions,slug'],
            'regency' => ['nullable', 'string', 'max:150'],
            'description' => ['nullable', 'string'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }

    /**
     * Custom validation attribute names.
     */
    public function attributes(): array
    {
        return [
            'name' => 'Nama Daerah',
            'slug' => 'Slug URL',
            'regency' => 'Nama Kabupaten/Kota',
            'description' => 'Deskripsi Daerah',
            'is_active' => 'Status Aktif',
        ];
    }

    /**
     * Custom validation messages.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Nama daerah wajib diisi.',
            'name.max' => 'Nama daerah tidak boleh melebihi 150 karakter.',
            'slug.unique' => 'Slug URL ini sudah digunakan oleh daerah lain.',
            'slug.max' => 'Slug URL tidak boleh melebihi 180 karakter.',
        ];
    }
}
