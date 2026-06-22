<?php

namespace App\Http\Controllers;

use App\DataTables\ChangeOrdersDataTable;
use App\Helper\Reply;
use App\Http\Requests\ChangeOrder\StoreChangeOrderRequest;
use App\Http\Requests\ChangeOrder\UpdateChangeOrderRequest;
use App\Models\ChangeOrder;
use App\Models\ChangeOrderItem;
use App\Models\Currency;
use App\Models\Project;

class ChangeOrderController extends AccountBaseController
{
    public function __construct()
    {
        parent::__construct();
        $this->pageTitle = 'app.menu.changeOrders';
        $this->middleware(function ($request, $next) {
            abort_403(! in_array('change-orders', $this->user->modules));

            return $next($request);
        });
    }

    public function index(ChangeOrdersDataTable $dataTable)
    {
        $viewPermission = user()->permission('view_change_order');
        abort_403(! in_array($viewPermission, ['all', 'added', 'owned', 'both']));

        return $dataTable->render('change-orders.index', $this->data);
    }

    public function create()
    {
        $this->addPermission = user()->permission('add_change_order');
        abort_403(! in_array($this->addPermission, ['all', 'added']));

        $this->pageTitle = __('app.menu.addChangeOrder');
        $this->projects = Project::all();
        $this->currencies = Currency::all();

        $this->view = 'change-orders.ajax.create';

        if (request()->ajax()) {
            return $this->returnAjax($this->view);
        }

        return view('change-orders.create', $this->data);
    }

    public function store(StoreChangeOrderRequest $request)
    {
        $this->addPermission = user()->permission('add_change_order');
        abort_403(! in_array($this->addPermission, ['all', 'added']));

        $changeOrder = new ChangeOrder;
        $changeOrder->change_order_number = $request->change_order_number;
        $changeOrder->project_id = $request->project_id;
        $changeOrder->title = $request->title;
        $changeOrder->description = trim_editor($request->description);
        $changeOrder->reason = trim_editor($request->reason);
        $changeOrder->sub_total = $request->sub_total;
        $changeOrder->tax = $request->tax ?? 0;
        $changeOrder->total = $request->total;
        $changeOrder->currency_id = $request->currency_id;
        $changeOrder->user_id = user()->id;
        $changeOrder->save();

        if ($request->items) {
            foreach ($request->items as $item) {
                $changeItem = new ChangeOrderItem;
                $changeItem->change_order_id = $changeOrder->id;
                $changeItem->item_name = $item['item_name'];
                $changeItem->description = $item['description'] ?? null;
                $changeItem->quantity = $item['quantity'];
                $changeItem->unit_price = $item['unit_price'];
                $changeItem->total = $item['total'];
                $changeItem->save();
            }
        }

        return Reply::successWithData(__('messages.recordSaved'), ['redirectUrl' => route('change-orders.index')]);
    }

    public function show($id)
    {
        $this->changeOrder = ChangeOrder::with('items', 'project', 'currency')->findOrFail($id);
        $this->viewPermission = user()->permission('view_change_order');
        abort_403(! ($this->viewPermission == 'all' || ($this->viewPermission == 'added' && $this->changeOrder->added_by == user()->id)));

        $this->pageTitle = $this->changeOrder->change_order_number;
        $this->view = 'change-orders.ajax.show';

        if (request()->ajax()) {
            return $this->returnAjax($this->view);
        }

        return view('change-orders.create', $this->data);
    }

    public function edit($id)
    {
        $this->changeOrder = ChangeOrder::with('items')->findOrFail($id);
        $this->editPermission = user()->permission('edit_change_order');
        abort_403(! ($this->editPermission == 'all' || ($this->editPermission == 'added' && $this->changeOrder->added_by == user()->id)));

        $this->pageTitle = __('app.menu.changeOrders');
        $this->projects = Project::all();
        $this->currencies = Currency::all();

        $this->view = 'change-orders.ajax.edit';

        if (request()->ajax()) {
            return $this->returnAjax($this->view);
        }

        return view('change-orders.create', $this->data);
    }

    public function update(UpdateChangeOrderRequest $request, $id)
    {
        $changeOrder = ChangeOrder::findOrFail($id);
        $this->editPermission = user()->permission('edit_change_order');
        abort_403(! ($this->editPermission == 'all' || ($this->editPermission == 'added' && $changeOrder->added_by == user()->id)));

        $changeOrder->project_id = $request->project_id;
        $changeOrder->title = $request->title;
        $changeOrder->description = trim_editor($request->description);
        $changeOrder->reason = trim_editor($request->reason);
        $changeOrder->sub_total = $request->sub_total;
        $changeOrder->tax = $request->tax ?? 0;
        $changeOrder->total = $request->total;
        $changeOrder->currency_id = $request->currency_id;
        $changeOrder->save();

        $changeOrder->items()->delete();
        if ($request->items) {
            foreach ($request->items as $item) {
                $changeItem = new ChangeOrderItem;
                $changeItem->change_order_id = $changeOrder->id;
                $changeItem->item_name = $item['item_name'];
                $changeItem->description = $item['description'] ?? null;
                $changeItem->quantity = $item['quantity'];
                $changeItem->unit_price = $item['unit_price'];
                $changeItem->total = $item['total'];
                $changeItem->save();
            }
        }

        return Reply::successWithData(__('messages.updateSuccess'), ['redirectUrl' => route('change-orders.index')]);
    }

    public function destroy($id)
    {
        $changeOrder = ChangeOrder::findOrFail($id);
        $this->deletePermission = user()->permission('delete_change_order');
        abort_403(! ($this->deletePermission == 'all' || ($this->deletePermission == 'added' && $changeOrder->added_by == user()->id)));

        ChangeOrder::destroy($id);

        return Reply::success(__('messages.deleteSuccess'));
    }

    public function approve($id)
    {
        abort_403(user()->permission('approve_change_order') != 'all');

        $changeOrder = ChangeOrder::findOrFail($id);
        $changeOrder->status = 'approved';
        $changeOrder->approved_by = user()->id;
        $changeOrder->save();

        return Reply::success(__('messages.updateSuccess'));
    }
}
