<?php

namespace Alison\ProjectManagementAssistant\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SendMessageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'message' => 'required|string|max:1000',
        ];
    }

    public function messages(): array
    {
        return [
            'message.required' => 'Message content is required',
            'message.string' => 'Message must be a string',
            'message.max' => 'Message cannot exceed 1000 characters',
        ];
    }
}
