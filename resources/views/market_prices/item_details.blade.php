@extends('layouts.app')

@section('content_body')
    <div class="container">
        <h1>Item Details for ID: {{ $item_id }}</h1>

        @foreach($groupedDetails as $city => $details)
            <div class="card mb-4">
                <div class="card-header">
                    <h2>{{ $city }}</h2>
                </div>
                <div class="card-body">
                    <table class="table table-striped">
                        <thead>
                        <tr>
                            <th>Item Name</th>
                            <th>Quantity</th>
                            <th>Quality</th>
                            <th>Sell Price Min</th>
                            <th>Sell Price Min Date</th>
                            <th>Sell Price Max</th>
                            <th>Sell Price Max Date</th>
                            <th>Buy Price Min</th>
                            <th>Buy Price Min Date</th>
                            <th>Buy Price Max</th>
                            <th>Buy Price Max Date</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($details as $item)
                            <tr>
                                <td>{{ $item->item_name }}</td>
                                <td>{{ $item->quantity }}</td>
                                <td>{{ $item->quality_name }}</td>
                                <td>{{ $item->sell_price_min }}</td>
                                <td>{{ $item->sell_price_min_date }}</td>
                                <td>{{ $item->sell_price_max }}</td>
                                <td>{{ $item->sell_price_max_date }}</td>
                                <td>{{ $item->buy_price_min }}</td>
                                <td>{{ $item->buy_price_min_date }}</td>
                                <td>{{ $item->buy_price_max }}</td>
                                <td>{{ $item->buy_price_max_date }}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endforeach
    </div>
@endsection
