<?php

namespace App\DataTables;

use App\Helper\Common;
use App\Models\Equipment;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;

class EquipmentDataTable extends BaseDataTable
{
    private $deleteEquipmentPermission;

    private $editEquipmentPermission;

    public function __construct()
    {
        parent::__construct();
        $this->editEquipmentPermission = user()->permission('edit_equipment');
        $this->deleteEquipmentPermission = user()->permission('delete_equipment');
    }

    public function dataTable($query)
    {
        $datatables = datatables()->eloquent($query);

        $datatables->addColumn('check', fn ($row) => $this->checkBox($row));
        $datatables->addColumn('equipment_type', function ($row) {
            return ($row->equipmentType) ? $row->equipmentType->name : '';
        });
        $datatables->addColumn('action', function ($row) {
            $action = '<div class="task_view">
            <a href="'.route('equipment.show', [$row->id]).'"
                class="taskView openRightModal text-darkest-grey f-w-500">View</a>

                    <div class="dropdown">
                        <a class="task_view_more d-flex align-items-center justify-content-center dropdown-toggle" type="link"
                            id="dropdownMenuLink-'.$row->id.'" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="icon-options-vertical icons"></i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuLink-'.$row->id.'" tabindex="0">';

            if ($this->editEquipmentPermission == 'all' || ($this->editEquipmentPermission == 'added' && user()->id == $row->added_by)) {
                $action .= '<a class="dropdown-item openRightModal" href="'.route('equipment.edit', [$row->id]).'">
                                <i class="fa fa-edit mr-2"></i>
                                Edit
                            </a>';
            }

            if ($this->deleteEquipmentPermission == 'all' || ($this->deleteEquipmentPermission == 'added' && user()->id == $row->added_by)) {
                $action .= '<a class="dropdown-item delete-table-row" href="javascript:;" data-equipment-id="'.$row->id.'">
                                <i class="fa fa-trash mr-2"></i>
                                Delete
                            </a>';
            }

            $action .= '</div>
                    </div>
                </div>';

            return $action;
        });

        $datatables->editColumn('name', function ($row) {
            return '<a href="'.route('equipment.show', [$row->id]).'" class="openRightModal text-darkest-grey" >'.$row->name.'</a>';
        });
        $datatables->addIndexColumn();
        $datatables->smart(false);
        $datatables->setRowId(fn ($row) => 'row-'.$row->id);

        $datatables->rawColumns(['action', 'check', 'name', 'equipment_type']);

        return $datatables;
    }

    public function query(Equipment $model)
    {
        $request = $this->request();

        $model = $model->with('equipmentType')->select('id', 'name', 'equipment_type_id', 'model', 'serial_no', 'purchase_date', 'purchase_price', 'status', 'location', 'added_by');

        if ($request->searchText != '') {
            $safeTerm = Common::safeString(request('searchText'));
            $model->where(function ($query) use ($safeTerm) {
                $query->where('equipment.name', 'like', '%'.$safeTerm.'%')
                    ->orWhere('equipment.model', 'like', '%'.$safeTerm.'%');
            });
        }

        if (user()->permission('view_equipment') == 'added') {
            $model->where('equipment.added_by', user()->id);
        }

        return $model;
    }

    public function html()
    {
        $dataTable = $this->setBuilder('equipment-table', 2)
            ->parameters([
                'initComplete' => 'function () {
                   window.LaravelDataTables["equipment-table"].buttons().container()
                    .appendTo( "#table-actions")
                }',
                'fnDrawCallback' => 'function( oSettings ) {
                    $("body").tooltip({
                        selector: \'[data-toggle="tooltip"]\'
                    })
                }',
            ]);

        if (canDataTableExport()) {
            $dataTable->buttons(Button::make(['extend' => 'excel', 'text' => '<i class="fa fa-file-export"></i> Export']));
        }

        return $dataTable;
    }

    protected function getColumns()
    {
        $data = [
            'check' => [
                'title' => '<input type="checkbox" name="select_all_table" id="select-all-table" onclick="selectAllTable(this)">',
                'exportable' => false,
                'orderable' => false,
                'searchable' => false,
                'visible' => true,
            ],
            '#' => ['data' => 'DT_RowIndex', 'orderable' => false, 'searchable' => false, 'visible' => false, 'title' => '#'],
            'id' => ['data' => 'id', 'name' => 'id', 'title' => '#', 'visible' => showId()],
            'Equipment' => ['data' => 'name', 'name' => 'name', 'title' => 'Equipment'],
            'Equipment Type' => ['data' => 'equipment_type', 'name' => 'equipment_type', 'title' => 'Equipment Type'],
            'Model' => ['data' => 'model', 'name' => 'model', 'title' => 'Model'],
            'Serial No' => ['data' => 'serial_no', 'name' => 'serial_no', 'title' => 'Serial No'],
            'Purchase Date' => ['data' => 'purchase_date', 'name' => 'purchase_date', 'title' => 'Purchase Date'],
            'Purchase Price' => ['data' => 'purchase_price', 'name' => 'purchase_price', 'title' => 'Purchase Price'],
            'Status' => ['data' => 'status', 'name' => 'status', 'title' => 'Status'],
            'Location' => ['data' => 'location', 'name' => 'location', 'title' => 'Location'],
        ];

        $action = [
            Column::computed('action', 'Action')
                ->exportable(false)
                ->printable(false)
                ->orderable(false)
                ->searchable(false)
                ->addClass('text-right pr-20'),
        ];

        return array_merge($data, $action);
    }
}
