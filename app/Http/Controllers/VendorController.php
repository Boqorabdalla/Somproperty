<?php

namespace App\Http\Controllers;

use App\DataTables\VendorsDataTable;
use App\Helper\Reply;
use App\Http\Requests\Vendor\StoreVendorRequest;
use App\Http\Requests\Vendor\UpdateVendorRequest;
use App\Models\Vendor;

class VendorController extends AccountBaseController
{
    public function __construct()
    {
        parent::__construct();
        $this->pageTitle = 'app.menu.vendors';
        $this->middleware(function ($request, $next) {
            abort_403(! in_array('vendors', $this->user->modules));

            return $next($request);
        });
    }

    public function index(VendorsDataTable $dataTable)
    {
        $viewPermission = user()->permission('view_vendor');
        abort_403(! in_array($viewPermission, ['all', 'added', 'owned', 'both']));

        return $dataTable->render('vendors.index', $this->data);
    }

    public function create()
    {
        $this->addPermission = user()->permission('add_vendor');
        abort_403(! in_array($this->addPermission, ['all', 'added']));

        $this->pageTitle = __('app.menu.addVendor');

        $this->view = 'vendors.ajax.create';

        if (request()->ajax()) {
            return $this->returnAjax($this->view);
        }

        return view('vendors.create', $this->data);
    }

    public function store(StoreVendorRequest $request)
    {
        $this->addPermission = user()->permission('add_vendor');
        abort_403(! in_array($this->addPermission, ['all', 'added']));

        $vendor = new Vendor;
        $vendor->name = $request->name;
        $vendor->contact_person = $request->contact_person;
        $vendor->email = $request->email;
        $vendor->phone = $request->phone;
        $vendor->address = trim_editor($request->address);
        $vendor->payment_terms = $request->payment_terms;
        $vendor->notes = trim_editor($request->notes);
        $vendor->save();

        return Reply::successWithData(__('messages.recordSaved'), ['redirectUrl' => route('vendors.index')]);
    }

    public function show($id)
    {
        $this->vendor = Vendor::with('purchaseOrders')->findOrFail($id);
        $this->viewPermission = user()->permission('view_vendor');
        abort_403(! ($this->viewPermission == 'all' || ($this->viewPermission == 'added' && $this->vendor->added_by == user()->id)));

        $this->pageTitle = $this->vendor->name;
        $this->view = 'vendors.ajax.show';

        if (request()->ajax()) {
            return $this->returnAjax($this->view);
        }

        return view('vendors.create', $this->data);
    }

    public function edit($id)
    {
        $this->vendor = Vendor::findOrFail($id);
        $this->editPermission = user()->permission('edit_vendor');
        abort_403(! ($this->editPermission == 'all' || ($this->editPermission == 'added' && $this->vendor->added_by == user()->id)));

        $this->pageTitle = __('app.menu.vendors');

        $this->view = 'vendors.ajax.edit';

        if (request()->ajax()) {
            return $this->returnAjax($this->view);
        }

        return view('vendors.create', $this->data);
    }

    public function update(UpdateVendorRequest $request, $id)
    {
        $vendor = Vendor::findOrFail($id);
        $this->editPermission = user()->permission('edit_vendor');
        abort_403(! ($this->editPermission == 'all' || ($this->editPermission == 'added' && $vendor->added_by == user()->id)));

        $vendor->name = $request->name;
        $vendor->contact_person = $request->contact_person;
        $vendor->email = $request->email;
        $vendor->phone = $request->phone;
        $vendor->address = trim_editor($request->address);
        $vendor->payment_terms = $request->payment_terms;
        $vendor->notes = trim_editor($request->notes);
        $vendor->save();

        return Reply::successWithData(__('messages.updateSuccess'), ['redirectUrl' => route('vendors.index')]);
    }

    public function destroy($id)
    {
        $vendor = Vendor::findOrFail($id);
        $this->deletePermission = user()->permission('delete_vendor');
        abort_403(! ($this->deletePermission == 'all' || ($this->deletePermission == 'added' && $vendor->added_by == user()->id)));

        Vendor::destroy($id);

        return Reply::success(__('messages.deleteSuccess'));
    }
}
