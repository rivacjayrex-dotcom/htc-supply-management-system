<x-app-layout>
    <x-slot name="header">
        {{ __('Procurement History') }}
    </x-slot>

    <div class="container-fluid py-2">

        <!-- SMO ANALYTICS BAR (Only visible to SMO) -->
        @if(Auth::user()->role == 'smo')
            <div class="row g-4 mb-4">
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm rounded-4 p-3 bg-white">
                        <div class="d-flex align-items-center">
                            <div class="p-3 rounded-circle bg-success-subtle text-success me-3">
                                <i data-lucide="check-check"></i>
                            </div>
                            <div>
                                <h6 class="text-muted small mb-1">Total Released</h6>
                                <h4 class="fw-bold mb-0">{{ $requests->where('status', 'released')->count() }}</h4>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm rounded-4 p-3 bg-white">
                        <div class="d-flex align-items-center">
                            <div class="p-3 rounded-circle bg-primary-subtle text-primary me-3">
                                <i data-lucide="package"></i>
                            </div>
                            <div>
                                <h6 class="text-muted small mb-1">Active Requisitions</h6>
                                <h4 class="fw-bold mb-0">{{ $requests->whereNotIn('status', ['released', 'rejected'])->count() }}</h4>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm rounded-4 p-3 bg-white">
                        <div class="d-flex align-items-center">
                            <div class="p-3 rounded-circle bg-info-subtle text-info me-3">
                                <i data-lucide="banknote"></i>
                            </div>
                            <div>
                                <h6 class="text-muted small mb-1">Total Distributed Value</h6>
                                <h4 class="fw-bold mb-0">₱{{ number_format($requests->where('status', 'released')->sum('grand_total'), 2) }}</h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
            <div class="card-header bg-white border-0 p-4 d-flex justify-content-between align-items-center">
                <h5 class="fw-bold mb-0">Institutional Requisition History</h5>
                <button onclick="window.print()" class="btn btn-sm btn-outline-dark px-3 rounded-pill">
                    <i data-lucide="printer" class="me-1" style="width:14px"></i> Export Report
                </button>
            </div>


        <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light text-muted small text-uppercase">
                        <tr>
                            <th class="ps-4 py-3">Req #</th>
                            @if(Auth::user()->role != 'employee') <th class="py-3">Requestor</th> @endif
                            <th class="py-3">Item Name</th>
                            <th class="py-3">Date Submitted</th>
                            <th class="py-3 text-center">Tier</th>
                            <th class="py-3">Status</th>
                            <th class="pe-4 py-3 text-end">Details</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($requests as $req)
                        <tr class="request-row" style="cursor: pointer;" onclick="window.location='{{ route('requests.show', $req->id) }}'">
                            <td class="ps-4 text-muted font-monospace">{{ $req->id }}</td>
                            @if(Auth::user()->role != 'employee')
                                <td class="fw-bold">{{ $req->user->name ?? 'Unknown' }}</td>
                            @endif
                            <td>
                                <div class="fw-bold text-dark">{{ $req->item_name }}</div>
                                <div class="small text-muted">{{ $req->quantity }} {{ $req->unit }}</div>
                            </td>
                            <td class="small text-muted">{{ $req->created_at->format('M d, Y') }}</td>
                            <td class="text-center">
                                <span class="badge {{ $req->request_type == 'major' ? 'bg-warning text-dark' : 'bg-info-subtle text-info' }} rounded-pill px-3">
                                    {{ ucfirst($req->request_type) }}
                                </span>
                            </td>
                            <td>
                                @php
                                    $statusLabel = str_replace('_', ' ', $req->status);
                                    $statusColor = match($req->status) {
                                        'released' => 'bg-success',
                                        'rejected' => 'bg-danger',
                                        'pending' => 'bg-secondary',
                                        default => 'bg-success-subtle text-success border border-success'
                                    };
                                @endphp
                                <span class="badge {{ $statusColor }} text-uppercase px-2 py-1" style="font-size: 10px;">
                                    {{ $statusLabel }}
                                </span>
                            </td>
                            <td class="pe-4 text-end">
                                <a href="{{ route('requests.show', $req->id) }}" class="btn btn-sm btn-light border rounded-pill">
                                    Track →
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted italic">
                                No procurement records found in the system.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <style>
        .request-row:hover { background-color: #f0f7f1 !important; }
    </style>
</x-app-layout>
