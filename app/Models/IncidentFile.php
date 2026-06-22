<?php

namespace App\Models;

use App\Traits\HasCompany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class IncidentFile extends BaseModel
{
    use HasCompany;

    protected $guarded = ['id'];

    public function incident(): BelongsTo
    {
        return $this->belongsTo(IncidentReport::class, 'incident_id');
    }
}
