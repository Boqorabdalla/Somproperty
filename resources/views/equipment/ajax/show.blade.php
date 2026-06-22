@php
$editPermission = user()->permission('add_equipment');
$deletePermission = user()->permission('delete_equipment');
@endphp

<div id="equipment-detail-section">
    <div class="row">
        <div class="col-sm-12">
            <div class="card bg-white border-0 b-shadow-4">
                <div class="card-header bg-white border-bottom-grey justify-content-between p-20">
                    <div class="row">
                        <div class="col-lg-10 col-10">
                            <h3 class="heading-h1 mb-3">Equipment Details</h3>
                        </div>
                        <div class="col-lg-2 col-2 text-right">
                            @if ($editPermission == 'all' || $editPermission == 'added' || $deletePermission == 'all' || $deletePermission == 'added')
                                <div class="dropdown">
                                    <button
                                        class="btn btn-lg f-14 px-2 py-1 text-dark-grey rounded dropdown-toggle"
                                        type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <i class="fa fa-ellipsis-h"></i>
                                    </button>

                                    <div class="dropdown-menu dropdown-menu-right border-grey rounded b-shadow-4 p-0"
                                        aria-labelledby="dropdownMenuLink" tabindex="0">
                                        @if ($editPermission == 'all' || $editPermission == 'added')
                                            <a class="dropdown-item openRightModal"
                                                href="{{ route('equipment.edit', $equipment->id) }}">Edit</a>
                                        @endif

                                        @if ($deletePermission == 'all' || $deletePermission == 'added')
                                            <a class="dropdown-item delete-equipment"
                                                data-equipment-id="{{ $equipment->id }}">Delete</a>
                                        @endif
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-12">
                            <x-cards.data-row :label="'Name'" :value="$equipment->name ?? '--'" />
                            <x-cards.data-row :label="'Equipment Type'" :value="$equipment->type->type_name ?? '--'" />
                            <x-cards.data-row :label="'Model'" :value="$equipment->model ?? '--'" />
                            <x-cards.data-row :label="'Serial No'" :value="$equipment->serial_no ?? '--'" />
                            <x-cards.data-row :label="'Purchase Date'" :value="$equipment->purchase_date ? $equipment->purchase_date->format(company()->date_format) : '--'" />
                            <x-cards.data-row :label="'Purchase Price'" :value="!is_null($equipment->purchase_price) ? currency_format($equipment->purchase_price, company()->currency_id) : '--'" />
                            <x-cards.data-row :label="'Status'" :value="ucfirst($equipment->status) ?? '--'" />
                            <x-cards.data-row :label="'Location'" :value="$equipment->location ?? '--'" />
                            <x-cards.data-row :label="'Next Maintenance Date'" :value="$equipment->next_maintenance_date ? $equipment->next_maintenance_date->format(company()->date_format) : '--'" />
                            <x-cards.data-row :label="'Project'" :value="$equipment->project->project_name ?? '--'" />
                            <x-cards.data-row :label="'Notes'" :value="$equipment->notes ?? '--'" html="true" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $('body').on('click', '.delete-equipment', function() {
        var id = $(this).data('equipment-id');
        Swal.fire({
            title: "@lang('messages.sweetAlertTitle')",
            text: "@lang('messages.recoverRecord')",
            icon: 'warning',
            showCancelButton: true,
            focusConfirm: false,
            confirmButtonText: "@lang('messages.confirmDelete')",
            cancelButtonText: "Cancel",
            customClass: {
                confirmButton: 'btn btn-primary mr-3',
                cancelButton: 'btn btn-secondary'
            },
            showClass: {
                popup: 'swal2-noanimation',
                backdrop: 'swal2-noanimation'
            },
            buttonsStyling: false
        }).then((result) => {
            if (result.isConfirmed) {
                var url = "{{ route('equipment.destroy', ':id') }}";
                url = url.replace(':id', id);

                var token = "{{ csrf_token() }}";

                $.easyAjax({
                    type: 'POST',
                    url: url,
                    data: {
                        '_token': token,
                        '_method': 'DELETE'
                    },
                    success: function(response) {
                        if (response.status === "success") {
                            window.location.href = response.redirectUrl;
                        }
                    }
                });
            }
        });
    });
</script>