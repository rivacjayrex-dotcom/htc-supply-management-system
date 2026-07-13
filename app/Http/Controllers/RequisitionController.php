<?php

namespace App\Http\Controllers;

use App\Models\Requisition;
use App\Models\RequisitionItem;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RequisitionController extends Controller
{
    public function store(Request $request)
    {
        // 1. VALIDATION
        // We tell Laravel to expect an array called 'items'
        $request->validate([
            'request_type' => 'required',
            'items' => 'required|array|min:1',
            'items.*.name' => 'required|string',
            'items.*.qty' => 'required|integer|min:1',
            'items.*.price' => 'required|numeric',
        ]);

        // 2. CREATE THE "HEADER" (The Receipt)
        // This creates the single entry that the Bosses will see and approve.
        $requisition = Requisition::create([
            'user_id' => Auth::id(),
            'request_type' => $request->request_type,
            'status' => 'pending',
            'grand_total' => 0, // We will calculate this in a moment
        ]);

        $calculatedGrandTotal = 0;

        // 3. THE CART LOOP (The Items)
        // Since 'items' is an array from your Alpine.js form, we loop through it.
        foreach ($request->items as $itemData) {
            $subtotal = $itemData['qty'] * $itemData['price'];
            $calculatedGrandTotal += $subtotal;

            // Save each item individually and link it to the requisition ID
            RequisitionItem::create([
                'requisition_id' => $requisition->id,
                'item_name' => $itemData['name'],
                'specifications' => $itemData['specs'] ?? null,
                'quantity' => $itemData['qty'],
                'unit' => $itemData['unit'] ?? 'pc',
                'unit_price' => $itemData['price'],
                'subtotal' => $subtotal,
            ]);
        }

        // 4. UPDATE THE GRAND TOTAL
        // Now that the loop is done, we know the final price.
        $requisition->update(['grand_total' => $calculatedGrandTotal]);

        // 5. SEND NOTIFICATION (Optional but good for UX)
        Notification::create([
            'user_id' => Auth::id(),
            'title' => 'Requisition Submitted',
            'message' => "Your request for " . count($request->items) . " item(s) has been sent for approval.",
            'icon' => 'send',
            'type' => 'info'
        ]);

        return redirect()->route('dashboard')->with('success', 'Requisition submitted successfully!');
    }
}
