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
    public function index(Request $request)
    {
        $user = Auth::user();

        // 1. Base Query with Relationships
        $query = \App\Models\Requisition::with(['items', 'user']);

        // 2. PRIVACY FILTER: If not SMO, only show own records
        if ($user->role !== 'smo') {
            $query->where('user_id', $user->id);
        }

        // 3. MULTI-FILTER ENGINE
        // Filter by Status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by Department (SMO Only)
        if ($user->role == 'smo' && $request->filled('dept')) {
            $query->whereHas('user', function($q) use ($request) {
                $q->where('department', $request->dept);
            });
        }

        // Filter by Date Range
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // 4. Multi-Directional Sorting
        $sort = $request->get('sort', 'created_at');
        $order = $request->get('order', 'desc');
        $query->orderBy($sort, $order);

        // 5. Paginate and Split for the Vertical View
        $all = $query->get();

        $activeRequests = $all->whereNotIn('status', ['released', 'rejected']);
        $completedRequests = $all->whereIn('status', ['released', 'rejected']);

        return view('requests.index', compact('activeRequests', 'completedRequests'));
    }
    /**
     * Admin/Signatory Approval Queue
     */
    public function adminIndex()
    {
        $user = Auth::user();

        $pendingRequests = Requisition::with(['items', 'user'])
            // 1. Dept Head sees brand new ones
            ->when($user->role == 'dept_head', function($q) {
                return $q->where('status', 'pending');
            })
            // 2. VP FINANCE sees MINOR requests approved by Dept Head
            ->when($user->role == 'vp_finance', function($q) {
                return $q->where('status', 'approved_dept')->where('request_type', 'minor');
            })
            // 3. VP ADMIN sees MAJOR requests approved by Dept Head
            ->when($user->role == 'vp_admin', function($q) {
                return $q->where('status', 'approved_dept')->where('request_type', 'major');
            })
            // 4. Provost sees MAJOR requests approved by VP Admin
            ->when($user->role == 'provost', function($q) {
                return $q->where('status', 'approved_vp')->where('request_type', 'major');
            })
            // 5. President sees MAJOR requests approved by Provost
            ->when($user->role == 'president', function($q) {
                return $q->where('status', 'approved_provost');
            })
            // 6. SMO FIX:
            // SMO sees ANYTHING that is 'approved_president' (Major OR President-submitted Minor)
            // OR anything 'approved_vp' that is 'minor' (Standard Minor path)
            ->when($user->role == 'smo', function($q) {
                return $q->where(function($query) {
                    $query->where('status', 'approved_president')
                        ->orWhere(function($sub) {
                            $sub->where('status', 'approved_vp')->where('request_type', 'minor');
                        });
                });
            })
            ->latest()->get();

        // SMO TABS SPLITTING
        if ($user->role == 'smo') {
            $pendingMinor = $pendingRequests->where('request_type', 'minor');
            $pendingMajor = $pendingRequests->where('request_type', 'major');

            return view('admin.approvals', compact('pendingMinor', 'pendingMajor'));
        }

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
            // Updated logic to recognize specific VP roles
            if ($role == 'dept_head') {
                $sr->status = 'approved_dept';
            }
            elseif ($role == 'vp_finance' || $role == 'vp_admin') {
                // Both VPs set the status to approved_vp when they sign off
                $sr->status = 'approved_vp';
            }
            elseif ($role == 'provost') {
                $sr->status = 'approved_provost';
            }
            elseif ($role == 'president') {
                $sr->status = 'approved_president';
            }
        }

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

        $sr->save();

        \App\Models\ApprovalLog::create([
            'requisition_id' => $sr->id,
            'action' => ($request->status == 'approved' ? 'Approved' : 'Rejected') . ' by ' . $position,
            'role' => $role,
            'remarks' => $request->remarks
        ]);

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
        $user = Auth::user();
        $allNotes = \App\Models\Notification::where('user_id', $user->id)->latest()->get();

        // Categorized notes for tabs
        $approvalNotes = $allNotes->where('type', 'success');
        $deadlineNotes = $allNotes->where('type', 'danger');

        // 1. Data for SMO (Critical Deadlines)
        $criticalRequests = ($user->role == 'smo')
            ? \App\Models\Requisition::whereIn('status', ['approved_president', 'approved_vp'])
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


