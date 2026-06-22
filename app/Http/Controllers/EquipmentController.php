<?php

namespace App\Http\Controllers;

use App\DataTables\EquipmentDataTable;
use App\Helper\Reply;
use App\Http\Requests\Equipment\StoreEquipmentRequest;
use App\Http\Requests\Equipment\UpdateEquipmentRequest;
use App\Models\Equipment;
use App\Models\EquipmentMaintenance;
use App\Models\EquipmentType;
use App\Models\Project;
use Illuminate\Http\Request;

class EquipmentController extends AccountBaseController
{
    public function __construct()
    {
        parent::__construct();
        $this->pageTitle = 'app.menu.equipment';
        $this->middleware(function ($request, $next) {
            abort_403(! in_array('equipment', $this->user->modules));

            return $next($request);
        });
    }

    public function index(EquipmentDataTable $dataTable)
    {
        $viewPermission = user()->permission('view_equipment');
        abort_403(! in_array($viewPermission, ['all', 'added', 'owned', 'both']));

        $this->equipmentTypes = EquipmentType::all();
        $this->totalEquipment = Equipment::count();

        return $dataTable->render('equipment.index', $this->data);
    }

    public function create()
    {
        $this->addPermission = user()->permission('add_equipment');
        abort_403(! in_array($this->addPermission, ['all', 'added']));

        $this->pageTitle = __('app.menu.addEquipment');

        $this->equipmentTypes = EquipmentType::all();
        $this->projects = Project::all();

        $this->view = 'equipment.ajax.create';

        if (request()->ajax()) {
            return $this->returnAjax($this->view);
        }

        return view('equipment.create', $this->data);
    }

    public function store(StoreEquipmentRequest $request)
    {
        $this->addPermission = user()->permission('add_equipment');
        abort_403(! in_array($this->addPermission, ['all', 'added']));

        $equipment = new Equipment;
        $equipment->name = $request->name;
        $equipment->equipment_type_id = $request->equipment_type_id;
        $equipment->model = $request->model;
        $equipment->serial_no = $request->serial_no;
        $equipment->purchase_date = $request->purchase_date;
        $equipment->purchase_price = $request->purchase_price;
        $equipment->status = $request->status ?? 'available';
        $equipment->location = $request->location;
        $equipment->next_maintenance_date = $request->next_maintenance_date;
        $equipment->notes = trim_editor($request->notes);
        $equipment->project_id = $request->project_id;
        $equipment->save();

        return Reply::successWithData(__('messages.recordSaved'), ['redirectUrl' => route('equipment.index')]);
    }

    public function show($id)
    {
        $this->equipment = Equipment::with('equipmentType', 'maintenanceLogs.user')->findOrFail($id);
        $this->viewPermission = user()->permission('view_equipment');
        abort_403(! ($this->viewPermission == 'all' || ($this->viewPermission == 'added' && $this->equipment->added_by == user()->id)));

        $this->pageTitle = $this->equipment->name;
        $this->view = 'equipment.ajax.show';

        if (request()->ajax()) {
            return $this->returnAjax($this->view);
        }

        return view('equipment.create', $this->data);
    }

    public function edit($id)
    {
        $this->equipment = Equipment::findOrFail($id);
        $this->editPermission = user()->permission('edit_equipment');
        abort_403(! ($this->editPermission == 'all' || ($this->editPermission == 'added' && $this->equipment->added_by == user()->id)));

        $this->pageTitle = __('app.menu.equipment');
        $this->equipmentTypes = EquipmentType::all();
        $this->projects = Project::all();

        $this->view = 'equipment.ajax.edit';

        if (request()->ajax()) {
            return $this->returnAjax($this->view);
        }

        return view('equipment.create', $this->data);
    }

    public function update(UpdateEquipmentRequest $request, $id)
    {
        $equipment = Equipment::findOrFail($id);
        $this->editPermission = user()->permission('edit_equipment');
        abort_403(! ($this->editPermission == 'all' || ($this->editPermission == 'added' && $equipment->added_by == user()->id)));

        $equipment->name = $request->name;
        $equipment->equipment_type_id = $request->equipment_type_id;
        $equipment->model = $request->model;
        $equipment->serial_no = $request->serial_no;
        $equipment->purchase_date = $request->purchase_date;
        $equipment->purchase_price = $request->purchase_price;
        $equipment->status = $request->status;
        $equipment->location = $request->location;
        $equipment->next_maintenance_date = $request->next_maintenance_date;
        $equipment->notes = trim_editor($request->notes);
        $equipment->project_id = $request->project_id;
        $equipment->save();

        return Reply::successWithData(__('messages.updateSuccess'), ['redirectUrl' => route('equipment.index')]);
    }

    public function destroy($id)
    {
        $equipment = Equipment::findOrFail($id);
        $this->deletePermission = user()->permission('delete_equipment');
        abort_403(! ($this->deletePermission == 'all' || ($this->deletePermission == 'added' && $equipment->added_by == user()->id)));

        Equipment::destroy($id);

        return Reply::success(__('messages.deleteSuccess'));
    }

    public function logMaintenance(Request $request, $id)
    {
        abort_403(user()->permission('manage_equipment_maintenance') != 'all');

        $request->validate([
            'description' => 'required',
            'maintenance_date' => 'required',
            'cost' => 'required|numeric',
        ]);

        $maintenance = new EquipmentMaintenance;
        $maintenance->equipment_id = $id;
        $maintenance->user_id = user()->id;
        $maintenance->description = $request->description;
        $maintenance->maintenance_date = $request->maintenance_date;
        $maintenance->cost = $request->cost;
        $maintenance->vendor = $request->vendor;
        $maintenance->next_maintenance_date = $request->next_maintenance_date;
        $maintenance->save();

        if ($request->next_maintenance_date) {
            $equipment = Equipment::findOrFail($id);
            $equipment->next_maintenance_date = $request->next_maintenance_date;
            $equipment->save();
        }

        return Reply::success(__('messages.recordSaved'));
    }
}
