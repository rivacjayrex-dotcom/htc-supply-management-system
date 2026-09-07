<x-app-layout>
    <x-slot name="header">
        <span style="color: white; font-weight: 800;">REPORT BUILDER</span>
    </x-slot>

    <div class="container-fluid py-2">
        <!-- FILTER BOX -->
        <div class="card border-0 shadow-sm rounded-4 mb-4 bg-white animate__animated animate__fadeIn">
            <div class="card-body p-4">
                <h6 class="fw-bold mb-3"><i data-lucide="filter" class="me-2" style="width:18px;"></i> Report Parameters</h6>
                <form action="{{ route('admin.reports.index') }}" method="GET" class="row g-3">
                    <div class="col-md-3">
                        <label class="small fw-bold text-muted uppercase">Department</label>
                        <select name="dept" class="form-select border-0 bg-light rounded-3 shadow-none">
                            <option value="">All Departments</option>
                            <option value="CETE" {{ request('dept') == 'CETE' ? 'selected' : '' }}>CETE</option>
                            <option value="CTE" {{ request('dept') == 'CTE' ? 'selected' : '' }}>CTE</option>
                            <option value="CBA" {{ request('dept') == 'CBA' ? 'selected' : '' }}>CBA</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="small fw-bold text-muted uppercase">Status</label>
                        <select name="status" class="form-select border-0 bg-light rounded-3 shadow-none">
                            <option value="">Any Status</option>
                            <option value="released" {{ request('status') == 'released' ? 'selected' : '' }}>Released</option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="small fw-bold text-muted uppercase">Date Range</label>
                        <div class="input-group">
                            <input type="date" name="date_from" value="{{ request('date_from') }}" class="form-control border-0 bg-light rounded-start-3 shadow-none">
                            <input type="date" name="date_to" value="{{ request('date_to') }}" class="form-control border-0 bg-light rounded-end-3 shadow-none">
                        </div>
                    </div>
                    <div class="col-md-3 d-flex align-items-end gap-2">
                        <button type="submit" class="btn btn-htc flex-grow-1 fw-bold rounded-3">Filter List</button>
                        <!-- THE DOWNLOAD BUTTON -->
                        <button type="submit" name="download" value="1" class="btn btn-dark fw-bold rounded-3 px-3">
                            <i data-lucide="file-down"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- PREVIEW TABLE -->
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-white animate__animated animate__fadeInUp">
            <div class="card-header bg-white border-0 p-4 pb-0">
                <h6 class="fw-bold">Report Preview <small class="text-muted fw-normal ms-2">({{ $requisitions->count() }} results found)</small></h6>
            </div>
            <div class="table-responsive p-4">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light small text-uppercase">
                        <tr>
                            <th class="ps-3 py-3">Date</th>
                            <th>Control #</th>
                            <th>Requestor / Dept</th>
                            <th>Total Value</th>
                            <th class="pe-3 text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($requisitions as $req)
                        <tr>
                            <td class="ps-3">{{ $req->created_at->format('M d, Y') }}</td>
                            <td class="fw-bold">#{{ str_pad($req->id, 5, '0', STR_PAD_LEFT) }}</td>
                            <td>
                                <div class="fw-bold">{{ $req->user->name }}</div>
                                <div class="small text-muted">{{ $req->user->department }}</div>
                            </td>
                            <td class="text-success fw-bold">₱{{ number_format($req->grand_total, 2) }}</td>
                            <td class="pe-3 text-center">
                                <span class="badge bg-light text-dark border rounded-pill px-3">{{ strtoupper($req->status) }}</span>
                            </td>
                            <td>
                                <div class="fw-bold">
                                    {{ $req->items->first()->item_name ?? 'No Items Found' }}
                                </div>
                                @if($req->items->count() > 1)
                                    <small class="text-primary">+{{ $req->items->count() - 1 }} more items</small>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="5" class="text-center py-5 text-muted">No records match your filters.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
