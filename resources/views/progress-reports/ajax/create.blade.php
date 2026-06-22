<div class="row">
    <div class="col-sm-12">
        <x-form id="save-progress-report-form">
            <div class="bg-white rounded add-client">
                <h4 class="p-20 mb-0 f-21 font-weight-normal border-bottom-grey">
                    Report Details</h4>
                <div class="p-20 row">

                    <div class="col-md-4">
                        <x-forms.label class="my-3" fieldId="project_id" :fieldLabel="'Project'" fieldRequired="true">
                        </x-forms.label>
                        <x-forms.input-group>
                            <select class="form-control select-picker" name="project_id" id="project_id"
                                    data-live-search="true">
                                <option value="">--</option>
                                @foreach ($projects as $project)
                                    <option value="{{ $project->id }}">{{ $project->project_name }}</option>
                                @endforeach
                            </select>
                        </x-forms.input-group>
                    </div>

                    <div class="col-md-4">
                        <x-forms.label class="my-3" fieldId="milestone_id" :fieldLabel="'Milestones'">
                        </x-forms.label>
                        <x-forms.input-group>
                            <select class="form-control select-picker" name="milestone_id" id="milestone_id"
                                    data-live-search="true">
                                <option value="">--</option>
                                @foreach ($milestones as $milestone)
                                    <option value="{{ $milestone->id }}">{{ $milestone->milestone_title }}</option>
                                @endforeach
                            </select>
                        </x-forms.input-group>
                    </div>

                    <div class="col-md-4">
                        <x-forms.datepicker fieldId="report_date" fieldRequired="true"
                                            :fieldLabel="'Report Date'" fieldName="report_date"
                                            :fieldPlaceholder="__('placeholders.date')"
                                            :fieldValue="now(company()->timezone)->format(company()->date_format)"/>
                    </div>

                    <div class="col-md-6">
                        <x-forms.text class="mr-0 mr-lg-2 mr-md-2"
                                      :fieldLabel="'Title'" fieldName="title" fieldRequired="true"
                                      fieldId="title" :fieldPlaceholder="__('placeholders.title')"/>
                    </div>

                    <div class="col-md-12">
                        <x-forms.label class="my-3" fieldId="description" :fieldLabel="'Description'">
                        </x-forms.label>
                        <div id="description"></div>
                        <textarea name="description" id="description-text" class="d-none"></textarea>
                    </div>

                    <div class="col-md-12">
                        <x-forms.label class="my-3" fieldId="work_summary" :fieldLabel="'Work Summary'">
                        </x-forms.label>
                        <div id="work_summary"></div>
                        <textarea name="work_summary" id="work_summary-text" class="d-none"></textarea>
                    </div>

                    <div class="col-md-6">
                        <x-forms.text class="mr-0 mr-lg-2 mr-md-2"
                                      :fieldLabel="'Weather Conditions'"
                                      fieldName="weather_conditions" fieldId="weather_conditions"/>
                    </div>

                    <div class="col-md-6">
                        <x-forms.file-multiple class="mr-0 mr-lg-2 mr-md-2"
                                               :fieldLabel="'Add File'" fieldName="photos"
                                               fieldId="photos-upload-dropzone"/>
                    </div>

                </div>

                <x-form-actions>
                    <x-forms.button-primary id="save-progress-report-form-btn" class="mr-3" icon="check">
                        Save
                    </x-forms.button-primary>
                    <x-forms.button-cancel :link="route('progress-reports.index')" class="border-0">
                        Cancel
                    </x-forms.button-cancel>
                </x-form-actions>

            </div>
        </x-form>
    </div>
</div>

<script>
    $(document).ready(function() {

        quillImageLoad('#description');
        quillImageLoad('#work_summary');

        $('#project_id').change(function() {
            var projectId = $(this).val();
            var url = "{{ route('projects.milestones', ':id') }}";
            url = url.replace(':id', projectId);
            $.easyAjax({
                url: url,
                type: "GET",
                container: '#save-progress-report-form',
                blockUI: true,
                success: function(response) {
                    if (response.status == 'success') {
                        $('#milestone_id').html(response.data);
                        $('#milestone_id').selectpicker('refresh');
                    }
                }
            });
        });

        $('#save-progress-report-form-btn').click(function() {
            var description = document.getElementById('description').children[0].innerHTML;
            document.getElementById('description-text').value = description;

            var workSummary = document.getElementById('work_summary').children[0].innerHTML;
            document.getElementById('work_summary-text').value = workSummary;

            const url = "{{ route('progress-reports.store') }}";

            $.easyAjax({
                url: url,
                container: '#save-progress-report-form',
                type: "POST",
                disableButton: true,
                blockUI: true,
                file: true,
                buttonSelector: "#save-progress-report-form-btn",
                data: $('#save-progress-report-form').serialize(),
                success: function(response) {
                    if (response.status == 'success') {
                        window.location.href = response.redirectUrl;
                    }
                }
            });
        });

        init(RIGHT_MODAL);
    });
</script>