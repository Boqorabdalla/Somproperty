<?php

namespace App\DataTables;

use App\Helper\Common;
use App\Models\PurchaseOrder;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;

class PurchaseOrdersDataTable extends BaseDataTable
{
    private $deletePurchaseOrderPermission;

    private $editPurchaseOrderPermission;

    public function __construct()
    {
        parent::__construct();
        $this->editPurchaseOrderPermission = user()->permission('edit_purchase_order');
        $this->deletePurchaseOrderPermission = user()->permission('delete_purchase_order');
    }

    public function dataTable($query)
    {
        $datatables = datatables()->eloquent($query);

        $datatables->addColumn('check', fn ($row) => $this->checkBox($row));
        $datatables->addColumn('vendor', function ($row) {
            return ($row->vendor) ? $row->vendor->name : '';
        });
        $datatables->addColumn('project', function ($row) {
            return ($row->project) ? $row->project->project_name : '';
        });
        $datatables->addColumn('action', function ($row) {
            $action = '<div class="task_view">
            <a href="'.route('purchase-orders.show', [$row->id]).'"
                class="taskView openRightModal text-darkest-grey f-w-500">View</a>

                    <div class="dropdown">
                        <a class="task_view_more d-flex align-items-center justify-content-center dropdown-toggle" type="link"
                            id="dropdownMenuLink-'.$row->id.'" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="icon-options-vertical icons"></i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuLink-'.$row->id.'" tabindex="0">';

            if ($this->editPurchaseOrderPermission == 'all' || ($this->editPurchaseOrderPermission == 'added' && user()->id == $row->added_by)) {
                $action .= '<a class="dropdown-item openRightModal" href="'.route('purchase-orders.edit', [$row->id]).'">
                                <i class="fa fa-edit mr-2"></i>
                                Edit
                            </a>';
            }

            if ($this->deletePurchaseOrderPermission == 'all' || ($this->deletePurchaseOrderPermission == 'added' && user()->id == $row->added_by)) {
                $action .= '<a class="dropdown-item delete-table-row" href="javascript:;" data-purchase-order-id="'.$row->id.'">
                                <i class="fa fa-trash mr-2"></i>
                                Delete
                            </a>';
            }

            $action .= '</div>
                    </div>
                </div>';

            return $action;
        });

        $datatables->editColumn('po_number', function ($row) {
            return '<a href="'.route('purchase-orders.show', [$row->id]).'" class="openRightModal text-darkest-grey" >'.$row->po_number.'</a>';
        });
        $datatables->addIndexColumn();
        $datatables->smart(false);
        $datatables->setRowId(fn ($row) => 'row-'.$row->id);

        $datatables->rawColumns(['action', 'check', 'po_number', 'vendor', 'project']);

        return $datatables;
    }

    public function query(PurchaseOrder $model)
    {
        $request = $this->request();

        $model = $model->with('vendor', 'project', 'currency')->select('id', 'po_number', 'vendor_id', 'project_id', 'order_date', 'expected_delivery', 'status', 'total', 'currency_id', 'added_by');

        if ($request->searchText != '') {
            $safeTerm = Common::safeString(request('searchText'));
            $model->where(function ($query) use ($safeTerm) {
                $query->where('purchase_orders.po_number', 'like', '%'.$safeTerm.'%')
                    ->orWhere('purchase_orders.total', 'like', '%'.$safeTerm.'%');
            });
        }

        if (user()->permission('view_purchase_order') == 'added') {
            $model->where('purchase_orders.added_by', user()->id);
        }

        return $model;
    }

    public function html()
    {
        $dataTable = $this->setBuilder('purchase-orders-table', 2)
            ->parameters([
                'initComplete' => 'function () {
                   window.LaravelDataTables["purchase-orders-table"].buttons().container()
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
            'PO Number' => ['data' => 'po_number', 'name' => 'po_number', 'title' => 'PO Number'],
            'Vendor' => ['data' => 'vendor', 'name' => 'vendor', 'title' => 'Vendor'],
            'Project' => ['data' => 'project', 'name' => 'project', 'title' => 'Project'],
            'Order Date' => ['data' => 'order_date', 'name' => 'order_date', 'title' => 'Order Date'],
            'Expected Delivery' => ['data' => 'expected_delivery', 'name' => 'expected_delivery', 'title' => 'Expected Delivery'],
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
