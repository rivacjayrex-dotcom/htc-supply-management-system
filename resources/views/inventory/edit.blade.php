<x-app-layout>
    <x-slot name="header">
        Edit Item: {{ $item->item_name }}
    </x-slot>

    <div class="container-fluid py-2">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <!-- Back Button -->
                <div class="mb-4">
                    <a href="{{ route('inventory.index') }}" class="btn btn-link text-decoration-none text-muted p-0">
                        <i data-lucide="arrow-left" class="me-1" style="width:16px;"></i> Back to Inventory
                    </a>
                </div>

                <!-- Form Card -->
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                    <div class="card-header bg-white border-0 p-4 pb-0">
                        <h5 class="fw-bold mb-1 text-success">Update Specifications</h5>
                        <p class="text-muted small">Modify the details for <strong>{{ $item->item_name }}</strong> below.</p>
                    </div>

                    <div class="card-body p-4">
                        <form action="{{ route('inventory.update', $item->id) }}" method="POST">
                            @csrf
                            @method('PATCH')

                            <!-- Item Name -->
                            <div class="mb-4">
                                <label class="form-label small fw-bold text-muted text-uppercase tracking-widest" style="font-size: 10px;">Item Name</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-0"><i data-lucide="package" style="width: 16px;"></i></span>
                                    <input type="text" name="item_name" class="form-control border-0 bg-light py-2 px-3 rounded-end-3 shadow-none"
                                           value="{{ old('item_name', $item->item_name) }}" required>
                                </div>
                                <x-input-error :messages="$errors->get('item_name')" class="mt-1" />
                            </div>

                            <!-- Specifications -->
                            <div class="mb-4">
                                <label class="form-label small fw-bold text-muted text-uppercase tracking-widest" style="font-size: 10px;">Detailed Specifications</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-0"><i data-lucide="align-left" style="width: 16px;"></i></span>
                                    <textarea name="specifications" class="form-control border-0 bg-light py-2 px-3 rounded-end-3 shadow-none"
                                              rows="3">{{ old('specifications', $item->specifications) }}</textarea>
                                </div>
                            </div>

                            <div class="row g-4 mb-5">
                                <!-- Stock Quantity -->
                                <div class="col-md-4">
                                    <label class="form-label small fw-bold text-muted text-uppercase tracking-widest" style="font-size: 10px;">Current Stock</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-0"><i data-lucide="layers" style="width: 16px;"></i></span>
                                        <input type="number" name="quantity" class="form-control border-0 bg-light py-2 px-3 rounded-end-3 shadow-none"
                                               value="{{ old('quantity', $item->quantity) }}" required min="0">
                                    </div>
                                </div>

                                <!-- Unit -->
                                <div class="col-md-4">
                                    <label class="form-label small fw-bold text-muted text-uppercase tracking-widest" style="font-size: 10px;">Unit of Measure</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-0"><i data-lucide="ruler" style="width: 16px;"></i></span>
                                        <input type="text" name="unit" class="form-control border-0 bg-light py-2 px-3 rounded-end-3 shadow-none"
                                               value="{{ old('unit', $item->unit) }}" required>
                                    </div>
                                </div>

                                <!-- Unit Price -->
                                <div class="col-md-4">
                                    <label class="form-label small fw-bold text-muted text-uppercase tracking-widest" style="font-size: 10px;">Unit Price (₱)</label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-0 fw-bold text-muted" style="font-size: 12px;">₱</span>
                                        <input type="number" step="0.01" name="unit_price" class="form-control border-0 bg-light py-2 px-3 rounded-end-3 shadow-none"
                                               value="{{ old('unit_price', $item->unit_price) }}" required min="0">
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex gap-3">
                                <a href="{{ route('inventory.index') }}" class="btn btn-light fw-bold flex-grow-1 py-3 rounded-3 border">
                                    Cancel Changes
                                </a>
                                <button type="submit" class="btn btn-htc fw-bold flex-grow-1 py-3 rounded-3 shadow-sm">
                                    <i data-lucide="save" class="me-2" style="width: 18px; vertical-align: middle;"></i>
                                    Update Item
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
