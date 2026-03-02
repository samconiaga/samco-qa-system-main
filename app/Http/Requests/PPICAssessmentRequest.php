<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PPICAssessmentRequest extends FormRequest
{
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
        return [
            'change_request_id' => 'required|exists:change_requests,id',
            'is_informed_to_toll_manufacturing' => 'required|boolean',
            'is_approval_required_from_toll_manufacturing' => 'required|boolean',
        ];
    }
}
