<!DOCTYPE html>
<html>
<head>
    <title>Black Market Prices</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.10.19/css/jquery.dataTables.min.css" rel="stylesheet">
    <style>
        .table-container {
            display: flex;
            justify-content: center;
        }
    </style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <a class="navbar-brand" href="{{ url('/') }}">Home</a>
    <div class="collapse navbar-collapse">
        <ul class="navbar-nav mr-auto">
            <li class="nav-item">
                <a class="nav-link" href="{{ route('prices') }}">Prices</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="{{ route('opportunities') }}">Black Market</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="{{ route('price-comparisons') }}">Price Comparisons</a>
            </li>
        </ul>
    </div>
</nav>
<div class="content-container">
    <div class="container mt-5">
        <h1 class="text-center mb-4">Black Market Prices</h1>
        <div class="table-container">
            <table id="priceTable" class="table table-bordered table-striped table-sm">
                <thead>
                <tr>
                    <th>ID</th>
                    <th>Item ID</th>
                    <th>City</th>
                    <th>Quality</th>
                    <th>Quantity</th>
                    <th>Sell Price Min</th>
                    <th>Sell Price Max</th>
                    <th>Buy Price Min</th>
                    <th>Buy Price Max</th>
                    <th>Description</th>
                    <th>Last Update (minutes ago)</th>
                </tr>
                </thead>
                <tbody>
                @foreach($marketPrices as $marketPrice)
                    <tr>
                        <td>{{ $marketPrice->id }}</td>
                        <td>{{ $marketPrice->item_id }}</td>
                        <td>{{ $marketPrice->city->name }}</td>
                        <td>{{ $marketPrice->quality->name }}</td>
                        <td>{{ $marketPrice->quantity }}</td>
                        <td>{{ $marketPrice->sell_price_min }}</td>
                        <td>{{ $marketPrice->sell_price_max }}</td>
                        <td>{{ $marketPrice->buy_price_min }}</td>
                        <td>{{ $marketPrice->buy_price_max }}</td>
                        <td>{{ $marketPrice->description }}</td>
                        <td>{{ now()->diffInMinutes($marketPrice->buy_price_min_date) }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
<script src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>
<script>
    $(document).ready(function () {
        var table = $('#priceTable').DataTable({
            "order": [[ 10, "asc" ]], // Default sorting by Last Update column
            "columnDefs": [
                { "orderable": false, "targets": [0,1,2,5, 10] } // Disable sorting for ID and Description columns
            ],
            "pageLength": 100 // Default page length
        });

        // Add search inputs for each column
        $('#priceTable thead tr').clone(true).appendTo('#priceTable thead');
        $('#priceTable thead tr:eq(1) th').each(function (i) {
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
        $('#priceTable thead input').on('click', function(e){
            e.stopPropagation();
        });
    });
</script>
</body>
</html>
