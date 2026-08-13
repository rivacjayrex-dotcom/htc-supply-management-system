<x-app-layout>
    <x-slot name="header">
        <div class="d-flex align-items-center">
            <span style="color: htc-green; font-weight: 800;">
                GoodHoly, {{ Auth::user()->name }}!
            </span>
        </div>
    </x-slot>

    <div class="container-fluid py-2">

        @if(Auth::user()->role == 'smo')
            <!-- ============================================================== -->
            <!-- SMO IN-CHARGE VIEW -->
            <!-- ============================================================== -->

            <div class="mb-4 animate__animated animate__fadeIn">
                <p class="text-muted small">Here is an overview of your activities.</p>
            </div>

            <!-- ROW 1: CORE METRICS -->
            <div class="row g-3 mb-4">
                <!-- Total Requests -->
                <div class="col-md-3 animate__animated animate__fadeInUp" style="animation-delay: 0.1s;">
                    <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100">
                        <small class="text-muted fw-bold uppercase tracking-widest" style="font-size: 10px;">Total Requests</small>
                        <div class="d-flex justify-content-between align-items-center mt-2" x-data="countUp({{ $stats['total'] }})">
                            <h3 class="fw-black mb-0 text-dark" x-text="current">0</h3>
                            <i data-lucide="database" class="text-muted" style="width: 20px;"></i>
                        </div>
                    </div>
                </div>

                <!-- Pending Requests -->
                <div class="col-md-3 animate__animated animate__fadeInUp" style="animation-delay: 0.2s;">
                    <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100">
                        <small class="text-muted fw-bold uppercase tracking-widest" style="font-size: 10px;">Pending Requests</small>
                        <div class="d-flex justify-content-between align-items-center mt-2" x-data="countUp({{ $stats['pending'] }})">
                            <h3 class="fw-black mb-0 text-primary" x-text="current">0</h3>
                            <i data-lucide="clock" class="text-primary" style="width: 20px;"></i>
                        </div>
                    </div>
                </div>

                <!-- NEAR DEADLINE (NOTICEABLE) -->
                <div class="col-md-3 animate__animated animate__fadeInUp" style="animation-delay: 0.3s;">
                    <div class="card border-0 shadow-lg rounded-4 p-3 {{ $stats['urgent'] > 0 ? 'bg-danger text-white pulse-alert' : 'bg-white' }} h-100">
                        <small class="{{ $stats['urgent'] > 0 ? 'text-white-50' : 'text-muted' }} fw-bold uppercase tracking-widest" style="font-size: 10px;">Near Deadline</small>
                        <div class="d-flex justify-content-between align-items-center mt-2" x-data="countUp({{ $stats['urgent'] }})">
                            <h3 class="fw-black mb-0" x-text="current">0</h3>
                            <i data-lucide="alert-circle" style="width: 24px;"></i>
                        </div>
                        @if($stats['urgent'] > 0)
                            <div class="mt-2 fw-bold" style="font-size: 11px; letter-spacing: 0.5px;">REQUIRES IMMEDIATE ACTION</div>
                        @endif
                    </div>
                </div>

                <!-- Monthly Distribution -->
                <div class="col-md-3 animate__animated animate__fadeInUp" style="animation-delay: 0.4s;">
                    <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100">
                        <small class="text-muted fw-bold uppercase tracking-widest" style="font-size: 10px;">Monthly Distribution</small>
                        <div class="d-flex justify-content-between align-items-center mt-2">
                            <h3 class="fw-black mb-0 text-success">₱<span x-data="countUp({{ $stats['monthly_value'] }})" x-text="current.toLocaleString()">0</span></h3>
                            <div class="p-2 rounded-circle bg-success-subtle text-success">
                                <i data-lucide="trending-up" style="width: 16px;"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ROW 2: DETAILS & NOTIFICATIONS -->
            <div class="row g-4">
                <div class="col-lg-8 animate__animated animate__fadeInLeft">
                    <div class="card border-0 shadow-sm rounded-4 overflow-hidden h-100">
                        <div class="card-header bg-white border-0 p-4 d-flex justify-content-between align-items-center">
                            <h6 class="fw-bold mb-0">Active Institutional Queue</h6>
                            <a href="{{ route('admin.approvals') }}" class="btn btn-sm btn-light border rounded-pill px-3 fw-bold" style="font-size: 11px;">View Full Queue</a>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="bg-light small text-muted uppercase">
                                    <tr>
                                        <th class="ps-4">Requestor</th>
                                        <th>Items</th>
                                        <th>Status</th>
                                        <th class="pe-4 text-end">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($allStaffRequests as $req)
                                    <tr>
                                        <td class="ps-4">
                                            <div class="fw-bold small">{{ $req->user->name }}</div>
                                            <div class="text-muted" style="font-size: 9px;">{{ $req->user->department }}</div>
                                        </td>
                                        <td class="small">
                                            {{ $req->items->first()->item_name }}
                                            @if($req->items->count() > 1) <span class="text-primary">(+{{ $req->items->count() - 1 }} more)</span> @endif
                                        </td>
                                        <td><span class="badge bg-light text-dark border rounded-pill" style="font-size: 9px;">{{ strtoupper($req->status) }}</span></td>
                                        <td class="pe-4 text-end">
                                            <a href="{{ route('requests.show', $req->id) }}" class="btn btn-sm btn-light rounded-circle shadow-sm"><i data-lucide="eye" style="width:14px"></i></a>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr><td colspan="4" class="text-center py-5 text-muted">Queue is empty.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="col-lg-4 animate__animated animate__fadeInRight">
                    <div class="card border-0 shadow-sm rounded-4 h-100 bg-white">
                        <div class="card-header bg-white border-0 p-4 pb-0 d-flex justify-content-between align-items-center">
                            <h6 class="fw-bold mb-0">System Alerts</h6>
                            <a href="{{ route('notifications') }}" class="small text-decoration-none">Center →</a>
                        </div>
                        <div class="card-body p-4">
                            @forelse($recentActivity as $activity)
                                <div class="d-flex align-items-start mb-3 border-bottom pb-3">
                                    <div class="p-2 rounded-3 bg-light text-{{ $activity->type }} me-3">
                                        <i data-lucide="{{ $activity->icon }}" style="width:14px;"></i>
                                    </div>
                                    <div>
                                        <div class="fw-bold small" style="font-size: 11px;">{{ $activity->title }}</div>
                                        <div class="text-muted" style="font-size: 10px;">{{ Str::limit($activity->message, 45) }}</div>
                                    </div>
                                </div>
                            @empty
                                <p class="text-center text-muted small">No recent activity.</p>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>

        @else
            <!-- ============================================================== -->
            <!-- EMPLOYEE & ADMIN VIEW (REQUISITION TRACKING) -->
            <!-- ============================================================== -->

            <div class="mb-4 animate__animated animate__fadeIn">
                <p class="text-muted small">Here is an overview of your procurement activities.</p>
            </div>

            <div class="row g-4 mb-5">
                <!-- Active Requests -->
                <div class="col-md-4 animate__animated animate__fadeInUp" style="animation-delay: 0.1s;">
                    <div class="card border-0 shadow-sm p-4 rounded-4 bg-white h-100">
                        <small class="text-uppercase fw-bold text-muted mb-2 d-block tracking-widest" style="font-size: 10px;">Active Requisitions</small>
                        <div class="d-flex justify-content-between align-items-center" x-data="countUp({{ $activeCount }})">
                            <h2 class="display-6 fw-black m-0" x-text="current">0</h2>
                            <div class="p-3 rounded-circle bg-primary-subtle text-primary">
                                <i data-lucide="clock"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Approved in last 24h -->
                <div class="col-md-4 animate__animated animate__fadeInUp" style="animation-delay: 0.2s;">
                    <div class="card border-0 shadow-sm p-4 rounded-4 bg-white h-100">
                        <small class="text-uppercase fw-bold text-muted mb-2 d-block tracking-widest" style="font-size: 10px;">Approved (24h)</small>
                        <div class="d-flex justify-content-between align-items-center" x-data="countUp({{ $approvedLast24h }})">
                            <h2 class="display-6 fw-black m-0 text-success" x-text="current">0</h2>
                            <div class="p-3 rounded-circle bg-success-subtle text-success">
                                <i data-lucide="check-circle"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- RECENT REQUESTS TABLE -->
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden animate__animated animate__fadeInUp" style="animation-delay: 0.4s;">
                <div class="card-header bg-white border-bottom p-4 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 fw-bold">My Active Requisitions</h6>
                    <a href="{{ route('requests.index') }}" class="text-decoration-none fw-bold small text-success">View Full History →</a>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light text-muted small text-uppercase">
                            <tr>
                                <th class="ps-4 py-3">Item Name</th>
                                <th class="py-3">Date Submitted</th>
                                <th class="py-3">Current Status</th>
                                <th class="pe-4 py-3 text-end">Track</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentRequests as $request)
                            <tr>
                                <td class="ps-4">
                                    <div class="fw-bold text-dark">
                                        {{ $request->items->first()->item_name ?? 'Empty Request' }}
                                        @if($request->items->count() > 1) <span class="text-muted small"> (+{{ $request->items->count() - 1 }} more)</span> @endif
                                    </div>
                                </td>
                                <td class="text-muted small">{{ $request->created_at->format('M d, Y') }}</td>
                                <td>
                                    <span class="badge bg-success-subtle text-success border border-success rounded-pill px-3 py-2 text-uppercase" style="font-size: 9px;">
                                        {{ str_replace('_', ' ', $request->status) }}
                                    </span>
                                </td>
                                <td class="pe-4 text-end">
                                    <a href="{{ route('requests.show', $request->id) }}" class="btn btn-sm btn-light border rounded-pill px-3 shadow-sm fw-bold" style="font-size: 11px;">Track →</a>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="4" class="text-center py-5 text-muted italic">No recent requests found. Click "+ New Requisition" to start.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
    </div>

    <style>
        .pulse-alert { animation: pulse-red 2s infinite; border: 2px solid #fff; }
        @keyframes pulse-red { 0% { box-shadow: 0 0 0 0 rgba(220, 53, 69, 0.7); } 70% { box-shadow: 0 0 0 15px rgba(220, 53, 69, 0); } 100% { box-shadow: 0 0 0 0 rgba(220, 53, 69, 0); } }
    </style>
</x-app-layout>
