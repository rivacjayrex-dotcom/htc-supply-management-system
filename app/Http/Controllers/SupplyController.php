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
        // STRICT VALIDATION RULES
        $request->validate([
            'item_name' => 'required|string|max:255|unique:supplies,item_name', // No duplicates
            'brand' => 'required|string|max:100',
            'category' => 'required|in:Office Supplies,IT Equipment,Janitorial,Furniture,Laboratory', // Restricted list
            'quantity' => 'required|integer|min:1', // Must be at least 1
            'unit' => 'required|string|in:Ream,Box,Piece,Set,Roll,Bottle,Pack', // Must be standard units
            'unit_price' => 'required|numeric|min:0.01', // Cannot be 0 or negative
            'min_stock_level' => 'required|integer|min:0',
        ], [
            // CUSTOM ERROR MESSAGES (To satisfy the "No wrong data" rule)
            'item_name.unique' => 'This item is already in the system. Use the "Edit" feature to update its stock instead.',
            'unit.in' => 'Please select a valid unit of measure from the provided list.',
            'unit_price.min' => 'An item must have a real market value greater than 0.'
        ]);

        Supply::create($request->all());

        return redirect()->route('inventory.index')->with('success', 'Item logged successfully with zero errors.');
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
