<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;

class AddToCollectionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    // Aturan validasi tambah koleksi
    public function rules(): array
    {
        return [
            'media_id' => 'required|exists:media,id',
            'status' => 'nullable|string|in:plan_to_watch,watching,completed,on_hold,dropped',
            'notes' => 'nullable|string|max:1000',
            'personal_rating' => 'nullable|integer|min:1|max:10',
        ];
    }

    // Pesan error validasi
    public function messages(): array
    {
        return [
            'media_id.required' => 'ID media wajib disertakan.',
            'media_id.exists' => 'Media yang dipilih tidak ditemukan.',
            'status.in' => 'Status koleksi tidak valid.',
            'personal_rating.between' => 'Rating pribadi harus antara 1 sampai 10.',
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
