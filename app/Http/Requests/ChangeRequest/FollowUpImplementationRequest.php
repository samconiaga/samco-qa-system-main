<?php

namespace App\Http\Requests\ChangeRequest;

use Illuminate\Foundation\Http\FormRequest;

class FollowUpImplementationRequest extends FormRequest
{

    protected $fill = [
        'id' => 1,
        'change_request_id' => 1,
        'impact_of_change_category' => 1,
        'custom_category' => 0,
        'deadline' => 1,
        'impact_of_change_description' => 1,
        'action_plan_id' => 0,
        // 'realization' => 1
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
        if($this->impact_of_change_category){
            $this->fill['custom_category'] = 0;
        }elseif($this->custom_category){
            $this->fill['impact_of_change_category'] = 0;
        }
        $dataValidate = [];
        foreach (array_keys($this->fill) as $key) {
            $dataValidate[$key] = ($this->fill[$key] == 1) ? 'required' : 'nullable';
        }
        return $dataValidate;
    }
}
