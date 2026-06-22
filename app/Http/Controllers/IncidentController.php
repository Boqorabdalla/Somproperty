<?php

namespace App\Http\Controllers;

use App\DataTables\IncidentsDataTable;
use App\Helper\Files;
use App\Helper\Reply;
use App\Http\Requests\Incident\StoreIncidentRequest;
use App\Http\Requests\Incident\UpdateIncidentRequest;
use App\Models\IncidentCategory;
use App\Models\IncidentFile;
use App\Models\IncidentReport;
use App\Models\Project;

class IncidentController extends AccountBaseController
{
    public function __construct()
    {
        parent::__construct();
        $this->pageTitle = 'app.menu.incidents';
        $this->middleware(function ($request, $next) {
            abort_403(! in_array('incidents', $this->user->modules));

            return $next($request);
        });
    }

    public function index(IncidentsDataTable $dataTable)
    {
        $viewPermission = user()->permission('view_incident');
        abort_403(! in_array($viewPermission, ['all', 'added', 'owned', 'both']));

        return $dataTable->render('incidents.index', $this->data);
    }

    public function create()
    {
        $this->addPermission = user()->permission('add_incident');
        abort_403(! in_array($this->addPermission, ['all', 'added']));

        $this->pageTitle = __('app.menu.addIncident');
        $this->projects = Project::all();
        $this->categories = IncidentCategory::all();

        $this->view = 'incidents.ajax.create';

        if (request()->ajax()) {
            return $this->returnAjax($this->view);
        }

        return view('incidents.create', $this->data);
    }

    public function store(StoreIncidentRequest $request)
    {
        $this->addPermission = user()->permission('add_incident');
        abort_403(! in_array($this->addPermission, ['all', 'added']));

        $incident = new IncidentReport;
        $incident->project_id = $request->project_id;
        $incident->category_id = $request->category_id;
        $incident->user_id = user()->id;
        $incident->title = $request->title;
        $incident->incident_date = $request->incident_date;
        $incident->severity = $request->severity;
        $incident->description = trim_editor($request->description);
        $incident->root_cause = trim_editor($request->root_cause);
        $incident->corrective_action = trim_editor($request->corrective_action);
        $incident->save();

        if ($request->hasFile('files')) {
            foreach ($request->file('files') as $file) {
                $fileModel = new IncidentFile;
                $fileModel->incident_id = $incident->id;
                $fileModel->filename = $file->getClientOriginalName();
                $fileModel->hashname = Files::uploadLocalOrS3($file, 'incident-files/'.$incident->id);
                $fileModel->size = $file->getSize();
                $fileModel->save();
            }
        }

        return Reply::successWithData(__('messages.recordSaved'), ['redirectUrl' => route('incidents.index')]);
    }

    public function show($id)
    {
        $this->incident = IncidentReport::with('project', 'category', 'files', 'user')->findOrFail($id);
        $this->viewPermission = user()->permission('view_incident');
        abort_403(! ($this->viewPermission == 'all' || ($this->viewPermission == 'added' && $this->incident->added_by == user()->id)));

        $this->pageTitle = $this->incident->title;
        $this->view = 'incidents.ajax.show';

        if (request()->ajax()) {
            return $this->returnAjax($this->view);
        }

        return view('incidents.create', $this->data);
    }

    public function edit($id)
    {
        $this->incident = IncidentReport::findOrFail($id);
        $this->editPermission = user()->permission('edit_incident');
        abort_403(! ($this->editPermission == 'all' || ($this->editPermission == 'added' && $this->incident->added_by == user()->id)));

        $this->pageTitle = __('app.menu.incidents');
        $this->projects = Project::all();
        $this->categories = IncidentCategory::all();

        $this->view = 'incidents.ajax.edit';

        if (request()->ajax()) {
            return $this->returnAjax($this->view);
        }

        return view('incidents.create', $this->data);
    }

    public function update(UpdateIncidentRequest $request, $id)
    {
        $incident = IncidentReport::findOrFail($id);
        $this->editPermission = user()->permission('edit_incident');
        abort_403(! ($this->editPermission == 'all' || ($this->editPermission == 'added' && $incident->added_by == user()->id)));

        $incident->project_id = $request->project_id;
        $incident->category_id = $request->category_id;
        $incident->title = $request->title;
        $incident->incident_date = $request->incident_date;
        $incident->severity = $request->severity;
        $incident->description = trim_editor($request->description);
        $incident->root_cause = trim_editor($request->root_cause);
        $incident->corrective_action = trim_editor($request->corrective_action);
        $incident->save();

        return Reply::successWithData(__('messages.updateSuccess'), ['redirectUrl' => route('incidents.index')]);
    }

    public function destroy($id)
    {
        $incident = IncidentReport::findOrFail($id);
        $this->deletePermission = user()->permission('delete_incident');
        abort_403(! ($this->deletePermission == 'all' || ($this->deletePermission == 'added' && $incident->added_by == user()->id)));

        IncidentReport::destroy($id);

        return Reply::success(__('messages.deleteSuccess'));
    }

    public function close($id)
    {
        abort_403(user()->permission('close_incident') != 'all');

        $incident = IncidentReport::findOrFail($id);
        $incident->status = 'closed';
        $incident->save();

        return Reply::success(__('messages.updateSuccess'));
    }
}
