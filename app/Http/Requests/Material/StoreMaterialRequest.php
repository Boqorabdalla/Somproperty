<?php

namespace App\Http\Requests\Material;

use App\Http\Requests\CoreRequest;

class StoreMaterialRequest extends CoreRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name' => 'required',
            'sku' => 'nullable',
            'description' => 'nullable',
            'category_id' => 'nullable|exists:material_categories,id',
            'unit_price' => 'nullable|numeric',
            'current_stock' => 'nullable|numeric',
            'min_stock' => 'nullable|numeric',
            'unit' => 'nullable',
            'unit_type' => 'nullable|exists:unit_types,id',
            'project_id' => 'nullable|exists:projects,id',
        ];
    }
}
