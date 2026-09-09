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
// Save a new item to the database
    public function store(Request $request)
    {
        $validated = $request->validate([
            'item_name'       => 'required|string|max:255|unique:supplies,item_name',
            'brand'           => 'required|string|max:100',
            'category'        => 'required|in:Cleaning Supplies,Construction Supplies,Drugs and Medicines,HDMI,Maintenance Supplies,Medical Supplies,Non-Medical Supplies,Office Supplies',
            'specifications'  => 'required|string|min:3',
            'quantity'        => 'required|integer|min:0',
            'unit'            => 'required|string|max:50',
            'unit_price'      => 'required|numeric|min:0.01',
            'min_stock_level' => 'required|integer|min:0',
            'model_number'    => 'nullable|string|max:100',
        ], [
            'item_name.unique'   => 'An item with this name already exists in the inventory.',
            'specifications.min' => 'Specifications must be at least 5 characters.',
            'category.in'        => 'Please choose a valid category from the list.',
            'unit.in'            => 'Please select an authorized unit of measure.',
        ]);

        // Mirror specifications into physical_description if the DB has both columns
        $validated['physical_description'] = $validated['specifications'];

        \App\Models\Supply::create($validated);

        return redirect()->route('inventory.index')->with('success', "Item '{$validated['item_name']}' has been registered into inventory.");
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
     * Save changes to an existing item
     */
    public function update(Request $request, $id)
    {
        $item = Supply::findOrFail($id);

        $validated = $request->validate([
            'item_name'       => 'required|string|max:255|unique:supplies,item_name,' . $id,
            'brand'           => 'required|string|max:100',
            'category'        => 'required|in:Cleaning Supplies,Construction Supplies,Drugs and Medicines,HDMI,Maintenance Supplies,Medical Supplies,Non-Medical Supplies,Office Supplies',
            'specifications'  => 'required|string|min:3',
            'quantity'        => 'required|integer|min:0',
            'unit'            => 'required|string|max:50',
            'unit_price'      => 'required|numeric|min:0.01',
            'min_stock_level' => 'required|integer|min:0',
            'model_number'    => 'nullable|string|max:100',
        ], [
            'item_name.unique'   => 'Another item with this name already exists in inventory.',
            'specifications.min' => 'Please provide at least 3 characters for specifications.',
            'category.in'        => 'Please select a valid institutional category.',
        ]);

        // Keep physical_description synced if the database column exists
        $validated['physical_description'] = $validated['specifications'];

        $item->update($validated);

        return redirect()->route('inventory.index')->with('success', "Record for '{$item->item_name}' has been successfully updated.");
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
