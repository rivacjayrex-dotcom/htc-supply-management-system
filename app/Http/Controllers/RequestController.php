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
     * Active Requisitions (Pending, In-Progress, Approved)
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        // 1. Base Query for ACTIVE ONLY
        $query = Requisition::with(['items', 'user'])
            ->whereNotIn('status', ['released', 'rejected']);

        // 2. Privacy: Non-SMO only sees their own
        if ($user->role !== 'smo') {
            $query->where('user_id', $user->id);
        }

        // 3. Filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($user->role == 'smo' && $request->filled('dept')) {
            $query->whereHas('user', fn($q) => $q->where('department', $request->dept));
        }
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // 4. Sorting
        $order = $request->get('order', 'desc');
        $activeRequests = $query->orderBy('created_at', $order)->paginate(15);

        return view('requests.index', compact('activeRequests'));
    }

    /**
     * Archived Requisitions (Released & Rejected)
     */
    public function archive(Request $request)
    {
        $user = Auth::user();

        // 1. Base Query for COMPLETED ONLY
        $query = Requisition::with(['items', 'user'])
            ->whereIn('status', ['released', 'rejected']);

        // 2. Privacy: Non-SMO only sees their own
        if ($user->role !== 'smo') {
            $query->where('user_id', $user->id);
        }

        // 3. Filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($user->role == 'smo' && $request->filled('dept')) {
            $query->whereHas('user', fn($q) => $q->where('department', $request->dept));
        }
        if ($request->filled('date_from')) {
            $query->whereDate('updated_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('updated_at', '<=', $request->date_to);
        }

        // 4. Sorting
        $order = $request->get('order', 'desc');
        $archivedRequests = $query->orderBy('updated_at', $order)->paginate(15);

        return view('requests.archive', compact('archivedRequests'));
    }

/**
     * Admin/Signatory Approval Queue
     */
    public function adminIndex()
    {
        $user = Auth::user();

        $query = Requisition::with(['items', 'user']);

        switch ($user->role) {
            case 'dept_head':
                // Department Head sees only pending requests from their own department
                $query->where('status', 'pending')
                      ->whereHas('user', function ($u) use ($user) {
                          $u->where('department', $user->department);
                      });
                break;

            case 'vp_finance':
                // VP Finance sees MINOR requests approved by Dept Head
                $query->where('status', 'approved_dept')
                      ->where('request_type', 'minor');
                break;

            case 'vp_admin':
                // VP Admin sees MAJOR requests approved by Dept Head
                $query->where('status', 'approved_dept')
                      ->where('request_type', 'major');
                break;

            case 'provost':
                // Provost sees MAJOR requests endorsed by VP Admin
                $query->where('status', 'approved_vp')
                      ->where('request_type', 'major');
                break;

            case 'president':
                // President sees MAJOR requests recommended by Provost
                $query->where('status', 'approved_provost')
                      ->where('request_type', 'major');
                break;

            case 'smo':
                // SMO sees fully approved requests ready for fulfillment:
                // - Minor requests approved by VP Finance ('approved_vp')
                // - Major requests approved by President ('approved_president')
                $query->where(function ($q) {
                    $q->where(function ($sub) {
                        $sub->where('request_type', 'minor')
                            ->where('status', 'approved_vp');
                    })->orWhere(function ($sub) {
                        $sub->where('status', 'approved_president');
                    });
                });
                break;

            default:
                // Other roles or employees have no approval duties
                $query->whereRaw('1 = 0');
                break;
        }

        $pendingRequests = $query->latest()->get();

        // If user is SMO, separate into tabs for Minor and Major
        if ($user->role === 'smo') {
            $pendingMinor = $pendingRequests->where('request_type', 'minor');
            $pendingMajor = $pendingRequests->where('request_type', 'major');

            return view('admin.approvals', compact('pendingMinor', 'pendingMajor'));
        }

        return view('admin.approvals', compact('pendingRequests'));
    }

    /**
     * Update Approval Status (Signatories)
     */
