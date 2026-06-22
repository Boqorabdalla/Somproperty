@php
$editPermission = user()->permission('add_material');
$deletePermission = user()->permission('delete_material');
@endphp

<div id="material-detail-section">
    <div class="row">
        <div class="col-sm-12">
            <div class="card bg-white border-0 b-shadow-4">
                <div class="card-header bg-white border-bottom-grey justify-content-between p-20">
                    <div class="row">
                        <div class="col-lg-10 col-10">
                            <h3 class="heading-h1 mb-3">@lang('app.materialDetails')</h3>
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
                                                href="{{ route('materials.edit', $material->id) }}">@lang('app.edit')</a>
                                        @endif

                                        @if ($deletePermission == 'all' || $deletePermission == 'added')
                                            <a class="dropdown-item delete-material"
                                                data-material-id="{{ $material->id }}">@lang('app.delete')</a>
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
                            <x-cards.data-row label="Name" :value="$material->name ?? '--'" />
                            <x-cards.data-row label="SKU" :value="$material->sku ?? '--'" />
                            <x-cards.data-row label="Unit" :value="$material->unit ?? '--'" />
                            <x-cards.data-row label="Unit Type" :value="$material->unitType->unit_type ?? '--'" />
                            <x-cards.data-row label="Category" :value="$material->category->category_name ?? '--'" />
                            <x-cards.data-row label="Unit Price" :value="!is_null($material->unit_price) ? currency_format($material->unit_price, company()->currency_id) : '--'" />
                            <x-cards.data-row label="Current Stock" :value="!is_null($material->current_stock) ? $material->current_stock : '--'" />
                            <x-cards.data-row label="Min Stock" :value="!is_null($material->min_stock) ? $material->min_stock : '--'" />
                            <x-cards.data-row label="Project" :value="$material->project->project_name ?? '--'" />
                            <x-cards.data-row label="Description" :value="$material->description ?? '--'" html="true" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $('body').on('click', '.delete-material', function() {
        var id = $(this).data('material-id');
        Swal.fire({
            title: "@lang('messages.sweetAlertTitle')",
            text: "@lang('messages.recoverRecord')",
            icon: 'warning',
            showCancelButton: true,
            focusConfirm: false,
            confirmButtonText: "@lang('messages.confirmDelete')",
            cancelButtonText: "@lang('app.cancel')",
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
                var url = "{{ route('materials.destroy', ':id') }}";
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
