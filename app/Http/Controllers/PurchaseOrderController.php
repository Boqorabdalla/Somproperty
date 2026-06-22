<?php

namespace App\Http\Controllers;

use App\DataTables\PurchaseOrdersDataTable;
use App\Helper\Reply;
use App\Http\Requests\PurchaseOrder\StorePurchaseOrderRequest;
use App\Http\Requests\PurchaseOrder\UpdatePurchaseOrderRequest;
use App\Models\Currency;
use App\Models\Project;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\Vendor;
use Illuminate\Http\Request;

class PurchaseOrderController extends AccountBaseController
{
    public function __construct()
    {
        parent::__construct();
        $this->pageTitle = 'app.menu.purchaseOrders';
        $this->middleware(function ($request, $next) {
            abort_403(! in_array('purchases', $this->user->modules));

            return $next($request);
        });
    }

    public function index(PurchaseOrdersDataTable $dataTable)
    {
        $viewPermission = user()->permission('view_purchase_order');
        abort_403(! in_array($viewPermission, ['all', 'added', 'owned', 'both']));

        return $dataTable->render('purchase-orders.index', $this->data);
    }

    public function create()
    {
        $this->addPermission = user()->permission('add_purchase_order');
        abort_403(! in_array($this->addPermission, ['all', 'added']));

        $this->pageTitle = __('app.menu.addPurchaseOrder');
        $this->vendors = Vendor::all();
        $this->currencies = Currency::all();
        $this->projects = Project::all();

        $this->view = 'purchase-orders.ajax.create';

        if (request()->ajax()) {
            return $this->returnAjax($this->view);
        }

        return view('purchase-orders.create', $this->data);
    }

    public function store(StorePurchaseOrderRequest $request)
    {
        $this->addPermission = user()->permission('add_purchase_order');
        abort_403(! in_array($this->addPermission, ['all', 'added']));

        $order = new PurchaseOrder;
        $order->po_number = $request->po_number;
        $order->vendor_id = $request->vendor_id;
        $order->project_id = $request->project_id;
        $order->order_date = $request->order_date;
        $order->expected_delivery = $request->expected_delivery;
        $order->sub_total = $request->sub_total;
        $order->discount = $request->discount ?? 0;
        $order->discount_type = $request->discount_type;
        $order->tax = $request->tax ?? 0;
        $order->total = $request->total;
        $order->currency_id = $request->currency_id;
        $order->notes = trim_editor($request->notes);
        $order->terms = trim_editor($request->terms);
        $order->save();

        if ($request->items) {
            foreach ($request->items as $item) {
                $orderItem = new PurchaseOrderItem;
                $orderItem->purchase_order_id = $order->id;
                $orderItem->item_name = $item['item_name'];
                $orderItem->quantity = $item['quantity'];
                $orderItem->unit_price = $item['unit_price'];
                $orderItem->total = $item['total'];
                $orderItem->unit = $item['unit'] ?? null;
                $orderItem->save();
            }
        }

        return Reply::successWithData(__('messages.recordSaved'), ['redirectUrl' => route('purchase-orders.index')]);
    }

    public function show($id)
    {
        $this->purchaseOrder = PurchaseOrder::with('items', 'vendor', 'project', 'currency')->findOrFail($id);
        $this->viewPermission = user()->permission('view_purchase_order');
        abort_403(! ($this->viewPermission == 'all' || ($this->viewPermission == 'added' && $this->purchaseOrder->added_by == user()->id)));

        $this->pageTitle = $this->purchaseOrder->po_number;
        $this->view = 'purchase-orders.ajax.show';

        if (request()->ajax()) {
            return $this->returnAjax($this->view);
        }

        return view('purchase-orders.create', $this->data);
    }

    public function edit($id)
    {
        $this->purchaseOrder = PurchaseOrder::with('items')->findOrFail($id);
        $this->editPermission = user()->permission('edit_purchase_order');
        abort_403(! ($this->editPermission == 'all' || ($this->editPermission == 'added' && $this->purchaseOrder->added_by == user()->id)));

        $this->pageTitle = __('app.menu.purchaseOrders');
        $this->vendors = Vendor::all();
        $this->currencies = Currency::all();
        $this->projects = Project::all();

        $this->view = 'purchase-orders.ajax.edit';

        if (request()->ajax()) {
            return $this->returnAjax($this->view);
        }

        return view('purchase-orders.create', $this->data);
    }

    public function update(UpdatePurchaseOrderRequest $request, $id)
    {
        $order = PurchaseOrder::findOrFail($id);
        $this->editPermission = user()->permission('edit_purchase_order');
        abort_403(! ($this->editPermission == 'all' || ($this->editPermission == 'added' && $order->added_by == user()->id)));

        $order->vendor_id = $request->vendor_id;
        $order->project_id = $request->project_id;
        $order->order_date = $request->order_date;
        $order->expected_delivery = $request->expected_delivery;
        $order->sub_total = $request->sub_total;
        $order->discount = $request->discount ?? 0;
        $order->discount_type = $request->discount_type;
        $order->tax = $request->tax ?? 0;
        $order->total = $request->total;
        $order->currency_id = $request->currency_id;
        $order->notes = trim_editor($request->notes);
        $order->terms = trim_editor($request->terms);
        $order->save();

        $order->items()->delete();
        if ($request->items) {
            foreach ($request->items as $item) {
                $orderItem = new PurchaseOrderItem;
                $orderItem->purchase_order_id = $order->id;
                $orderItem->item_name = $item['item_name'];
                $orderItem->quantity = $item['quantity'];
                $orderItem->unit_price = $item['unit_price'];
                $orderItem->total = $item['total'];
                $orderItem->unit = $item['unit'] ?? null;
                $orderItem->save();
            }
        }

        return Reply::successWithData(__('messages.updateSuccess'), ['redirectUrl' => route('purchase-orders.index')]);
    }

    public function destroy($id)
    {
        $order = PurchaseOrder::findOrFail($id);
        $this->deletePermission = user()->permission('delete_purchase_order');
        abort_403(! ($this->deletePermission == 'all' || ($this->deletePermission == 'added' && $order->added_by == user()->id)));

        PurchaseOrder::destroy($id);

        return Reply::success(__('messages.deleteSuccess'));
    }

    public function changeStatus(Request $request, $id)
    {
        abort_403(user()->permission('approve_purchase_order') != 'all');

        $order = PurchaseOrder::findOrFail($id);
        $order->status = $request->status;
        $order->save();

        return Reply::success(__('messages.updateSuccess'));
    }
}
