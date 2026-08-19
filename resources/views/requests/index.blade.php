<x-app-layout>
    <x-slot name="header">
        {{ __('Institutional Requisition Logs') }}
    </x-slot>

    <div class="container-fluid py-2">
        <!-- HEADER & EXPORT -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="fw-black text-dark mb-0">Requisition Logs</h4>
                <p class="text-muted small mb-0">Search and filter through requisitions.</p>
            </div>
            @if(Auth::user()->role == 'smo')
                <button onclick="window.print()" class="btn btn-sm btn-htc px-4 rounded-pill shadow-sm">
                    <i data-lucide="printer" class="me-2" style="width:14px"></i> Generate Report
                </button>
            @endif
        </div>

        <div class="d-flex align-items-center mb-1">
            <div class="p-2 rounded-3 bg-primary-subtle text-primary me-2">
                <i data-lucide="clock" style="width:16px;"></i>
            </div>
            <h6 class="fw-bold mb-0 text-dark">Active Requisitions ({{ $activeRequests->count() }})</h6>
        </div>

        <!-- SEARCH & FILTER TOOLBAR -->
        <div class="card border-0 shadow-sm rounded-4 mb-1">
            <div class="card-body p-3">
                <form action="{{ route('requests.index') }}" method="GET" class="row g-2 align-items-center">
                    <!-- Status Filter -->
                    <div class="col-md-2">
                        <select name="status" class="form-select form-select-sm border-0 bg-light rounded-3 shadow-none" onchange="this.form.submit()">
                            <option value="">All Statuses</option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="approved_vp" {{ request('status') == 'approved_vp' ? 'selected' : '' }}>Approved (VP)</option>
                            <option value="approved_president" {{ request('status') == 'approved_president' ? 'selected' : '' }}>Approved (President)</option>
                            <option value="released" {{ request('status') == 'released' ? 'selected' : '' }}>Released</option>
                            <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                        </select>
                    </div>

                    <!-- Dept Filter (SMO Only) -->
                    @if(Auth::user()->role == 'smo')
                    <div class="col-md-2">
                        <select name="dept" class="form-select form-select-sm border-0 bg-light rounded-3 shadow-none" onchange="this.form.submit()">
                            <option value="">All Depts</option>
                            <option value="CETE" {{ request('dept') == 'CETE' ? 'selected' : '' }}>CETE</option>
                            <option value="CTE" {{ request('dept') == 'CTE' ? 'selected' : '' }}>CTE</option>
                            <option value="CBA" {{ request('dept') == 'CBA' ? 'selected' : '' }}>CBA</option>
                        </select>
                    </div>
                    @endif

                    <!-- Date Range -->
                    <div class="col-md-4">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text border-0 bg-light text-muted" style="font-size: 10px;">FROM</span>
                            <input type="date" name="date_from" value="{{ request('date_from') }}" class="form-control border-0 bg-light" onchange="this.form.submit()">
                            <span class="input-group-text border-0 bg-light text-muted" style="font-size: 10px;">TO</span>
                            <input type="date" name="date_to" value="{{ request('date_to') }}" class="form-control border-0 bg-light" onchange="this.form.submit()">
                        </div>
                    </div>

                    <!-- Sort -->
                    <div class="col-md-2">
                        <select name="order" class="form-select form-select-sm border-0 bg-light rounded-3 shadow-none" onchange="this.form.submit()">
                            <option value="desc" {{ request('order') == 'desc' ? 'selected' : '' }}>Newest First</option>
                            <option value="asc" {{ request('order') == 'asc' ? 'selected' : '' }}>Oldest First</option>
                        </select>
                    </div>

                    <!-- Clear -->
                    <div class="col-md-2 text-end">
                        <a href="{{ route('requests.index') }}" class="btn btn-sm btn-link text-danger text-decoration-none fw-bold" style="font-size: 11px;">Reset Filters</a>
                    </div>
                </form>
            </div>
        </div>

        <!-- SECTION 1: ACTIVE TRACKING -->
        <div class="mb-5">
            @include('requests.partials.history-table', ['requisitions' => $activeRequests, 'type' => 'active'])
        </div>
    </div>
</x-app-layout>
