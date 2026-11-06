<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreReviewRequest extends FormRequest
{
    protected $errorBag = 'review';

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()?->everHadMembership() ?? false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:100'],
            'rating' => ['required', 'integer', 'between:1,5'],
            'review' => ['required', 'string', 'min:10', 'max:2000'],
        ];
    }

    /** @return array<string, string> */
    public function messages(): array
    {
        return [
            'name.required' => 'Isi nama yang ingin ditampilkan.',
            'name.string' => 'Nama harus berupa teks.',
            'name.max' => 'Nama maksimal 100 karakter.',
            'rating.required' => 'Pilih jumlah bintang terlebih dahulu.',
            'rating.integer' => 'Rating harus berupa angka bulat.',
            'rating.between' => 'Pilih rating antara 1 sampai 5 bintang.',
            'review.required' => 'Ceritakan pengalaman latihanmu.',
            'review.string' => 'Deskripsi harus berupa teks.',
            'review.min' => 'Deskripsi minimal 10 karakter.',
            'review.max' => 'Deskripsi maksimal 2.000 karakter.',
        ];
    }

    protected function getRedirectUrl(): string
    {
        return route('home').'#write-review';
    }
}
