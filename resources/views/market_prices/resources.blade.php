@extends('layouts.app')
@section('content_body')
    <div class="content-container">
        <div class="container mt-5">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="text-center mb-0">Upgrade Resources</h1>
            </div>
            <div class="table-container">
                <table id="PriceTable" class="table table-bordered table-striped table-sm">
                    <thead>
                    <tr>
                        <th>Item Name</th>
                        <th>Enchantment Level</th>
                        <th>Upgrade Resource Name</th>
                        <th>Upgrade Resource Count</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach ($listResources as $itemName => $upgradeResources)
                        @foreach ($upgradeResources as $resource)
                            <tr>
                                <td>{{ $itemName }}</td>
                                <td>{{ $resource->enchantmentlevel }}</td>
                                <td>{{ $resource->upgraderesource_name }}</td>
                                <td>{{ $resource->upgraderesource_count }}</td>
                            </tr>
                        @endforeach
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

@endsection

@push('css')
    <link href="https://cdn.datatables.net/1.10.19/css/jquery.dataTables.min.css" rel="stylesheet">
    <link href="{{ asset('css/item_details.css') }}" rel="stylesheet">
    <style>
        .table-container {
            display: flex;
            justify-content: center;
        }
    </style>
@endpush

@push('js')
    <script src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
    <script>
        $(document).ready(function () {
            var table = $('#PriceTable').DataTable({
                "order": [[1, "asc"]], // Default sorting by Enchantment Level column
                "pageLength": 25 // Default page length
            });

            // Add search inputs for each column
            $('#PriceTable thead tr').clone(true).appendTo('#PriceTable thead');
            $('#PriceTable thead tr:eq(1) th').each(function (i) {
                var title = $(this).text();
                $(this).html('<input type="text" placeholder="Search ' + title + '" />');

                $('input', this).on('keyup change', function () {
                    if (table.column(i).search() !== this.value) {
                        table
                            .column(i)
                            .search(this.value)
                            .draw();
                    }
                });
            });

            // Prevent sorting on search input click
            $('#PriceTable thead input').on('click', function(e){
                e.stopPropagation();
            });
        });
    </script>
@endpush
Açıklamalar
