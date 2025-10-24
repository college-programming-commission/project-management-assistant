<?php

namespace Alison\ProjectManagementAssistant\Http\Requests;

use Alison\ProjectManagementAssistant\Models\Project;
use Illuminate\Foundation\Http\FormRequest;

class StoreOfferRequest extends FormRequest
{
    public function authorize(): bool
    {
        $project = $this->route('project');
        return $this->user()->can('create', [Project::class, $project]);
    }

    public function rules(): array
    {
        return [];
    }
}
