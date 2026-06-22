<?php

namespace App\Http\Requests\Equipment;

use App\Http\Requests\CoreRequest;

class UpdateEquipmentRequest extends CoreRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name' => 'required',
            'equipment_type_id' => 'nullable|exists:equipment_types,id',
            'model' => 'nullable',
            'serial_no' => 'nullable',
            'purchase_date' => 'nullable|date_format:Y-m-d',
            'purchase_price' => 'nullable|numeric',
            'status' => 'nullable',
            'location' => 'nullable',
            'next_maintenance_date' => 'nullable',
            'notes' => 'nullable',
            'project_id' => 'nullable|exists:projects,id',
        ];
    }
}
