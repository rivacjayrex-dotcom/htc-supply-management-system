<div class="card border-0 shadow-sm rounded-4 overflow-hidden">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="bg-light text-muted small text-uppercase">
                <tr>
                    <th class="ps-4 py-3">Req #</th>
                    @if(Auth::user()->role != 'employee') <th class="py-3">Requestor</th> @endif
                    <th class="py-3">Primary Item</th>
                    <th class="py-3">Total Value</th>
                    <th class="py-3">Status</th>
                    <th class="pe-4 py-3 text-end">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($requisitions as $req)
                <tr>
                    <td class="ps-4 text-muted fw-mono">#{{ str_pad($req->id, 4, '0', STR_PAD_LEFT) }}</td>

                    @if(Auth::user()->role != 'employee')
                    <td>
                        <div class="fw-bold small text-dark">{{ $req->user->name }}</div>
                        <div class="text-muted" style="font-size: 9px;">{{ $req->user->department }}</div>
                    </td>
                    @endif

                    <td>
                        <div class="fw-bold text-dark">{{ $req->items->first()->item_name ?? 'N/A' }}</div>
                        @if($req->items->count() > 1)
                            <small class="text-primary fw-bold">(+{{ $req->items->count() - 1 }} other items)</small>
                        @endif
                    </td>

                    <td class="fw-bold text-success">₱{{ number_format($req->grand_total, 2) }}</td>

                    <td>
                        @php
                            $statusColor = match($req->status) {
                                'released' => 'bg-success',
                                'rejected' => 'bg-danger',
                                'pending' => 'bg-secondary',
                                default => 'bg-info-subtle text-info border border-info'
                            };
                        @endphp
                        <span class="badge {{ $statusColor }} text-uppercase px-3 py-2 rounded-pill" style="font-size: 9px;">
                            {{ str_replace('_', ' ', $req->status) }}
                        </span>
                    </td>

                    <td class="pe-4 text-end">
                        <div class="dropdown">
                            <button class="btn btn-light btn-sm rounded-circle" type="button" data-bs-toggle="dropdown">
                                <i data-lucide="more-vertical" style="width:14px"></i>
                            </button>
                            <ul class="dropdown-menu shadow-lg border-0 rounded-3">
                                <li><a class="dropdown-item small" href="{{ route('requests.show', $req->id) }}"><i data-lucide="eye" class="me-2" style="width:14px"></i> Track Progress</a></li>
                                <li><a class="dropdown-item small" href="{{ route('requests.download', $req->id) }}"><i data-lucide="file-text" class="me-2" style="width:14px"></i> Download PDF</a></li>
                            </ul>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center py-5 text-muted italic">
                        <i data-lucide="inbox" class="mb-2 opacity-25" style="width: 48px; height: 48px;"></i>
                        <p>No {{ $type }} requisitions found.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
