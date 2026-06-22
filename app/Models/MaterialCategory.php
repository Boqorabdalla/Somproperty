<?php

namespace App\Models;

use App\Traits\HasCompany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MaterialCategory extends BaseModel
{
    use HasCompany;

    protected $guarded = ['id'];

    public function materials(): HasMany
    {
        return $this->hasMany(Material::class, 'category_id');
    }
}
