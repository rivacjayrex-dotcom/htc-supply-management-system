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

    // 1. Count Active Requisitions
    $activeCount = Requisition::where('user_id', $user->id)
        ->whereNotIn('status', ['released', 'rejected'])
        ->count();

    // 2. Count Approved/Released in last 24 hours
    $approvedLast24h = Requisition::where('user_id', $user->id)
        ->whereIn('status', ['approved_president', 'released'])
        ->where('updated_at', '>=', Carbon::now()->subDay())
        ->count();

    // 3. Get Recent Requisitions (with their items)
    $recentRequests = Requisition::with('items')
        ->where('user_id', $user->id)
        ->latest()
        ->take(5)
        ->get();

    $urgentCount = 0;
    $lowStockCount = 0;

    if ($user->role == 'smo') {
        $urgentCount = Requisition::whereIn('status', ['approved_president', 'approved_vp'])
            ->where('updated_at', '<=', now()->subDays(2))
            ->count();
        $lowStockCount = \App\Models\Supply::where('quantity', '<=', 10)->count();
    }

    return view('dashboard', compact('activeCount', 'approvedLast24h', 'recentRequests', 'urgentCount', 'lowStockCount'));

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
