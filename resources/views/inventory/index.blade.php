<x-app-layout>
    <x-slot name="header">
        {{ __('Inventory Management') }}
    </x-slot>

    <div class="container-fluid py-2">
        @if(session('success'))
            <div class="alert alert-success border-0 shadow-sm mb-4 rounded-4">{{ session('success') }}</div>
        @endif

        <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
            <div class="card-header bg-white border-0 p-4 d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="fw-bold mb-0">Current Stock Levels</h5>
                    <p class="text-muted small mb-0">Monitor and manage institutional supplies.</p>
                </div>
                <a href="{{ route('inventory.create') }}" class="btn btn-htc px-4 rounded-pill shadow-sm">
                    <i data-lucide="plus-circle" class="me-1" style="width:16px;"></i> Add New Item
                </a>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light text-muted small text-uppercase">
                        <tr>
                            <th class="ps-4 py-3">Item Name</th>
                            <th class="py-3">Specifications</th>
                            <th class="py-3">Stock Status</th>
                            <th class="py-3 text-end">Unit Price</th>
                            <th class="pe-4 py-3 text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($supplies as $item)
                        <tr>
                            <td class="ps-4">
                                <div class="fw-bold text-dark">{{ $item->item_name }}</div>
                                <div class="small text-muted">{{ $item->unit }}</div>
                            </td>
                            <td class="small text-muted text-truncate" style="max-width: 200px;">
                                {{ $item->specifications ?? 'N/A' }}
                            </td>
                            <td>
                                @if($item->quantity <= 0)
                                    <span class="badge bg-danger px-3 rounded-pill text-uppercase">Out of Stock</span>
                                @elseif($item->quantity <= 10)
                                    <span class="badge bg-warning text-dark px-3 rounded-pill text-uppercase">Low: {{ $item->quantity }}</span>
                                @else
                                    <span class="badge bg-success-subtle text-success border border-success px-3 rounded-pill text-uppercase">Healthy: {{ $item->quantity }}</span>
                                @endif
                            </td>
                            <td class="text-end fw-bold">₱{{ number_format($item->unit_price, 2) }}</td>
                            <td class="pe-4 text-end">
                                <div class="d-flex justify-content-end gap-2">
                                    <a href="{{ route('inventory.edit', $item->id) }}" class="btn btn-sm btn-outline-secondary rounded-circle" title="Edit">
                                        <i data-lucide="edit-3" style="width:14px;"></i>
                                    </a>

                                    <!-- Updated Delete Trigger -->
                                    <button type="button" class="btn btn-sm btn-outline-danger rounded-circle"
                                            onclick="confirmDelete({{ $item->id }}, '{{ $item->item_name }}')">
                                        <i data-lucide="trash-2" style="width:14px;"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="5" class="text-center py-5 text-muted">Inventory is empty.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- DELETE CONFIRMATION MODAL -->
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content border-0 shadow-lg rounded-4">
                <div class="modal-body p-4 text-center">
                    <div class="rounded-circle bg-danger-subtle text-danger d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 60px; height: 60px;">
                        <i data-lucide="alert-triangle" style="width: 30px; height: 30px;"></i>
                    </div>
                    <h6 class="fw-bold mb-1">Remove Item?</h6>
                    <p class="text-muted small mb-4">Are you sure you want to delete <strong id="deleteItemName"></strong> from inventory?</p>

                    <form id="deleteForm" method="POST">
                        @csrf
                        @method('DELETE')
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-danger fw-bold py-2 rounded-3">Yes, Delete Item</button>
                            <button type="button" class="btn btn-light fw-bold py-2 rounded-3 border" data-bs-dismiss="modal">Cancel</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        function confirmDelete(id, name) {
            // Set the item name in the modal text
            document.getElementById('deleteItemName').innerText = name;
            // Set the form action dynamically
            document.getElementById('deleteForm').action = '/inventory/' + id;

            // Show the modal
            const myModal = new bootstrap.Modal(document.getElementById('deleteModal'));
            myModal.show();

            // Refresh icons inside modal
            setTimeout(() => lucide.createIcons(), 50);
        }
    </script>
</x-app-layout>
