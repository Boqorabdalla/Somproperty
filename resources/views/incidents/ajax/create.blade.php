<div class="row">
    <div class="col-sm-12">
        <x-form id="save-incident-form">
            <div class="bg-white rounded add-client">
                <h4 class="p-20 mb-0 f-21 font-weight-normal border-bottom-grey">
                    Incident Details</h4>
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
                        <x-forms.label class="my-3" fieldId="category_id" :fieldLabel="'Category'">
                        </x-forms.label>
                        <x-forms.input-group>
                            <select class="form-control select-picker" name="category_id" id="category_id"
                                    data-live-search="true">
                                <option value="">--</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->category_name }}</option>
                                @endforeach
                            </select>
                        </x-forms.input-group>
                    </div>

                    <div class="col-md-4">
                        <x-forms.text class="mr-0 mr-lg-2 mr-md-2"
                                      :fieldLabel="'Title'" fieldName="title" fieldRequired="true"
                                      fieldId="title" :fieldPlaceholder="__('placeholders.title')"/>
                    </div>

                    <div class="col-md-4">
                        <x-forms.datepicker fieldId="incident_date" fieldRequired="true"
                                            :fieldLabel="'Incident Date'" fieldName="incident_date"
                                            :fieldPlaceholder="__('placeholders.date')"
                                            :fieldValue="now(company()->timezone)->format(company()->date_format)"/>
                    </div>

                    <div class="col-md-4">
                        <x-forms.label class="my-3" fieldId="severity" :fieldLabel="'Severity'">
                        </x-forms.label>
                        <x-forms.input-group>
                            <select class="form-control select-picker" name="severity" id="severity">
                                <option value="minor">Minor</option>
                                <option value="major">Major</option>
                                <option value="critical">Critical</option>
                            </select>
                        </x-forms.input-group>
                    </div>

                    <div class="col-md-12">
                        <x-forms.label class="my-3" fieldId="description" :fieldLabel="'Description'" fieldRequired="true">
                        </x-forms.label>
                        <div id="description"></div>
                        <textarea name="description" id="description-text" class="d-none"></textarea>
                    </div>

                    <div class="col-md-12">
                        <x-forms.label class="my-3" fieldId="root_cause" :fieldLabel="'Root Cause'">
                        </x-forms.label>
                        <div id="root_cause"></div>
                        <textarea name="root_cause" id="root_cause-text" class="d-none"></textarea>
                    </div>

                    <div class="col-md-12">
                        <x-forms.label class="my-3" fieldId="corrective_action" :fieldLabel="'Corrective Action'">
                        </x-forms.label>
                        <div id="corrective_action"></div>
                        <textarea name="corrective_action" id="corrective_action-text" class="d-none"></textarea>
                    </div>

                    <div class="col-md-6">
                        <x-forms.file-multiple class="mr-0 mr-lg-2 mr-md-2"
                                               :fieldLabel="'Add File'" fieldName="files"
                                               fieldId="files-upload-dropzone"/>
                    </div>

                </div>

                <x-form-actions>
                    <x-forms.button-primary id="save-incident-form-btn" class="mr-3" icon="check">
                        Save
                    </x-forms.button-primary>
                    <x-forms.button-cancel :link="route('incidents.index')" class="border-0">
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
        quillImageLoad('#root_cause');
        quillImageLoad('#corrective_action');

        $('#save-incident-form-btn').click(function() {
            var description = document.getElementById('description').children[0].innerHTML;
            document.getElementById('description-text').value = description;

            var rootCause = document.getElementById('root_cause').children[0].innerHTML;
            document.getElementById('root_cause-text').value = rootCause;

            var correctiveAction = document.getElementById('corrective_action').children[0].innerHTML;
            document.getElementById('corrective_action-text').value = correctiveAction;

            const url = "{{ route('incidents.store') }}";

            $.easyAjax({
                url: url,
                container: '#save-incident-form',
                type: "POST",
                disableButton: true,
                blockUI: true,
                file: true,
                buttonSelector: "#save-incident-form-btn",
                data: $('#save-incident-form').serialize(),
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