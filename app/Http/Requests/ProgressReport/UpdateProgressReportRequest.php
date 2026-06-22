<?php

namespace App\Http\Requests\ProgressReport;

use App\Http\Requests\CoreRequest;

class UpdateProgressReportRequest extends CoreRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'project_id' => 'required|exists:projects,id',
            'milestone_id' => 'nullable|exists:project_milestones,id',
            'report_date' => 'required',
            'title' => 'required',
            'description' => 'nullable',
            'work_summary' => 'nullable',
            'weather_conditions' => 'nullable',
        ];
    }
}
