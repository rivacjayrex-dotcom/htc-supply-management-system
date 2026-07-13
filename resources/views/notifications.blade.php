<x-app-layout>
    <x-slot name="header">
        {{ __('Notifications') }}
    </x-slot>

    <div class="container py-4">
        <!-- Header Section -->
        <div class="mb-5">
            <p class="text-muted fs-5">Manage alerts and approval updates</p>
        </div>

        <div class="row">
            <div class="col-lg-9 mx-auto">
                <!-- Mark All Read / Clear Logic would go here -->

                <div class="list-group list-group-flush shadow-sm rounded-4 overflow-hidden border">

                    @forelse($notifications as $note)
                        <div class="list-group-item p-4 border-bottom {{ $note->is_read ? 'bg-white' : 'bg-light' }}" style="{{ $note->is_read ? '' : 'border-start: 4px solid var(--htc-green);' }}">
                            <div class="d-flex align-items-start">

                                <!-- Modern Icon based on notification type -->
                                <div class="me-4">
                                    <div class="rounded-circle d-flex align-items-center justify-content-center shadow-sm"
                                         style="width: 48px; height: 48px;
                                                background-color: {{ $note->type == 'success' ? '#f0f7f1' : ($note->type == 'danger' ? '#fff5f5' : '#f0f4ff') }};
                                                color: {{ $note->type == 'success' ? '#198754' : ($note->type == 'danger' ? '#dc3545' : '#0d6efd') }};">
                                        <i data-lucide="{{ $note->icon ?? 'bell' }}"></i>
                                    </div>
                                </div>

                                <!-- Notification Text -->
                                <div class="flex-grow-1">
                                    <div class="d-flex w-100 justify-content-between align-items-center mb-1">
                                        <h6 class="fw-bold mb-0 text-dark">
                                            {{ $note->title }}
                                        </h6>
                                        <small class="text-muted italic">{{ $note->created_at->diffForHumans() }}</small>
                                    </div>

                                    <p class="text-muted mb-0 small">
                                        {{ $note->message }}
                                    </p>

                                    <div class="mt-2">
                                        <!-- Note: You can add a link to the specific request tracker here -->
                                        <a href="{{ route('requests.index') }}" class="text-decoration-none small fw-bold" style="color: var(--htc-green);">
                                            View in History →
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <!-- Empty State -->
                        <div class="list-group-item text-center py-5 bg-white">
                            <div class="py-5">
                                <i data-lucide="mail-open" class="text-light mb-3" style="width: 64px; height: 64px;"></i>
                                <p class="text-muted mt-3 mb-0">Your inbox is clear! No new updates at this time.</p>
                            </div>
                        </div>
                    @endforelse

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
