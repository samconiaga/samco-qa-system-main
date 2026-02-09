<?php

namespace App\Http\Requests\Master;

use Illuminate\Foundation\Http\FormRequest;

class EmployeeRequest extends FormRequest
{
    protected $fill =
    [
        'name' => 1,
        'employee_code' => 1,
        'email' => 1,
        'position_id' => 1,
        'department_id' => 1,
        'gender' => 1,
        'phone' => 1,
        'address' => 1,
        'permissions' => 1,
    ];
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    protected function prepareForValidation()
    {
        $trimmed = [];

        foreach ($this->fill as $key) {
            if ($this->has($key)) {
                $trimmed[$key] = is_string($this->input($key)) ? trim($this->input($key)) : $this->input($key);
            }
        }

        $this->merge($trimmed);
    }
    public function rules(): array
    {
        $dataValidate = [];
        $employee = $this->route('employee');
        $employeeUserId = $employee?->user_id;
        foreach (array_keys($this->fill) as $key) {
            $dataValidate[$key] = ($this->fill[$key] == 1) ? 'required' : 'nullable';
            switch ($key) {
                case 'email':
                    $dataValidate[$key] .= '|email:dns|unique:users,email,' . ($employeeUserId ?? 'NULL') . ',id';
                    break;
                case 'employee_code':
                    $dataValidate[$key] .= '|unique:employees,employee_code,' . $this->employee_code . ',employee_code';
                    break;
                case 'phone':
                    $dataValidate[$key] .= '|phone:AUTO,ID';
                    break;
            }
        }

        return $dataValidate;
    }
    public function messages(): array
    {
        return [

            'position_id.required' => __(':Field Not Yet Selected', ['field' => __('Position')]),
            'department_id.required' => __(':Field Not Yet Selected', ['field' => __('Department')]),
        ];
    }
}
