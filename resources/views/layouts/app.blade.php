<!DOCTYPE html>
<html>
<head>
    <title>Laravel Application</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
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
                <a class="nav-link" href="{{ route('price-comparisons') }}">Price Comparisons</a>
            </li>
        </ul>
    </div>
</nav>

<div class="container">
    @yield('content')
</div>
</body>
</html>
