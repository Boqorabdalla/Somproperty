<?php

namespace App\Http\Requests\ChangeOrder;

use App\Http\Requests\CoreRequest;

class UpdateChangeOrderRequest extends CoreRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'change_order_number' => 'required|unique:change_orders,change_order_number,'.$this->route('change_order'),
            'project_id' => 'nullable|exists:projects,id',
            'title' => 'required',
            'description' => 'nullable',
            'reason' => 'nullable',
            'sub_total' => 'nullable|numeric',
            'tax' => 'nullable|numeric',
            'total' => 'nullable|numeric',
            'currency_id' => 'nullable|exists:currencies,id',
            'items' => 'nullable|array',
            'items.*.item_name' => 'required_with:items',
            'items.*.description' => 'nullable',
            'items.*.quantity' => 'required_with:items|numeric',
            'items.*.unit_price' => 'required_with:items|numeric',
            'items.*.total' => 'nullable|numeric',
        ];
    }
}
