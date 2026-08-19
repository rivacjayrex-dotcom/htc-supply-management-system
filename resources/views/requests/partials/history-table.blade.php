<div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-white">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0" style="font-size: 0.85rem;"> <!-- Compact font -->
            <thead class="bg-light text-muted small text-uppercase">
                <tr>
                    <th class="ps-4 py-3" style="width: 80px;">Req #</th>

                    <!-- Only SMO sees who made the request -->
                    @if(Auth::user()->role == 'smo')
                        <th class="py-3">Requestor / Dept</th>
                    @endif

                    <th class="py-3">Primary Item</th>
                    <th class="py-3 text-center">Tier</th>
                    <th class="py-3 text-end">Grand Total</th>
                    <th class="py-3 text-center">Status</th>
                    <th class="pe-4 py-3 text-end">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($requisitions as $req)
                <tr>
                    <td class="ps-4 text-muted fw-mono">#{{ str_pad($req->id, 4, '0', STR_PAD_LEFT) }}</td>

                    @if(Auth::user()->role == 'smo')
                    <td>
                        <div class="fw-bold text-dark">{{ $req->user->name }}</div>
                        <div class="text-muted" style="font-size: 10px;">{{ $req->user->department ?? 'General' }}</div>
                    </td>
                    @endif

                    <td>
                        <div class="fw-bold text-dark">{{ $req->items->first()->item_name ?? 'N/A' }}</div>
                        @if($req->items->count() > 1)
                            <small class="text-primary fw-bold" style="font-size: 10px;">+ {{ $req->items->count() - 1 }} other items</small>
                        @endif
                    </td>

                    <td class="text-center">
                    @php
                        // Standardized Color Logic
                        $statusClass = 'status-approved';
                        if($req->status == 'pending') $statusClass = 'status-pending';
                        if($req->status == 'released') $statusClass = 'status-released';
                        if($req->status == 'rejected') $statusClass = 'status-rejected';

                        // Identify if it's nearing deadline even in history
                        $isNearDeadline = $req->updated_at <= now()->subDays(2) && !in_array($req->status, ['released', 'rejected']);
                        if($isNearDeadline) $statusClass = 'status-deadline';
                    @endphp

                    <span class="badge {{ $statusClass }} text-uppercase px-3 py-2 rounded-pill" style="font-size: 8px;">
                        {{ str_replace('_', ' ', $req->status) }}
                    </span>
                </td>

                    <td class="text-end fw-bold text-dark">₱{{ number_format($req->grand_total, 2) }}</td>

                    <td class="text-center">
                        @php
                            $statusColor = match($req->status) {
                                'released' => 'bg-success',
                                'rejected' => 'bg-danger',
                                'pending' => 'bg-secondary',
                                default => 'bg-success-subtle text-success border border-success'
                            };
                        @endphp
                        <span class="badge {{ $statusColor }} text-uppercase px-2 py-1" style="font-size: 9px;">
                            {{ str_replace('_', ' ', $req->status) }}
                        </span>
                    </td>

                    <td class="pe-4 text-end">
                        <a href="{{ route('requests.show', $req->id) }}" class="btn btn-sm btn-light border rounded-pill px-3 shadow-none fw-bold" style="font-size: 10px;">
                            Details →
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="{{ Auth::user()->role == 'smo' ? '7' : '6' }}" class="text-center py-5 text-muted">
                        No requisition records found.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
