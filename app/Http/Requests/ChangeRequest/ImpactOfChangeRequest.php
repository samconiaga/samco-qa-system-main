<?php

namespace App\Http\Requests\ChangeRequest;

use Illuminate\Foundation\Http\FormRequest;

class ImpactOfChangeRequest extends FormRequest
{
    protected $fill = [
      
        'product_affected' => 1,
        'affected_products' => 0,
        'third_party_involved' => 1,
        'third_party_name' => 0
    ];
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

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
        $this->fill['third_party_name'] = (int) $this->input('third_party_involved');
        $this->fill['affected_products'] = $this->input('product_affected') === 'Yes' ? 1 : 0;
        foreach (array_keys($this->fill) as $key) {
            $dataValidate[$key] = ($this->fill[$key] == 1) ? 'required' : 'nullable';
        }
        return $dataValidate;
    }
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
 
}
