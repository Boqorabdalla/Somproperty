<?php

namespace App\Http\Controllers;

use App\DataTables\SubcontractorsDataTable;
use App\Helper\Reply;
use App\Http\Requests\Subcontractor\StoreSubcontractorRequest;
use App\Http\Requests\Subcontractor\UpdateSubcontractorRequest;
use App\Models\Subcontractor;

class SubcontractorController extends AccountBaseController
{
    public function __construct()
    {
        parent::__construct();
        $this->pageTitle = 'app.menu.subcontractors';
        $this->middleware(function ($request, $next) {
            abort_403(! in_array('subcontractors', $this->user->modules));

            return $next($request);
        });
    }

    public function index(SubcontractorsDataTable $dataTable)
    {
        $viewPermission = user()->permission('view_subcontractor');
        abort_403(! in_array($viewPermission, ['all', 'added', 'owned', 'both']));

        return $dataTable->render('subcontractors.index', $this->data);
    }

    public function create()
    {
        $this->addPermission = user()->permission('add_subcontractor');
        abort_403(! in_array($this->addPermission, ['all', 'added']));

        $this->pageTitle = __('app.menu.addSubcontractor');

        $this->view = 'subcontractors.ajax.create';

        if (request()->ajax()) {
            return $this->returnAjax($this->view);
        }

        return view('subcontractors.create', $this->data);
    }

    public function store(StoreSubcontractorRequest $request)
    {
        $this->addPermission = user()->permission('add_subcontractor');
        abort_403(! in_array($this->addPermission, ['all', 'added']));

        $subcontractor = new Subcontractor;
        $subcontractor->company_name = $request->company_name;
        $subcontractor->contact_person = $request->contact_person;
        $subcontractor->email = $request->email;
        $subcontractor->phone = $request->phone;
        $subcontractor->address = trim_editor($request->address);
        $subcontractor->trade_type = $request->trade_type;
        $subcontractor->license_no = $request->license_no;
        $subcontractor->insurance_expiry = $request->insurance_expiry;
        $subcontractor->rating = $request->rating;
        $subcontractor->notes = trim_editor($request->notes);
        $subcontractor->save();

        return Reply::successWithData(__('messages.recordSaved'), ['redirectUrl' => route('subcontractors.index')]);
    }

    public function show($id)
    {
        $this->subcontractor = Subcontractor::with('projects', 'documents')->findOrFail($id);
        $this->viewPermission = user()->permission('view_subcontractor');
        abort_403(! ($this->viewPermission == 'all' || ($this->viewPermission == 'added' && $this->subcontractor->added_by == user()->id)));

        $this->pageTitle = $this->subcontractor->company_name;
        $this->view = 'subcontractors.ajax.show';

        if (request()->ajax()) {
            return $this->returnAjax($this->view);
        }

        return view('subcontractors.create', $this->data);
    }

    public function edit($id)
    {
        $this->subcontractor = Subcontractor::findOrFail($id);
        $this->editPermission = user()->permission('edit_subcontractor');
        abort_403(! ($this->editPermission == 'all' || ($this->editPermission == 'added' && $this->subcontractor->added_by == user()->id)));

        $this->pageTitle = __('app.menu.subcontractors');

        $this->view = 'subcontractors.ajax.edit';

        if (request()->ajax()) {
            return $this->returnAjax($this->view);
        }

        return view('subcontractors.create', $this->data);
    }

    public function update(UpdateSubcontractorRequest $request, $id)
    {
        $subcontractor = Subcontractor::findOrFail($id);
        $this->editPermission = user()->permission('edit_subcontractor');
        abort_403(! ($this->editPermission == 'all' || ($this->editPermission == 'added' && $subcontractor->added_by == user()->id)));

        $subcontractor->company_name = $request->company_name;
        $subcontractor->contact_person = $request->contact_person;
        $subcontractor->email = $request->email;
        $subcontractor->phone = $request->phone;
        $subcontractor->address = trim_editor($request->address);
        $subcontractor->trade_type = $request->trade_type;
        $subcontractor->license_no = $request->license_no;
        $subcontractor->insurance_expiry = $request->insurance_expiry;
        $subcontractor->rating = $request->rating;
        $subcontractor->notes = trim_editor($request->notes);
        $subcontractor->save();

        return Reply::successWithData(__('messages.updateSuccess'), ['redirectUrl' => route('subcontractors.index')]);
    }

    public function destroy($id)
    {
        $subcontractor = Subcontractor::findOrFail($id);
        $this->deletePermission = user()->permission('delete_subcontractor');
        abort_403(! ($this->deletePermission == 'all' || ($this->deletePermission == 'added' && $subcontractor->added_by == user()->id)));

        Subcontractor::destroy($id);

        return Reply::success(__('messages.deleteSuccess'));
    }
}
