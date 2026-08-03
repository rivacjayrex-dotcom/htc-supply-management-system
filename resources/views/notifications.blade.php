<x-app-layout>
    <x-slot name="header">
        {{ __('Notification Center') }}
    </x-slot>

    <div class="container-fluid py-2">
        <div class="row g-4">

            <!-- LEFT SIDE: CONTEXTUAL SIDEBAR (Col 4) -->
            <div class="col-lg-4">
                <div class="sticky-top" style="top: 100px;">

                    @if(Auth::user()->role == 'smo')
                        <!-- SMO VIEW: CRITICAL DEADLINES -->
                        <div class="d-flex align-items-center mb-3">
                            <i data-lucide="alert-triangle" class="text-danger me-2" style="width: 20px;"></i>
                            <h6 class="fw-bold mb-0 uppercase tracking-widest text-danger" style="font-size: 12px;">Critical Deadlines</h6>
                        </div>
                        @forelse($criticalRequests as $req)
                            <div class="card border-0 shadow-sm rounded-4 mb-3 border-start border-4 border-danger animate-pulse">
                                <div class="card-body p-3">
                                    <div class="d-flex justify-content-between mb-2">
                                        <span class="badge bg-danger-subtle text-danger rounded-pill px-2" style="font-size: 9px;">ACTION REQUIRED</span>
                                        <small class="text-muted fw-bold">#{{ $req->id }}</small>
                                    </div>
                                    <h6 class="fw-bold mb-1 small">{{ $req->items->first()->item_name }}...</h6>
                                    <p class="text-muted mb-3" style="font-size: 11px;">This requisition requires immediate release.</p>
                                    <a href="{{ route('admin.approvals') }}" class="btn btn-danger btn-sm w-100 fw-bold rounded-3">Process Now</a>
                                </div>
                            </div>
                        @empty
                            <div class="card border-0 bg-white shadow-sm rounded-4 p-4 text-center">
                                <p class="text-muted small mb-0">No critical deadlines.</p>
                            </div>
                        @endforelse

                    @else
                        <!-- EMPLOYEE/STAFF VIEW: PERSONAL SUMMARY -->
                        <div class="d-flex align-items-center mb-3">
                            <i data-lucide="bar-chart-2" class="text-success me-2" style="width: 20px;"></i>
                            <h6 class="fw-bold mb-0 uppercase tracking-widest text-dark" style="font-size: 12px;">Requisition Summary</h6>
                        </div>

                        <div class="card border-0 shadow-sm rounded-4 bg-white p-4 mb-4">
                            <div class="row text-center">
                                <div class="col-4 border-end">
                                    <div class="fw-bold fs-5 text-dark">{{ $personalStats['total'] }}</div>
                                    <div class="text-muted" style="font-size: 9px; text-transform: uppercase;">Total</div>
                                </div>
                                <div class="col-4 border-end">
                                    <div class="fw-bold fs-5 text-warning">{{ $personalStats['pending'] }}</div>
                                    <div class="text-muted" style="font-size: 9px; text-transform: uppercase;">Pending</div>
                                </div>
                                <div class="col-4">
                                    <div class="fw-bold fs-5 text-success">{{ $personalStats['released'] }}</div>
                                    <div class="text-muted" style="font-size: 9px; text-transform: uppercase;">Released</div>
                                </div>
                            </div>
                        </div>

                        <!-- HTC PROCUREMENT GUIDE (Unique for Staff) -->
                        <div class="card border-0 shadow-sm rounded-4 bg-success text-white p-4">
                            <h6 class="fw-bold mb-2"><i data-lucide="help-circle" class="me-1" style="width: 16px;"></i> Quick Guide</h6>
                            <div class="small opacity-75 mb-3">Understanding the HTC Process:</div>

                            <ul class="list-unstyled mb-0" style="font-size: 11px;">
                                <li class="mb-2 d-flex">
                                    <i data-lucide="check-circle" class="me-2" style="width: 12px;"></i>
                                    <span><strong>Minor Tier:</strong> Approved by Dept Head and VP Finance only.</span>
                                </li>
                                <li class="mb-2 d-flex">
                                    <i data-lucide="check-circle" class="me-2" style="width: 12px;"></i>
                                    <span><strong>Major Tier:</strong> Requires School Provost and President signatures.</span>
                                </li>
                                <li class="d-flex">
                                    <i data-lucide="check-circle" class="me-2" style="width: 12px;"></i>
                                    <span><strong>Fulfillment:</strong> Visit the SMO once your status turns "Released".</span>
                                </li>
                            </ul>
                        </div>
                    @endif

                </div>
            </div>

            <!-- RIGHT SIDE: ACTIVITY FEED (Col 8) -->
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                    <div class="card-header bg-white border-0 p-4 pb-0">
                        <h5 class="fw-bold mb-3">System Activity</h5>

                        <!-- TAB NAVIGATION -->
                        <ul class="nav nav-tabs border-0 gap-4" id="noteTabs" role="tablist">
                            <li class="nav-item">
                                <button class="nav-link active border-0 px-0 fw-bold small text-uppercase tracking-wider" id="all-tab" data-bs-toggle="tab" data-bs-target="#all">All Activity</button>
                            </li>
                            <li class="nav-item">
                                <button class="nav-link border-0 px-0 fw-bold small text-uppercase tracking-wider" id="approvals-tab" data-bs-toggle="tab" data-bs-target="#approvals">Approvals</button>
                            </li>
                            <li class="nav-item">
                                <button class="nav-link border-0 px-0 fw-bold small text-uppercase tracking-wider" id="deadlines-tab" data-bs-toggle="tab" data-bs-target="#deadlines">Deadlines</button>
                            </li>
                        </ul>
                    </div>

                    <div class="card-body p-0">
                        <div class="tab-content" id="noteTabsContent">

                            <!-- TAB 1: ALL ACTIVITY -->
                            <div class="tab-pane fade show active" id="all">
                                @include('notifications.list', ['notes' => $allNotes])
                            </div>

                            <!-- TAB 2: APPROVALS -->
                            <div class="tab-pane fade" id="approvals">
                                @include('notifications.list', ['notes' => $approvalNotes])
                            </div>

                            <!-- TAB 3: DEADLINES -->
                            <div class="tab-pane fade" id="deadlines">
                                @include('notifications.list', ['notes' => $deadlineNotes])
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .nav-tabs .nav-link { color: #94a3b8; transition: all 0.3s; border-bottom: 2px solid transparent !important; }
        .nav-tabs .nav-link.active { color: var(--htc-green) !important; border-bottom: 2px solid var(--htc-green) !important; background: transparent; }
        .animate-pulse { animation: pulse-red 2s infinite; }
        @keyframes pulse-red { 0% { box-shadow: 0 0 0 0 rgba(220, 53, 69, 0.4); } 70% { box-shadow: 0 0 0 10px rgba(220, 53, 69, 0); } 100% { box-shadow: 0 0 0 0 rgba(220, 53, 69, 0); } }
    </style>

    <script>
        // Re-initialize icons when switching tabs
        document.querySelectorAll('button[data-bs-toggle="tab"]').forEach(tabEl => {
            tabEl.addEventListener('shown.bs.tab', () => {
                lucide.createIcons();
            });
        });
    </script>

</x-app-layout>

