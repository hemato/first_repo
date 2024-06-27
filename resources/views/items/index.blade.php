<!DOCTYPE html>
<html>
<head>
    <title>Items</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h1>Items</h1>
    <button class="btn btn-success mb-3" onclick="showAddItemForm()">Add Item</button>
    <button class="btn btn-primary mb-3" onclick="showFetchFromApiForm()">Fetch Items from API</button>
    <table class="table table-bordered">
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
            <th>Last Update</th>
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
                <td>{{ now()->diffInMinutes($item->buy_price_min_date) }} minutes ago</td>
            </tr>
        @endforeach
        </tbody>
    </table>
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
                    <div class="form-group">
                        <label for="item_id">Item ID</label>
                        <input type="text" class="form-control" id="item_id" name="item_id" required>
                    </div>
                    <div class="form-group">
                        <label for="item_name">Item Name</label>
                        <input type="text" class="form-control" id="item_name" name="item_name" required>
                    </div>
                    <div class="form-group">
                        <label for="city_id">City</label>
                        <input type="number" class="form-control" id="city_id" name="city_id" required>
                    </div>
                    <div class="form-group">
                        <label for="quality">Quality</label>
                        <input type="number" class="form-control" id="quality" name="quality" required>
                    </div>
                    <div class="form-group">
                        <label for="quantity">Quantity</label>
                        <input type="number" class="form-control" id="quantity" name="quantity" required>
                    </div>
                    <div class="form-group">
                        <label for="sell_price_min">Sell Price Min</label>
                        <input type="number" class="form-control" id="sell_price_min" name="sell_price_min" required>
                    </div>
                    <div class="form-group">
                        <label for="sell_price_min_date">Sell Price Min Date</label>
                        <input type="datetime-local" class="form-control" id="sell_price_min_date" name="sell_price_min_date" required>
                    </div>
                    <div class="form-group">
                        <label for="sell_price_max">Sell Price Max</label>
                        <input type="number" class="form-control" id="sell_price_max" name="sell_price_max" required>
                    </div>
                    <div class="form-group">
                        <label for="sell_price_max_date">Sell Price Max Date</label>
                        <input type="datetime-local" class="form-control" id="sell_price_max_date" name="sell_price_max_date" required>
                    </div>
                    <div class="form-group">
                        <label for="buy_price_min">Buy Price Min</label>
                        <input type="number" class="form-control" id="buy_price_min" name="buy_price_min" required>
                    </div>
                    <div class="form-group">
                        <label for="buy_price_min_date">Buy Price Min Date</label>
                        <input type="datetime-local" class="form-control" id="buy_price_min_date" name="buy_price_min_date" required>
                    </div>
                    <div class="form-group">
                        <label for="buy_price_max">Buy Price Max</label>
                        <input type="number" class="form-control" id="buy_price_max" name="buy_price_max" required>
                    </div>
                    <div class="form-group">
                        <label for="buy_price_max_date">Buy Price Max Date</label>
                        <input type="datetime-local" class="form-control" id="buy_price_max_date" name="buy_price_max_date" required>
                    </div>
                    <div class="form-group">
                        <label for="description">Description</label>
                        <textarea class="form-control" id="description" name="description" required></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">Add Item</button>
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

<script>
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
