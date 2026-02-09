<?php

namespace App\Http\Requests\ChangeRequest;

use Illuminate\Foundation\Http\FormRequest;

class ProofOfWorkRequest extends FormRequest
{
    protected $fill = [
        'id' => 1,
        'completion_proof_file' => 1,
        'realization' => 1,
    ];

    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation()
    {
        $trimmed = [];

        foreach ($this->fill as $key => $allow) {
            if ($this->has($key)) {
                $trimmed[$key] = is_string($this->input($key))
                    ? trim($this->input($key))
                    : $this->input($key);
            }
        }

        $this->merge($trimmed);
    }

    public function rules(): array
    {
        $dataValidate = [];

        foreach (array_keys($this->fill) as $key) {
            // Default required/nullable
            $dataValidate[$key] = ($this->fill[$key] == 1) ? 'required' : 'nullable';

            switch ($key) {
                case 'completion_proof_file':
                    $dataValidate[$key] .= '|array|max:10';
                    $dataValidate["{$key}.*"] = 'file|mimes:pdf|max:10240';
                    break;
            }
        }

        // ================================================
        // 🔥 Override rule sesuai kondisi yang kamu mau
        // ================================================

        $dataValidate['realization'] = 'nullable|required_without:completion_proof_file';
        $dataValidate['completion_proof_file'] = 'nullable|required_without:realization|array|max:10';

        return $dataValidate;
    }


    protected function withValidator($validator)
    {
        $validator->after(function ($validator) {

            $errors = $validator->errors();

            // Normalkan error array menjadi 1 error saja
            foreach (['completion_proof_file'] as $field) {

                $firstError = $errors->first($field . '.*');

                if ($firstError) {

                    // Bersihkan key key seperti completion_proof_file.0, .1, .2
                    $keys = array_filter(
                        array_keys($errors->messages()),
                        fn($k) => str_starts_with($k, $field . '.')
                    );

                    foreach ($keys as $k) {
                        $errors->forget($k);
                    }

                    // Tambahkan sebagai error tunggal
                    $errors->add($field, $firstError);
                }
            }
        });
    }

    public function messages()
    {
        return [
            'completion_proof_file.required'      => __("Completion proof file is required."),
            'completion_proof_file.*.file'        => __("Completion proof file must be a valid file."),
            'completion_proof_file.*.mimes'       => __("Completion proof file must be a PDF."),
            'completion_proof_file.*.max'         => __("Completion proof file size must not exceed 10 MB."),
            'realization.required_without'        => __("Realization is required when completion proof file is not provided."),
            'completion_proof_file.required_without' => __("Completion proof file is required when realization is not provided."),
        ];
    }
}
