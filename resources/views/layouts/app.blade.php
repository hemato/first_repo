<!DOCTYPE html>
<html>
<head>
    <title>??</title>
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
                <a class="nav-link" href="{{ route('price-comparisons1') }}">Sell Min - Buy Max</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="{{ route('price-comparisons2') }}">Buy Max - Sell Min</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="{{ route('price-comparisons3') }}">Buy Max - Buy Max</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="{{ route('price-comparisons4') }}">Sell Min - Sell Min</a>
            </li>
        </ul>
    </div>
</nav>

    @yield('content')

</body>
@yield('scripts')
</html>
