<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTransactionProofRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // pengecekan pemilik transaksi dilakukan di controller
    }

    public function rules(): array
    {
        return [
            'payment_proof' => ['required', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:2048'],
        ];
    }

    public function messages(): array
    {
        return [
            'payment_proof.required' => 'Silakan pilih file bukti transfer.',
            'payment_proof.mimes' => 'File harus berformat JPG, PNG, atau PDF.',
            'payment_proof.max' => 'Ukuran file maksimal 2 MB.',
        ];
    }
}