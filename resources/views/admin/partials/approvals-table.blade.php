<div class="table-responsive">
    <table class="table table-hover align-middle mb-0">
        <thead class="bg-light text-muted small text-uppercase">
            <tr>
                <th class="ps-4 py-3">Req #</th>
                <th class="py-3">Requestor</th>
                <th class="py-3">Items</th>
                <th class="py-3">Total Amount</th>
                <th class="pe-4 py-3 text-end">Action</th>
            </tr>
        </thead>
        <tbody>
            @forelse($requests as $req)
                <tr>
                    <td class="ps-4 fw-mono text-muted">#{{ $req->id }}</td>
                    <td>
                        <div class="fw-bold text-dark">{{ $req->user->name }}</div>
                        <div class="small text-muted" style="font-size: 10px;">{{ $req->user->department ?? 'General' }}</div>
                    </td>
                    <td>
                        <span class="fw-semibold">{{ $req->items->first()->item_name }}</span>
                        @if($req->items->count() > 1)
                            <span class="text-muted small"> (+{{ $req->items->count() - 1 }} others)</span>
                        @endif
                    </td>
                    <td class="fw-bold text-success">₱{{ number_format($req->grand_total, 2) }}</td>
                    <td class="pe-4 text-end">
                        <button class="btn btn-sm {{ Auth::user()->role == 'smo' ? 'btn-primary' : 'btn-htc' }} px-3 rounded-pill shadow-sm"
                                @click="openDetails({{ $req }}, {{ $req->items }}, '{{ addslashes($req->user->name) }}', '{{ $req->user->department }}')">
                            {{ Auth::user()->role == 'smo' ? 'Review & Release' : 'See More & Review' }}
                        </button>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center py-5 text-muted italic">No requisitions currently in this queue.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
