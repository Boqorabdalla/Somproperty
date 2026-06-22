<div class="row">
    <div class="col-sm-12">
        <x-cards.data :title="'Vendor Details'">
            <x-cards.data-row :label="'Vendor Name'"
                :value="$vendor->name" />
            <x-cards.data-row :label="'Contact Person'"
                :value="$vendor->contact_person ?? '--'" />
            <x-cards.data-row :label="'Email'"
                :value="$vendor->email ?? '--'" />
            <x-cards.data-row :label="'Phone'"
                :value="$vendor->phone ?? '--'" />
            <x-cards.data-row :label="'Address'"
                :value="$vendor->address ?? '--'" />
            <x-cards.data-row :label="'Payment Terms'"
                :value="$vendor->payment_terms ?? '--'" />
            <x-cards.data-row :label="'Notes'"
                :value="$vendor->notes ?? '--'" html="true" />
        </x-cards.data>
    </div>
</div>