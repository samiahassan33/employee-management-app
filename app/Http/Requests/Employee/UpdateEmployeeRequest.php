<?php

namespace App\Http\Requests\Employee;

use Illuminate\Foundation\Http\FormRequest;

class UpdateEmployeeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('employees.edit');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'email',
                Rule::unique('employees', 'email')->ignore($employeeId)
            ],
            'phone' => 'required|string|max:20',
            'department_id' => 'required|integer|exists:departments,id',
            'joining_date' => 'required|date|before_or_equal:today',
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'Employee name is required.',
            'email.unique' => 'This email is already registered.',
            'department_id.exists' => 'Selected department does not exist.',
            'joining_date.before_or_equal' => 'Joining date cannot be in the future.',
        ];
    }

    protected function prepareForValidation()
    {
        $this->merge([
            'email' => strtolower($this->email),
            'phone' => $this->formatPhone($this->phone),
        ]);
    }

    private function formatPhone($phone)
    {
        return preg_replace('/[^0-9+]/', '', $phone);
    }

}
