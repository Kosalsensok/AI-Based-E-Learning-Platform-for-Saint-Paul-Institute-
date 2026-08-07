<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreMajorRequest extends FormRequest
{
    public function authorize(): bool { return true; }
    
    public function rules(): array
    {
        return [
            'department_id' => 'required|exists:departments,id',
            'name'          => 'required|string|max:255',
            'name_km'       => 'nullable|string|max:255',
            'code'          => 'nullable|string|max:20|unique:majors,code,'.$this->major?->id,
            'description'   => 'nullable|string',
        ];
    }
}
