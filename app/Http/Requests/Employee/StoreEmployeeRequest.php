<?php

namespace App\Http\Requests\Employee;

use Illuminate\Foundation\Http\FormRequest;

class StoreEmployeeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('employees.create');
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
            'email' => 'required|email|unique:employees,email',
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
            'email.email' => 'Please enter a valid email address.',
            'phone.required' => 'Phone number is required.',
            'department_id.required' => 'Please select a department.',
            'department_id.exists' => 'Selected department does not exist.',
            'joining_date.required' => 'Joining date is required.',
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
