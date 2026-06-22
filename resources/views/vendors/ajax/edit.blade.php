<div class="row">
    <div class="col-sm-12">
        <x-form id="save-vendor-data-form">
            <div class="bg-white rounded add-client">
                <h4 class="p-20 mb-0 f-21 font-weight-normal border-bottom-grey">
                    Vendor Details</h4>
                <div class="p-20 row">
                    <div class="col-lg-4 col-md-6">
                        <x-forms.text :fieldLabel="'Vendor Name'"
                            fieldName="name" fieldRequired="true" fieldId="name"
                            :fieldPlaceholder="__('placeholders.name')"
                            :fieldValue="$vendor->name"/>
                    </div>
                    <div class="col-lg-4 col-md-6">
                        <x-forms.text :fieldLabel="'Contact Person'"
                            fieldName="contact_person" fieldRequired="false" fieldId="contact_person"
                            :fieldPlaceholder="__('placeholders.name')"
                            :fieldValue="$vendor->contact_person"/>
                    </div>
                    <div class="col-lg-4 col-md-6">
                        <x-forms.email :fieldLabel="'Email'"
                            fieldName="email" fieldRequired="false" fieldId="email"
                            :fieldPlaceholder="__('placeholders.email')"
                            :fieldValue="$vendor->email"/>
                    </div>
                    <div class="col-lg-4 col-md-6">
                        <x-forms.text :fieldLabel="'Phone'"
                            fieldName="phone" fieldRequired="false" fieldId="phone"
                            :fieldPlaceholder="__('placeholders.phone')"
                            :fieldValue="$vendor->phone"/>
                    </div>
                    <div class="col-lg-4 col-md-6">
                        <x-forms.text :fieldLabel="'Payment Terms'"
                            fieldName="payment_terms" fieldRequired="false" fieldId="payment_terms"
                            :fieldPlaceholder="__('placeholders.paymentTerms')"
                            :fieldValue="$vendor->payment_terms"/>
                    </div>
                    <div class="col-lg-8 col-md-12">
                        <x-forms.textarea :fieldLabel="'Address'"
                            fieldName="address" fieldRequired="false" fieldId="address"
                            :fieldPlaceholder="__('placeholders.address')"
                            :fieldValue="$vendor->address"/>
                    </div>
                    <div class="col-lg-12">
                        <x-forms.textarea :fieldLabel="'Notes'"
                            fieldName="notes" fieldRequired="false" fieldId="notes"
                            :fieldPlaceholder="__('placeholders.notes')"
                            :fieldValue="$vendor->notes"/>
                    </div>
                </div>

                <x-form-actions>
                    <x-forms.button-primary id="update-vendor-form" class="mr-3" icon="check">Save
                    </x-forms.button-primary>
                    <x-forms.button-cancel :link="route('vendors.index')" class="border-0">Cancel
                    </x-forms.button-cancel>
                </x-form-actions>
            </div>
        </x-form>
    </div>
</div>

<script>
    $(document).ready(function () {
        $('#update-vendor-form').click(function () {
            const url = "{{ route('vendors.update', $vendor->id) }}";

            $.easyAjax({
                url: url,
                container: '#save-vendor-data-form',
                type: "POST",
                disableButton: true,
                blockUI: true,
                buttonSelector: "#update-vendor-form",
                data: $('#save-vendor-data-form').serialize() + '&_method=PUT',
                success: function (response) {
                    if (response.status == 'success') {
                        window.location.href = response.redirectUrl;
                    }
                }
            });
        });

        init(RIGHT_MODAL);
    });
</script>