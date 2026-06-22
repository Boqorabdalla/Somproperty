<div class="row">
    <div class="col-sm-12">
        <x-form id="save-subcontractor-data-form">
            <div class="bg-white rounded add-client">
                <h4 class="p-20 mb-0 f-21 font-weight-normal border-bottom-grey">
                    Subcontractor Details</h4>
                <div class="p-20 row">
                    <div class="col-lg-4 col-md-6">
                        <x-forms.text :fieldLabel="'Company Name'"
                            fieldName="company_name" fieldRequired="true" fieldId="company_name"
                            :fieldPlaceholder="__('placeholders.companyName')"/>
                    </div>
                    <div class="col-lg-4 col-md-6">
                        <x-forms.text :fieldLabel="'Contact Person'"
                            fieldName="contact_person" fieldRequired="false" fieldId="contact_person"
                            :fieldPlaceholder="__('placeholders.name')"/>
                    </div>
                    <div class="col-lg-4 col-md-6">
                        <x-forms.email :fieldLabel="'Email'"
                            fieldName="email" fieldRequired="false" fieldId="email"
                            :fieldPlaceholder="__('placeholders.email')"/>
                    </div>
                    <div class="col-lg-4 col-md-6">
                        <x-forms.text :fieldLabel="'Phone'"
                            fieldName="phone" fieldRequired="false" fieldId="phone"
                            :fieldPlaceholder="__('placeholders.phone')"/>
                    </div>
                    <div class="col-lg-4 col-md-6">
                        <x-forms.text :fieldLabel="'Trade Type'"
                            fieldName="trade_type" fieldRequired="false" fieldId="trade_type"
                            :fieldPlaceholder="__('placeholders.tradeType')"/>
                    </div>
                    <div class="col-lg-4 col-md-6">
                        <x-forms.text :fieldLabel="'License No'"
                            fieldName="license_no" fieldRequired="false" fieldId="license_no"
                            :fieldPlaceholder="__('placeholders.licenseNo')"/>
                    </div>
                    <div class="col-lg-4 col-md-6">
                        <x-forms.datepicker fieldId="insurance_expiry"
                            :fieldLabel="'Insurance Expiry'" fieldName="insurance_expiry"
                            :fieldPlaceholder="__('placeholders.date')"/>
                    </div>
                    <div class="col-lg-4 col-md-6">
                        <x-forms.number :fieldLabel="'Rating'"
                            fieldName="rating" fieldRequired="false" fieldId="rating" :fieldValue="0"
                            :fieldPlaceholder="__('placeholders.rating')" max="5" min="0"/>
                    </div>
                    <div class="col-lg-8 col-md-12">
                        <x-forms.textarea :fieldLabel="'Address'"
                            fieldName="address" fieldRequired="false" fieldId="address"
                            :fieldPlaceholder="__('placeholders.address')"/>
                    </div>
                    <div class="col-lg-12">
                        <x-forms.textarea :fieldLabel="'Notes'"
                            fieldName="notes" fieldRequired="false" fieldId="notes"
                            :fieldPlaceholder="__('placeholders.notes')"/>
                    </div>
                </div>

                <x-form-actions>
                    <x-forms.button-primary id="save-subcontractor-form" class="mr-3" icon="check">Save
                    </x-forms.button-primary>
                    <x-forms.button-cancel :link="route('subcontractors.index')" class="border-0">Cancel
                    </x-forms.button-cancel>
                </x-form-actions>
            </div>
        </x-form>
    </div>
</div>

<script>
    $(document).ready(function () {
        $('.custom-date-picker').each(function(ind, el) {
            datepicker(el, {
                position: 'bl',
                ...datepickerConfig
            });
        });

        const dp = datepicker('#insurance_expiry', {
            position: 'bl',
            ...datepickerConfig
        });

        $('#save-subcontractor-form').click(function () {
            const url = "{{ route('subcontractors.store') }}";

            $.easyAjax({
                url: url,
                container: '#save-subcontractor-data-form',
                type: "POST",
                disableButton: true,
                blockUI: true,
                buttonSelector: "#save-subcontractor-form",
                data: $('#save-subcontractor-data-form').serialize(),
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