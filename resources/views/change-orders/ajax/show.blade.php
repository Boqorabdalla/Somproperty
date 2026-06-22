<div class="row">
    <div class="col-sm-12">
        <div class="card border-0">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h4 class="mb-0 f-21 font-weight-normal">Change Order Details</h4>
                    <div>
                        @if (user()->permission('edit_change_orders') == 'all' || user()->permission('edit_change_orders') == 'added')
                            <x-forms.link-secondary :link="route('change-orders.edit', $changeOrder->id)"
                                                    class="mr-2 openRightModal" icon="edit">
                                Edit
                            </x-forms.link-secondary>
                        @endif
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <table class="table table-bordered">
                            <tr>
                                <th class="bg-light-grey" width="40%">Change Order Number</th>
                                <td>{{ $changeOrder->change_order_number }}</td>
                            </tr>
                            <tr>
                                <th class="bg-light-grey">Title</th>
                                <td>{{ $changeOrder->title }}</td>
                            </tr>
                            <tr>
                                <th class="bg-light-grey">Project</th>
                                <td>{{ $changeOrder->project->project_name ?? '--' }}</td>
                            </tr>
                            <tr>
                                <th class="bg-light-grey">Currency</th>
                                <td>{{ $changeOrder->currency->currency_code ?? '--' }}</td>
                            </tr>
                        </table>
                    </div>
                </div>

                @if ($changeOrder->description)
                    <div class="row mt-3">
                        <div class="col-sm-12">
                            <h5 class="mb-2">Description</h5>
                            <p class="f-14">{!! $changeOrder->description !!}</p>
                        </div>
                    </div>
                @endif

                @if ($changeOrder->reason)
                    <div class="row mt-3">
                        <div class="col-sm-12">
                            <h5 class="mb-2">Reason</h5>
                            <p class="f-14">{!! $changeOrder->reason !!}</p>
                        </div>
                    </div>
                @endif

                @if ($changeOrder->items->count() > 0)
                    <div class="row mt-3">
                        <div class="col-sm-12">
                            <h5 class="mb-2">Items</h5>
                            <table class="table table-bordered">
                                <thead class="bg-light-grey text-dark-grey">
                                    <tr>
                                        <th>Description</th>
                                        <th align="right">Qty</th>
                                        <th align="right">Unit Price</th>
                                        <th align="right">Amount</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($changeOrder->items as $item)
                                        <tr>
                                            <td>
                                                {{ $item->item_name }}
                                                @if ($item->item_summary)
                                                    <br><small class="text-dark-grey">{!! $item->item_summary !!}</small>
                                                @endif
                                            </td>
                                            <td align="right">{{ $item->quantity }}</td>
                                            <td align="right">{{ currency_format($item->unit_price, $changeOrder->currency_id, false) }}</td>
                                            <td align="right">{{ currency_format($item->amount, $changeOrder->currency_id, false) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th colspan="3" align="right">Sub Total</th>
                                        <td align="right">{{ currency_format($changeOrder->sub_total, $changeOrder->currency_id, false) }}</td>
                                    </tr>
                                    @if ($changeOrder->tax > 0)
                                        <tr>
                                            <th colspan="3" align="right">Tax</th>
                                            <td align="right">{{ currency_format($changeOrder->tax, $changeOrder->currency_id, false) }}</td>
                                        </tr>
                                    @endif
                                    <tr class="bg-amt-grey f-16 f-w-500">
                                        <th colspan="3" align="right">Total</th>
                                        <td align="right">{{ currency_format($changeOrder->total, $changeOrder->currency_id, false) }}</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                @endif

                <div class="mt-4">
                    <x-forms.button-cancel :link="route('change-orders.index')" class="border-0">
                        Close
                    </x-forms.button-cancel>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    init(RIGHT_MODAL);
</script>