<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreMajorRequest extends FormRequest
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
            'code' => ['nullable','string','max:50','unique:majors,code'],
            'department_id' => ['required','exists:departments,id'],
            'description' => ['nullable','string'],
            'is_active' => ['boolean'],
        ];
    }
}
