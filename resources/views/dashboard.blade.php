<x-app-layout>
    <x-slot name="header">
        <div class="d-flex align-items-center">
            <span style="color: white; font-weight: 800; letter-spacing: 1px;">
                {{ Auth::user()->role == 'smo' ? 'SMO COMMAND CENTER' : 'USER DASHBOARD' }}
            </span>
        </div>
    </x-slot>

    <div class="container-fluid py-2">

        @if(Auth::user()->role == 'smo')
            <!-- ============================================================== -->
            <!-- SMO IN-CHARGE VIEW (AUGUST 20 OPTIMIZATION) -->
            <!-- ============================================================== -->

            <!-- ROW 1: CORE METRICS -->
            <div class="row g-3 mb-4">
                <!-- 1. Total Requests -->
                <div class="col-md-3 animate__animated animate__fadeInUp" style="animation-delay: 0.1s;">
                    <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100 border-bottom border-4 border-dark">
                        <small class="text-muted fw-bold uppercase tracking-widest" style="font-size: 9px;">Total Requests</small>
                        <div class="d-flex justify-content-between align-items-center mt-2" x-data="countUp({{ $stats['total'] }})">
                            <h3 class="fw-black mb-0 text-dark" x-text="current">0</h3>
                            <div class="p-2 rounded-3 bg-light text-dark">
                                <i data-lucide="database" style="width: 18px;"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 2. Pending Requests -->
                <div class="col-md-3 animate__animated animate__fadeInUp" style="animation-delay: 0.2s;">
                    <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100 border-bottom border-4 border-warning">
                        <small class="text-muted fw-bold uppercase tracking-widest" style="font-size: 9px;">Active / Pending</small>
                        <div class="d-flex justify-content-between align-items-center mt-2" x-data="countUp({{ $stats['pending'] }})">
                            <h3 class="fw-black mb-0 text-warning" x-text="current">0</h3>
                            <div class="p-2 rounded-3 bg-warning-subtle text-warning">
                                <i data-lucide="clock" style="width: 18px;"></i>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 3. NEAR DEADLINE (HIGH VISIBILITY) -->
                <div class="col-md-3 animate__animated animate__fadeInUp" style="animation-delay: 0.3s;">
                    <div class="card border-0 shadow-lg rounded-4 p-3 {{ $stats['urgent'] > 0 ? 'bg-orange text-white pulse-alert' : 'bg-white border-bottom border-4 border-light' }} h-100">
                        <small class="{{ $stats['urgent'] > 0 ? 'text-white-50' : 'text-muted' }} fw-bold uppercase tracking-widest" style="font-size: 9px;">Near Deadline</small>
                        <div class="d-flex justify-content-between align-items-center mt-2" x-data="countUp({{ $stats['urgent'] }})">
                            <h3 class="fw-black mb-0" x-text="current">0</h3>
                            <i data-lucide="alert-circle" style="width: 24px;"></i>
                        </div>
                        @if($stats['urgent'] > 0)
                            <div class="mt-2 fw-bold animate__animated animate__flash animate__infinite" style="font-size: 9px; letter-spacing: 0.5px;">
                                REQUIRES IMMEDIATE ACTION
                            </div>
                        @endif
                    </div>
                </div>

                <!-- 4. Monthly Distribution -->
                <div class="col-md-3 animate__animated animate__fadeInUp" style="animation-delay: 0.4s;">
                    <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100 border-bottom border-4 border-success">
                        <small class="text-muted fw-bold uppercase tracking-widest" style="font-size: 9px;">Monthly Distribution</small>
                        <div class="d-flex justify-content-between align-items-center mt-2">
                            <h3 class="fw-black mb-0 text-success">₱<span x-data="countUp({{ $stats['monthly_value'] }})" x-text="current.toLocaleString()">0</span></h3>
                            <div class="p-2 rounded-3 bg-success-subtle text-success">
                                <i data-lucide="trending-up" style="width: 18px;"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ROW 2: QUEUE & NOTIFICATIONS -->
            <div class="row g-4">
                <!-- LEFT: INSTITUTIONAL QUEUE (Col 8) -->
                <div class="col-lg-8 animate__animated animate__fadeInLeft">
                    <div class="card border-0 shadow-sm rounded-4 overflow-hidden h-100 bg-white">
                        <div class="card-header bg-white border-0 p-4 pb-2">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h6 class="fw-bold mb-0">Institutional Queue Management</h6>
                                <div class="card-header bg-white border-0 p-0 d-flex align-items-center gap-2">
                                    <a href="{{ route('requests.index') }}" class="text-decoration-none fw-bold small text-success">View Full Requisition →</a>
                                </div>
                                <div class="d-flex gap-2">
                                    @if(request()->anyFilled(['status', 'dept', 'date_from']))
                                        <a href="{{ route('dashboard') }}" class="btn btn-xs btn-outline-danger rounded-pill px-3" style="font-size: 9px;">Clear Filters</a>
                                    @endif
                                </div>
                            </div>

                            <!-- ADVANCED MULTI-FILTER TOOLBAR -->
                            <form action="{{ route('dashboard') }}" method="GET" class="row g-2 pb-3 border-bottom">
                                <div class="col-md-3">
                                    <select name="status" class="form-select form-select-sm border-0 bg-light rounded-3 shadow-none" onchange="this.form.submit()" style="font-size: 11px;">
                                        <option value="">All Statuses</option>
                                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>🟡 Pending Dept</option>
                                        <option value="approved_vp" {{ request('status') == 'approved_vp' ? 'selected' : '' }}>🔵 Pending Release</option>
                                        <option value="near_deadline" {{ request('status') == 'near_deadline' ? 'selected' : '' }}>🟠 Near Deadline</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <select name="dept" class="form-select form-select-sm border-0 bg-light rounded-3 shadow-none" onchange="this.form.submit()" style="font-size: 11px;">
                                        <option value="">All Departments</option>
                                        <option value="CETE" {{ request('dept') == 'CETE' ? 'selected' : '' }}>CETE</option>
                                        <option value="CTE" {{ request('dept') == 'CTE' ? 'selected' : '' }}>CTE</option>
                                        <option value="CBA" {{ request('dept') == 'CBA' ? 'selected' : '' }}>CBA</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <div class="input-group input-group-sm">
                                        <input type="date" name="date_from" value="{{ request('date_from') }}" class="form-control border-0 bg-light rounded-start-3" onchange="this.form.submit()" style="font-size: 10px;">
                                        <input type="date" name="date_to" value="{{ request('date_to') }}" class="form-control border-0 bg-light rounded-end-3" onchange="this.form.submit()" style="font-size: 10px;">
                                    </div>
                                </div>
                                <div class="col-md-2 text-end">
                                    <select name="order" class="form-select form-select-sm border-0 bg-light rounded-3 shadow-none" onchange="this.form.submit()" style="font-size: 10px;">
                                        <option value="desc" {{ request('order') == 'desc' ? 'selected' : '' }}>Newest</option>
                                        <option value="asc" {{ request('order') == 'asc' ? 'selected' : '' }}>Oldest</option>
                                    </select>
                                </div>
                            </form>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="bg-light text-muted uppercase" style="font-size: 9px;">
                                    <tr>
                                        <th class="ps-4 py-3">Requestor</th>
                                        <th>Primary Item</th>
                                        <th class="text-center">Status</th>
                                        <th class="pe-4 text-end">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($allStaffRequests as $req)
                                    @php
                                        $isNearDeadline = $req->updated_at <= now()->subDays(2) && !in_array($req->status, ['released', 'rejected']);
                                        $statusClass = 'status-approved';
                                        if($req->status == 'pending') $statusClass = 'status-pending';
                                        if($req->status == 'released') $statusClass = 'status-released';
                                        if($req->status == 'rejected') $statusClass = 'status-rejected';
                                        if($isNearDeadline) $statusClass = 'status-deadline';
                                    @endphp
                                    <tr class="{{ $isNearDeadline ? 'row-near-deadline' : '' }}">
                                        <td class="ps-4">
                                            <div class="fw-bold text-dark" style="font-size: 11px;">{{ $req->user->name }}</div>
                                            <div class="text-muted" style="font-size: 9px;">{{ $req->user->department }}</div>
                                        </td>
                                        <td class="small">
                                            <div class="fw-semibold text-dark">{{ $req->items->first()->item_name ?? 'N/A' }}</div>
                                            @if($req->items->count() > 1) <small class="text-primary fw-bold" style="font-size: 9px;">+{{ $req->items->count() - 1 }} others</small> @endif
                                        </td>
                                        <td class="text-center">
                                            <span class="badge {{ $statusClass }} text-uppercase px-3 py-2 rounded-pill" style="font-size: 8px;">
                                                {{ str_replace('_', ' ', $req->status) }}
                                            </span>
                                        </td>
                                        <td class="pe-4 text-end">
                                            <a href="{{ route('requests.show', $req->id) }}" class="btn btn-sm btn-light border rounded-circle"><i data-lucide="eye" style="width:12px"></i></a>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr><td colspan="4" class="text-center py-5 text-muted small">No requisitions match filters.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- RIGHT: MINI NOTIFICATION CENTER (Col 4) -->
                <div class="col-lg-4 animate__animated animate__fadeInRight">
                    <div class="card border-0 shadow-sm rounded-4 h-100 bg-white">
                        <div class="card-header bg-white border-0 p-4 pb-0 d-flex justify-content-between align-items-center">
                            <h6 class="fw-bold mb-0">System Alerts</h6>
                            <a href="{{ route('notifications') }}" class="btn btn-success btn-sm rounded-pill px-3 fw-bold shadow-sm" style="font-size: 10px; background-color: var(--htc-green);">
                                <i data-lucide="bell" class="me-1" style="width:12px; vertical-align: middle;"></i> View All
                            </a>
                        </div>
                        <div class="card-body p-4">
                            <div class="mini-alert-feed">
                                @forelse($recentActivity as $activity)
                                <div class="d-flex align-items-start mb-4 border-bottom border-light pb-3">
                                    <div class="p-2 rounded-3 bg-light me-3 text-{{ $activity->type }}">
                                        <i data-lucide="{{ $activity->icon }}" style="width: 14px;"></i>
                                    </div>
                                    <div style="flex: 1;">
                                        <div class="fw-bold text-dark" style="font-size: 11px; line-height: 1.1;">{{ $activity->title }}</div>
                                        <div class="text-muted mt-1" style="font-size: 10px; line-height: 1.3;">{{ Str::limit($activity->message, 60) }}</div>
                                        <small class="text-muted italic d-block mt-1" style="font-size: 9px;">{{ $activity->created_at->diffForHumans() }}</small>
                                    </div>
                                </div>
                                @empty
                                <div class="text-center py-5 text-muted small italic">No recent alerts.</div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        @else
            <!-- ============================================================== -->
            <!-- EMPLOYEE & STAFF VIEW (ONLY PENDING) -->
            <!-- ============================================================== -->
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
            <div class="card-header bg-white border-0 p-4">
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
                    <div>
                        <h6 class="fw-bold mb-0">Institutional Queue Management</h6>
                        <p class="text-muted extra-small mb-0">Apply filters to prioritize urgent requisitions.</p>
                    </div>

                    <!-- FILTER & SORT TOOLBAR -->
                    <div class="d-flex gap-2">
                        <!-- Filter Dropdown -->
                        <div class="dropdown">
                            <button class="btn btn-sm btn-light border rounded-pill px-3 dropdown-toggle fw-bold" type="button" data-bs-toggle="dropdown">
                                <i data-lucide="filter" class="me-1" style="width:12px"></i>
                                {{ request('status') ? 'Filter: '.ucfirst(str_replace('_', ' ', request('status'))) : 'All Active' }}
                            </button>
                            <ul class="dropdown-menu shadow-sm border-0 small">
                                <li><a class="dropdown-item" href="{{ route('dashboard') }}">All Active</a></li>
                                <li><a class="dropdown-item text-danger fw-bold" href="{{ route('dashboard', ['status' => 'near_deadline']) }}">⚠️ Near Deadline</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="{{ route('dashboard', ['status' => 'pending']) }}">Pending Dept Head</a></li>
                                <li><a class="dropdown-item" href="{{ route('dashboard', ['status' => 'approved_vp']) }}">Pending VP/SMO</a></li>
                            </ul>
                        </div>

                        <!-- Sort Dropdown -->
                        <div class="dropdown">
                            <button class="btn btn-sm btn-light border rounded-pill px-3 dropdown-toggle fw-bold" type="button" data-bs-toggle="dropdown">
                                <i data-lucide="arrow-up-down" class="me-1" style="width:12px"></i> Sort
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 small">
                                <li class="dropdown-header fw-bold text-uppercase" style="font-size: 9px;">Request Date</li>
                                <li><a class="dropdown-item" href="{{ route('dashboard', array_merge(request()->query(), ['sort' => 'created_at', 'order' => 'desc'])) }}">Newest to Oldest</a></li>
                                <li><a class="dropdown-item" href="{{ route('dashboard', array_merge(request()->query(), ['sort' => 'created_at', 'order' => 'asc'])) }}">Oldest to Newest</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li class="dropdown-header fw-bold text-uppercase" style="font-size: 9px;">Urgency (Approval Date)</li>
                                <li><a class="dropdown-item" href="{{ route('dashboard', array_merge(request()->query(), ['sort' => 'updated_at', 'order' => 'asc'])) }}">Oldest Approval First</a></li>
                                <li><a class="dropdown-item" href="{{ route('dashboard', array_merge(request()->query(), ['sort' => 'updated_at', 'order' => 'desc'])) }}">Newest Approval First</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
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
        .bg-orange { background-color: #fd7e14 !important; }
        .status-pending { background-color: #fff3cd; color: #856404; border: 1px solid #ffeeba; }
        .status-approved { background-color: #d1ecf1; color: #0c5460; border: 1px solid #bee5eb; }
        .status-released { background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .status-rejected { background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        .status-deadline { background-color: #fff3cd; color: #fd7e14; border: 2px solid #fd7e14; font-weight: 800; }
        .row-near-deadline { background-color: rgba(253, 126, 20, 0.05) !important; border-left: 4px solid #fd7e14 !important; }
        .pulse-alert { animation: pulse-red 2s infinite; border: 2px solid #fff; }
        @keyframes pulse-red { 0% { box-shadow: 0 0 0 0 rgba(253, 126, 20, 0.7); } 70% { box-shadow: 0 0 0 15px rgba(253, 126, 20, 0); } 100% { box-shadow: 0 0 0 0 rgba(253, 126, 20, 0); } }
        .mini-alert-feed { max-height: 400px; overflow-y: auto; }
        .btn-xs { padding: 4px 8px; font-size: 10px; }
    </style>
</x-app-layout>
