<?php

namespace App\Http\Controllers;

use App\Models\Supply;
use Illuminate\Http\Request;

class SupplyController extends Controller
{
    public function index()
    {
        $supplies = Supply::all();
        return view('inventory.index', compact('supplies'));
    }

    public function create()
    {
        return view('inventory.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'item_name' => 'required',
            'quantity' => 'required|integer',
            'unit' => 'required',
            'unit_price' => 'required|numeric',
        ]);

        Supply::create($request->all());

        return redirect()->route('inventory.index')->with('success', 'Item added successfully!');
    }
}
