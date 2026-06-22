<div class="row">
    <div class="col-sm-12">
        <x-cards.data :title="'Subcontractor Details'">
            <x-cards.data-row :label="'Company Name'"
                :value="$subcontractor->company_name" />
            <x-cards.data-row :label="'Contact Person'"
                :value="$subcontractor->contact_person ?? '--'" />
            <x-cards.data-row :label="'Email'"
                :value="$subcontractor->email ?? '--'" />
            <x-cards.data-row :label="'Phone'"
                :value="$subcontractor->phone ?? '--'" />
            <x-cards.data-row :label="'Address'"
                :value="$subcontractor->address ?? '--'" />
            <x-cards.data-row :label="'Trade Type'"
                :value="$subcontractor->trade_type ?? '--'" />
            <x-cards.data-row :label="'License No'"
                :value="$subcontractor->license_no ?? '--'" />
            <x-cards.data-row :label="'Insurance Expiry'"
                :value="$subcontractor->insurance_expiry ? $subcontractor->insurance_expiry->format(company()->date_format) : '--'" />
            <x-cards.data-row :label="'Rating'"
                :value="$subcontractor->rating ?? '--'" />
            <x-cards.data-row :label="'Notes'"
                :value="$subcontractor->notes ?? '--'" html="true" />
        </x-cards.data>
    </div>
</div>