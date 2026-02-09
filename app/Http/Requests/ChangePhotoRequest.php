<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ChangePhotoRequest extends FormRequest
{
    protected $fill =
    [
        'photo' => 1,
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
    public function rules(): array
    {
        $dataValidate = [];
        foreach (array_keys($this->fill) as $key) {
            $dataValidate[$key] = ($this->fill[$key] == 1) ? 'required' : 'nullable';
            switch ($key) {
                case 'photo':
                    $dataValidate[$key] .= '|image|max:10240';
                    break;
            }
        }
        return $dataValidate;
    }

    public function messages()
    {
        return [
            'photo.max' => __('Photo May Not Be Greater Than 10MB'),
        ];
    }
}
