<?php

namespace App\DataTables;

use App\Helper\Common;
use App\Models\IncidentReport;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;

class IncidentsDataTable extends BaseDataTable
{
    private $deleteIncidentPermission;

    private $editIncidentPermission;

    public function __construct()
    {
        parent::__construct();
        $this->editIncidentPermission = user()->permission('edit_incident');
        $this->deleteIncidentPermission = user()->permission('delete_incident');
    }

    public function dataTable($query)
    {
        $datatables = datatables()->eloquent($query);

        $datatables->addColumn('check', fn ($row) => $this->checkBox($row));
        $datatables->addColumn('project', function ($row) {
            return ($row->project) ? $row->project->project_name : '';
        });
        $datatables->addColumn('category', function ($row) {
            return ($row->category) ? $row->category->name : '';
        });
        $datatables->addColumn('action', function ($row) {
            $action = '<div class="task_view">
            <a href="'.route('incidents.show', [$row->id]).'"
                class="taskView openRightModal text-darkest-grey f-w-500">View</a>

                    <div class="dropdown">
                        <a class="task_view_more d-flex align-items-center justify-content-center dropdown-toggle" type="link"
                            id="dropdownMenuLink-'.$row->id.'" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="icon-options-vertical icons"></i>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuLink-'.$row->id.'" tabindex="0">';

            if ($this->editIncidentPermission == 'all' || ($this->editIncidentPermission == 'added' && user()->id == $row->added_by)) {
                $action .= '<a class="dropdown-item openRightModal" href="'.route('incidents.edit', [$row->id]).'">
                                <i class="fa fa-edit mr-2"></i>
                                Edit
                            </a>';
            }

            if ($this->deleteIncidentPermission == 'all' || ($this->deleteIncidentPermission == 'added' && user()->id == $row->added_by)) {
                $action .= '<a class="dropdown-item delete-table-row" href="javascript:;" data-incident-id="'.$row->id.'">
                                <i class="fa fa-trash mr-2"></i>
                                Delete
                            </a>';
            }

            $action .= '</div>
                    </div>
                </div>';

            return $action;
        });

        $datatables->editColumn('title', function ($row) {
            return '<a href="'.route('incidents.show', [$row->id]).'" class="openRightModal text-darkest-grey" >'.$row->title.'</a>';
        });
        $datatables->addIndexColumn();
        $datatables->smart(false);
        $datatables->setRowId(fn ($row) => 'row-'.$row->id);

        $datatables->rawColumns(['action', 'check', 'title', 'project', 'category']);

        return $datatables;
    }

    public function query(IncidentReport $model)
    {
        $request = $this->request();

        $model = $model->with('project', 'category')->select('id', 'title', 'project_id', 'category_id', 'incident_date', 'severity', 'status', 'added_by');

        if ($request->searchText != '') {
            $safeTerm = Common::safeString(request('searchText'));
            $model->where(function ($query) use ($safeTerm) {
                $query->where('incident_reports.title', 'like', '%'.$safeTerm.'%')
                    ->orWhere('incident_reports.severity', 'like', '%'.$safeTerm.'%');
            });
        }

        if (user()->permission('view_incident') == 'added') {
            $model->where('incident_reports.added_by', user()->id);
        }

        return $model;
    }

    public function html()
    {
        $dataTable = $this->setBuilder('incidents-table', 2)
            ->parameters([
                'initComplete' => 'function () {
                   window.LaravelDataTables["incidents-table"].buttons().container()
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
            'Title' => ['data' => 'title', 'name' => 'title', 'title' => 'Title'],
            'Project' => ['data' => 'project', 'name' => 'project', 'title' => 'Project'],
            'Category' => ['data' => 'category', 'name' => 'category', 'title' => 'Category'],
            'Incident Date' => ['data' => 'incident_date', 'name' => 'incident_date', 'title' => 'Incident Date'],
            'Severity' => ['data' => 'severity', 'name' => 'severity', 'title' => 'Severity'],
            'Status' => ['data' => 'status', 'name' => 'status', 'title' => 'Status'],
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
