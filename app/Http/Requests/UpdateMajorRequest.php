<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateMajorRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $major = $this->route('major');
        $majorId = $major instanceof \App\Models\Major ? $major->id : $major;

        return [
            'name' => ['required','string','max:255'],
            'name_kh' => ['nullable','string','max:255'],
            'code' => ['nullable','string','max:50', \Illuminate\Validation\Rule::unique('majors', 'code')->ignore($majorId)],
            'department_id' => ['required','exists:departments,id'],
            'description' => ['nullable','string'],
            'is_active' => ['boolean'],
        ];
    }
}
