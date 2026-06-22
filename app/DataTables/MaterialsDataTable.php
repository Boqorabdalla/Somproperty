<?php

namespace App\DataTables;

use App\Helper\Common;
use App\Models\Material;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;

class MaterialsDataTable extends BaseDataTable
{
    private $deleteMaterialPermission;

    private $editMaterialPermission;

    public function __construct()
    {
        parent::__construct();
        $this->editMaterialPermission = user()->permission('edit_material');
        $this->deleteMaterialPermission = user()->permission('delete_material');
    }

    public function dataTable($query)
    {
        $datatables = datatables()->eloquent($query);

        $datatables->addColumn('check', fn ($row) => $this->checkBox($row));
        $datatables->addColumn('category', function ($row) {
            return ($row->category) ? $row->category->name : '';
        });
        $datatables->addColumn('action', function ($row) {
            $action = '<div class="task_view">
            <a href="'.route('materials.show', [$row->id]).'"
                class="taskView openRightModal text-darkest-grey f-w-500">View</a>

                    <div class="dropdown">
                        <a class="task_view_more d-flex align-items-center justify-content-center dropdown-toggle" type="link"
                            id="dropdownMenuLink-'.$row->id.'" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="icon-options-vertical icons"></i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuLink-'.$row->id.'" tabindex="0">';

            if ($this->editMaterialPermission == 'all' || ($this->editMaterialPermission == 'added' && user()->id == $row->added_by)) {
                $action .= '<a class="dropdown-item openRightModal" href="'.route('materials.edit', [$row->id]).'">
                                <i class="fa fa-edit mr-2"></i>
                                Edit
                            </a>';
            }

            if ($this->deleteMaterialPermission == 'all' || ($this->deleteMaterialPermission == 'added' && user()->id == $row->added_by)) {
                $action .= '<a class="dropdown-item delete-table-row" href="javascript:;" data-material-id="'.$row->id.'">
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
            return '<a href="'.route('materials.show', [$row->id]).'" class="openRightModal text-darkest-grey" >'.$row->name.'</a>';
        });
        $datatables->addIndexColumn();
        $datatables->smart(false);
        $datatables->setRowId(fn ($row) => 'row-'.$row->id);

        $datatables->rawColumns(['action', 'check', 'name', 'category']);

        return $datatables;
    }

    public function query(Material $model)
    {
        $request = $this->request();

        $model = $model->with('category')->select('id', 'name', 'sku', 'category_id', 'unit_price', 'current_stock', 'min_stock', 'unit', 'added_by');

        if ($request->searchText != '') {
            $safeTerm = Common::safeString(request('searchText'));
            $model->where(function ($query) use ($safeTerm) {
                $query->where('materials.name', 'like', '%'.$safeTerm.'%')
                    ->orWhere('materials.sku', 'like', '%'.$safeTerm.'%');
            });
        }

        if (user()->permission('view_material') == 'added') {
            $model->where('materials.added_by', user()->id);
        }

        return $model;
    }

    public function html()
    {
        $dataTable = $this->setBuilder('materials-table', 2)
            ->parameters([
                'initComplete' => 'function () {
                   window.LaravelDataTables["materials-table"].buttons().container()
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
            'Material' => ['data' => 'name', 'name' => 'name', 'title' => 'Material'],
            'SKU' => ['data' => 'sku', 'name' => 'sku', 'title' => 'SKU'],
            'Category' => ['data' => 'category', 'name' => 'category', 'title' => 'Category'],
            'Unit Price' => ['data' => 'unit_price', 'name' => 'unit_price', 'title' => 'Unit Price'],
            'Current Stock' => ['data' => 'current_stock', 'name' => 'current_stock', 'title' => 'Current Stock'],
            'Min Stock' => ['data' => 'min_stock', 'name' => 'min_stock', 'title' => 'Min Stock'],
            'Unit' => ['data' => 'unit', 'name' => 'unit', 'title' => 'Unit'],
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
