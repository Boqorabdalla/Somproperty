<?php

namespace App\Models;

use App\Traits\HasCompany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EquipmentType extends BaseModel
{
    use HasCompany;

    protected $guarded = ['id'];

    public function equipment(): HasMany
    {
        return $this->hasMany(Equipment::class, 'equipment_type_id');
    }
}
