<?php

namespace App\Http\Controllers;

use App\Models\Item;
use Illuminate\Http\Request;

class OpportunitiesController extends Controller
{
    public function index()
    {
        $items = Item::where('city_id', 3)->get();
        return view('opportunities.index', compact('items'));
    }
}
