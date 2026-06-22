<?php

namespace App\Http\Requests\PurchaseOrder;

use App\Http\Requests\CoreRequest;

class StorePurchaseOrderRequest extends CoreRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'po_number' => 'required|unique:purchase_orders,po_number',
            'vendor_id' => 'nullable|exists:vendors,id',
            'project_id' => 'nullable|exists:projects,id',
            'order_date' => 'required',
            'expected_delivery' => 'nullable',
            'sub_total' => 'nullable|numeric',
            'discount' => 'nullable|numeric',
            'discount_type' => 'nullable',
            'tax' => 'nullable|numeric',
            'total' => 'nullable|numeric',
            'currency_id' => 'nullable|exists:currencies,id',
            'notes' => 'nullable',
            'terms' => 'nullable',
            'items' => 'nullable|array',
            'items.*.item_name' => 'required_with:items',
            'items.*.quantity' => 'required_with:items|numeric',
            'items.*.unit_price' => 'required_with:items|numeric',
            'items.*.total' => 'nullable|numeric',
        ];
    }
}
