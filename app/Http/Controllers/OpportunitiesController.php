<?php

namespace App\Http\Controllers;

use App\Models\MarketPrices;
use Illuminate\Http\Request;

class OpportunitiesController extends Controller
{
    public function index()
    {
        $marketPrices = MarketPrices::where('city_id', 3)->get();
        return view('opportunities.index', compact('marketPrices'));
    }
}
