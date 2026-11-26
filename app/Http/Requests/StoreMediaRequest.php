<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreMediaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    // Aturan validasi tambah media
    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255|unique:media,title',
            'type' => 'required|string|in:Movie,Series,Anime,Podcast,Music,Book',
            'genre' => 'required|string|max:100',
            'release_year' => 'nullable|integer|min:1900|max:' . (date('Y') + 5),
            'duration_minutes' => 'nullable|integer|min:1',
            'rating' => 'nullable|numeric|min:0|max:10',
            'description' => 'nullable|string',
        ];
    }

    // Pesan error validasi
    public function messages(): array
    {
        return [
            'title.required' => 'Judul media wajib diisi.',
            'title.unique' => 'Judul media sudah terdaftar di FlipPlay.',
            'type.required' => 'Tipe media wajib dipilih.',
            'type.in' => 'Tipe media harus berupa Movie, Series, Anime, Podcast, Music, atau Book.',
            'genre.required' => 'Genre media wajib diisi.',
            'rating.max' => 'Rating maksimal adalah 10.0.',
        ];
    }

    // Format respon JSON jika validasi gagal (HTTP 422)
    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'success' => false,
            'message' => 'Validasi gagal.',
            'errors' => $validator->errors(),
        ], 422));
    }
}
