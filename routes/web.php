<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SupplyController;
use App\Http\Controllers\RequestController;
use App\Http\Controllers\RequisitionController;
use App\Models\SupplyRequest;
use Carbon\Carbon;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Models\Requisition;


// Welcome Page
Route::get('/', function () {
    return redirect()->route('login');
});

// DASHBOARD LOGIC (Updated for Multi-item Requisitions)
Route::get('/dashboard', function () {
    $user = Auth::user();

    // 1. Employee Logic (Keep as is)
    $activeCount = Requisition::where('user_id', $user->id)->whereNotIn('status', ['released', 'rejected'])->count();
    $approvedLast24h = Requisition::where('user_id', $user->id)->whereIn('status', ['approved_president', 'released'])->where('updated_at', '>=', now()->subDay())->count();
    $recentRequests = Requisition::with('items')->where('user_id', $user->id)->latest()->take(5)->get();

    $stats = [
            'total' => 0, 'pending' => 0, 'urgent' => 0, 'monthly_value' => 0
        ];
        $recentActivity = [];
        $allStaffRequests = [];

        if ($user->role == 'smo') {
            // 1. Total Requests (All time)
            $stats['total'] = Requisition::count();

            // 2. Pending Requests (Active in any stage of approval)
            $stats['pending'] = Requisition::whereNotIn('status', ['released', 'rejected'])->count();

            // 3. Near Deadline (Approved for > 48hrs but not released)
            $stats['urgent'] = Requisition::whereIn('status', ['approved_president', 'approved_vp'])
                ->where('updated_at', '<=', now()->subDays(2))
                ->count();

            // 4. Monthly Distribution Value
            $stats['monthly_value'] = Requisition::where('status', 'released')
                ->whereMonth('updated_at', now()->month)
                ->sum('grand_total');

            // Left Column: All Recent Requests from everyone
            $allStaffRequests = Requisition::with(['items', 'user'])->latest()->take(8)->get();

            // Right Column: Mini Notification Feed
            $recentActivity = \App\Models\Notification::where('user_id', $user->id)->latest()->take(5)->get();
        }

        return view('dashboard', compact(
            'activeCount', 'approvedLast24h', 'recentRequests',
            'stats', 'allStaffRequests', 'recentActivity'
    ));
})->middleware(['auth'])->name('dashboard');

    // EMPLOYEE REQUEST ROUTES
    Route::middleware(['auth'])->group(function () {
        Route::get('/requests', [RequestController::class, 'index'])->name('requests.index');
        Route::post('/requests', [RequestController::class, 'store'])->name('requests.store');
    });

    // SMO INVENTORY & USER MANAGEMENT ROUTES
    Route::middleware(['auth', 'role:smo'])->group(function () {
        // Inventory Management
        Route::get('/inventory', [SupplyController::class, 'index'])->name('inventory.index');
        Route::get('/inventory/create', [SupplyController::class, 'create'])->name('inventory.create');
        Route::post('/inventory', [SupplyController::class, 'store'])->name('inventory.store');

        // --- ADDED THESE INVENTORY ROUTES ---
        Route::get('/inventory/{id}/edit', [SupplyController::class, 'edit'])->name('inventory.edit');
        Route::patch('/inventory/{id}', [SupplyController::class, 'update'])->name('inventory.update');
        Route::delete('/inventory/{id}', [SupplyController::class, 'destroy'])->name('inventory.destroy');
        // -------------------------------------

        // USER ACCESS MANAGEMENT
        Route::get('/admin/users/pending', [ProfileController::class, 'pendingUsers'])->name('admin.users.pending');
        Route::post('/admin/users/{id}/approve', [ProfileController::class, 'approveUser'])->name('admin.users.approve');
    });

    // PROFILE ROUTES (Standard Laravel)
    Route::middleware('auth')->group(function () {
        Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
        Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    });

    require __DIR__.'/auth.php';

    // ADMIN APPROVAL ROUTES
    Route::middleware(['auth', 'role:dept_head,vp_finance,vp_admin,provost,president,smo'])->group(function () {
        Route::get('/admin/approvals', [RequestController::class, 'adminIndex'])->name('admin.approvals');
        Route::post('/admin/requests/{id}/status', [RequestController::class, 'updateStatus'])->name('admin.status.update');
        Route::post('/admin/requests/{id}/release', [RequestController::class, 'releaseRequest'])->name('admin.requests.release');
    });

    Route::get('/notifications', [RequestController::class, 'notifications'])->name('notifications');

    // Change the submission route to point to the new Requisition Controller
    Route::post('/requisitions', [RequisitionController::class, 'store'])->name('requisitions.store');


    Route::get('/requests/{id}/download', [RequestController::class, 'downloadPDF'])->name('requests.download');

    // Route for the "Details" / Shopee-style tracker page
    Route::get('/requests/{id}', [RequestController::class, 'show'])->name('requests.show');


    Route::get('/api/latest-notification', [RequestController::class, 'getLatestNotification']);


    // Requisition Management (Edit/Delete)
    Route::get('/requisitions/{id}/edit', [RequisitionController::class, 'edit'])->name('requisitions.edit');
    Route::patch('/requisitions/{id}', [RequisitionController::class, 'update'])->name('requisitions.update');
    Route::delete('/requisitions/{id}', [RequisitionController::class, 'destroy'])->name('requisitions.destroy');

    //Statistics and Reports Page
    Route::get('/admin/reports', [App\Http\Controllers\ReportController::class, 'index'])->name('admin.reports');

    //Archive Page route
    Route::get('/admin/archive', [App\Http\Controllers\ArchiveController::class, 'index'])->name('admin.archive');
