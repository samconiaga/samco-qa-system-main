<?php

namespace App\Http\Requests\ChangeRequest;

use Illuminate\Foundation\Http\FormRequest;

class ChangeRequestRequest extends FormRequest
{
    protected $fill =
    [
        'id' => 0,
        'title' => 1,
        'initiator_name' => 1,
        'employee_id' => 1,
        'department_id' => 1,
        'scopes' => 1,
        'type_of_change' => 1,
        'current_status' => 0,
        'current_status_file' => 0,
        'current_status_file_names' => 0,
        'current_status_file_keep_ids' => 0,
        'proposed_change' => 0,
        'proposed_change_file' => 0,
        'proposed_change_file_names' => 0,
        'proposed_change_file_keep_ids' => 0,
        'reason' => 1,
        'supporting_attachment' => 0,
        'supporting_attachment_names' => 0,
        'supporting_attachment_keep_ids' => 0,
        'overall_status' => 0,


        'source_of_risk' => 1,
        'impact_of_risk' => 1,
        'severity' => 1,
        'probability' => 1,
        'detectability' => 1,
        'cause_of_risk' => 1,
        'control_implemented' => 1,
        'risk_category' => 1,
        'rpn' => 1,

        'product_affected' => 1,
        'affected_products' => 0,
        'third_party_involved' => 1,
        'third_party_name' => 0,


        //
        'related_departments' => 1,
        'isDraft' => 1,
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
                case 'severity':
                case 'probability':
                case 'detectability':
                case 'rpn':
                    $rules[$key] = ($required ? 'required|' : 'nullable|') . 'numeric|min:1';
                    break;

                case 'current_status_file':
                case 'proposed_change_file':
                case 'supporting_attachment':
                    $rules[$key] = ($required ? 'required|' : 'nullable|') . 'array';
                    $rules[$key . '.*'] = 'file|mimes:pdf,jpg,jpeg,png|max:10240';
                    break;
                default:
                    $rules[$key] = $required ? 'required' : 'nullable';
            }
        }

        return $rules;
    }
}
