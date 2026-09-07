<?php

namespace App\Http\Controllers;

use App\Models\Requisition;
use App\Models\RequisitionItem;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function index()
    {
        // 1. TIME-BASED COUNTS
        $stats = [
            'week'  => Requisition::where('status', 'released')->where('updated_at', '>=', now()->startOfWeek())->count(),
            'month' => Requisition::where('status', 'released')->where('updated_at', '>=', now()->startOfMonth())->count(),
            'year'  => Requisition::where('status', 'released')->where('updated_at', '>=', now()->startOfYear())->count(),
        ];

        // 2. COST ANALYSIS
        $costs = [
            'total_spent' => Requisition::where('status', 'released')->sum('grand_total'),
            'avg_per_req' => Requisition::where('status', 'released')->avg('grand_total') ?? 0,
        ];

        // 3. CHART DATA: Monthly releases for the current year
        $monthlyData = Requisition::select(
            DB::raw('count(id) as count'),
            DB::raw("DATE_FORMAT(updated_at, '%M') as month")
        )
        ->where('status', 'released')
        ->whereYear('updated_at', date('Y'))
        ->groupBy('month')
        ->orderBy('updated_at')
        ->get();

        // 4. CHART DATA: Department Spending
        $deptSpending = User::join('requisitions', 'users.id', '=', 'requisitions.user_id')
            ->select('users.department', DB::raw('SUM(requisitions.grand_total) as total'))
            ->where('requisitions.status', 'released')
            ->groupBy('users.department')
            ->get();

        // 5. TOP CONSUMED ITEMS
        $topItems = RequisitionItem::select('item_name', DB::raw('SUM(quantity) as total_qty'))
            ->join('requisitions', 'requisition_items.requisition_id', '=', 'requisitions.id')
            ->where('requisitions.status', 'released')
            ->groupBy('item_name')
            ->orderByDesc('total_qty')
            ->take(5)
            ->get();

        return view('admin.reports', compact('stats', 'costs', 'monthlyData', 'deptSpending', 'topItems'));
    }

    public function generate(Request $request)
    {
        // 1. Start the query
        $query = \App\Models\Requisition::with(['user', 'items']);

        // 2. Apply Filters (The "Altered/Filtered" part the Dean wants)
        if ($request->filled('dept')) {
            $query->whereHas('user', fn($q) => $q->where('department', $request->dept));
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $requisitions = $query->latest()->get();

        // 3. Check if user clicked "Download PDF" or just "Search"
        if ($request->has('download')) {
            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('admin.reports.pdf_template', [
                'requisitions' => $requisitions,
                'filters' => $request->all()
            ])->setPaper('a4', 'landscape'); // Landscape is better for reports

            return $pdf->download('Institutional_Report_'.now()->format('Y-m-d').'.pdf');
        }

        return view('admin.reports.index', compact('requisitions'));
    }
}
