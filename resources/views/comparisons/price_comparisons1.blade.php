@extends('layouts.app')
@section('content_body')
    <div class="content-container">
        <div class="container mt-5">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="text-center mb-0">Sell Min - Buy Max</h1>
            </div>
            <div class="table-container">
                <table id="DataTable" class="table table-bordered table-striped table-sm">
                    <thead>
                    <tr>
                        <th>Item ID</th>
                        <th>Enchant</th>
                        <th>Quality</th>
                        <th>Cheapest City</th>
                        <th>Min Sell Price</th>
                        <th>Sell Last Update</th>
                        <th>Expensive City</th>
                        <th>Max Buy Price</th>
                        <th>Buy Last Update</th>
                        <th>Profit</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($priceComparisons1 as $comparison)
                        <tr>
                            <td>
                                <a target="_blank" href="{{ url('/item/' . $comparison['item_id']) }}">
                                    {{ $comparison['item_name'] }}
                                </a>
                            </td>
                            <td> {{ $comparison['enchant'] }}</td>
                            <td>{{ $comparison['city1_quality'] }}</td>
                            <td>{{ $comparison['city2'] }}</td>
                            <td>{{ number_format($comparison['city2_sell_price_min'], 2) }} $</td>
                            <td>{{ now()->diffInMinutes($comparison['city2_sell_price_min_date']) }}</td>
                            <td>{{ $comparison['city1'] }}</td>
                            <td>{{ number_format($comparison['city1_buy_price_max'], 2) }} $</td>
                            <td>{{ now()->diffInMinutes($comparison['city1_buy_price_max_date']) }}</td>
                            <td>{{ number_format($comparison['profit'], 2) }} $</td>
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
                "order": [[ 9, "asc" ]], // Default sorting by Last Update column
                "columnDefs": [
                    { "orderable": false, "targets": [4] } // Disable sorting for ID and Description columns
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
