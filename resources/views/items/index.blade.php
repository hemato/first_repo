<!DOCTYPE html>
<html>
<head>
    <title>Items</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.10.19/css/jquery.dataTables.min.css" rel="stylesheet">
    <style>
        .table-container {
            display: flex;
            justify-content: center;
        }
        .menu-container {
            position: fixed;
            top: 0;
            left: 0;
            height: 100%;
            width: 250px;
            background-color: #f8f9fa;
            padding-top: 20px;
        }
        .menu-item {
            cursor: pointer;
            padding: 10px 20px;
            border-bottom: 1px solid #ccc;
        }
        .menu-item:hover {
            background-color: #e9ecef;
        }
        .content-container {
            margin-left: 250px; /* Adjust based on menu width */
            padding: 20px;
        }
    </style>
</head>
<body>
<div class="menu-container">
    <div class="menu-item" onclick="openItemIndex()">
        Item Index
    </div>
    <!-- Add more menu items as needed -->
</div>

<div class="content-container">
    <div class="container mt-5">
        <h1 class="text-center mb-4">Items</h1>
        <div class="table-container">
            <table id="itemTable" class="table table-bordered table-striped table-sm">
                <thead>
                <tr>
                    <th>ID</th>
                    <th>Item ID</th>
                    <th>Item Name</th>
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
                @foreach($items as $item)
                    <tr>
                        <td>{{ $item->id }}</td>
                        <td>{{ $item->item_id }}</td>
                        <td>{{ $item->item_name }}</td>
                        <td>{{ $item->city->name }}</td>
                        <td>{{ $item->quality }}</td>
                        <td>{{ $item->quantity }}</td>
                        <td>{{ $item->sell_price_min }}</td>
                        <td>{{ $item->sell_price_max }}</td>
                        <td>{{ $item->buy_price_min }}</td>
                        <td>{{ $item->buy_price_max }}</td>
                        <td>{{ $item->description }}</td>
                        <td>{{ now()->diffInMinutes($item->buy_price_min_date) }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<div id="addItemModal" style="display:none;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add Item</h5>
                <button type="button" class="close" onclick="closeAddItemForm()">&times;</button>
            </div>
            <div class="modal-body">
                <form action="/items" method="POST">
                    @csrf
                    <!-- Form fields for adding items -->
                </form>
            </div>
        </div>
    </div>
</div>

<div id="fetchFromApiModal" style="display:none;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Fetch Items from API</h5>
                <button type="button" class="close" onclick="closeFetchFromApiForm()">&times;</button>
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

<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
<script src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>
<script>
    $(document).ready(function () {
        var table = $('#itemTable').DataTable({
            "order": [[ 11, "asc" ]], // Default sorting by Last Update column
            "columnDefs": [
                { "orderable": false, "targets": [0,1,2,5, 10] } // Disable sorting for ID and Description columns
            ],
            "pageLength": 100 // Default page length
        });

        // Add search inputs for each column
        $('#itemTable thead tr').clone(true).appendTo('#itemTable thead');
        $('#itemTable thead tr:eq(1) th').each(function (i) {
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
    });
    function openItemIndex() {
        window.location.href = "http://127.0.0.1:8000/items"; // Redirect to the item index page
    }

    function showAddItemForm() {
        document.getElementById('addItemModal').style.display = 'block';
    }

    function closeAddItemForm() {
        document.getElementById('addItemModal').style.display = 'none';
    }

    function showFetchFromApiForm() {
        document.getElementById('fetchFromApiModal').style.display = 'block';
    }

    function closeFetchFromApiForm() {
        document.getElementById('fetchFromApiModal').style.display = 'none';
    }
</script>
</body>
</html>
