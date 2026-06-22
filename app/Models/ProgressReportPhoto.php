<?php

namespace App\Models;

use App\Traits\HasCompany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProgressReportPhoto extends BaseModel
{
    use HasCompany;

    protected $guarded = ['id'];

    public function progressReport(): BelongsTo
    {
        return $this->belongsTo(ProjectProgressReport::class, 'progress_report_id');
    }
}
