@extends('layouts.app')
@section('content')
<div class="content-container">
    <div class="container mt-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="text-center mb-0">Price Comparisons</h1>
            <div>
                <button class="btn btn-primary mr-2" onclick="showAddItemForm()">Add Item</button>
                <button class="btn btn-primary" onclick="showFetchFromApiForm()">Fetch Items from API</button>
            </div>
        </div>
        <div class="table-container">
            <table id="DataTable" class="table table-bordered table-striped table-sm">
                <thead>
                <tr>
                    <th>Item ID</th>
                    <th>Quality</th>
                    <th>Cheapest City</th>
                    <th>Max Buy Price</th>
                    <th>Buy Last Update</th>
                    <th>Most Expensive City</th>
                    <th>Max Sell Price</th>
                    <th>Profit</th>
                    <th>Sell Last Update</th>
                </tr>
                </thead>
                <tbody>
                @foreach($priceComparisons as $comparison)
                    <tr>
                        <td>{{ $comparison['item_id'] }}</td>
                        <td>{{ $comparison['cheapest_quality'] }}</td>
                        <td>{{ $comparison['cheapest_city'] }}</td>
                        <td>{{ number_format($comparison['max_buy_price'], 2) }} $</td>
                        <td>{{ now()->diffInMinutes($comparison['buy_price_max_date']) }}</td>
                        <td>{{ $comparison['expensive_city'] }}</td>
                        <td>{{ number_format($comparison['max_sell_price'], 2) }} $</td>
                        <td>{{ number_format($comparison['max_sell_price'] - $comparison['max_buy_price'], 2) }} $</td>
                        <td>{{ now()->diffInMinutes($comparison['sell_price_max_date']) }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<div id="addItemModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="addItemModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addItemModalLabel">Add Item</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick="closeAddItemForm()">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="/prices" method="POST">
                    @csrf
                    <!-- Form fields for adding items -->
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal for Fetch Items from API -->
<div id="fetchFromApiModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="fetchFromApiModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="fetchFromApiModalLabel">Fetch Items from API</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" onclick="closeFetchFromApiForm()">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form action="/fetch-from-api" method="POST">
                    @csrf
                    <div class="form-group">
                        <label for="api_url">API URL</label>
                        <input type="text" class="form-control" id="api_url" name="api_url" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Fetch Items</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
@section('scripts')
<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
<script src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
<script>
    $(document).ready(function () {
        var table = $('#DataTable').DataTable({
            "order": [[ 7, "asc" ]], // Default sorting by Last Update column
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

    function showAddItemForm() {
        $('#addItemModal').modal('show');
    }

    function closeAddItemForm() {
        $('#addItemModal').modal('hide');
    }

    function showFetchFromApiForm() {
        $('#fetchFromApiModal').modal('show');
    }

    function closeFetchFromApiForm() {
        $('#fetchFromApiModal').modal('hide');
    }
</script>
@endsection
