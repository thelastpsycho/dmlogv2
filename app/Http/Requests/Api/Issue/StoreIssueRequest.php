<?php

namespace App\Http\Requests\Api\Issue;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class StoreIssueRequest extends FormRequest
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
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'location' => ['nullable', 'string', 'max:255'],
            'name' => ['nullable', 'string', 'max:255'],
            'room_number' => ['nullable', 'string', 'max:100'],
            'checkin_date' => ['nullable', 'date'],
            'checkout_date' => ['nullable', 'date', 'after:checkin_date'],
            'issue_date' => ['nullable', 'date'],
            'source' => ['nullable', 'string', 'max:100'],
            'nationality' => ['nullable', 'string', 'max:100'],
            'contact' => ['nullable', 'string', 'max:100'],
            'recovery' => ['nullable', 'string'],
            'recovery_cost' => ['nullable', 'integer'],
            'training' => ['nullable', 'boolean'],
            'priority' => ['required', 'in:low,medium,high,urgent'],
            'status' => ['nullable', 'in:open,in_progress,closed'],
            'created_by' => ['nullable', 'exists:users,id'],
            'updated_by' => ['nullable', 'exists:users,id'],
            'assigned_to_user_id' => ['nullable', 'exists:users,id'],
            'closed_by_user_id' => ['nullable', 'exists:users,id'],
            'issue_type_id' => ['nullable', 'exists:issue_types,id'],
            'department_ids' => ['nullable', 'array'],
            'department_ids.*' => ['exists:departments,id'],
            'issue_type_ids' => ['nullable', 'array'],
            'issue_type_ids.*' => ['exists:issue_types,id'],
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422)
        );
    }
}
