<div class="row">
    <div class="col-sm-12">
        <x-form id="save-material-data-form">
            <div class="add-client bg-white rounded">
                <h4 class="mb-0 p-20 f-21 font-weight-normal border-bottom-grey">
                    Add Material</h4>
                <div class="row p-20">
                    <div class="col-lg-4 col-md-6">
                        <x-forms.text fieldId="name" fieldLabel="Name" fieldName="name" fieldRequired="true"
                            :fieldPlaceholder="__('placeholders.name')">
                        </x-forms.text>
                    </div>

                    <div class="col-lg-4 col-md-6">
                        <x-forms.text fieldId="sku" fieldLabel="SKU" fieldName="sku"
                            :fieldPlaceholder="__('placeholders.sku')">
                        </x-forms.text>
                    </div>

                    <div class="col-lg-4 col-md-6">
                        <x-forms.text fieldId="unit" fieldLabel="Unit" fieldName="unit"
                            :fieldPlaceholder="__('placeholders.unit')">
                        </x-forms.text>
                    </div>

                    <div class="col-lg-4 col-md-6">
                        <x-forms.select fieldId="unit_type" fieldLabel="Unit Type" fieldName="unit_type"
                            search="true">
                            <option value="">--</option>
                            @foreach ($unitTypes as $unitType)
                                <option value="{{ $unitType->id }}">{{ $unitType->unit_type }}</option>
                            @endforeach
                        </x-forms.select>
                    </div>

                    <div class="col-lg-4 col-md-6">
                        <x-forms.select fieldId="category_id" fieldLabel="Category"
                            fieldName="category_id" search="true">
                            <option value="">--</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}">{{ $category->category_name }}</option>
                            @endforeach
                        </x-forms.select>
                    </div>

                    <div class="col-lg-4 col-md-6">
                        <x-forms.number class="mr-0 mr-lg-2 mr-md-2" fieldLabel="Unit Price"
                            fieldName="unit_price" fieldId="unit_price" :fieldPlaceholder="__('placeholders.price')"
                            fieldValue="0" />
                    </div>

                    <div class="col-lg-4 col-md-6">
                        <x-forms.number class="mr-0 mr-lg-2 mr-md-2" fieldLabel="Current Stock"
                            fieldName="current_stock" fieldId="current_stock" :fieldPlaceholder="__('placeholders.quantity')"
                            fieldValue="0" />
                    </div>

                    <div class="col-lg-4 col-md-6">
                        <x-forms.number class="mr-0 mr-lg-2 mr-md-2" fieldLabel="Min Stock"
                            fieldName="min_stock" fieldId="min_stock" :fieldPlaceholder="__('placeholders.quantity')"
                            fieldValue="0" />
                    </div>

                    <div class="col-lg-4 col-md-6">
                        <x-forms.select fieldId="project_id" fieldLabel="Project" fieldName="project_id"
                            search="true">
                            <option value="">--</option>
                            @foreach ($projects as $project)
                                <option value="{{ $project->id }}">{{ $project->project_name }}</option>
                            @endforeach
                        </x-forms.select>
                    </div>

                    <div class="col-md-12 mt-3">
                        <div class="form-group">
                            <x-forms.label class="my-3" fieldId="description-text"
                                fieldLabel="Description">
                            </x-forms.label>
                            <textarea name="description" id="description-text" rows="4"
                                class="form-control"></textarea>
                        </div>
                    </div>
                </div>

                <x-form-actions>
                    <x-forms.button-primary id="save-material-form" class="mr-3" icon="check">Save
                    </x-forms.button-primary>
                    <x-forms.button-cancel :link="route('materials.index')" class="border-0">Cancel
                    </x-forms.button-cancel>
                </x-form-actions>
            </div>
        </x-form>
    </div>
</div>

<script>
    $(document).ready(function() {

        $('#save-material-form').click(function() {
            const url = "{{ route('materials.store') }}";

            $.easyAjax({
                url: url,
                container: '#save-material-data-form',
                type: "POST",
                disableButton: true,
                blockUI: true,
                buttonSelector: "#save-material-form",
                data: $('#save-material-data-form').serialize(),
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
