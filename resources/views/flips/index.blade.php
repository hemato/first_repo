@extends('layouts.app')
@section('content_body')
    <div class="content-container">
        <div class="container mt-5">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="text-center mb-0">Flips by HematoTR</h1>
            </div>
            <div class="table-container">
                <table id="DataTable" class="table table-bordered table-striped table-sm">
                    <thead>
                    <tr>
                        <th>ID</th>
                        <th>You need to sell this</th>
                        <th>Buy Order Location</th>
                        <th>Buy Order Quality</th>
                        <th>Buy Order Enchant</th>
                        <th>Buy Order Price</th>
                        <th>Buy Consumed</th>
                        <th>You need to buy this</th>
                        <th>Sell Order Location</th>
                        <th>Sell Order Quality</th>
                        <th>Sell Order Enchant</th>
                        <th>Sell Order Price</th>
                        <th>Sell Consumed</th>
                        <th>Profit</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($flipsData as $flip)
                        <tr>
                            <td>{{ $flip->api_id }}</td>
                            <td>{{ $flip->buy_order_item_type_id }}</td>
                            <td>{{ $flip->buy_order_location }}</td>
                            <td>{{ $flip->buy_order_quality_level }}</td>
                            <td>{{ $flip->buy_order_enchantment_level }}</td>
                            <td>{{ number_format($flip->buy_order_unit_price_silver, 2) }} $</td>
                            <td>{{ $flip->buy_order_is_consumed }}</td>
                            <td>{{ $flip->sell_order_item_type_id }}</td>
                            <td>{{ $flip->sell_order_location }}</td>
                            <td>{{ $flip->sell_order_quality_level }}</td>
                            <td>{{ $flip->sell_order_enchantment_level }}</td>
                            <td>{{ number_format($flip->sell_order_unit_price_silver, 2) }} $</td>
                            <td>{{ $flip->sell_order_is_consumed }}</td>
                            <td>{{ number_format($flip->profit, 2) }} $</td>
                        </tr>
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
            var table = $('#DataTable').DataTable({
                "order": [[ 13, "asc" ]], // Default sorting by Last Update column
                "columnDefs": [
                    { "orderable": false, "targets": [0] } // Disable sorting for ID and Description columns
                ],
                "pageLength": 100 // Default page length
            });

            // Add search inputs for each column
            $('#DataTable thead tr').clone(true).appendTo('#DataTable thead');
            $('#DataTable thead tr:eq(1) th').each(function (i) {
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
            $('#DataTable thead input').on('click', function(e){
                e.stopPropagation();
            });
        });

    </script>
@endpush
