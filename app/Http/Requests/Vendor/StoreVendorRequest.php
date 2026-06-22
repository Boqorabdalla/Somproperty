<?php

namespace App\Http\Requests\Vendor;

use App\Http\Requests\CoreRequest;

class StoreVendorRequest extends CoreRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name' => 'required',
            'contact_person' => 'nullable',
            'email' => 'nullable|email',
            'phone' => 'nullable',
            'address' => 'nullable',
            'payment_terms' => 'nullable',
            'notes' => 'nullable',
        ];
    }
}
