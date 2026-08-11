<x-app-layout>
    <x-slot name="header">
        <span style="color: htc-green; font-weight: 800;">GoodHoly, {{ Auth::user()->name }}!</span>
    </x-slot>

    <div class="container-fluid py-2">
        @if(Auth::user()->role == 'smo')
            <!-- ROW 1: CORE METRICS -->
            <div class="row g-3 mb-4">
                <!-- Total Requests -->
                <div class="col-md-3">
                    <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100">
                        <small class="text-muted fw-bold uppercase tracking-widest" style="font-size: 10px;">Total Requests</small>
                        <div class="d-flex justify-content-between align-items-center mt-2">
                            <h3 class="fw-black mb-0 text-dark">{{ $stats['total'] }}</h3>
                            <i data-lucide="database" class="text-muted" style="width: 20px;"></i>
                        </div>
                    </div>
                </div>

                <!-- Pending Requests -->
                <div class="col-md-3">
                    <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100">
                        <small class="text-muted fw-bold uppercase tracking-widest" style="font-size: 10px;">Pending Requests</small>
                        <div class="d-flex justify-content-between align-items-center mt-2">
                            <h3 class="fw-black mb-0 text-primary">{{ $stats['pending'] }}</h3>
                            <i data-lucide="clock" class="text-primary" style="width: 20px;"></i>
                        </div>
                    </div>
                </div>

                <!-- NEAR DEADLINE (HIGHLY NOTICEABLE) -->
                <div class="col-md-3">
                    <div class="card border-0 shadow-lg rounded-4 p-3 {{ $stats['urgent'] > 0 ? 'bg-danger text-white pulse-alert' : 'bg-white' }} h-100">
                        <small class="{{ $stats['urgent'] > 0 ? 'text-white-50' : 'text-muted' }} fw-bold uppercase tracking-widest" style="font-size: 10px;">Near Deadline</small>
                        <div class="d-flex justify-content-between align-items-center mt-2">
                            <h3 class="fw-black mb-0">{{ $stats['urgent'] }}</h3>
                            <i data-lucide="alert-circle" style="width: 24px;"></i>
                        </div>
                        @if($stats['urgent'] > 0)
                            <div class="mt-2 fw-bold" style="font-size: 11px; letter-spacing: 0.5px;">REQUIRES IMMEDIATE ACTION</div>
                        @endif
                    </div>
                </div>

                <!-- Monthly Distribution -->
                <div class="col-md-3">
                    <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100">
                        <small class="text-muted fw-bold uppercase tracking-widest" style="font-size: 10px;">Monthly Distribution</small>
                        <div class="d-flex justify-content-between align-items-center mt-2">
                            <h3 class="fw-black mb-0 text-success">₱{{ number_format($stats['monthly_value'], 0) }}</h3>
                            <div class="p-2 rounded-circle bg-success-subtle text-success">
                                <i data-lucide="trending-up" style="width: 16px;"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ROW 2: DETAILS & NOTIFICATIONS -->
            <div class="row g-4">
                <!-- LEFT: ALL STAFF REQUESTS -->
                <div class="col-lg-8">
                    <div class="card border-0 shadow-sm rounded-4 overflow-hidden h-100">
                        <div class="card-header bg-white border-0 p-4 d-flex justify-content-between align-items-center">
                            <h6 class="fw-bold mb-0">Recent Institutional Requests</h6>
                            <a href="{{ route('admin.approvals') }}" class="btn btn-sm btn-light border rounded-pill px-3 fw-bold" style="font-size: 11px;">View All Log</a>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="bg-light text-muted small uppercase">
                                    <tr>
                                        <th class="ps-4">Requestor</th>
                                        <th>Items</th>
                                        <th>Tier</th>
                                        <th>Total</th>
                                        <th class="pe-4">Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($allStaffRequests as $req)
                                    <tr style="cursor: pointer;" onclick="window.location='{{ route('requests.show', $req->id) }}'">
                                        <td class="ps-4">
                                            <div class="fw-bold small text-dark">{{ $req->user->name }}</div>
                                            <div class="text-muted" style="font-size: 9px;">{{ $req->user->department ?? 'General' }}</div>
                                        </td>
                                        <td class="small">
                                            {{ $req->items->first()->item_name }}
                                            @if($req->items->count() > 1) <span class="text-primary font-bold">(+{{ $req->items->count() - 1 }})</span> @endif
                                        </td>
                                        <td>
                                            <span class="badge {{ $req->request_type == 'major' ? 'bg-warning-subtle text-warning border border-warning' : 'bg-info-subtle text-info border border-info' }} rounded-pill" style="font-size: 9px;">{{ strtoupper($req->request_type) }}</span>
                                        </td>
                                        <td class="fw-bold small">₱{{ number_format($req->grand_total, 2) }}</td>
                                        <td class="pe-4">
                                            <span class="badge {{ $req->status == 'released' ? 'bg-success' : 'bg-secondary' }} rounded-pill" style="font-size: 9px;">{{ strtoupper($req->status) }}</span>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr><td colspan="5" class="text-center py-5 text-muted">No institutional requests found.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- RIGHT: MINI NOTIFICATION CENTER -->
                <div class="col-lg-4">
                    <div class="card border-0 shadow-sm rounded-4 h-100 bg-white">
                        <div class="card-header bg-white border-0 p-4 pb-0 d-flex justify-content-between align-items-center">
                            <h6 class="fw-bold mb-0">System Alerts</h6>
                            <a href="{{ route('notifications') }}" class="btn btn-link p-0 text-decoration-none small fw-bold text-success">
                                Go to Center <i data-lucide="arrow-right" class="ms-1" style="width: 12px;"></i>
                            </a>
                        </div>
                        <div class="card-body p-4">
                            <div class="mini-alert-feed">
                                @forelse($recentActivity as $activity)
                                <div class="d-flex align-items-start mb-4">
                                    <div class="p-2 rounded-3 bg-light me-3 text-{{ $activity->type }}">
                                        <i data-lucide="{{ $activity->icon }}" style="width: 16px;"></i>
                                    </div>
                                    <div style="flex: 1;">
                                        <div class="fw-bold small text-dark" style="line-height: 1.1;">{{ $activity->title }}</div>
                                        <div class="text-muted mt-1" style="font-size: 10px; line-height: 1.3;">{{ Str::limit($activity->message, 60) }}</div>
                                        <small class="text-muted italic" style="font-size: 9px;">{{ $activity->created_at->diffForHumans() }}</small>
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
            <!-- Employee Dashboard stays same -->
        @endif
    </div>

    <style>
        .pulse-alert {
            animation: pulse-red 2s infinite;
            border: 2px solid #fff;
        }
        @keyframes pulse-red {
            0% { box-shadow: 0 0 0 0 rgba(220, 53, 69, 0.7); }
            70% { box-shadow: 0 0 0 15px rgba(220, 53, 69, 0); }
            100% { box-shadow: 0 0 0 0 rgba(220, 53, 69, 0); }
        }
        .table-hover tbody tr:hover { background-color: #f8fafc; }
    </style>
</x-app-layout>
