<?php

namespace App\DataTables;

use App\Helper\Common;
use App\Models\Subcontractor;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;

class SubcontractorsDataTable extends BaseDataTable
{
    private $deleteSubcontractorPermission;

    private $editSubcontractorPermission;

    public function __construct()
    {
        parent::__construct();
        $this->editSubcontractorPermission = user()->permission('edit_subcontractor');
        $this->deleteSubcontractorPermission = user()->permission('delete_subcontractor');
    }

    public function dataTable($query)
    {
        $datatables = datatables()->eloquent($query);

        $datatables->addColumn('check', fn ($row) => $this->checkBox($row));
        $datatables->addColumn('action', function ($row) {
            $action = '<div class="task_view">
            <a href="'.route('subcontractors.show', [$row->id]).'"
                class="taskView openRightModal text-darkest-grey f-w-500">View</a>

                    <div class="dropdown">
                        <a class="task_view_more d-flex align-items-center justify-content-center dropdown-toggle" type="link"
                            id="dropdownMenuLink-'.$row->id.'" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="icon-options-vertical icons"></i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuLink-'.$row->id.'" tabindex="0">';

            if ($this->editSubcontractorPermission == 'all' || ($this->editSubcontractorPermission == 'added' && user()->id == $row->added_by)) {
                $action .= '<a class="dropdown-item openRightModal" href="'.route('subcontractors.edit', [$row->id]).'">
                                <i class="fa fa-edit mr-2"></i>
                                Edit
                            </a>';
            }

            if ($this->deleteSubcontractorPermission == 'all' || ($this->deleteSubcontractorPermission == 'added' && user()->id == $row->added_by)) {
                $action .= '<a class="dropdown-item delete-table-row" href="javascript:;" data-subcontractor-id="'.$row->id.'">
                                <i class="fa fa-trash mr-2"></i>
                                Delete
                            </a>';
            }

            $action .= '</div>
                    </div>
                </div>';

            return $action;
        });

        $datatables->editColumn('company_name', function ($row) {
            return '<a href="'.route('subcontractors.show', [$row->id]).'" class="openRightModal text-darkest-grey" >'.$row->company_name.'</a>';
        });
        $datatables->addIndexColumn();
        $datatables->smart(false);
        $datatables->setRowId(fn ($row) => 'row-'.$row->id);

        $datatables->rawColumns(['action', 'check', 'company_name']);

        return $datatables;
    }

    public function query(Subcontractor $model)
    {
        $request = $this->request();

        $model = $model->select('id', 'company_name', 'contact_person', 'email', 'phone', 'trade_type', 'license_no', 'rating', 'added_by');

        if ($request->searchText != '') {
            $safeTerm = Common::safeString(request('searchText'));
            $model->where(function ($query) use ($safeTerm) {
                $query->where('subcontractors.company_name', 'like', '%'.$safeTerm.'%')
                    ->orWhere('subcontractors.email', 'like', '%'.$safeTerm.'%');
            });
        }

        if (user()->permission('view_subcontractor') == 'added') {
            $model->where('subcontractors.added_by', user()->id);
        }

        return $model;
    }

    public function html()
    {
        $dataTable = $this->setBuilder('subcontractors-table', 2)
            ->parameters([
                'initComplete' => 'function () {
                   window.LaravelDataTables["subcontractors-table"].buttons().container()
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
            'Company Name' => ['data' => 'company_name', 'name' => 'company_name', 'title' => 'Company Name'],
            'Contact Person' => ['data' => 'contact_person', 'name' => 'contact_person', 'title' => 'Contact Person'],
            'Email' => ['data' => 'email', 'name' => 'email', 'title' => 'Email'],
            'Phone' => ['data' => 'phone', 'name' => 'phone', 'title' => 'Phone'],
            'Trade Type' => ['data' => 'trade_type', 'name' => 'trade_type', 'title' => 'Trade Type'],
            'License No' => ['data' => 'license_no', 'name' => 'license_no', 'title' => 'License No'],
            'Rating' => ['data' => 'rating', 'name' => 'rating', 'title' => 'Rating'],
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
