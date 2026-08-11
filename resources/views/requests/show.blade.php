<x-app-layout>
    <x-slot name="header">
        <span style="color: white; font-weight: 800;">REQUISITION DETAILS</span>
    </x-slot>

    <div class="container-fluid py-2">

        <!-- 1. NAVIGATION & PRIMARY ACTIONS ROW -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <a href="{{ route('dashboard') }}" class="btn btn-link text-decoration-none text-muted p-0 d-flex align-items-center">
                <i data-lucide="chevron-left" class="me-1" style="width:18px"></i>
                <span class="fw-bold small text-uppercase tracking-wider">Back to Dashboard</span>
            </a>

            <!-- EXPORT BUTTON MOVED HERE: More accessible and styled -->
            <a href="{{ route('requests.download', $request->id) }}" class="btn btn-htc px-4 rounded-pill shadow-sm d-flex align-items-center">
                <i data-lucide="file-down" class="me-2" style="width:18px"></i>
                EXPORT AS OFFICIAL PDF
            </a>
        </div>

        <div class="row g-4">
            <!-- LEFT COLUMN: REQUEST INFO & ITEMS -->
            <div class="col-lg-8">

                <!-- 2. REQUESTOR PROFILE CARD -->
                <div class="card border-0 shadow-sm rounded-4 p-4 mb-4 bg-white">
                    <div class="d-flex justify-content-between align-items-start">
                        <div class="d-flex align-items-center">
                            <div class="rounded-circle bg-success-subtle p-3 me-3">
                                <i data-lucide="user" class="text-success" style="width: 24px; height: 24px;"></i>
                            </div>
                            <div>
                                <h6 class="info-label mb-0">Official Requestor</h6>
                                <h5 class="fw-bold text-dark mb-0">{{ $request->user->name }}</h5>
                                <small class="text-muted">{{ $request->user->department }} • {{ $request->user->school_id }}</small>
                            </div>
                        </div>
                        <div class="text-end">
                            <div class="info-label">Requisition ID</div>
                            <div class="fw-black text-dark">#{{ str_pad($request->id, 5, '0', STR_PAD_LEFT) }}</div>
                            <span class="badge bg-dark px-3 rounded-pill mt-2" style="font-size: 9px;">{{ strtoupper($request->request_type) }} TIER</span>
                        </div>
                    </div>
                    <hr class="my-4 opacity-50">
                    <div class="px-2">
                        <h6 class="info-label">General Remarks / Purpose</h6>
                        <!-- We use the specific purpose column if available, else first item info -->
                        <p class="text-dark small mb-0">{{ $request->remarks ?? 'Institutional procurement for departmental supplies.' }}</p>
                    </div>
                </div>

                <!-- 3. TRACKING TIMELINE -->
                <div class="card border-0 shadow-sm rounded-4 p-4 mb-4 bg-white">
                    <h6 class="info-label mb-4">Real-time Approval Flow</h6>
                    <div class="px-4">
                        <div class="tracking-stepper">
                            <!-- Step 1: Submission -->
                            <div class="step-item completed">
                                <div class="step-icon"><i data-lucide="send"></i></div>
                                <div class="step-label">Submitted</div>
                            </div>

                            <!-- Step 2: Dept Head -->
                            @php
                                $isDeptDone = in_array($request->status, ['approved_dept', 'approved_vp', 'approved_provost', 'approved_president', 'released']);
                                $isDeptActive = ($request->status == 'pending');
                                $isDeptFlowing = ($request->status == 'approved_dept');
                            @endphp
                            <div class="step-item {{ $isDeptDone ? 'completed' : ($isDeptActive ? 'active' : '') }} {{ $isDeptFlowing ? 'in-progress' : '' }}">
                                <div class="step-icon"><i data-lucide="user-check"></i></div>
                                <div class="step-label">Dept. Head</div>
                            </div>

                            <!-- Step 3: VP Finance/Admin -->
                            @php
                                $isVPDone = in_array($request->status, ['approved_vp', 'approved_provost', 'approved_president', 'released']);
                                $isVPActive = ($request->status == 'approved_dept');
                                $isVPFlowing = ($request->status == 'approved_vp');
                            @endphp
                            <div class="step-item {{ $isVPDone ? 'completed' : ($isVPActive ? 'active' : '') }} {{ $isVPFlowing ? 'in-progress' : '' }}">
                                <div class="step-icon"><i data-lucide="shield-check"></i></div>
                                <div class="step-label">{{ $request->request_type == 'minor' ? 'VP Finance' : 'VP Admin' }}</div>
                            </div>

                            @if($request->request_type == 'major')
                                <!-- Step 4: President -->
                                @php
                                    $isPresDone = in_array($request->status, ['approved_president', 'released']);
                                    $isPresActive = ($request->status == 'approved_provost');
                                @endphp
                                <div class="step-item {{ $isPresDone ? 'completed' : ($isPresActive ? 'active' : '') }}">
                                    <div class="step-icon"><i data-lucide="award"></i></div>
                                    <div class="step-label">President</div>
                                </div>
                            @endif

                            <!-- Step 5: Released -->
                            <div class="step-item {{ $request->status == 'released' ? 'completed' : '' }}">
                                <div class="step-icon"><i data-lucide="package-check"></i></div>
                                <div class="step-label">Released</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 4. ITEM SUMMARY -->
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-white">
                    <div class="card-header bg-light border-0 p-4">
                        <h6 class="fw-bold mb-0">Itemized Requisition Summary</h6>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light small text-muted uppercase">
                                <tr>
                                    <th class="ps-4 py-3">Item & Specifications</th>
                                    <th class="text-center">Qty</th>
                                    <th class="text-end">Unit Price</th>
                                    <th class="pe-4 text-end">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($request->items as $item)
                                <tr>
                                    <td class="ps-4">
                                        <div class="fw-bold text-dark">{{ $item->item_name }}</div>
                                        <small class="text-muted">{{ $item->specifications }}</small>
                                    </td>
                                    <td class="text-center">{{ $item->quantity }} {{ $item->unit }}</td>
                                    <td class="text-end text-muted">₱{{ number_format($item->unit_price, 2) }}</td>
                                    <td class="pe-4 text-end fw-bold text-success">₱{{ number_format($item->subtotal, 2) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="bg-success-subtle">
                                <tr>
                                    <td colspan="3" class="ps-4 py-3 fw-bold text-uppercase small text-dark">Total Requisition Value</td>
                                    <td class="pe-4 py-3 text-end"><h4 class="fw-black text-success mb-0">₱{{ number_format($request->grand_total, 2) }}</h4></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>

            <!-- RIGHT COLUMN: ACTIVITY LOG -->
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm rounded-4 bg-white h-100">
                    <div class="card-header bg-white border-0 p-4">
                        <h6 class="fw-bold mb-0">Activity Audit Trail</h6>
                    </div>
                    <div class="card-body p-4 pt-0">
                        <div class="activity-log-stream">

                            <div class="activity-log-item pb-4 border-start ps-4 position-relative" style="border-left: 2px dashed #e2e8f0 !important;">
                                <div class="dot bg-success shadow-sm"></div>
                                <div class="fw-bold small text-dark">Requisition Created</div>
                                <p class="text-muted extra-small mb-0">{{ $request->created_at->format('F d, Y • h:i A') }}</p>
                                <small class="text-muted d-block mt-1">Logged by {{ $request->user->name }}</small>
                            </div>

                            @foreach($request->logs ?? [] as $log)
                            <div class="activity-log-item pb-4 border-start ps-4 position-relative" style="border-left: 2px dashed #e2e8f0 !important;">
                                <div class="dot bg-primary shadow-sm"></div>
                                <div class="fw-bold small text-dark">{{ $log->action }}</div>
                                <p class="text-muted extra-small mb-1">{{ $log->created_at->format('M d, Y • h:i A') }}</p>

                                @if($log->remarks)
                                    <div class="bg-light p-2 rounded-3 small text-muted italic">
                                        <i data-lucide="message-square" style="width:10px;" class="me-1"></i> "{{ $log->remarks }}"
                                    </div>
                                @endif
                            </div>
                            @endforeach

                            @if($request->status == 'released')
                            <div class="activity-log-item ps-4 position-relative">
                                <div class="dot bg-success shadow-sm"></div>
                                <div class="fw-bold small text-success">Order Released</div>
                                <p class="text-muted extra-small mb-0">{{ $request->updated_at->format('M d, Y • h:i A') }}</p>
                                <small class="text-muted">Fulfillment complete.</small>
                            </div>
                            @endif

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .info-label { font-size: 10px; font-weight: 800; text-transform: uppercase; color: #94a3b8; letter-spacing: 1px; margin-bottom: 5px; display: block;}
        .extra-small { font-size: 10px; }
        .activity-log-item .dot { width: 12px; height: 12px; border-radius: 50%; position: absolute; left: -7px; top: 5px; border: 2px solid white; }
        .btn-htc { background-color: var(--htc-green); color: white; font-weight: bold; }
        .btn-htc:hover { background-color: #0d2e16; color: white; transform: translateY(-1px); }
    </style>
</x-app-layout>
