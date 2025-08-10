<?php

namespace App\Http\Requests\Department;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;


class UpdateDepartmentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('departments.edit');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $departmentId = $this->route('department')->id;

        return [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('departments', 'name')->ignore($departmentId)
            ],
            'code' => [
                'required',
                'string',
                'max:10',
                Rule::unique('departments', 'code')->ignore($departmentId)
            ],
            'description' => 'nullable|string|max:1000',
        ];
    
    }

    public function messages()
    {
        return [
            'name.required' => 'Department name is required.',
            'name.unique' => 'This department name already exists.',
            'code.required' => 'Department code is required.',
            'code.unique' => 'This department code already exists.',
        ];
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'code' => strtoupper($this->code),
        ]);
    }
}
