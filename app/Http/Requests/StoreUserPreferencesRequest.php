<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreUserPreferencesRequest extends FormRequest
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
        // add validation for detail preferences could be only details without parent_id!!!
        return [
            'details' => 'required|array', // Ensure the 'details' field is an array
            'details.*' => 'exists:details,id', // Ensure each detail ID is valid
            'age_range_start' => 'nullable|integer|min:1', // Validate age_range_start
            'age_range_end' => 'nullable|integer|min:1|gte:age_range_start', // Validate age_range_end and it must be greater than or equal to age_range_start
            'height' => 'nullable|integer|min:1', // Validate height
        ];
    }

    public function messages()
    {
        return [
            'details.required' => 'You must select at least one preference.',
            'details.*.exists' => 'Selected preference must be a valid detail.',
            'age_range_end.gte' => 'Age range end must be greater than or equal to the start.',
        ];
    }
}
