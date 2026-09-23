<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreCategoryRequest extends FormRequest
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
            'slug' => ['nullable', 'string', 'max:180', 'unique:categories,slug'],
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
            'name' => 'Nama Kategori',
            'slug' => 'Slug URL',
            'description' => 'Deskripsi Kategori',
            'is_active' => 'Status Aktif',
        ];
    }

    /**
     * Custom validation messages.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Nama kategori wajib diisi.',
            'name.max' => 'Nama kategori tidak boleh melebihi 150 karakter.',
            'slug.unique' => 'Slug URL ini sudah digunakan oleh kategori lain.',
            'slug.max' => 'Slug URL tidak boleh melebihi 180 karakter.',
        ];
    }
}
