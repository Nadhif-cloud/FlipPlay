<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        // Abaikan ID user saat ini agar update sebagian tidak dianggap duplikat.
        $userParam = $this->route('user');
        $userId = $userParam instanceof User ? $userParam->id : $userParam;
        $isPartial = $this->isMethod('PATCH');

        $presence = $isPartial ? ['sometimes', 'required'] : ['required'];

        return [
            'name' => [
                ...$presence,
                'string',
                'max:255',
            ],
            'username' => [
                ...$presence,
                'string',
                'alpha_dash',
                'max:50',
                Rule::unique('users', 'username')->ignore($userId),
            ],
            'email' => [
                ...$presence,
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($userId),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Nama pengguna wajib diisi.',
            'username.required' => 'Username wajib diisi.',
            'username.alpha_dash' => 'Username hanya boleh berisi huruf, angka, tanda hubung, dan garis bawah.',
            'username.unique' => 'Username sudah digunakan oleh pengguna lain.',
            'email.required' => 'Alamat email wajib diisi.',
            'email.email' => 'Format alamat email tidak valid.',
            'email.unique' => 'Alamat email sudah terdaftar.',
        ];
    }
}
