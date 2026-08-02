<?php
namespace App\Http\Controllers;

use App\Models\Notification;
use App\Models\Requisition;
use App\Models\RequisitionItem;
use App\Models\Supply;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;

class RequestController extends Controller
{
    /**
     * Requisition History / Archive
     */
    public function index()
    {
        $user = Auth::user();

        // FIX: Point to Requisition model instead of SupplyRequest
        // This ensures finished (released) and pending requests show up here.
        if ($user->role == 'employee') {
            $requests = Requisition::with('items')
                ->where('user_id', $user->id)
                ->latest()
                ->get();
        } else {
            $requests = Requisition::with(['items', 'user'])
                ->latest()
                ->get();
        }

        return view('requests.index', compact('requests'));
    }

    /**
     * Admin/Signatory Approval Queue
     */
    public function adminIndex()
    {
        $user = Auth::user();

        $pendingRequests = Requisition::with(['items', 'user'])
            ->when($user->role == 'dept_head', function($q) {
                return $q->where('status', 'pending');
            })
            ->when($user->role == 'vp', function($q) {
                return $q->where('status', 'approved_dept');
            })
            ->when($user->role == 'provost', function($q) {
                return $q->where('status', 'approved_vp')->where('request_type', 'major');
            })
            ->when($user->role == 'president', function($q) {
                return $q->where('status', 'approved_provost');
            })
            ->when($user->role == 'smo', function($q) {
                return $q->where(function($query) {
                    $query->where('status', 'approved_president') // Major path
                        ->orWhere(function($sub) {
                            $sub->where('status', 'approved_vp')->where('request_type', 'minor'); // Minor path
                        });
                });
            })
            ->latest()->get();

        return view('admin.approvals', compact('pendingRequests'));
    }

    /**
     * Update Approval Status (Signatories)
     */
    public function updateStatus(Request $request, $id)
    {
        $sr = Requisition::with('items')->findOrFail($id);
        $role = Auth::user()->role;

        $sr->remarks = $request->remarks;

        if ($request->status == 'rejected') {
            $sr->status = 'rejected';
        } else {
            if ($role == 'dept_head') $sr->status = 'approved_dept';
            elseif ($role == 'vp') $sr->status = 'approved_vp';
            elseif ($role == 'provost') $sr->status = 'approved_provost';
            elseif ($role == 'president') $sr->status = 'approved_president';
        }

        $sr->save();

        // Notification logic
        $itemName = $sr->items->first()->item_name ?? 'Items';
        $statusLabel = str_replace('_', ' ', $sr->status);

        $this->sendAlert(
            $sr->user_id,
            "Requisition Update",
            "Your request for {$itemName} has been updated to: {$statusLabel}.",
            $request->status == 'approved' ? 'check-circle' : 'x-circle',
            $request->status == 'approved' ? 'success' : 'danger'
        );

        return redirect()->route('admin.approvals')->with('success', 'Status updated successfully.');
    }

    /**
     * SMO Confirmation & Release (Fulfillment)
     */
    public function releaseRequest($id)
    {
        // FIX: Requisition has multiple items, so we need to loop through them
        return DB::transaction(function () use ($id) {
            $sr = Requisition::with('items')->findOrFail($id);

            foreach ($sr->items as $item) {
                $inventoryItem = Supply::where('item_name', $item->item_name)->first();

                if ($inventoryItem) {
                    // Check if we have enough stock before releasing
                    if ($inventoryItem->quantity < $item->quantity) {
                        return back()->with('error', "Insufficient stock for: {$item->item_name}");
                    }
                    $inventoryItem->decrement('quantity', $item->quantity);
                }
            }

            $sr->status = 'released';
            $sr->save();

            $this->sendAlert($sr->user_id, "Supplies Released", "Items for Req #{$sr->id} are ready for pickup.", 'package-check', 'success');

            return redirect()->route('admin.approvals')->with('success', 'Requisition confirmed and moved to archives.');
        });
    }

    /**
     * Notifications Center
     */
    public function notifications()
    {
        $notifications = Notification::where('user_id', Auth::id())->latest()->get();
        Notification::where('user_id', Auth::id())->where('is_read', false)->update(['is_read' => true]);

        return view('notifications', compact('notifications'));
    }

    public function show($id)
    {
        $request = Requisition::with(['items', 'user'])->findOrFail($id);
        return view('requests.show', compact('request'));
    }

    public function downloadPDF($id)
    {
        // FIX: Change SupplyRequest to Requisition for the PDF
        $request = Requisition::with(['user', 'items'])->findOrFail($id);
        $pdf = Pdf::loadView('requests.pdf', compact('request'));
        return $pdf->download('HTC-Requisition-'.$request->id.'.pdf');
    }

    private function sendAlert($userId, $title, $message, $icon = 'bell', $type = 'info')
    {
        Notification::create([
            'user_id' => $userId,
            'title' => $title,
            'message' => $message,
            'icon' => $icon,
            'type' => $type
        ]);
    }
}
