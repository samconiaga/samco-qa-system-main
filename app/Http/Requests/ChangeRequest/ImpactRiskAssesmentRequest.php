<?php

namespace App\Http\Requests\ChangeRequest;

use Illuminate\Foundation\Http\FormRequest;

class ImpactRiskAssesmentRequest extends FormRequest
{
    protected $fill = [
        'source_of_risk' => 1,
        'impact_of_risk' => 1,
        'severity' => 1,
        'probability' => 1,
        'detectability' => 1,
        'cause_of_risk' => 1,
        'control_implemented' => 1,
        'risk_category' => 1,
        'rpn' => 1,
        'isLastStep' => 1
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
        foreach (array_keys($this->fill) as $key) {
            $dataValidate[$key] = ($this->fill[$key] == 1) ? 'required' : 'nullable';
            switch ($key) {
                case 'severity':
                case 'probability':
                case 'detectability':
                    $dataValidate[$key] .= '|numeric|min:1';
                    break;
                case 'rpn':
                    $dataValidate[$key] .= '|numeric|min:1';
                    break;
                
            }
        }
        return $dataValidate;
    }

    public function messages()
    {
        return [
          
            'severity.min' => __("Risk Evaluation Criteria Is Required"),
            'probability.min' => __("Risk Evaluation Criteria Is Required"),
            'detectability.min' => __("Risk Evaluation Criteria Is Required")
        ];
    }
}
