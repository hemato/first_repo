@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>Item Price Comparisons</h1>
        <table class="table">
            <thead>
            <tr>
                <th>Item ID</th>
                <th>Quality</th>
                <th>Cheapest City</th>
                <th>Min Buy Price</th>
                <th>Buy Last Update (minutes ago)</th>
                <th>Most Expensive City</th>
                <th>Max Sell Price</th>
                <th>Sell Last Update (minutes ago)</th>
            </tr>
            </thead>
            <tbody>
            @foreach($priceComparisons as $comparison)
                <tr>
                    <td>{{ $comparison['item_id'] }}</td>
                    <td>{{ $comparison['cheapest_quality'] }}</td>
                    <td>{{ $comparison['cheapest_city'] }}</td>
                    <td>{{ $comparison['min_buy_price'] }}</td>
                    <td>{{ now()->diffInMinutes($comparison['buy_price_min_date']) }}</td>
                    <td>{{ $comparison['most_expensive_city'] }}</td>
                    <td>{{ $comparison['max_sell_price'] }}</td>
                    <td>{{ now()->diffInMinutes($comparison['sell_price_max_date']) }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
@endsection
