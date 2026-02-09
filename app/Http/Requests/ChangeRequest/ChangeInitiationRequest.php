<?php

namespace App\Http\Requests\ChangeRequest;

use Illuminate\Foundation\Http\FormRequest;

class ChangeInitiationRequest extends FormRequest
{
    protected $fill =
    [
        'title' => 1,
        'initiator_name' => 1,
        'scopes' => 1,
        'type_of_change' => 1,
        'current_status' => 1,
        'current_status_file' => 0,
        'current_status_file_names' => 0,
        'current_status_file_keep_ids' => 0,
        'proposed_change' => 1,
        'proposed_change_file' => 0,
        'proposed_change_file_names' => 0,
        'proposed_change_file_keep_ids' => 0,
        'reason' => 1,

        'supporting_attachment' => 0,
        'supporting_attachment_names' => 0,
        'supporting_attachment_keep_ids' => 0,

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
        // dd($this->all());
        $dataValidate = [];
        foreach (array_keys($this->fill) as $key) {
            $dataValidate[$key] = ($this->fill[$key] == 1) ? 'required' : 'nullable';
            switch ($key) {
                case 'current_status_file':
                case 'proposed_change_file':
                case 'supporting_attachment':
                    $dataValidate[$key] .= '|array';
                    $dataValidate["{$key}.*"] = 'file|mimes:pdf,jpg,jpeg,png|max:10240';
                    break;
            }
        }
        return $dataValidate;
    }
    protected function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $errors = $validator->errors();

            // Gabungkan error *.0, *.1 menjadi satu key biasa
            foreach (['current_status_file', 'proposed_change_file', 'supporting_attachment'] as $field) {
                $firstError = $errors->first($field . '.*');
                if ($firstError) {
                    // Hapus error array
                    $keys = array_filter(array_keys($errors->messages()), function ($k) use ($field) {
                        return str_starts_with($k, $field . '.');
                    });
                    foreach ($keys as $k) {
                        $errors->forget($k);
                    }

                    // Tambah ulang sebagai single key
                    $errors->add($field, $firstError);
                }
            }
        });
    }

    public function messages()
    {
        return [
            'current_status_file.*.max'      => __("Current Status file size must not exceed 10 MB."),
            'proposed_change_file.*.max'     => __("Proposed Change file size must not exceed 10 MB."),
            'supporting_attachment.*.max'    => __("Supporting Attachment file size must not exceed 10 MB."),

            'current_status_file.*.mimes'    => __("Current Status file must be a PDF, JPG, JPEG, or PNG."),
            'proposed_change_file.*.mimes'   => __("Proposed Change file must be a PDF, JPG, JPEG, or PNG."),
            'supporting_attachment.*.mimes'  => __("Supporting Attachment file must be a PDF, JPG, JPEG, or PNG."),
        ];
    }
}
