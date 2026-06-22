<?php

namespace App\Models;

use App\Traits\HasCompany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Subcontractor extends BaseModel
{
    use HasCompany;

    protected $guarded = ['id'];

    public function projects(): BelongsToMany
    {
        return $this->belongsToMany(Project::class, 'project_subcontractors')
            ->withPivot('contract_amount', 'start_date', 'end_date', 'status')
            ->withTimestamps();
    }

    public function documents(): HasMany
    {
        return $this->hasMany(SubcontractorDocument::class, 'subcontractor_id');
    }
}
