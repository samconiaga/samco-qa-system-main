<?php

namespace App\Http\Requests\ChangeRequest;

use Illuminate\Foundation\Http\FormRequest;

class ChangeRequestDraftRequest extends FormRequest
{
    protected $fill =
    [
        'id' => 0,
        'title' => 0,
        'initiator_name' => 0,
        'employee_id' => 0,
        'department_id' => 0,
        'scopes' => 0,
        'type_of_change' => 0,
        'current_status' => 0,
        'current_status_file' => 0,
        'current_status_file_names' => 0,
        'current_status_file_keep_ids' => 0,
        'proposed_change' => 0,
        'proposed_change_file' => 0,
        'proposed_change_file_names' => 0,
        'proposed_change_file_keep_ids' => 0,
        'reason' => 0,
        'supporting_attachment' => 0,
        'supporting_attachment_names' => 0,
        'supporting_attachment_keep_ids' => 0,
        'overall_status' => 0,


        'source_of_risk' => 0,
        'impact_of_risk' => 0,
        'severity' => 0,
        'probability' => 0,
        'detectability' => 0,
        'cause_of_risk' => 0,
        'control_implemented' => 0,
        'risk_category' => 0,
        'rpn' => 0,

        'facility_affected' => 0,
        'product_affected' => 0,
        'affected_products' => 0,
        'regulatory_related' => 0,
        'halal_status' => 0,
        'third_party_involved' => 0,
        'third_party_name' => 0,


        //
        'related_departments' => 0,

        'isDraft' => 1
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
                    $rules[$key] = ($required ? 'required|' : 'nullable|') . 'numeric|min:0';
                    break;

                case 'current_status_file':
                case 'proposed_change_file':
                case 'supporting_attachment':
                    $rules[$key] = ($required ? 'required|' : 'nullable|') . 'array';
                    $rules[$key . '.*'] = 'file|mimes:pdf,jpg,jpeg,png|max:10240';
                    break;

                case 'action_plan':
                    $rules['action_plan'] = ($required ? 'required|array|min:0' : 'nullable|array');
                    $rules['action_plan.*.department_id'] = 'required';
                    break;

                default:
                    $rules[$key] = $required ? 'required' : 'nullable';
            }
        }

        return $rules;
    }

    public function messages()
    {
        return [
            'action_plan.*.proposed_action.required' => __('Proposed action is required'),
            'action_plan.*.department_id.required' => __('Department is required'),
            'action_plan.*.timeline.required' => __('Timeline is required'),
            'action_plan.*.timeline.date' => __('Timeline must be a valid date'),
            'action_plan.*.timeline.after_or_equal' => __('Timeline must be today or later'),
        ];
    }
}
