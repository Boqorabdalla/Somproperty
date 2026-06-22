<div class="row">
    <div class="col-sm-12">
        <div class="card border-0">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h4 class="mb-0 f-21 font-weight-normal">Incident Details</h4>
                    <div>
                        @if (user()->permission('edit_incidents') == 'all' || user()->permission('edit_incidents') == 'added')
                            <x-forms.link-secondary :link="route('incidents.edit', $incident->id)"
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
                                <td>{{ $incident->project->project_name ?? '--' }}</td>
                            </tr>
                            <tr>
                                <th class="bg-light-grey">Category</th>
                                <td>{{ $incident->category->category_name ?? '--' }}</td>
                            </tr>
                            <tr>
                                <th class="bg-light-grey">Title</th>
                                <td>{{ $incident->title }}</td>
                            </tr>
                            <tr>
                                <th class="bg-light-grey">Incident Date</th>
                                <td>{{ $incident->incident_date->format(company()->date_format) }}</td>
                            </tr>
                            <tr>
                                <th class="bg-light-grey">Severity</th>
                                <td>
                                    @if ($incident->severity == 'critical')
                                        <span class="badge badge-danger">Critical</span>
                                    @elseif ($incident->severity == 'major')
                                        <span class="badge badge-warning">Major</span>
                                    @else
                                        <span class="badge badge-info">Minor</span>
                                    @endif
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>

                @if ($incident->description)
                    <div class="row mt-3">
                        <div class="col-sm-12">
                            <h5 class="mb-2">Description</h5>
                            <p class="f-14">{!! $incident->description !!}</p>
                        </div>
                    </div>
                @endif

                @if ($incident->root_cause)
                    <div class="row mt-3">
                        <div class="col-sm-12">
                            <h5 class="mb-2">Root Cause</h5>
                            <p class="f-14">{!! $incident->root_cause !!}</p>
                        </div>
                    </div>
                @endif

                @if ($incident->corrective_action)
                    <div class="row mt-3">
                        <div class="col-sm-12">
                            <h5 class="mb-2">Corrective Action</h5>
                            <p class="f-14">{!! $incident->corrective_action !!}</p>
                        </div>
                    </div>
                @endif

                @if ($incident->files->count() > 0)
                    <div class="row mt-3">
                        <div class="col-sm-12">
                            <h5 class="mb-2">Add File</h5>
                            <div class="d-flex flex-wrap">
                                @foreach ($incident->files as $file)
                                    <div class="card bg-light p-2 m-1" style="width: 150px;">
                                        <a href="{{ $file->file_url }}" target="_blank">
                                            <i class="fa fa-file fa-3x text-primary"></i>
                                            <p class="mt-2 mb-0 text-center f-12">{{ $file->filename ?? '--' }}</p>
                                        </a>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endif

                <div class="mt-4">
                    <x-forms.button-cancel :link="route('incidents.index')" class="border-0">
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