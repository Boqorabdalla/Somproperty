<?php

namespace App\Http\Requests\Subcontractor;

use App\Http\Requests\CoreRequest;

class UpdateSubcontractorRequest extends CoreRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'company_name' => 'required',
            'contact_person' => 'nullable',
            'email' => 'nullable|email',
            'phone' => 'nullable',
            'address' => 'nullable',
            'trade_type' => 'nullable',
            'license_no' => 'nullable',
            'insurance_expiry' => 'nullable',
            'rating' => 'nullable|numeric|min:0|max:5',
            'notes' => 'nullable',
        ];
    }
}
