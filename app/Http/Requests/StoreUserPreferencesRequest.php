<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;
use App\Models\Detail; // Assuming you have a Detail model

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
        return [
            'details' => 'required|array',
            'details.*' => 'exists:details,id',
            'age_range_start' => 'nullable|integer|min:1',
            'age_range_end' => 'nullable|integer|min:1|gte:age_range_start',
            'height' => 'nullable|integer|min:1',
        ];
    }

    /**
     * Custom error messages.
     */
    public function messages()
    {
        return [
            'details.required' => 'You must select at least one preference.',
            'details.*.exists' => 'Selected preference must be a valid detail.',
            'age_range_end.gte' => 'Age range end must be greater than or equal to the start.',
        ];
    }

    /**
     * Add custom validation after default validation.
     */
    public function withValidator(Validator $validator): void
    {
        $validator->after(function ($validator) {
            if ($this->has('details')) {
                $details = $this->input('details');

                $invalidDetails = Detail::whereIn('id', $details)
                    ->whereNotNull('parent_id')
                    ->pluck('id')
                    ->toArray();

                if (!empty($invalidDetails)) {
                    $validator->errors()->add('details', 'Selected preferences cannot be associated with a parent.');
                }
            }
        });
    }
}
