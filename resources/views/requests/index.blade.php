<x-app-layout>
    <x-slot name="header">
        {{ __('Institutional Requisition Logs') }}
    </x-slot>

    <div class="container-fluid py-2">
        <!-- HEADER -->
        <div class="d-flex justify-content-between align-items-center mb-5">
            <div>
                <h4 class="fw-black text-dark mb-0">Procurement Archive</h4>
                <p class="text-muted small mb-0">Comprehensive list of all institutional supply activities.</p>
            </div>
            @if(Auth::user()->role == 'smo')
                <button onclick="window.print()" class="btn btn-sm btn-htc px-4 rounded-pill">
                    <i data-lucide="printer" class="me-2" style="width:16px"></i> Generate Master Report
                </button>
            @endif
        </div>

        <!-- SECTION 1: ACTIVE TRACKING -->
        <div class="mb-5 animate__animated animate__fadeIn">
            <div class="d-flex align-items-center mb-3">
                <div class="p-2 rounded-3 bg-primary-subtle text-primary me-2">
                    <i data-lucide="clock" style="width:18px;"></i>
                </div>
                <h5 class="fw-bold mb-0 text-dark">Active Requests ({{ $activeRequests->count() }})</h5>
            </div>
            @include('requests.partials.history-table', ['requisitions' => $activeRequests, 'type' => 'active'])
        </div>

        <!-- SECTION 2: COMPLETED ARCHIVE -->
        <div class="animate__animated animate__fadeIn" style="animation-delay: 0.2s;">
            <div class="d-flex align-items-center mb-3">
                <div class="p-2 rounded-3 bg-success-subtle text-success me-2">
                    <i data-lucide="archive" style="width:18px;"></i>
                </div>
                <h5 class="fw-bold mb-0 text-dark">Completed Archive ({{ $completedRequests->count() }})</h5>
            </div>
            @include('requests.partials.history-table', ['requisitions' => $completedRequests, 'type' => 'completed'])
        </div>
    </div>
</x-app-layout>
