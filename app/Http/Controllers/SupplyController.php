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
        // 1. STRICT VALIDATION
        $validated = $request->validate([
            'item_name' => 'required|string|max:255|unique:supplies,item_name',
            'brand' => 'required|string|max:100',
            'category' => 'required|in:Office Supplies,IT Equipment,Janitorial,Furniture,Laboratory',
            'specifications' => 'required|string|min:15', // Ensuring detailed input
            'quantity' => 'required|integer|min:0',
            'unit' => 'required|string|in:Ream,Box,Piece,Set,Roll,Bottle,Pack',
            'unit_price' => 'required|numeric|min:0.01',
            'min_stock_level' => 'required|integer|min:0',
        ], [
            'specifications.min' => 'Incomplete Specs: Please provide more physical details as required by institutional policy.',
            'item_name.unique' => 'Database Error: This item is already registered in the inventory.',
        ]);

        // 2. CREATE DATA
        \App\Models\Supply::create($validated);

        return redirect()->route('inventory.index')->with('success', 'Institutional Item Verified and Logged.');
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
