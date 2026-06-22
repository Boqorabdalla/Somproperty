<div class="row">
    <div class="col-sm-12">
        <x-form id="save-purchase-order-data-form">
            <div class="bg-white rounded b-shadow-4 create-inv">
                <div class="px-lg-4 px-md-4 px-3 py-3">
                    <h4 class="mb-0 f-21 font-weight-normal">Purchase Order Details</h4>
                </div>
                <hr class="m-0 border-top-grey">

                <div class="row px-lg-4 px-md-4 px-3 py-3">
                    <div class="col-md-3">
                        <x-forms.text :fieldLabel="'PO Number'"
                            fieldName="po_number" fieldRequired="true" fieldId="po_number"
                            :fieldPlaceholder="__('placeholders.poNumber')"/>
                    </div>
                    <div class="col-md-3">
                        <x-forms.datepicker fieldId="order_date"
                            :fieldLabel="'Order Date'" fieldName="order_date"
                            :fieldPlaceholder="__('placeholders.date')"
                            :fieldValue="now(company()->timezone)->format(company()->date_format)"/>
                    </div>
                    <div class="col-md-3">
                        <x-forms.datepicker fieldId="expected_delivery"
                            :fieldLabel="'Expected Delivery'" fieldName="expected_delivery"
                            :fieldPlaceholder="__('placeholders.date')"/>
                    </div>
                    <div class="col-md-3">
                        <x-forms.select fieldId="currency_id" :fieldLabel="'Currency'"
                            fieldName="currency_id" search="true">
                            @foreach ($currencies as $currency)
                                <option @selected($currency->id == company()->currency_id) value="{{ $currency->id }}">
                                    {{ $currency->currency_symbol . ' (' . $currency->currency_code . ')' }}
                                </option>
                            @endforeach
                        </x-forms.select>
                    </div>
                    <div class="col-md-4">
                        <x-forms.select fieldId="vendor_id" :fieldLabel="'Vendor'"
                            fieldName="vendor_id" search="true" fieldRequired="true">
                            <option value="">--</option>
                            @foreach ($vendors as $vendor)
                                <option value="{{ $vendor->id }}">{{ $vendor->name }}</option>
                            @endforeach
                        </x-forms.select>
                    </div>
                    <div class="col-md-4">
                        <x-forms.select fieldId="project_id" :fieldLabel="'Project'"
                            fieldName="project_id" search="true">
                            <option value="">--</option>
                            @foreach ($projects as $project)
                                <option value="{{ $project->id }}">{{ $project->project_name }}</option>
                            @endforeach
                        </x-forms.select>
                    </div>
                </div>

                <hr class="m-0 border-top-grey">

                <div class="row px-lg-4 px-md-4 px-3 py-3">
                    <div class="col-md-12">
                        <h5 class="mb-0 f-15 font-weight-normal">Order Items</h5>
                    </div>
                </div>

                <div id="sortable">
                    <div class="d-flex px-4 py-3 c-inv-desc item-row">
                        <div class="c-inv-desc-table w-100 d-lg-flex d-md-flex d-block">
                            <table width="100%">
                                <tbody>
                                    <tr class="text-dark-grey font-weight-bold f-14">
                                        <td width="40%" class="border-0 inv-desc-mbl btlr">Description</td>
                                        <td width="15%" class="border-0" align="right">Qty</td>
                                        <td width="15%" class="border-0" align="right">Unit Price</td>
                                        <td width="15%" class="border-0 bblr-mbl" align="right">Amount</td>
                                        <td width="15%" class="border-0"></td>
                                    </tr>
                                    <tr>
                                        <td class="border-bottom-0 btrr-mbl btlr">
                                            <input type="text" class="form-control f-14 border-0 w-100 item_name"
                                                name="item_name[]" placeholder="@lang('modules.expenses.itemName')">
                                            <textarea class="form-control f-14 border-0 w-100" name="item_summary[]"
                                                placeholder="@lang('placeholders.invoices.description')" rows="2"></textarea>
                                        </td>
                                        <td class="border-bottom-0">
                                            <input type="number" min="1"
                                                class="form-control f-14 border-0 w-100 text-right quantity mt-3"
                                                value="1" name="quantity[]">
                                        </td>
                                        <td class="border-bottom-0">
                                            <input type="number" min="1"
                                                class="f-14 border-0 w-100 text-right cost_per_item form-control"
                                                placeholder="0.00" value="0" name="cost_per_item[]">
                                        </td>
                                        <td rowspan="2" align="right" valign="top" class="bg-amt-grey btrr-bbrr">
                                            <span class="amount-html">0.00</span>
                                            <input type="hidden" class="amount" name="amount[]" value="0">
                                        </td>
                                        <td class="border-bottom-0">
                                            <a href="javascript:;"
                                                class="d-flex align-items-center justify-content-center remove-item"><i
                                                    class="fa fa-times-circle f-20 text-lightest"></i></a>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="row px-lg-4 px-md-4 px-3 pb-3 pt-0 mb-3 mt-2">
                    <div class="col-md-12">
                        <a class="f-15 f-w-500" href="javascript:;" id="add-item"><i
                                class="icons icon-plus font-weight-bold mr-1"></i>Add Item</a>
                    </div>
                </div>

                <hr class="m-0 border-top-grey">

                <div class="d-flex px-lg-4 px-md-4 px-3 pb-3 c-inv-total">
                    <table width="100%" class="text-right f-14">
                        <tbody>
                            <tr>
                                <td width="50%" class="border-0 d-lg-table d-md-table d-none"></td>
                                <td width="50%" class="p-0 border-0 c-inv-total-right">
                                    <table width="100%">
                                        <tbody>
                                            <tr>
                                                <td colspan="2" class="border-top-0 text-dark-grey">
                                                    Sub Total</td>
                                                <td width="30%" class="border-top-0 sub-total">0.00</td>
                                                <input type="hidden" class="sub-total-field" name="sub_total" value="0">
                                            </tr>
                                            <tr>
                                                <td width="20%" class="text-dark-grey">Discount</td>
                                                <td width="40%" style="padding: 5px;">
                                                    <table width="100%" class="mw-250">
                                                        <tbody>
                                                            <tr>
                                                                <td width="70%" class="c-inv-sub-padding">
                                                                    <input type="number" min="0" name="discount"
                                                                        class="form-control f-14 border-0 w-100 text-right discount_value"
                                                                        placeholder="0" value="0">
                                                                </td>
                                                                <td width="30%" align="left" class="c-inv-sub-padding">
                                                                    <div class="select-others select-tax height-35 rounded border-0">
                                                                        <select class="form-control select-picker"
                                                                            id="discount_type" name="discount_type">
                                                                            <option value="percentage">%</option>
                                                                            <option value="fixed">Amount</option>
                                                                        </select>
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </td>
                                                <td><span id="discount_amount">0.00</span></td>
                                            </tr>
                                            <tr>
                                                <td>Tax</td>
                                                <td colspan="2" class="p-0 border-0">
                                                    <table width="100%" id="purchase-order-taxes">
                                                        <tr>
                                                            <td colspan="2"><span class="tax-percent">0.00</span></td>
                                                        </tr>
                                                    </table>
                                                </td>
                                            </tr>
                                            <tr class="bg-amt-grey f-16 f-w-500">
                                                <td colspan="2">Total</td>
                                                <td><span class="total">0.00</span></td>
                                                <input type="hidden" class="total-field" name="total" value="0">
                                            </tr>
                                        </tbody>
                                    </table>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="d-flex flex-wrap px-lg-4 px-md-4 px-3 py-3">
                    <div class="col-md-6 col-sm-12 c-inv-note-terms p-0 mb-lg-0 mb-md-0 mb-3">
                        <x-forms.label fieldId="notes" :fieldLabel="'Notes'"></x-forms.label>
                        <textarea class="form-control" name="notes" id="notes" rows="4"
                            placeholder="@lang('placeholders.notes')"></textarea>
                    </div>
                    <div class="col-md-6 col-sm-12 p-0 c-inv-note-terms">
                        <x-forms.label fieldId="terms" :fieldLabel="'Terms'"></x-forms.label>
                        <textarea class="form-control" name="terms" id="terms" rows="4"
                            placeholder="@lang('placeholders.terms')"></textarea>
                    </div>
                </div>

                <x-form-actions>
                    <x-forms.button-primary id="save-purchase-order-form" class="mr-3" icon="check">Save
                    </x-forms.button-primary>
                    <x-forms.button-cancel :link="route('purchase-orders.index')" class="border-0">Cancel
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

        const dp1 = datepicker('#order_date', {
            position: 'bl',
            ...datepickerConfig
        });

        const dp2 = datepicker('#expected_delivery', {
            position: 'bl',
            ...datepickerConfig
        });

        $('#add-item').click(function() {
            var item = '<div class="d-flex px-4 py-3 c-inv-desc item-row">' +
                '<div class="c-inv-desc-table w-100 d-lg-flex d-md-flex d-block">' +
                '<table width="100%">' +
                '<tbody>' +
                '<tr class="text-dark-grey font-weight-bold f-14">' +
                '<td width="40%" class="border-0 inv-desc-mbl btlr">Description</td>' +
                '<td width="15%" class="border-0" align="right">Qty</td>' +
                '<td width="15%" class="border-0" align="right">Unit Price</td>' +
                '<td width="15%" class="border-0 bblr-mbl" align="right">Amount</td>' +
                '<td width="15%" class="border-0"></td>' +
                '</tr>' +
                '<tr>' +
                '<td class="border-bottom-0 btrr-mbl btlr">' +
                '<input type="text" class="form-control f-14 border-0 w-100 item_name" name="item_name[]" placeholder="Item Name">' +
                '<textarea class="form-control f-14 border-0 w-100" name="item_summary[]" placeholder="Description" rows="2"></textarea>' +
                '</td>' +
                '<td class="border-bottom-0">' +
                '<input type="number" min="1" class="form-control f-14 border-0 w-100 text-right quantity mt-3" value="1" name="quantity[]">' +
                '</td>' +
                '<td class="border-bottom-0">' +
                '<input type="number" min="1" class="f-14 border-0 w-100 text-right cost_per_item form-control" placeholder="0.00" value="0" name="cost_per_item[]">' +
                '</td>' +
                '<td rowspan="2" align="right" valign="top" class="bg-amt-grey btrr-bbrr">' +
                '<span class="amount-html">0.00</span>' +
                '<input type="hidden" class="amount" name="amount[]" value="0">' +
                '</td>' +
                '<td class="border-bottom-0">' +
                '<a href="javascript:;" class="d-flex align-items-center justify-content-center remove-item"><i class="fa fa-times-circle f-20 text-lightest"></i></a>' +
                '</td>' +
                '</tr>' +
                '</tbody>' +
                '</table>' +
                '</div>' +
                '</div>';

            $(item).hide().appendTo("#sortable").fadeIn(500);
        });

        $(document).on('click', '.remove-item', function() {
            $(this).closest('.item-row').fadeOut(300, function() {
                $(this).remove();
                calculateTotal();
            });
        });

        $(document).on('keyup', '.quantity, .cost_per_item', function() {
            var quantity = $(this).closest('.item-row').find('.quantity').val();
            var perItemCost = $(this).closest('.item-row').find('.cost_per_item').val();
            var amount = (quantity * perItemCost);

            $(this).closest('.item-row').find('.amount').val(amount);
            $(this).closest('.item-row').find('.amount-html').html(amount);
            calculateTotal();
        });

        function calculateTotal() {
            var subtotal = 0;
            var discount = 0;
            var discountAmount = 0;
            var discountType = $('#discount_type').val();
            var discountValue = $('.discount_value').val();

            $('.amount').each(function() {
                subtotal += parseFloat($(this).val());
            });

            if (discountType == 'percentage' && discountValue != '') {
                discountAmount = (subtotal * discountValue) / 100;
            } else if (discountValue != '') {
                discountAmount = parseFloat(discountValue);
            }

            discount = discountAmount;

            var total = subtotal - discount;

            $('.sub-total').html(subtotal);
            $('.sub-total-field').val(subtotal);
            $('#discount_amount').html(discount);
            $('.total').html(total);
            $('.total-field').val(total);
        }

        $('#discount_type, .discount_value').on('change keyup', function() {
            calculateTotal();
        });

        $('#save-purchase-order-form').click(function () {
            const url = "{{ route('purchase-orders.store') }}";

            $.easyAjax({
                url: url,
                container: '#save-purchase-order-data-form',
                type: "POST",
                disableButton: true,
                blockUI: true,
                buttonSelector: "#save-purchase-order-form",
                data: $('#save-purchase-order-data-form').serialize(),
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