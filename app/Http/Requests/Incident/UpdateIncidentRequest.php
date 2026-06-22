<?php

namespace App\Http\Requests\Incident;

use App\Http\Requests\CoreRequest;

class UpdateIncidentRequest extends CoreRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'project_id' => 'nullable|exists:projects,id',
            'category_id' => 'nullable|exists:incident_categories,id',
            'title' => 'required',
            'incident_date' => 'required',
            'severity' => 'required|in:minor,major,critical',
            'description' => 'required',
            'root_cause' => 'nullable',
            'corrective_action' => 'nullable',
        ];
    }
}
