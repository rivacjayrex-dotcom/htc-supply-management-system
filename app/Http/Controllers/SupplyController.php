<?php

namespace App\Http\Controllers;

use App\Models\Supply;
use Illuminate\Http\Request;

class SupplyController extends Controller
{
    // List all inventory items
    public function index()
    {
        // We order by item_name so the list is alphabetical and easier to read
        $supplies = Supply::orderBy('item_name', 'asc')->get();
        return view('inventory.index', compact('supplies'));
    }

    // Show the form to add a new item
    public function create()
    {
        return view('inventory.create');
    }

    // Save a new item to the database
    public function store(Request $request)
    {
        $request->validate([
            'item_name' => 'required|string|unique:supplies,item_name',
            'brand' => 'required|string',
            'category' => 'required|in:Office Supplies,IT Equipment,Janitorial,Furniture,Laboratory',
            'quantity' => 'required|integer|min:0',
            'unit' => 'required|in:Ream,Box,Piece,Set,Bottle,Pack,Roll', // Strict Units
            'unit_price' => 'required|numeric|min:0.01',
            'min_stock_level' => 'required|integer|min:0',
            'physical_description' => 'required|string|min:10', // Forced detail
        ], [
            'item_name.unique' => 'Duplicate Error: This item already exists in the registry.',
            'unit.in' => 'Error: Please select a standardized unit of measure.',
            'physical_description.min' => 'Incomplete Data: Please be more specific with the item description.',
        ]);

        Supply::create($request->all());
        return redirect()->route('inventory.index')->with('success', 'Item Verified and Registered.');
    }

    /**
     * NEW: Show the form to edit an existing item
     */
    public function edit($id)
    {
        $item = Supply::findOrFail($id);
        return view('inventory.edit', compact('item'));
    }

    /**
     * NEW: Save changes to an existing item
     */
    public function update(Request $request, $id)
    {
        $item = Supply::findOrFail($id);

        // 1. Validation (Ensures no "wrong data" gets in)
        $request->validate([
            'item_name' => 'required|string|max:255|unique:supplies,item_name,' . $id,
            'brand' => 'required|string',
            'category' => 'required',
            'quantity' => 'required|integer|min:0',
            'unit' => 'required',
            'unit_price' => 'required|numeric|min:0',
            'min_stock_level' => 'required|integer|min:0',
        ]);

        // 2. The Update Call
        // This looks at the $fillable in the model and saves everything from the form
        $item->update($request->all());

        return redirect()->route('inventory.index')->with('success', "Record for {$item->item_name} has been updated.");
    }

    /**
     * NEW: Remove an item from inventory
     */
    public function destroy($id)
    {
        $item = Supply::findOrFail($id);
        $item->delete();

        return redirect()->route('inventory.index')->with('success', 'Item has been removed from inventory.');
    }
}
