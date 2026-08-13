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
}
