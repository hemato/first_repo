@extends('layouts.app')
@section('content')
<div class="content-container">
    <div class="container mt-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="text-center mb-0">Market Prices</h1>
            <div>
                <button class="btn btn-primary mr-2" onclick="showAddItemForm()">Add Item</button>
                <button class="btn btn-primary" onclick="showFetchFromApiForm()">Fetch Items from API</button>
            </div>
        </div>
        <div class="table-container">
            <table id="PriceTable" class="table table-bordered table-striped table-sm">
                <thead>
                <tr>
                    <th>Item Name</th>
                    <th>Enchant</th>
                    <th>Quality</th>
                    <th>City</th>
                    <th>Sell Price Min</th>
                    <th>Sell Price Max</th>
                    <th>Sell Update (minutes ago)</th>
                    <th>Buy Price Min</th>
                    <th>Buy Price Max</th>
                    <th>Buy Update (minutes ago)</th>
                </tr>
                </thead>
                <tbody>
                @foreach($marketPrices as $marketPrice)
                    <tr>
                        <td>
                            <a target="_blank" href="{{ url('/item/' . $marketPrice->item_id) }}">
                                {{ $marketPrice->itemName }}
                            </a>
                        </td>
                        <td>                            @php
                                // Determine the suffix value based on item_id
                                $suffix = 0; // Default value
                                if (strpos($marketPrice->item_id, '@') !== false) {
                                    $suffix = (int) substr($marketPrice->item_id, -1);
                                }
                            @endphp
                            {{ $suffix }}</td>
                        <td>{{ $marketPrice->quality->name }}</td>
                        <td>{{ $marketPrice->city->name }}</td>
                        <td>{{ $marketPrice->sell_price_min }}</td>
                        <td>{{ $marketPrice->sell_price_max }}</td>
                        <td>{{ now()->diffInMinutes($marketPrice->sell_price_max_date) }}</td>
                        <td>{{ $marketPrice->buy_price_min }}</td>
                        <td>{{ $marketPrice->buy_price_max }}</td>
                        <td>{{ now()->diffInMinutes($marketPrice->buy_price_max_date) }}</td>
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
        var table = $('#PriceTable').DataTable({
            "order": [[ 8, "asc" ]], // Default sorting by Last Update column
            "columnDefs": [
                { "orderable": false, "targets": [4] } // Disable sorting for ID and Description columns
            ],
            "pageLength": 100 // Default page length
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
