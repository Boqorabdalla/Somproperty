<?php

namespace App\Models;

use App\Traits\HasCompany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChangeOrderItem extends BaseModel
{
    use HasCompany;

    protected $guarded = ['id'];

    public function changeOrder(): BelongsTo
    {
        return $this->belongsTo(ChangeOrder::class, 'change_order_id');
    }
}
