<div class="row">
    <div class="col-sm-12">
        <x-form id="save-equipment-data-form" method="PUT">
            <div class="add-client bg-white rounded">
                <h4 class="mb-0 p-20 f-21 font-weight-normal border-bottom-grey">
                    Edit Equipment</h4>
                <div class="row p-20">
                    <div class="col-lg-4 col-md-6">
                        <x-forms.text fieldId="name" :fieldLabel="'Name'" fieldName="name" fieldRequired="true"
                            :fieldPlaceholder="__('placeholders.name')" :fieldValue="$equipment->name">
                        </x-forms.text>
                    </div>

                    <div class="col-lg-4 col-md-6">
                        <x-forms.select fieldId="equipment_type_id" :fieldLabel="'Equipment Type'" fieldName="equipment_type_id"
                            search="true">
                            <option value="">--</option>
                            @foreach ($equipmentTypes as $equipmentType)
                                <option value="{{ $equipmentType->id }}" @if ($equipmentType->id == $equipment->equipment_type_id) selected @endif>{{ $equipmentType->type_name }}</option>
                            @endforeach
                        </x-forms.select>
                    </div>

                    <div class="col-lg-4 col-md-6">
                        <x-forms.text fieldId="model" :fieldLabel="'Model'" fieldName="model"
                            :fieldPlaceholder="__('placeholders.model')" :fieldValue="$equipment->model">
                        </x-forms.text>
                    </div>

                    <div class="col-lg-4 col-md-6">
                        <x-forms.text fieldId="serial_no" :fieldLabel="'Serial No'" fieldName="serial_no"
                            :fieldPlaceholder="__('placeholders.serialNo')" :fieldValue="$equipment->serial_no">
                        </x-forms.text>
                    </div>

                    <div class="col-lg-4 col-md-6">
                        <x-forms.datepicker fieldId="purchase_date" fieldRequired="false"
                            :fieldLabel="'Purchase Date'" fieldName="purchase_date"
                            :fieldPlaceholder="__('placeholders.date')"
                            :fieldValue="$equipment->purchase_date ? $equipment->purchase_date->format(company()->date_format) : ''" />
                    </div>

                    <div class="col-lg-4 col-md-6">
                        <x-forms.number class="mr-0 mr-lg-2 mr-md-2" :fieldLabel="'Purchase Price'"
                            fieldName="purchase_price" fieldId="purchase_price" :fieldPlaceholder="__('placeholders.price')"
                            :fieldValue="$equipment->purchase_price" />
                    </div>

                    <div class="col-lg-4 col-md-6">
                        <x-forms.select fieldId="status" :fieldLabel="'Status'" fieldName="status"
                            search="true">
                            <option value="available" @if ($equipment->status == 'available') selected @endif>Available</option>
                            <option value="in-use" @if ($equipment->status == 'in-use') selected @endif>In Use</option>
                            <option value="under-maintenance" @if ($equipment->status == 'under-maintenance') selected @endif>Under Maintenance</option>
                            <option value="retired" @if ($equipment->status == 'retired') selected @endif>Retired</option>
                        </x-forms.select>
                    </div>

                    <div class="col-lg-4 col-md-6">
                        <x-forms.text fieldId="location" :fieldLabel="'Location'" fieldName="location"
                            :fieldPlaceholder="__('placeholders.location')" :fieldValue="$equipment->location">
                        </x-forms.text>
                    </div>

                    <div class="col-lg-4 col-md-6">
                        <x-forms.datepicker fieldId="next_maintenance_date" fieldRequired="false"
                            :fieldLabel="'Next Maintenance Date'" fieldName="next_maintenance_date"
                            :fieldPlaceholder="__('placeholders.date')"
                            :fieldValue="$equipment->next_maintenance_date ? $equipment->next_maintenance_date->format(company()->date_format) : ''" />
                    </div>

                    <div class="col-lg-4 col-md-6">
                        <x-forms.select fieldId="project_id" :fieldLabel="'Project'" fieldName="project_id"
                            search="true">
                            <option value="">--</option>
                            @foreach ($projects as $project)
                                <option value="{{ $project->id }}" @if ($project->id == $equipment->project_id) selected @endif>{{ $project->project_name }}</option>
                            @endforeach
                        </x-forms.select>
                    </div>

                    <div class="col-md-12 mt-3">
                        <div class="form-group">
                            <x-forms.label class="my-3" fieldId="notes-text"
                                :fieldLabel="'Notes'">
                            </x-forms.label>
                            <textarea name="notes" id="notes-text" rows="4"
                                class="form-control">{{ $equipment->notes }}</textarea>
                        </div>
                    </div>
                </div>

                <x-form-actions>
                    <x-forms.button-primary id="save-equipment-form" class="mr-3" icon="check">Save
                    </x-forms.button-primary>
                    <x-forms.button-cancel :link="route('equipment.index')" class="border-0">Cancel
                    </x-forms.button-cancel>
                </x-form-actions>
            </div>
        </x-form>
    </div>
</div>

<script>
    $(document).ready(function() {

        $('#save-equipment-form').click(function() {
            const url = "{{ route('equipment.update', $equipment->id) }}";

            $.easyAjax({
                url: url,
                container: '#save-equipment-data-form',
                type: "POST",
                disableButton: true,
                blockUI: true,
                buttonSelector: "#save-equipment-form",
                data: $('#save-equipment-data-form').serialize(),
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