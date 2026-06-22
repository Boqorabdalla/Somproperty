<?php

namespace App\Http\Controllers;

use App\DataTables\MaterialsDataTable;
use App\Helper\Reply;
use App\Http\Requests\Material\StoreMaterialRequest;
use App\Http\Requests\Material\UpdateMaterialRequest;
use App\Models\Material;
use App\Models\MaterialCategory;
use App\Models\MaterialInventory;
use App\Models\Project;
use App\Models\UnitType;
use Illuminate\Http\Request;

class MaterialController extends AccountBaseController
{
    public function __construct()
    {
        parent::__construct();
        $this->pageTitle = 'app.menu.materials';
        $this->middleware(function ($request, $next) {
            abort_403(! in_array('materials', $this->user->modules));

            return $next($request);
        });
    }

    public function index(MaterialsDataTable $dataTable)
    {
        $viewPermission = user()->permission('view_material');
        abort_403(! in_array($viewPermission, ['all', 'added', 'owned', 'both']));

        $this->categories = MaterialCategory::all();
        $this->unitTypes = UnitType::all();
        $this->totalMaterials = Material::count();

        return $dataTable->render('materials.index', $this->data);
    }

    public function create()
    {
        $this->addPermission = user()->permission('add_material');
        abort_403(! in_array($this->addPermission, ['all', 'added']));

        $this->pageTitle = __('app.menu.addMaterial');
        $this->categories = MaterialCategory::all();
        $this->unitTypes = UnitType::all();
        $this->projects = Project::all();

        $this->view = 'materials.ajax.create';

        if (request()->ajax()) {
            return $this->returnAjax($this->view);
        }

        return view('materials.create', $this->data);
    }

    public function store(StoreMaterialRequest $request)
    {
        $this->addPermission = user()->permission('add_material');
        abort_403(! in_array($this->addPermission, ['all', 'added']));

        $material = new Material;
        $material->name = $request->name;
        $material->sku = $request->sku;
        $material->description = trim_editor($request->description);
        $material->category_id = $request->category_id;
        $material->unit_price = $request->unit_price;
        $material->current_stock = $request->current_stock ?? 0;
        $material->min_stock = $request->min_stock ?? 0;
        $material->unit = $request->unit;
        $material->unit_id = $request->unit_type;
        $material->project_id = $request->project_id;
        $material->save();

        return Reply::successWithData(__('messages.recordSaved'), ['redirectUrl' => route('materials.index')]);
    }

    public function show($id)
    {
        $this->material = Material::with('category', 'transactions.user', 'unitType')->findOrFail($id);
        $this->viewPermission = user()->permission('view_material');
        abort_403(! ($this->viewPermission == 'all' || ($this->viewPermission == 'added' && $this->material->added_by == user()->id)));

        $this->pageTitle = $this->material->name;
        $this->view = 'materials.ajax.show';

        if (request()->ajax()) {
            return $this->returnAjax($this->view);
        }

        return view('materials.create', $this->data);
    }

    public function edit($id)
    {
        $this->material = Material::findOrFail($id);
        $this->editPermission = user()->permission('edit_material');
        abort_403(! ($this->editPermission == 'all' || ($this->editPermission == 'added' && $this->material->added_by == user()->id)));

        $this->pageTitle = __('app.menu.materials');
        $this->categories = MaterialCategory::all();
        $this->unitTypes = UnitType::all();
        $this->projects = Project::all();

        $this->view = 'materials.ajax.edit';

        if (request()->ajax()) {
            return $this->returnAjax($this->view);
        }

        return view('materials.create', $this->data);
    }

    public function update(UpdateMaterialRequest $request, $id)
    {
        $material = Material::findOrFail($id);
        $this->editPermission = user()->permission('edit_material');
        abort_403(! ($this->editPermission == 'all' || ($this->editPermission == 'added' && $material->added_by == user()->id)));

        $material->name = $request->name;
        $material->sku = $request->sku;
        $material->description = trim_editor($request->description);
        $material->category_id = $request->category_id;
        $material->unit_price = $request->unit_price;
        $material->current_stock = $request->current_stock ?? 0;
        $material->min_stock = $request->min_stock ?? 0;
        $material->unit = $request->unit;
        $material->unit_id = $request->unit_type;
        $material->project_id = $request->project_id;
        $material->save();

        return Reply::successWithData(__('messages.updateSuccess'), ['redirectUrl' => route('materials.index')]);
    }

    public function destroy($id)
    {
        $material = Material::findOrFail($id);
        $this->deletePermission = user()->permission('delete_material');
        abort_403(! ($this->deletePermission == 'all' || ($this->deletePermission == 'added' && $material->added_by == user()->id)));

        Material::destroy($id);

        return Reply::success(__('messages.deleteSuccess'));
    }

    public function adjustStock(Request $request, $id)
    {
        abort_403(user()->permission('manage_inventory') != 'all');

        $material = Material::findOrFail($id);
        $quantity = $request->quantity;
        $type = $quantity >= 0 ? 'added' : 'removed';

        $inventory = new MaterialInventory;
        $inventory->material_id = $material->id;
        $inventory->user_id = user()->id;
        $inventory->type = $type;
        $inventory->quantity = abs($quantity);
        $inventory->quantity_after = $material->current_stock + $quantity;
        $inventory->remarks = $request->remarks;
        $inventory->save();

        $material->current_stock = $inventory->quantity_after;
        $material->save();

        return Reply::success(__('messages.recordSaved'));
    }
}
