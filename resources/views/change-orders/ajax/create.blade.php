<div class="row">
    <div class="col-sm-12">
        <x-form id="save-change-order-form">
            <div class="bg-white rounded add-client">
                <h4 class="p-20 mb-0 f-21 font-weight-normal border-bottom-grey">
                    Change Order Details</h4>
                <div class="p-20 row">

                    <div class="col-md-4">
                        <x-forms.text class="mr-0 mr-lg-2 mr-md-2"
                                      :fieldLabel="'Change Order Number'"
                                      fieldName="change_order_number" fieldRequired="true"
                                      fieldId="change_order_number"
                                      :fieldPlaceholder="__('placeholders.changeOrderNumber')"/>
                    </div>

                    <div class="col-md-4">
                        <x-forms.label class="my-3" fieldId="project_id" :fieldLabel="'Project'" fieldRequired="true">
                        </x-forms.label>
                        <x-forms.input-group>
                            <select class="form-control select-picker" name="project_id" id="project_id"
                                    data-live-search="true">
                                <option value="">--</option>
                                @foreach ($projects as $project)
                                    <option value="{{ $project->id }}">{{ $project->project_name }}</option>
                                @endforeach
                            </select>
                        </x-forms.input-group>
                    </div>

                    <div class="col-md-4">
                        <x-forms.label class="my-3" fieldId="currency_id" :fieldLabel="'Currency'">
                        </x-forms.label>
                        <x-forms.input-group>
                            <select class="form-control select-picker" name="currency_id" id="currency_id"
                                    data-live-search="true">
                                @foreach ($currencies as $currency)
                                    <option @selected($currency->id == company()->currency_id)
                                    value="{{ $currency->id }}">
                                        {{ $currency->currency_symbol . ' (' . $currency->currency_code . ')' }}
                                    </option>
                                @endforeach
                            </select>
                        </x-forms.input-group>
                    </div>

                    <div class="col-md-12">
                        <x-forms.text class="mr-0 mr-lg-2 mr-md-2"
                                      :fieldLabel="'Title'" fieldName="title" fieldRequired="true"
                                      fieldId="title" :fieldPlaceholder="__('placeholders.title')"/>
                    </div>

                    <div class="col-md-12">
                        <x-forms.label class="my-3" fieldId="description" :fieldLabel="'Description'">
                        </x-forms.label>
                        <div id="description"></div>
                        <textarea name="description" id="description-text" class="d-none"></textarea>
                    </div>

                    <div class="col-md-12">
                        <x-forms.label class="my-3" fieldId="reason" :fieldLabel="'Reason'">
                        </x-forms.label>
                        <div id="reason"></div>
                        <textarea name="reason" id="reason-text" class="d-none"></textarea>
                    </div>

                </div>

                <h4 class="p-20 mb-0 f-21 font-weight-normal border-top-grey">
                    Items</h4>

                <div class="p-20 row">
                    <div class="col-md-12">
                        <div class="d-flex justify-content-end mb-3">
                            <a href="javascript:;" class="f-15 f-w-500" id="add-item">
                                <i class="icons icon-plus font-weight-bold mr-1"></i>Add Item
                            </a>
                        </div>

                        <div id="sortable">
                            <div class="d-flex px-4 py-3 c-inv-desc item-row">
                                <div class="c-inv-desc-table w-100 d-lg-flex d-md-flex d-block">
                                    <table width="100%">
                                        <tbody>
                                            <tr class="text-dark-grey font-weight-bold f-14">
                                                <td width="50%" class="border-0 inv-desc-mbl btlr">Description</td>
                                                <td width="15%" class="border-0" align="right">Qty</td>
                                                <td width="15%" class="border-0" align="right">Unit Price</td>
                                                <td width="20%" class="border-0 bblr-mbl" align="right">Amount</td>
                                            </tr>
                                            <tr>
                                                <td class="border-bottom-0 btrr-mbl btlr">
                                                    <input type="text" class="form-control f-14 border-0 w-100 item_name"
                                                        name="item_name[]" placeholder="@lang('modules.expenses.itemName')">
                                                </td>
                                                <td class="border-bottom-0">
                                                    <input type="number" min="1"
                                                        class="form-control f-14 border-0 w-100 text-right quantity mt-3" value="1"
                                                        name="quantity[]">
                                                </td>
                                                <td class="border-bottom-0">
                                                    <input type="number" min="0" step="0.01"
                                                        class="f-14 border-0 w-100 text-right cost_per_item form-control" placeholder="0.00"
                                                        value="0" name="cost_per_item[]">
                                                </td>
                                                <td rowspan="2" align="right" valign="top" class="bg-amt-grey btrr-bbrr">
                                                    <span class="amount-html">0.00</span>
                                                    <input type="hidden" class="amount" name="amount[]" value="0">
                                                </td>
                                            </tr>
                                            <tr class="d-none d-md-table-row d-lg-table-row">
                                                <td colspan="3" class="dash-border-top bblr">
                                                    <textarea class="f-14 border-0 w-100 desktop-description form-control" name="item_summary[]"
                                                        placeholder="@lang('placeholders.invoices.description')"></textarea>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                    <a href="javascript:;"
                                        class="d-flex align-items-center justify-content-center ml-3 remove-item"><i
                                            class="fa fa-times-circle f-20 text-lightest"></i></a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="d-flex px-lg-4 px-md-4 px-3 pb-3 c-inv-total">
                    <table width="100%" class="text-right f-14 ">
                        <tbody>
                            <tr>
                                <td width="50%" class="border-0 d-lg-table d-md-table d-none"></td>
                                <td width="50%" class="p-0 border-0 c-inv-total-right">
                                    <table width="100%">
                                        <tbody>
                                            <tr>
                                                <td class="border-top-0 text-dark-grey">Sub Total</td>
                                                <td width="30%" class="border-top-0 sub-total">0.00</td>
                                            </tr>
                                            <tr>
                                                <td class="text-dark-grey">Tax</td>
                                                <td>
                                                    <input type="number" min="0" step="0.01" name="tax"
                                                        class="form-control f-14 border-0 w-100 text-right" id="tax"
                                                        placeholder="0.00" value="0">
                                                </td>
                                            </tr>
                                            <tr class="bg-amt-grey f-16 f-w-500">
                                                <td>Total</td>
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

                <x-form-actions>
                    <x-forms.button-primary id="save-change-order-form-btn" class="mr-3" icon="check">
                        Save
                    </x-forms.button-primary>
                    <x-forms.button-cancel :link="route('change-orders.index')" class="border-0">
                        Cancel
                    </x-forms.button-cancel>
                </x-form-actions>

            </div>
        </x-form>
    </div>
