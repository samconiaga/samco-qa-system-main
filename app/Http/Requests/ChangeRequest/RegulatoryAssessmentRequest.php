<?php

namespace App\Http\Requests\ChangeRequest;

use Illuminate\Foundation\Http\FormRequest;

class RegulatoryAssessmentRequest extends FormRequest
{
    protected $fill = [
        "change_request_id"=>1,
        "facility_affected" => 1,
        // "regulatory_related" => 1,
        "halal_status" => 1,
        "regulatory_change_type" => 1,
        "regulatory_variation" => 1,
        "reported_by" => 0,
        "notification_date" => 0,
        "regulatory_variation" => 1,

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
        if ($this->regulatory_change_type == 'regulatory_change_type_3') {
            $this->fill['reported_by'] = 1;
            $this->fill['notification_date'] = 1;
        }
        foreach (array_keys($this->fill) as $key) {
            $dataValidate[$key] = ($this->fill[$key] == 1) ? 'required' : 'nullable';
        }
        return $dataValidate;
    }
}
