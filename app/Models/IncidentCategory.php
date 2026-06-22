<?php

namespace App\Models;

use App\Traits\HasCompany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class IncidentCategory extends BaseModel
{
    use HasCompany;

    protected $guarded = ['id'];

    public function incidents(): HasMany
    {
        return $this->hasMany(IncidentReport::class, 'category_id');
    }
}
