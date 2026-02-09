<?php

namespace App\Http\Requests\Capa;

use Illuminate\Foundation\Http\FormRequest;

class IssueResolutionRequest extends FormRequest
{

    protected $fill =
    [
        'id' => 1,
        'resolution_description' => 1,
        'gap_analysis' => 1,
        'root_cause_analysis' => 1,
        'preventive_action' => 1,
        'corrective_action' => 1,
        'issue_resolution_file' => 1,
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

        foreach ($this->fill as $key => $required) {
            if ($key === 'action_plan' && $this->has('action_plan')) {
                // trim each item in action_plan
                $trimmedActionPlan = [];
                foreach ($this->input('action_plan') as $row) {
                    $trimmedActionPlan[] = [
                        'department_id' => $row['department_id'] ?? null,
                    ];
                }
                $trimmed['action_plan'] = $trimmedActionPlan;
            } else if ($this->has($key)) {
                $trimmed[$key] = is_string($this->input($key)) ? trim($this->input($key)) : $this->input($key);
            }
        }

        $this->merge($trimmed);
    }


    public function rules(): array
    {
        $rules = [];
        foreach ($this->fill as $key => $required) {
            switch ($key) {
                case 'issue_resolution_file':
                    $rules[$key] = ($required ? 'required|' : 'nullable|') . 'array';
                    $rules[$key . '.*'] = 'file|mimes:pdf|max:10240';
                    break;
                default:
                    $rules[$key] = $required ? 'required' : 'nullable';
            }
        }

        return $rules;
    }
    public function message()
    {
        return [
            'issue_resolution_file.*.required' =>  __('The issue resolution file field is required.'),
            'issue_resolution_file.*.max' => __('Each issue resolution file may not be greater than 10MB.'),
            'issue_resolution_file.*.mimes' => __('Each issue resolution file must be a PDF, JPG, JPEG, or PNG.'),
        ];
    }
}
