<?php

namespace App\Http\Controllers;

use App\DataTables\ProgressReportsDataTable;
use App\Helper\Files;
use App\Helper\Reply;
use App\Http\Requests\ProgressReport\StoreProgressReportRequest;
use App\Http\Requests\ProgressReport\UpdateProgressReportRequest;
use App\Models\ProgressReportPhoto;
use App\Models\Project;
use App\Models\ProjectMilestone;
use App\Models\ProjectProgressReport;

class ProgressReportController extends AccountBaseController
{
    public function __construct()
    {
        parent::__construct();
        $this->pageTitle = 'app.menu.progressReports';
        $this->middleware(function ($request, $next) {
            abort_403(! in_array('progress-reports', $this->user->modules));

            return $next($request);
        });
    }

    public function index(ProgressReportsDataTable $dataTable)
    {
        $viewPermission = user()->permission('view_progress_report');
        abort_403(! in_array($viewPermission, ['all', 'added', 'owned', 'both']));

        return $dataTable->render('progress-reports.index', $this->data);
    }

    public function create()
    {
        $this->addPermission = user()->permission('add_progress_report');
        abort_403(! in_array($this->addPermission, ['all', 'added']));

        $this->pageTitle = __('app.menu.addProgressReport');
        $this->projects = Project::all();
        $this->milestones = ProjectMilestone::all();

        $this->view = 'progress-reports.ajax.create';

        if (request()->ajax()) {
            return $this->returnAjax($this->view);
        }

        return view('progress-reports.create', $this->data);
    }

    public function store(StoreProgressReportRequest $request)
    {
        $this->addPermission = user()->permission('add_progress_report');
        abort_403(! in_array($this->addPermission, ['all', 'added']));

        $report = new ProjectProgressReport;
        $report->project_id = $request->project_id;
        $report->milestone_id = $request->milestone_id;
        $report->user_id = user()->id;
        $report->report_date = $request->report_date;
        $report->title = $request->title;
        $report->description = trim_editor($request->description);
        $report->work_summary = trim_editor($request->work_summary);
        $report->weather_conditions = $request->weather_conditions;
        $report->save();

        if ($request->hasFile('photos')) {
            foreach ($request->file('photos') as $photo) {
                $file = new ProgressReportPhoto;
                $file->progress_report_id = $report->id;
                $file->filename = $photo->getClientOriginalName();
                $file->hashname = Files::uploadLocalOrS3($photo, 'progress-reports/'.$report->id);
                $file->size = $photo->getSize();
                $file->save();
            }
        }

        return Reply::successWithData(__('messages.recordSaved'), ['redirectUrl' => route('progress-reports.index')]);
    }

    public function show($id)
    {
        $this->report = ProjectProgressReport::with('project', 'milestone', 'photos', 'user')->findOrFail($id);
        $this->viewPermission = user()->permission('view_progress_report');
        abort_403(! ($this->viewPermission == 'all' || ($this->viewPermission == 'added' && $this->report->added_by == user()->id)));

        $this->pageTitle = $this->report->title;
        $this->view = 'progress-reports.ajax.show';

        if (request()->ajax()) {
            return $this->returnAjax($this->view);
        }

        return view('progress-reports.create', $this->data);
    }

    public function edit($id)
    {
        $this->report = ProjectProgressReport::findOrFail($id);
        $this->editPermission = user()->permission('edit_progress_report');
        abort_403(! ($this->editPermission == 'all' || ($this->editPermission == 'added' && $this->report->added_by == user()->id)));

        $this->pageTitle = __('app.menu.progressReports');
        $this->projects = Project::all();
        $this->milestones = ProjectMilestone::all();

        $this->view = 'progress-reports.ajax.edit';

        if (request()->ajax()) {
            return $this->returnAjax($this->view);
        }

        return view('progress-reports.create', $this->data);
    }

    public function update(UpdateProgressReportRequest $request, $id)
    {
        $report = ProjectProgressReport::findOrFail($id);
        $this->editPermission = user()->permission('edit_progress_report');
        abort_403(! ($this->editPermission == 'all' || ($this->editPermission == 'added' && $report->added_by == user()->id)));

        $report->project_id = $request->project_id;
        $report->milestone_id = $request->milestone_id;
        $report->report_date = $request->report_date;
        $report->title = $request->title;
        $report->description = trim_editor($request->description);
        $report->work_summary = trim_editor($request->work_summary);
        $report->weather_conditions = $request->weather_conditions;
        $report->save();

        return Reply::successWithData(__('messages.updateSuccess'), ['redirectUrl' => route('progress-reports.index')]);
    }

    public function destroy($id)
    {
        $report = ProjectProgressReport::findOrFail($id);
        $this->deletePermission = user()->permission('delete_progress_report');
        abort_403(! ($this->deletePermission == 'all' || ($this->deletePermission == 'added' && $report->added_by == user()->id)));

        ProjectProgressReport::destroy($id);

        return Reply::success(__('messages.deleteSuccess'));
    }

    public function approve($id)
    {
        abort_403(user()->permission('approve_progress_report') != 'all');

        $report = ProjectProgressReport::findOrFail($id);
        $report->status = 'approved';
        $report->save();

        return Reply::success(__('messages.updateSuccess'));
    }
}
