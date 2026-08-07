<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateFacultyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $faculty = $this->route('faculty');
        $facultyId = $faculty instanceof \App\Models\Faculty ? $faculty->id : $faculty;

        return [
            'name' => ['required', 'string', 'max:255'],
            'name_kh' => ['nullable', 'string', 'max:255'],
            'code' => ['nullable', 'string', 'max:50', \Illuminate\Validation\Rule::unique('faculties', 'code')->ignore($facultyId)],
            'description' => ['nullable','string'],
            'is_active' => ['boolean'],
        ];
    }
}
