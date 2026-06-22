<?php

namespace App\DataTables;

use App\Helper\Common;
use App\Models\ChangeOrder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;

class ChangeOrdersDataTable extends BaseDataTable
{
    private $deleteChangeOrderPermission;

    private $editChangeOrderPermission;

    public function __construct()
    {
        parent::__construct();
        $this->editChangeOrderPermission = user()->permission('edit_change_order');
        $this->deleteChangeOrderPermission = user()->permission('delete_change_order');
    }

    public function dataTable($query)
    {
        $datatables = datatables()->eloquent($query);

        $datatables->addColumn('check', fn ($row) => $this->checkBox($row));
        $datatables->addColumn('project', function ($row) {
            return ($row->project) ? $row->project->project_name : '';
        });
        $datatables->addColumn('action', function ($row) {
            $action = '<div class="task_view">
            <a href="'.route('change-orders.show', [$row->id]).'"
                class="taskView openRightModal text-darkest-grey f-w-500">View</a>

                    <div class="dropdown">
                        <a class="task_view_more d-flex align-items-center justify-content-center dropdown-toggle" type="link"
                            id="dropdownMenuLink-'.$row->id.'" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="icon-options-vertical icons"></i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuLink-'.$row->id.'" tabindex="0">';

            if ($this->editChangeOrderPermission == 'all' || ($this->editChangeOrderPermission == 'added' && user()->id == $row->added_by)) {
                $action .= '<a class="dropdown-item openRightModal" href="'.route('change-orders.edit', [$row->id]).'">
                                <i class="fa fa-edit mr-2"></i>
                                Edit
                            </a>';
            }

            if ($this->deleteChangeOrderPermission == 'all' || ($this->deleteChangeOrderPermission == 'added' && user()->id == $row->added_by)) {
                $action .= '<a class="dropdown-item delete-table-row" href="javascript:;" data-change-order-id="'.$row->id.'">
                                <i class="fa fa-trash mr-2"></i>
                                Delete
                            </a>';
            }

            $action .= '</div>
                    </div>
                </div>';

            return $action;
        });

        $datatables->editColumn('change_order_number', function ($row) {
            return '<a href="'.route('change-orders.show', [$row->id]).'" class="openRightModal text-darkest-grey" >'.$row->change_order_number.'</a>';
        });
        $datatables->addIndexColumn();
        $datatables->smart(false);
        $datatables->setRowId(fn ($row) => 'row-'.$row->id);

        $datatables->rawColumns(['action', 'check', 'change_order_number', 'project']);

        return $datatables;
    }

    public function query(ChangeOrder $model)
    {
        $request = $this->request();

        $model = $model->with('project', 'currency')->select('id', 'change_order_number', 'project_id', 'title', 'status', 'total', 'currency_id', 'added_by');

        if ($request->searchText != '') {
            $safeTerm = Common::safeString(request('searchText'));
            $model->where(function ($query) use ($safeTerm) {
                $query->where('change_orders.change_order_number', 'like', '%'.$safeTerm.'%')
                    ->orWhere('change_orders.title', 'like', '%'.$safeTerm.'%');
            });
        }

        if (user()->permission('view_change_order') == 'added') {
            $model->where('change_orders.added_by', user()->id);
        }

        return $model;
    }

    public function html()
    {
        $dataTable = $this->setBuilder('change-orders-table', 2)
            ->parameters([
                'initComplete' => 'function () {
                   window.LaravelDataTables["change-orders-table"].buttons().container()
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
            'Change Order Number' => ['data' => 'change_order_number', 'name' => 'change_order_number', 'title' => 'Change Order Number'],
            'Project' => ['data' => 'project', 'name' => 'project', 'title' => 'Project'],
            'Title' => ['data' => 'title', 'name' => 'title', 'title' => 'Title'],
            'Status' => ['data' => 'status', 'name' => 'status', 'title' => 'Status'],
            'Total' => ['data' => 'total', 'name' => 'total', 'title' => 'Total'],
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
