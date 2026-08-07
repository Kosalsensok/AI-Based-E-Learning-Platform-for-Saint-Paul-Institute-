<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreFacultyRequest extends FormRequest
{
    public function authorize(): bool { return true; }
    
    public function rules(): array
    {
        return [
            'name'      => 'required|string|max:255|unique:faculties,name,'.$this->faculty?->id,
            'name_km'   => 'nullable|string|max:255',
            'code'      => 'nullable|string|max:50',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ];
    }
    
    public function attributes(): array
    {
        return ['name' => 'Faculty Name', 'name_km' => 'ឈ្មោះជាខ្មែរ'];
    }
}
