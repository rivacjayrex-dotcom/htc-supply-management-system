<x-app-layout>
    <x-slot name="header">
        <span style="color: var(--htc-green);">GoodHoly,</span> {{ Auth::user()->name }}!
    </x-slot>

    <div class="mb-4">
        <p class="text-muted">Here's an overview of your requests</p>
    </div>

    <!-- START URGENT ALERT -->
    @if(Auth::user()->isSMO())
        <div class="col-md-12 mb-4">
            <div class="card border-0 shadow-sm bg-danger text-white p-4 rounded-4">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="fw-bold m-0">Urgent: Deadline Warning</h5>
                        <p class="small mb-0 opacity-75">There are {{ $urgentCount }} requests nearing the 3-day processing limit.</p>
                    </div>
                    <a href="{{ route('admin.approvals') }}" class="btn btn-light btn-sm fw-bold">View Queue</a>
                </div>
            </div>
        </div>
    @endif

    @if(Auth::user()->role == 'smo' && $lowStockCount > 0)
        <div class="col-md-12 mb-4">
            <div class="card border-0 shadow-sm bg-warning text-dark p-4 rounded-4">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="fw-bold m-0">📦 Low Stock Alert</h5>
                        <p class="small mb-0 opacity-75">There are {{ $lowStockCount }} items in your inventory with 10 or fewer units remaining.</p>
                    </div>
                    <a href="{{ route('inventory.index') }}" class="btn btn-dark btn-sm fw-bold px-4">Manage Stock</a>
                </div>
            </div>
        </div>
    @endif

    <!-- STATS CARDS (Bootstrap Row/Col) -->
    <div class="row g-4 mb-5">
        <div class="col-md-6">
            <div class="card border-0 shadow-sm p-4 rounded-4">
                <small class="text-uppercase fw-bold text-muted mb-2 d-block">Active Requests</small>
                <div class="d-flex justify-content-between align-items-center">
                    <h2 class="display-5 fw-black m-0">{{ $activeCount }}</h2>
                    <div class="p-3 rounded-circle" style="background-color: #f0f7f1; color: #144521;">
                        📦
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card border-0 shadow-sm p-4 rounded-4">
                <small class="text-uppercase fw-bold text-muted mb-2 d-block">Approved in last 24hrs</small>
                <div class="d-flex justify-content-between align-items-center">
                    <h2 class="display-5 fw-black m-0 text-success">{{ $approvedLast24h }}</h2>
                    <div class="p-3 rounded-circle bg-light text-success">
                        ✅
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- RECENT REQUESTS TABLE -->
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="card-header bg-white border-bottom p-4 d-flex justify-content-between align-items-center">
            <h5 class="m-0 fw-bold">Recent Request Status</h5>
            <a href="{{ route('requests.index') }}" class="text-decoration-none fw-bold" style="color: #144521;">View All History →</a>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light text-muted small text-uppercase">
                    <tr>
                        <th class="ps-4 py-3">Item Name</th>
                        <th class="py-3">Date Requested</th>
                        <th class="py-3">Status</th>
                        <th class="pe-4 py-3 text-end">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentRequests as $request)
                    <tr>
                        <td class="ps-4 fw-semibold">
                            <!-- Show first item name + count of others -->
                            {{ $request->items->first()->item_name ?? 'Empty Request' }}
                            @if($request->items->count() > 1)
                                <span class="text-muted small"> (+{{ $request->items->count() - 1 }} more)</span>
                            @endif
                        </td>
                        <td class="text-muted">{{ $request->created_at->format('M d, Y') }}</td>
                        <td>
                            @php
                                $badgeClass = match($request->status) {
                                    'approved' => 'bg-success-subtle text-success',
                                    'rejected' => 'bg-danger-subtle text-danger',
                                    default => 'bg-warning-subtle text-warning'
                                };
                            @endphp
                            <span class="badge {{ $badgeClass }} text-uppercase px-3 py-2">
                                {{ $request->status }}
                            </span>
                        </td>
                        <td class="pe-4 text-end">
                            <a href="{{ route('requests.show', $request->id) }}" class="btn btn-sm fw-bold" style="color: #144521;">Details</a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="text-center py-5 text-muted italic">No recent requests found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
