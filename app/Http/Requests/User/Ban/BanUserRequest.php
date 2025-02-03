<?php

// app/Http/Requests/User/Ban/BanUserRequest.php

namespace App\Http\Requests\User\Ban;

use App\Enums\BanType;
use Illuminate\Foundation\Http\FormRequest;
use Spatie\Permission\Models\Role;

class BanUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Check if the authenticated user has 'admin' or 'moderator' role
        return auth()->user()->hasAnyRole(['admin', 'moderator']);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'ban_type' => 'required|in:' . implode(',', BanType::values()), // Ensure ban_type is valid
            'duration' => 'required_if:ban_type,temporary|integer|min:1', // Required if the ban type is temporary
        ];
    }

    public function messages(): array
    {
        return [
            'ban_type.in' => 'The selected ban type is invalid.',
            'duration.required_if' => 'Duration is required if the ban type is temporary.',
        ];
    }
}
