@props([
    'id' => 'datatable',
    'columns' => [],
    'ajax' => null,
    'datatableColumns' => [],
    'filters' => [],
    'order' => [],
    'pageLength' => 10
])

@php
    $datatableColumns = collect($datatableColumns)
        ->map(function ($column) {
            return array_merge([
                'defaultContent' => '',
            ], $column);
        })
        ->values()
        ->all();
@endphp

@if($filters)
    <div id="{{ $id }}-filters" class="datatable-filters d-none">

        @foreach($filters as $filter)

            @if($filter['type'] === 'select')
                <select
                    name="{{ $filter['name'] }}"
                    class="form-select form-select-sm datatable-filter"
                >
                    <option value="">{{ $filter['label'] }}</option>

                    @foreach($filter['options'] as $key => $value)
                        <option value="{{ $key }}">
                            {{ $value }}
                        </option>
                    @endforeach

                </select>
            @endif

            @if($filter['type'] === 'select2')
                <select
                    name="{{ $filter['name'] }}"
                    class="form-select form-select-sm datatable-filter datatable-select2"
                    data-placeholder="{{ $filter['label'] }}"
                    data-ajax="{{ $filter['ajax'] ?? '' }}"
                    style="width:180px"
                >
                    <option value=""></option>
                </select>
            @endif

            @if($filter['type'] === 'date')
                <input
                    type="date"
                    name="{{ $filter['name'] }}"
                    class="form-control form-control-sm datatable-filter"
                >
            @endif

        @endforeach

        <button
            type="button"
            class="btn btn-light btn-sm datatable-reset"
        >
            <i class="bi bi-x"></i>
        </button>

    </div>
@endif

<div class="table-responsive">

    <table
        id="{{ $id }}"
        class="table table-hover align-middle w-100 datatable"
    >
        <thead>
        <tr>

            @foreach($columns as $column)
                <th>{!! $column !!}</th>
            @endforeach

        </tr>
        </thead>

        <tbody>

        @unless($ajax)
            {{ $slot }}
        @endunless

        </tbody>

    </table>

</div>

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {

            const table = $('#{{ $id }}').DataTable({

                processing: true,

                @if($ajax)
                serverSide: true,

                ajax: {
                    url: '{{ $ajax }}',
                    data: function (d) {

                        $('#{{ $id }}-filters .datatable-filter').each(function () {

                            const value = $(this).val();

                            if (value !== '' && value !== null) {
                                d[$(this).attr('name')] = value;
                            }

                        });

                        $('.datatable-external-filter').each(function () {

                            const value = $(this).val();

                            if (value !== '' && value !== null) {
                                d[$(this).attr('name')] = value;
                            }

                        });

                    }
                },

                columns: @json($datatableColumns),
                @endif

                autoWidth: false,

                responsive: true,

                scrollX: true,
                scrollCollapse: true,

                pagingType: 'simple_numbers',

                pageLength: {{ $pageLength }},

                lengthChange: true,

                order: @json($order),

                dom:
                    "<'datatable-toolbar row align-items-center mb-3'" +
                    "<'col-md-6 d-flex align-items-center gap-2 flex-wrap'lB>" +
                    "<'col-md-6 d-flex justify-content-end align-items-center gap-2 flex-wrap'f>" +
                    ">" +
                    "<'row'<'col-12'tr>>" +
                    "<'datatable-footer row align-items-center mt-3'" +
                    "<'col-md-6'i>" +
                    "<'col-md-6 d-flex justify-content-end'p>" +
                    ">",

                buttons: [
                    {
                        extend: 'copy',
                        text: '<i class="bi bi-clipboard"></i>',
                        className: 'btn btn-light btn-md'
                    },
                    {
                        extend: 'excel',
                        text: '<i class="bi bi-file-earmark-excel"></i>',
                        className: 'btn btn-light btn-md'
                    },
                    {
                        extend: 'csv',
                        text: '<i class="bi bi-filetype-csv"></i>',
                        className: 'btn btn-light btn-md'
                    },
                    {
                        extend: 'print',
                        text: '<i class="bi bi-printer"></i>',
                        className: 'btn btn-light btn-md'
                    }
                ],

                language: {
                    search: '',
                    searchPlaceholder: 'Search logs...',
                    info: 'Showing _START_ to _END_ of _TOTAL_ logs',
                    paginate: {
                        previous: '‹',
                        next: '›'
                    }
                },

                initComplete: function () {

                    const api = this.api();

                    setTimeout(function () {

                        $('#{{ $id }}_wrapper').css({
                            width: '100%'
                        });

                        $('.dataTables_scroll').css({
                            width: '100%'
                        });

                        $('.dataTables_scrollHead').css({
                            width: '100%'
                        });

                        $('.dataTables_scrollBody').css({
                            width: '100%'
                        });

                        $('.dataTables_scrollFoot').css({
                            width: '100%'
                        });

                        api.columns.adjust();

                    }, 50);

                },

                drawCallback: function () {

                    $('#{{ $id }}_wrapper').css({
                        width: '100%'
                    });

                    $('.dataTables_scroll').css({
                        width: '100%'
                    });

                    $('.dataTables_scrollHead').css({
                        width: '100%'
                    });

                    $('.dataTables_scrollBody').css({
                        width: '100%'
                    });

                    $('.dataTables_scrollFoot').css({
                        width: '100%'
                    });

                    this.api().columns.adjust();

                }

            });

            function adjustTable() {

                $('#{{ $id }}_wrapper').css({
                    width: '100%'
                });

                $('.dataTables_scroll').css({
                    width: '100%'
                });

                $('.dataTables_scrollHead').css({
                    width: '100%'
                });

                $('.dataTables_scrollBody').css({
                    width: '100%'
                });

                $('.dataTables_scrollFoot').css({
                    width: '100%'
                });

                table.columns.adjust();

            }

            $(window).on('resize', function () {

                adjustTable();

            });

            if (window.ResizeObserver) {

                const observer = new ResizeObserver(function () {

                    adjustTable();

                });

                observer.observe(document.querySelector('#{{ $id }}').closest('.table-responsive'));

            }

            let filterContainer = $('#{{ $id }}-filters');

            if (filterContainer.length) {

                $('.datatable-toolbar .col-md-6:last')
                    .prepend(filterContainer.removeClass('d-none'));

            }

            $(document).on(
                'change select2:select',
                '#{{ $id }}-filters .datatable-filter,.datatable-external-filter',
                function () {

                    if (table.ajax) {

                        table.ajax.reload(null, false);

                    }

                }
            );

            $(document).on('click', '#{{ $id }}-filters .datatable-reset', function () {

                $('#{{ $id }}-filters')
                    .find('.datatable-filter')
                    .val('')
                    .trigger('change');

            });

        });

        document.addEventListener('DOMContentLoaded', function () {

            $(document).on('submit', '.delete-form', function (e) {

                e.preventDefault();

                const form = this;

                Swal.fire({

                    title: 'Delete Record?',
                    text: 'This action cannot be undone.',
                    icon: 'warning',

                    showCancelButton: true,

                    confirmButtonColor: '#dc3545',
                    cancelButtonColor: '#6c757d',

                    confirmButtonText: 'Yes, delete it',
                    cancelButtonText: 'Cancel'

                }).then((result) => {

                    if (result.isConfirmed) {

                        form.submit();

                    }

                });

            });

        });
    </script>
@endpush
