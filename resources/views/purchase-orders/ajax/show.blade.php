<div class="row">
    <div class="col-sm-12">
        <x-cards.data :title="'Purchase Order Details'">
            <x-cards.data-row :label="'PO Number'"
                :value="$purchaseOrder->po_number" />
            <x-cards.data-row :label="'Vendor'"
                :value="$purchaseOrder->vendor->name ?? '--'" />
            <x-cards.data-row :label="'Project'"
                :value="$purchaseOrder->project->project_name ?? '--'" />
            <x-cards.data-row :label="'Order Date'"
                :value="$purchaseOrder->order_date ? $purchaseOrder->order_date->format(company()->date_format) : '--'" />
            <x-cards.data-row :label="'Expected Delivery'"
                :value="$purchaseOrder->expected_delivery ? $purchaseOrder->expected_delivery->format(company()->date_format) : '--'" />
            <x-cards.data-row :label="'Sub Total'"
                :value="currency_format($purchaseOrder->sub_total, $purchaseOrder->currency_id, false)" />
            <x-cards.data-row :label="'Discount'"
                :value="$purchaseOrder->discount ? $purchaseOrder->discount . ($purchaseOrder->discount_type == 'percentage' ? '%' : '') : '--'" />
            <x-cards.data-row :label="'Tax'"
                :value="currency_format($purchaseOrder->tax, $purchaseOrder->currency_id, false) ?? '--'" />
            <x-cards.data-row :label="'Total'"
                :value="currency_format($purchaseOrder->total, $purchaseOrder->currency_id, false)" />
            <x-cards.data-row :label="'Notes'"
                :value="$purchaseOrder->notes ?? '--'" html="true" />
            <x-cards.data-row :label="'Terms'"
                :value="$purchaseOrder->terms ?? '--'" html="true" />
        </x-cards.data>
    </div>
</div>

<div class="row mt-4">
    <div class="col-sm-12">
        <x-cards.data :title="'Order Items'">
            <div class="table-responsive">
                <x-table class="border-0">
                    <thead>
                        <tr>
                            <th>Description</th>
                            <th align="right">Qty</th>
                            <th align="right">Unit Price</th>
                            <th align="right">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($purchaseOrder->items as $item)
                            <tr>
                                <td>
                                    {{ $item->item_name }}
                                    @if ($item->item_summary)
                                        <br><small class="text-dark-grey">{{ $item->item_summary }}</small>
                                    @endif
                                </td>
                                <td align="right">{{ $item->quantity }}</td>
                                <td align="right">{{ currency_format($item->unit_price, $purchaseOrder->currency_id, false) }}</td>
                                <td align="right">{{ currency_format($item->amount, $purchaseOrder->currency_id, false) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </x-table>
            </div>
        </x-cards.data>
    </div>
</div>