</div>

<script>
    $(document).ready(function() {

        quillImageLoad('#description');
        quillImageLoad('#reason');

        $('#add-item').click(function() {
            var i = $(document).find('.item_name').length;
            var item = '<div class="d-flex px-4 py-3 c-inv-desc item-row">' +
                '<div class="c-inv-desc-table w-100 d-lg-flex d-md-flex d-block">' +
                '<table width="100%">' +
                '<tbody>' +
                '<tr class="text-dark-grey font-weight-bold f-14">' +
                '<td width="50%" class="border-0 inv-desc-mbl btlr">Description</td>' +
                '<td width="15%" class="border-0" align="right">Qty</td>' +
                '<td width="15%" class="border-0" align="right">Unit Price</td>' +
                '<td width="20%" class="border-0 bblr-mbl" align="right">Amount</td>' +
                '</tr>' +
                '<tr>' +
                '<td class="border-bottom-0 btrr-mbl btlr">' +
                '<input type="text" class="form-control f-14 border-0 w-100 item_name" name="item_name[]" placeholder="Item Name">' +
                '</td>' +
                '<td class="border-bottom-0">' +
                '<input type="number" min="1" class="form-control f-14 border-0 w-100 text-right quantity" value="1" name="quantity[]">' +
                '</td>' +
                '<td class="border-bottom-0">' +
                '<input type="number" min="0" step="0.01" class="f-14 border-0 w-100 text-right cost_per_item form-control" placeholder="0.00" value="0" name="cost_per_item[]">' +
                '</td>' +
                '<td rowspan="2" align="right" valign="top" class="bg-amt-grey btrr-bbrr">' +
                '<span class="amount-html">0.00</span>' +
                '<input type="hidden" class="amount" name="amount[]" value="0">' +
                '</td>' +
                '</tr>' +
                '<tr class="d-none d-md-table-row d-lg-table-row">' +
                '<td colspan="3" class="dash-border-top bblr">' +
                '<textarea class="f-14 border-0 w-100 desktop-description form-control" name="item_summary[]" placeholder="Description"></textarea>' +
                '</td>' +
                '</tr>' +
                '</tbody>' +
                '</table>' +
                '</div>' +
                '<a href="javascript:;" class="d-flex align-items-center justify-content-center ml-3 remove-item"><i class="fa fa-times-circle f-20 text-lightest"></i></a>' +
                '</div>';
            $(item).hide().appendTo("#sortable").fadeIn(500);
        });

        $('#save-change-order-form').on('click', '.remove-item', function() {
            $(this).closest('.item-row').fadeOut(300, function() {
                $(this).remove();
                calculateTotal();
            });
        });

        $(document).on('keyup change', '.quantity, .cost_per_item', function() {
            var quantity = $(this).closest('.item-row').find('.quantity').val();
            var perItemCost = $(this).closest('.item-row').find('.cost_per_item').val();
            var amount = (quantity * perItemCost);

            $(this).closest('.item-row').find('.amount').val(decimalupto2(amount));
            $(this).closest('.item-row').find('.amount-html').html(decimalupto2(amount));

            calculateTotal();
        });

        $('#tax').on('keyup change', function() {
            calculateTotal();
        });

        function calculateTotal() {
            var subTotal = 0;

            $('.item-row').each(function() {
                var quantity = $(this).find('.quantity').val();
                var perItemCost = $(this).find('.cost_per_item').val();
                var amount = (quantity * perItemCost);
                subTotal += amount;
            });

            var tax = parseFloat($('#tax').val()) || 0;
            var total = subTotal + tax;

            $('.sub-total').html(decimalupto2(subTotal));
            $('.sub-total').val(decimalupto2(subTotal));
            $('.total').html(decimalupto2(total));
            $('.total-field').val(decimalupto2(total));
        }

        $('#save-change-order-form-btn').click(function() {
            var description = document.getElementById('description').children[0].innerHTML;
            document.getElementById('description-text').value = description;

            var reason = document.getElementById('reason').children[0].innerHTML;
            document.getElementById('reason-text').value = reason;

            const url = "{{ route('change-orders.store') }}";

            $.easyAjax({
                url: url,
                container: '#save-change-order-form',
                type: "POST",
                disableButton: true,
                blockUI: true,
                buttonSelector: "#save-change-order-form-btn",
                data: $('#save-change-order-form').serialize(),
                success: function(response) {
                    if (response.status == 'success') {
                        window.location.href = response.redirectUrl;
                    }
                }
            });
        });

        init(RIGHT_MODAL);
    });
</script>