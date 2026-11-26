<?php // Validasi input tambah (POST) dan ubah (PATCH) data media agar sesuai aturan.

namespace App\Http\Requests;

use App\Models\Media;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreMediaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        // Pada PATCH hanya validasi kolom yang dikirim, dan abaikan ID sendiri saat cek judul unik.
        $mediaParam = $this->route('media');
        $mediaId = $mediaParam instanceof Media ? $mediaParam->id : $mediaParam;
        $isPartial = $this->isMethod('PATCH');

        $presence = $isPartial ? ['sometimes', 'required'] : ['required'];

        return [
            'title' => [
                ...$presence,
                'string',
                'max:255',
                Rule::unique('media', 'title')->ignore($mediaId),
            ],
            'type' => [
                ...$presence,
                'string',
                Rule::in(['Anime', 'Manga']),
            ],
            'genre' => [
                ...$presence,
                'string',
                'max:255',
            ],
            'creator' => [
                ...$presence,
                'string',
                'max:255',
            ],
            'synopsis' => [
                'nullable',
                'string',
            ],
            'total_units' => [
                ...$presence,
                'integer',
                'min:1',
            ],
            'status' => [
                ...$presence,
                'string',
                Rule::in(['Ongoing', 'Completed', 'Berlanjut', 'Selesai']),
            ],
            'release_year' => [
                'nullable',
                'integer',
                'min:1900',
                'max:2100',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'Judul media wajib diisi.',
            'title.string' => 'Judul media harus berupa teks.',
            'title.max' => 'Judul media maksimal 255 karakter.',
            'title.unique' => 'Judul media sudah terdaftar di sistem FlipPlay.',
            'type.required' => 'Jenis media wajib diisi.',
            'type.in' => 'Jenis media hanya boleh bernilai Anime atau Manga.',
            'genre.required' => 'Genre media wajib diisi.',
            'creator.required' => 'Nama kreator wajib diisi.',
            'total_units.required' => 'Jumlah episode atau volume wajib diisi.',
            'total_units.integer' => 'Jumlah episode atau volume harus berupa angka bulat.',
            'total_units.min' => 'Jumlah episode atau volume minimal 1.',
            'status.required' => 'Status media wajib diisi.',
            'status.in' => 'Status media harus bernilai Ongoing, Completed, Berlanjut, atau Selesai.',
            'release_year.integer' => 'Tahun rilis harus berupa angka tahun yang valid.',
            'release_year.min' => 'Tahun rilis minimal 1900.',
            'release_year.max' => 'Tahun rilis tidak boleh melebihi 2100.',
        ];
    }
}
