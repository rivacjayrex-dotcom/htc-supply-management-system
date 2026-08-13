<x-app-layout>
    <x-slot name="header">
        {{ __('Institutional Archive') }}
    </x-slot>

    <div class="container-fluid py-2">
        <!-- TOP STATS: ARCHIVE OVERVIEW -->
        <div class="row g-3 mb-4">
            <div class="col-md-4">
                <div class="card border-0 shadow-sm rounded-4 p-3 bg-white">
                    <div class="d-flex align-items-center">
                        <div class="p-2 rounded-3 bg-primary-subtle text-primary me-3">
                            <i data-lucide="calendar-check" style="width:20px"></i>
                        </div>
                        <div>
                            <small class="text-muted fw-bold uppercase" style="font-size: 9px;">Released Today</small>
                            <h5 class="fw-black mb-0">{{ $stats['today_count'] }} Requisitions</h5>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm rounded-4 p-3 bg-white">
                    <div class="d-flex align-items-center">
                        <div class="p-2 rounded-3 bg-success-subtle text-success me-3">
                            <i data-lucide="banknote" style="width:20px"></i>
                        </div>
                        <div>
                            <small class="text-muted fw-bold uppercase" style="font-size: 9px;">Weekly Value</small>
                            <h5 class="fw-black mb-0">₱{{ number_format($stats['week_val'], 2) }}</h5>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm rounded-4 p-3 bg-dark text-white">
                    <div class="d-flex align-items-center">
                        <div class="p-2 rounded-3 bg-white bg-opacity-10 text-white me-3">
                            <i data-lucide="layers" style="width:20px"></i>
                        </div>
                        <div>
                            <small class="text-white-50 fw-bold uppercase" style="font-size: 9px;">Monthly Volume</small>
                            <h5 class="fw-black mb-0">{{ $stats['month_count'] }} Items Moved</h5>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- FILTER BAR -->
        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-body p-3 d-flex justify-content-between align-items-center">
                <div class="btn-group p-1 bg-light rounded-pill">
                    <a href="{{ route('admin.archive', ['filter' => 'all']) }}" class="btn btn-sm rounded-pill px-3 {{ $filter == 'all' ? 'btn-htc' : 'btn-light' }}">All Time</a>
                    <a href="{{ route('admin.archive', ['filter' => 'today']) }}" class="btn btn-sm rounded-pill px-3 {{ $filter == 'today' ? 'btn-htc' : 'btn-light' }}">Today</a>
                    <a href="{{ route('admin.archive', ['filter' => 'week']) }}" class="btn btn-sm rounded-pill px-3 {{ $filter == 'week' ? 'btn-htc' : 'btn-light' }}">This Week</a>
                    <a href="{{ route('admin.archive', ['filter' => 'month']) }}" class="btn btn-sm rounded-pill px-3 {{ $filter == 'month' ? 'btn-htc' : 'btn-light' }}">This Month</a>
                    <a href="{{ route('admin.archive', ['filter' => 'year']) }}" class="btn btn-sm rounded-pill px-3 {{ $filter == 'year' ? 'btn-htc' : 'btn-light' }}">This Year</a>
                </div>
                <div>
                    <button onclick="window.print()" class="btn btn-sm btn-outline-dark px-3 rounded-pill fw-bold">
                        <i data-lucide="printer" class="me-1" style="width:14px"></i> Print PDF Report
                    </button>
                </div>
            </div>
        </div>

        <!-- ARCHIVE TABLE -->
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light text-muted small uppercase">
                        <tr>
                            <th class="ps-4 py-3">Control #</th>
                            <th class="py-3">Released Date</th>
                            <th class="py-3">Requestor</th>
                            <th class="py-3">Total Value</th>
                            <th class="py-3 text-center">Items</th>
                            <th class="pe-4 py-3 text-end">Documents</th>
                        </tr>
                    </thead>
                    <tbody style="font-size: 0.9rem;">
                        @forelse($archives as $req)
                        <tr>
                            <td class="ps-4 fw-mono text-muted">#{{ str_pad($req->id, 6, '0', STR_PAD_LEFT) }}</td>
                            <td class="fw-bold text-dark">
                                {{ $req->updated_at->format('M d, Y') }}
                                <div class="small text-muted fw-normal" style="font-size: 10px;">{{ $req->updated_at->format('h:i A') }}</div>
                            </td>
                            <td>
                                <div class="fw-bold">{{ $req->user->name }}</div>
                                <div class="small text-success fw-bold" style="font-size: 9px;">{{ strtoupper($req->user->department) }}</div>
                            </td>
                            <td class="fw-bold text-dark">₱{{ number_format($req->grand_total, 2) }}</td>
                            <td class="text-center">
                                <span class="badge bg-light text-dark border rounded-pill px-3">{{ $req->items->count() }} items</span>
                            </td>
                            <td class="pe-4 text-end">
                                <div class="d-flex justify-content-end gap-2">
                                    <a href="{{ route('requests.show', $req->id) }}" class="btn btn-sm btn-outline-secondary rounded-pill px-3" style="font-size: 10px;">View Info</a>
                                    <a href="{{ route('requests.download', $req->id) }}" class="btn btn-sm btn-success rounded-pill px-3 fw-bold" style="font-size: 10px;">PDF</a>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">
                                <i data-lucide="archive" class="mb-2 opacity-25" style="width: 48px; height: 48px;"></i>
                                <p>No released records found for the selected period.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($archives->hasPages())
                <div class="card-footer bg-white border-0 p-4">
                    {{ $archives->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
