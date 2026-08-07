<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateDepartmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $department = $this->route('department');
        $departmentId = $department instanceof \App\Models\Department ? $department->id : $department;

        return [
            'name' => ['required','string','max:255'],
            'name_kh' => ['nullable','string','max:255'],
            'code' => ['nullable','string','max:50', \Illuminate\Validation\Rule::unique('departments', 'code')->ignore($departmentId)],
            'faculty_id' => ['required','exists:faculties,id'],
            'description' => ['nullable','string'],
            'is_active' => ['boolean'],
        ];
    }
}
