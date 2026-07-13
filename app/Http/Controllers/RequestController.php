<?php
namespace App\Http\Controllers;

use App\Models\Notification;
use App\Models\Requisition;
use App\Models\Supply;
use App\Models\SupplyRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;


class RequestController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        // Employees see only their requests. Others (Admins/SMO) see everyone's.
        if ($user->role == 'employee') {
            $requests = SupplyRequest::where('user_id', $user->id)->latest()->get();
        } else {
            $requests = SupplyRequest::with('user')->latest()->get();
        }

        return view('requests.index', compact('requests'));
    }

    public function store(Request $request)
        {
            // 1. Basic validation
            $request->validate([
                'request_type' => 'required',
                'quantity' => 'required|integer|min:1',
            ]);

            // 2. Logic: If it's a common item, pull from Supply table. If not, use manual inputs.
            if ($request->filled('item_id')) {
                $supply = \App\Models\Supply::find($request->item_id);
                $itemName = $supply->item_name;
                $specs = $supply->specifications;
                $unit = $supply->unit;
                $price = $supply->unit_price;
            } else {
                $itemName = $request->manual_item_name;
                $specs = $request->specifications;
                $unit = $request->manual_unit;
                $price = $request->manual_unit_price;
            }

            // 3. Create the request
            \App\Models\SupplyRequest::create([
                'user_id' => Auth::id(),
                'item_name' => $itemName,
                'specifications' => $specs,
                'quantity' => $request->quantity,
                'unit' => $unit,
                'unit_price' => $price,
                'total_amount' => $price * $request->quantity,
                'request_type' => $request->request_type,
                'status' => 'pending',
            ]);

            // After the SupplyRequest::create() line:
            \App\Models\Notification::create([
                'user_id' => Auth::id(),
                'title' => 'Request Initiated',
                'message' => "Your requisition for {$itemName} has been submitted successfully.",
                'icon' => 'send',
                'type' => 'info'
            ]);

            // Find any Dept Head (or a specific one if you want)

            $deptHead = \App\Models\User::where('role', 'dept_head')->first();
            if ($deptHead) {
                $this->sendAlert($deptHead->id, 'New Procurement Request', "A new request for {$itemName} is waiting for your review.", 'clipboard-list', 'info');
            }

            return redirect()->route('dashboard')->with('success', 'Request submitted! It is now in the approval routing queue.');


        }

    // List all pending requests for the Admin
   public function adminIndex()
    {
        $user = Auth::user();

        // We switch SupplyRequest to Requisition
        // We add .with(['items', 'user']) to pull the "Cart" items and the Employee name
        $pendingRequests = Requisition::with(['items', 'user'])
            // 1. Dept Head sees brand new requests
            ->when($user->isDeptHead(), function($q) {
                return $q->where('status', 'pending');
            })
            // 2. VP sees requests approved by Dept Head
            ->when($user->isVP(), function($q) {
                return $q->where('status', 'approved_dept');
            })
            // 3. Provost sees requests approved by VP (Only Major)
            ->when($user->isProvost(), function($q) {
                return $q->where('status', 'approved_vp')->where('request_type', 'major');
            })
            // 4. President sees requests approved by Provost
            ->when($user->isPresident(), function($q) {
                return $q->where('status', 'approved_provost');
            })
            // 5. SMO sees requests ready for release (Approved by President for Major, or VP for Minor)
            ->when($user->isSMO(), function($q) {
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

    public function updateStatus(Request $request, $id)
    {
        $sr = SupplyRequest::findOrFail($id);
        $role = Auth::user()->role;

        // Save the remark (if provided)
        $sr->remarks = $request->remarks;

        if ($request->status == 'rejected') {
            $sr->status = 'rejected';
        } else {
            // Your existing routing logic...
            if ($role == 'dept_head') $sr->status = 'approved_dept';
            elseif ($role == 'vp') $sr->status = 'approved_vp';
            elseif ($role == 'provost') $sr->status = 'approved_provost';
            elseif ($role == 'president') $sr->status = 'approved_president';
        }

            $statusLabel = str_replace('_', ' ', $sr->status);
            $type = ($request->status == 'rejected') ? 'danger' : 'success';
            $icon = ($request->status == 'rejected') ? 'x-circle' : 'check-circle';

            $this->sendAlert($sr->user_id, "Request Update: {$statusLabel}", "Your request for {$sr->item_name} has been {$statusLabel}.", $icon, $type);

            $sr->save();

            // After the $sr->save() line:
            \App\Models\Notification::create([
                'user_id' => $sr->user_id, // Notify the employee
                'title' => 'Request Approved',
                'message' => "Update: Your request for {$sr->item_name} was approved by " . Auth::user()->role . ".",
                'icon' => 'check-circle',
                'type' => 'success'
            ]);

            return back()->with('success', 'Status updated with remarks.');
        }

    public function notifications()
    {
        // Fetch requests that are NOT pending (meaning they have an update)
        $notifications = SupplyRequest::where('user_id', Auth::id())
            ->whereIn('status', ['approved', 'rejected', 'released'])
            ->latest('updated_at')
            ->get();

        // Fetch all alerts for the logged-in user, newest first
        $notifications = \App\Models\Notification::where('user_id', Auth::id())
            ->latest()
            ->get();

        // Mark all as read so the red badge in the sidebar disappears
        \App\Models\Notification::where('user_id', Auth::id())
            ->where('is_read', false)
            ->update(['is_read' => true]);

        return view('notifications', compact('notifications'));
    }

    public function show($id)
    {
        // Load the requisition and all items inside it
        $request = Requisition::with(['items', 'user'])->findOrFail($id);
        return view('requests.show', compact('request'));
    }
    public function releaseRequest($id)
    {
        $sr = SupplyRequest::findOrFail($id);

        // Find the actual item in inventory
        $item = Supply::where('item_name', $sr->item_name)->first();

        if ($item && $item->quantity >= $sr->quantity) {
            // 1. Subtract from inventory
            $item->decrement('quantity', $sr->quantity);

            // 2. Mark request as released
            $sr->status = 'released';
            $sr->save();

            return back()->with('success', 'Supplies released and inventory updated!');
        }

        return back()->with('error', 'Not enough stock in inventory!');
    }

    public function downloadPDF($id)
    {
        $request = SupplyRequest::with('user')->findOrFail($id);

        // This points to the blade file we will create in the next step
        $pdf = Pdf::loadView('requests.pdf', compact('request'));

        // Download the file with a specific name
        return $pdf->download('HTC-Request-'.$request->id.'.pdf');
    }

    private function sendAlert($userId, $title, $message, $icon = 'bell', $type = 'info')
    {
        \App\Models\Notification::create([
            'user_id' => $userId,
            'title' => $title,
            'message' => $message,
            'icon' => $icon,
            'type' => $type
        ]);
    }
}
