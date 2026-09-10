<?php

namespace App\Http\Controllers;

use App\Models\Requisition;
use App\Models\RequisitionItem;
use App\Models\User;
use App\Models\Supply;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;

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
        $reportType = $request->get('report_type', 'requisitions'); // requisitions, inventory, dept_spending, sla_audit

        // 1. REQUISITIONS REPORT ENGINE
        if ($reportType === 'requisitions') {
            $query = Requisition::with(['user', 'items', 'logs']);

            if ($request->filled('dept')) {
                $query->whereHas('user', fn($q) => $q->where('department', $request->dept));
            }
            if ($request->filled('tier')) {
                $query->where('request_type', $request->tier);
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

            $records = $query->latest('created_at')->get();
            $totalValue = $records->sum('grand_total');

        // 2. INVENTORY CATALOG & VALUATION REPORT
        } elseif ($reportType === 'inventory') {
            $query = \App\Models\Supply::query();

            if ($request->filled('category')) {
                $query->where('category', $request->category);
            }
            if ($request->get('stock_filter') === 'low') {
                $query->whereRaw('quantity <= min_stock_level AND quantity > 0');
            } elseif ($request->get('stock_filter') === 'out') {
                $query->where('quantity', '<=', 0);
            }

            $records = $query->orderBy('category')->orderBy('item_name')->get();
            $totalValue = $records->sum(fn($i) => $i->quantity * $i->unit_price);

        // 3. DEPARTMENT EXPENDITURE AGGREGATION
        } elseif ($reportType === 'dept_spending') {
            $query = Requisition::join('users', 'requisitions.user_id', '=', 'users.id')
                ->select(
                    'users.department',
                    DB::raw('COUNT(requisitions.id) as total_requests'),
                    DB::raw('SUM(requisitions.grand_total) as total_spent'),
                    DB::raw('AVG(requisitions.grand_total) as avg_cost')
                )
                ->where('requisitions.status', 'released');

            if ($request->filled('date_from')) {
                $query->whereDate('requisitions.updated_at', '>=', $request->date_from);
            }
            if ($request->filled('date_to')) {
                $query->whereDate('requisitions.updated_at', '<=', $request->date_to);
            }

            $records = $query->groupBy('users.department')->get();
            $totalValue = $records->sum('total_spent');
        }

        // Selected Columns / Sections to Include in Export
        $selectedColumns = $request->input('columns', [
            'id', 'date', 'requestor', 'department', 'tier', 'items', 'status', 'total'
        ]);

        // GENERATE OFFICIAL PDF
        if ($request->has('download_pdf')) {
            $pdf = Pdf::loadView('admin.reports.pdf_template', [
                'records'         => $records,
                'reportType'      => $reportType,
                'totalValue'      => $totalValue ?? 0,
                'filters'         => $request->all(),
                'selectedColumns' => $selectedColumns,
                'generatedBy'     => Auth::user()->name,
                'generatedDate'   => now()->format('F d, Y • h:i A'),
            ])->setPaper('a4', 'landscape');

            return $pdf->download('HTC_SMO_Report_' . date('Ymd_His') . '.pdf');
        }

        return view('admin.reports.index', compact('records', 'reportType', 'totalValue', 'selectedColumns'));
    }
}
