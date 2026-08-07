<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreFacultyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required','string','max:255'],
            'name_kh' => ['nullable','string','max:255'],
            'code' => ['nullable','string','max:50','unique:faculties,code'],
            'description' => ['nullable','string'],
            'is_active' => ['boolean'],
        ];
    }
}
