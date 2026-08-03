<x-app-layout>
    <x-slot name="header">
        Track Request #{{ $request->id }}
    </x-slot>

    <div class="container-fluid">
        <!-- TOP ACTIONS -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <a href="{{ route('dashboard') }}" class="btn btn-link text-decoration-none text-muted p-0">
                <i data-lucide="arrow-left" class="me-1"></i> Back to Dashboard
            </a>
            <a href="{{ route('requests.download', $request->id) }}" class="btn btn-outline-dark fw-bold btn-sm shadow-sm px-3">
                <i data-lucide="file-text" class="me-2"></i> Download Requisition Slip
            </a>
        </div>

        <!-- TRACKING CARD -->
        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-body p-4 p-lg-5">
                <div class="tracking-stepper">
                    <div class="tracking-stepper">
                        <!-- 1. Submitted (Always Done) -->
                        <div class="step-item completed {{ $request->status == 'pending' ? 'in-progress' : '' }}">
                            <div class="step-icon"><i data-lucide="send"></i></div>
                            <div class="step-label">Submitted</div>
                        </div>

                        <!-- 2. Dept Head -->
                        @php
                            $isDeptDone = in_array($request->status, ['approved_dept', 'approved_vp', 'approved_provost', 'approved_president', 'released']);
                            $isDeptActive = ($request->status == 'pending');
                            $isDeptFlowing = ($request->status == 'approved_dept');
                        @endphp
                        <div class="step-item {{ $isDeptDone ? 'completed' : ($isDeptActive ? 'active' : '') }} {{ $isDeptFlowing ? 'in-progress' : '' }}">
                            <div class="step-icon"><i data-lucide="user-check"></i></div>
                            <div class="step-label">Dept. Head</div>
                        </div>

                        <!-- 3. VP Office (Finance for Minor, Admin for Major) -->
                        @php
                            $isVPDone = in_array($request->status, ['approved_vp', 'approved_provost', 'approved_president', 'released']);
                            $isVPActive = ($request->status == 'approved_dept');
                            // Flowing logic: If Minor, flow to Released. If Major, flow to Provost.
                            $isVPFlowing = ($request->status == 'approved_vp');
                        @endphp
                        <div class="step-item {{ $isVPDone ? 'completed' : ($isVPActive ? 'active' : '') }} {{ $isVPFlowing ? 'in-progress' : '' }}">
                            <div class="step-icon"><i data-lucide="shield-check"></i></div>
                            <div class="step-label">{{ $request->request_type == 'minor' ? 'VP Finance' : 'VP Admin' }}</div>
                        </div>

                        @if($request->request_type == 'major')
                            <!-- 4. Provost (MAJOR ONLY) -->
                            @php
                                $isProvostDone = in_array($request->status, ['approved_provost', 'approved_president', 'released']);
                                $isProvostActive = ($request->status == 'approved_vp');
                                $isProvostFlowing = ($request->status == 'approved_provost');
                            @endphp
                            <div class="step-item {{ $isProvostDone ? 'completed' : ($isProvostActive ? 'active' : '') }} {{ $isProvostFlowing ? 'in-progress' : '' }}">
                                <div class="step-icon"><i data-lucide="graduation-cap"></i></div>
                                <div class="step-label">Provost</div>
                            </div>

                            <!-- 5. President (MAJOR ONLY) -->
                            @php
                                $isPresDone = in_array($request->status, ['approved_president', 'released']);
                                $isPresActive = ($request->status == 'approved_provost');
                                $isPresFlowing = ($request->status == 'approved_president');
                            @endphp
                            <div class="step-item {{ $isPresDone ? 'completed' : ($isPresActive ? 'active' : '') }} {{ $isPresFlowing ? 'in-progress' : '' }}">
                                <div class="step-icon"><i data-lucide="award"></i></div>
                                <div class="step-label">President</div>
                            </div>
                        @endif

                        <!-- 6. Released (Final Goal) -->
                        <div class="step-item {{ $request->status == 'released' ? 'completed' : '' }} {{ ($request->status == 'approved_vp' && $request->request_type == 'minor') || ($request->status == 'approved_president') ? 'active' : '' }}">
                            <div class="step-icon"><i data-lucide="package-check"></i></div>
                            <div class="step-label">Released</div>
                        </div>
                    </div>
                    </div>
                <div>
            </div>
        </div>

        <div class="row">
            <!-- REQUEST DETAILS -->
            <div class="col-lg-12"> <!-- Expanded to full width to show the table clearly -->
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-header bg-white border-0 p-4 d-flex justify-content-between">
                        <h6 class="fw-bold mb-0">Items in this Requisition</h6>
                        <span class="badge bg-success-subtle text-success rounded-pill px-3">
                            Total: ₱{{ number_format($request->grand_total, 2) }}
                        </span>
                    </div>
                    <div class="table-responsive p-4">
                        <table class="table table-sm align-middle">
                            <thead class="text-muted small text-uppercase">
                                <tr>
                                    <th>Item & Specs</th>
                                    <th>Qty/Unit</th>
                                    <th class="text-end">Unit Price</th>
                                    <th class="text-end">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($request->items as $item)
                                <tr>
                                    <td>
                                        <div class="fw-bold">{{ $item->item_name }}</div>
                                        <small class="text-muted">{{ $item->specifications }}</small>
                                    </td>
                                    <td>{{ $item->quantity }} {{ $item->unit }}</td>
                                    <td class="text-end">₱{{ number_format($item->unit_price, 2) }}</td>
                                    <td class="text-end fw-bold">₱{{ number_format($item->subtotal, 2) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- STATUS LOG & REMARKS -->
            <div class="col-lg-5">
                <div class="card border-0 shadow-sm rounded-4 h-100">
                    <div class="card-header bg-white border-0 p-4 pb-0">
                        <h6 class="fw-bold mb-0">Signatory Remarks</h6>
                    </div>
                    <div class="card-body p-4 text-center d-flex flex-column justify-content-center">
                        @if($request->remarks)
                            <div class="p-4 bg-light rounded-4 border-start border-4 border-success text-start">
                                <i data-lucide="message-square" class="text-success mb-2" style="width: 20px;"></i>
                                <p class="mb-0 text-dark font-italic">"{{ $request->remarks }}"</p>
                            </div>
                        @else
                            <div class="py-4">
                                <i data-lucide="message-square-dashed" class="text-light mb-3" style="width: 48px; height: 48px;"></i>
                                <p class="text-muted small">No specific remarks have been left by the approving authorities yet.</p>
                            </div>
                        @endif

                        <div class="mt-auto pt-4 border-top text-start">
                            <div class="info-label">Last Activity</div>
                            <div class="small fw-bold">{{ $request->updated_at->format('F d, Y - h:i A') }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