/**
     * Update Approval Status (Signatories)
     */
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status'  => 'required|in:approved,rejected',
            'remarks' => 'nullable|string|max:1000',
        ]);

        $sr = Requisition::with('items')->findOrFail($id);
        $role = Auth::user()->role;

        // Map system role to a clean, readable title for logging
        $roleTitles = [
            'dept_head'  => 'Department Head',
            'vp_finance' => 'VP for Finance',
            'vp_admin'   => 'VP for Administration',
            'provost'    => 'School Provost',
            'president'  => 'School President',
            'smo'        => 'SMO In-Charge',
        ];
        $position = $roleTitles[$role] ?? ucwords(str_replace('_', ' ', $role));

        $sr->remarks = $request->remarks;

        if ($request->status == 'rejected') {
            $sr->status = 'rejected';
        } else {
            // State progression based on signatory hierarchy
            if ($role == 'dept_head') {
                $sr->status = 'approved_dept';
            } elseif ($role == 'vp_finance' || $role == 'vp_admin') {
                $sr->status = 'approved_vp';
            } elseif ($role == 'provost') {
                $sr->status = 'approved_provost';
            } elseif ($role == 'president') {
                $sr->status = 'approved_president';
            }
        }

        $sr->save();

        // Log the approval action
        \App\Models\ApprovalLog::create([
            'requisition_id' => $sr->id,
            'action'         => ($request->status == 'approved' ? 'Approved' : 'Rejected') . ' by ' . $position,
            'role'           => $role,
            'remarks'        => $request->remarks,
        ]);

        // Send notification to the requisition owner
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
        $sr = Requisition::with('items')->findOrFail($id);

        // 1. Guard against double-releasing
        if ($sr->status === 'released') {
            return back()->with('error', 'This requisition has already been released.');
        }

        // 2. Only SMO can release
        if (Auth::user()->role !== 'smo') {
            return back()->with('error', 'Unauthorized. Only the SMO In-Charge can release supplies.');
        }

        return DB::transaction(function () use ($sr) {
            // STEP A: Pre-validation Check (Check all items before deducting any stock)
            foreach ($sr->items as $item) {
                $inventoryItem = Supply::where('item_name', $item->item_name)->first();

                if (!$inventoryItem) {
                    return back()->with('error', "Cannot release: Item '{$item->item_name}' is not found in the inventory database.");
                }

                if ($inventoryItem->quantity < $item->quantity) {
                    return back()->with('error', "Insufficient stock for '{$item->item_name}'. Available: {$inventoryItem->quantity}, Requested: {$item->quantity}.");
                }
            }

            // STEP B: Perform the deductions
            foreach ($sr->items as $item) {
                $inventoryItem = Supply::where('item_name', $item->item_name)->first();
                $inventoryItem->decrement('quantity', $item->quantity);
            }

            // STEP C: Mark as released and log
            $sr->status = 'released';
            $sr->save();

            \App\Models\ApprovalLog::create([
                'requisition_id' => $sr->id,
                'action'         => 'Released and Fulfilled by SMO In-Charge',
                'role'           => 'smo',
                'remarks'        => 'All requested supplies have been deducted from inventory and prepared for release.',
            ]);

            // STEP D: Notify the requester
            $this->sendAlert(
                $sr->user_id,
                "Supplies Ready for Pickup",
                "Your requisition #{$sr->id} has been fulfilled by the SMO and is ready for pickup.",
                'package-check',
                'success'
            );

            return redirect()->route('admin.approvals')->with('success', "Requisition #{$sr->id} successfully released and inventory updated.");
        });
    }

    /**
     * Notifications Center
     */
    public function notifications()
    {
        $user = Auth::user();
        $allNotes = \App\Models\Notification::where('user_id', $user->id)->latest()->get();

        // Categorized notes for tabs
        $approvalNotes = $allNotes->where('type', 'success');
        $deadlineNotes = $allNotes->where('type', 'danger');

        // 1. Data for SMO (Critical Deadlines)
        $criticalRequests = ($user->role == 'smo')
            ? \App\Models\Requisition::with('items') // <--- ADD .with('items') HERE
                ->whereIn('status', ['approved_president', 'approved_vp'])
                ->where('updated_at', '<=', now()->subDays(2))->get()
            : [];

        // 2. Data for Employee/Staff (Personal Summary)
        $personalStats = [
            'total' => \App\Models\Requisition::where('user_id', $user->id)->count(),
            'pending' => \App\Models\Requisition::where('user_id', $user->id)->where('status', 'pending')->count(),
            'released' => \App\Models\Requisition::where('user_id', $user->id)->where('status', 'released')->count(),
        ];

        \App\Models\Notification::where('user_id', $user->id)->where('is_read', false)->update(['is_read' => true]);

        return view('notifications', compact('allNotes', 'approvalNotes', 'deadlineNotes', 'criticalRequests', 'personalStats'));
    }

    public function show($id)
    {
        $request = Requisition::with(['user', 'items', 'logs'])->findOrFail($id);
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

    public function getLatestNotification()
    {
        // Fetch the most recent unread notification for the user
        $notification = \App\Models\Notification::where('user_id', auth()->id())
            ->where('is_read', false)
            ->latest()
            ->first();

        if ($notification) {
            // We don't mark it as read yet, so the badge in the sidebar stays.
            // But we return it to show the pop-up.
            return response()->json($notification);
        }

        return response()->json(null);
    }
}


