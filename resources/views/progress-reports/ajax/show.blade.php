<div class="row">
    <div class="col-sm-12">
        <div class="card border-0">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h4 class="mb-0 f-21 font-weight-normal">Report Details</h4>
                    <div>
                        @if (user()->permission('edit_progress_reports') == 'all' || user()->permission('edit_progress_reports') == 'added')
                            <x-forms.link-secondary :link="route('progress-reports.edit', $report->id)"
                                                    class="mr-2 openRightModal" icon="edit">
                                Edit
                            </x-forms.link-secondary>
                        @endif
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <table class="table table-bordered">
                            <tr>
                                <th class="bg-light-grey" width="40%">Project</th>
                                <td>{{ $report->project->project_name ?? '--' }}</td>
                            </tr>
                            <tr>
                                <th class="bg-light-grey">Milestones</th>
                                <td>{{ $report->milestone->milestone_title ?? '--' }}</td>
                            </tr>
                            <tr>
                                <th class="bg-light-grey">Report Date</th>
                                <td>{{ $report->report_date->format(company()->date_format) }}</td>
                            </tr>
                            <tr>
                                <th class="bg-light-grey">Title</th>
                                <td>{{ $report->title }}</td>
                            </tr>
                            <tr>
                                <th class="bg-light-grey">Weather Conditions</th>
                                <td>{{ $report->weather_conditions ?? '--' }}</td>
                            </tr>
                        </table>
                    </div>
                </div>

                @if ($report->description)
                    <div class="row mt-3">
                        <div class="col-sm-12">
                            <h5 class="mb-2">Description</h5>
                            <p class="f-14">{!! $report->description !!}</p>
                        </div>
                    </div>
                @endif

                @if ($report->work_summary)
                    <div class="row mt-3">
                        <div class="col-sm-12">
                            <h5 class="mb-2">Work Summary</h5>
                            <p class="f-14">{!! $report->work_summary !!}</p>
                        </div>
                    </div>
                @endif

                @if ($report->photos->count() > 0)
                    <div class="row mt-3">
                        <div class="col-sm-12">
                            <h5 class="mb-2">Add File</h5>
                            <div class="d-flex flex-wrap">
                                @foreach ($report->photos as $photo)
                                    <div class="card bg-light p-2 m-1" style="width: 150px;">
                                        <a href="{{ $photo->file_url }}" target="_blank">
                                            <img src="{{ $photo->file_url }}" class="img-thumbnail" height="100" alt="Photo">
                                        </a>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif

                <div class="mt-4">
                    <x-forms.button-cancel :link="route('progress-reports.index')" class="border-0">
                        Close
                    </x-forms.button-cancel>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    init(RIGHT_MODAL);
</script>