<div class="list-group list-group-flush">
    @forelse($notes as $note)
        <div class="list-group-item p-4 border-0 border-bottom">
            <div class="d-flex align-items-start">
                <!-- Icon -->
                <div class="rounded-circle bg-light d-flex align-items-center justify-content-center me-3 shadow-sm"
                     style="width: 45px; height: 45px; color: var(--htc-green);">
                    <i data-lucide="{{ $note->icon ?? 'bell' }}"></i>
                </div>

                <!-- Text Content -->
                <div class="flex-grow-1">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <h6 class="fw-bold mb-0 text-dark">{{ $note->title }}</h6>
                        <small class="text-muted" style="font-size: 11px;">{{ $note->created_at->format('M d, g:i A') }}</small>
                    </div>
                    <p class="text-muted small mb-2">{{ $note->message }}</p>

                    <!-- REFINED ACTION LOGIC -->
                    <div class="mt-2">
                        @if($note->icon == 'user-plus' || $note->title == 'New User Access Request')
                            <!-- 1. Priority: User Access (Only for SMO) -->
                            <a href="{{ route('admin.users.pending') }}" class="btn btn-sm btn-htc px-3 rounded-3 shadow-sm">
                                <i data-lucide="shield-check" class="me-1" style="width: 14px; vertical-align: middle;"></i>
                                Review Access Request
                            </a>

                        @elseif(str_contains($note->title, 'Requisition') || str_contains($note->title, 'Supplies') || $note->title == 'Request Initiated' || $note->title == 'Request Approved')
                            <!-- 2. Requisition Actions (Separated by Role) -->
                            @if(Auth::user()->role == 'employee')
                                <a href="{{ route('requests.index') }}" class="text-decoration-none small fw-bold" style="color: var(--htc-green);">
                                    View My History →
                                </a>
                            @else
                                <a href="{{ route('admin.approvals') }}" class="text-decoration-none small fw-bold" style="color: var(--htc-green);">
                                    Open Approval Queue →
                                </a>
                            @endif
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @empty
        <div class="p-5 text-center text-muted">
            <i data-lucide="inbox" class="mb-3 opacity-25" style="width: 48px; height: 48px;"></i>
            <p class="small italic">No notifications found in this category.</p>
        </div>
    @endforelse
</div>
