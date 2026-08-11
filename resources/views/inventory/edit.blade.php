<x-app-layout>
    <x-slot name="header">
        {{ __('Update Supply Specification') }}
    </x-slot>

    <div class="container-fluid py-2">
        <div class="row justify-content-center">
            <div class="col-xl-9 col-lg-11">

                <!-- Breadcrumb & Item Info -->
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <a href="{{ route('inventory.index') }}" class="btn btn-link text-decoration-none text-muted p-0 d-flex align-items-center">
                        <i data-lucide="chevron-left" class="me-1" style="width:18px"></i> Back to Inventory
                    </a>
                    <div class="text-end">
                        <span class="small text-muted text-uppercase fw-bold tracking-widest">Last Updated:</span>
                        <span class="small fw-bold">{{ $item->updated_at->format('M d, Y - h:i A') }}</span>
                    </div>
                </div>

                <form action="{{ route('inventory.update', $item->id) }}" method="POST">
                    @csrf
                    @method('PATCH')

                    <!-- ITEM STATUS HEADER -->
                    <div class="card border-0 shadow-sm rounded-4 bg-white mb-4">
                        <div class="card-body p-4 d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center">
                                <div class="p-3 rounded-circle bg-light me-3">
                                    <i data-lucide="package" class="text-success" style="width: 28px; height: 28px;"></i>
                                </div>
                                <div>
                                    <h4 class="fw-bold mb-0 text-dark">{{ $item->item_name }}</h4>
                                    <span class="text-muted small">Current Stock: <strong>{{ $item->quantity }} {{ $item->unit }}</strong></span>
                                </div>
                            </div>
                            <div class="text-end">
                                @if($item->quantity <= $item->min_stock_level)
                                    <span class="badge bg-danger px-3 py-2 rounded-pill">CRITICAL STOCK LEVEL</span>
                                @else
                                    <span class="badge bg-success-subtle text-success border border-success px-3 py-2 rounded-pill">HEALTHY STATUS</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- MAIN EDIT ISLAND -->
                    <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
                        <!-- Section 1: Identity -->
                        <div class="card-body p-4 p-md-5 border-bottom">
                            <div class="d-flex align-items-center mb-4">
                                <div class="p-2 bg-success-subtle text-success rounded-3 me-3">
                                    <i data-lucide="edit-3" style="width:20px"></i>
                                </div>
                                <h6 class="fw-bold mb-0">Modify Identity</h6>
                            </div>

                            <div class="row g-4">
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold text-dark text-uppercase">Item Name <span class="text-danger">*</span></label>
                                    <input type="text" name="item_name" class="form-control border-0 bg-light py-2 rounded-3 shadow-none" value="{{ old('item_name', $item->item_name) }}" required>
                                    <x-input-error :messages="$errors->get('item_name')" class="mt-1" />
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold text-dark text-uppercase">Brand Name <span class="text-danger">*</span></label>
                                    <input type="text" name="brand" class="form-control border-0 bg-light py-2 rounded-3 shadow-none" value="{{ old('brand', $item->brand) }}" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold text-dark text-uppercase">Institutional Category</label>
                                    <select name="category" class="form-select border-0 bg-light py-2 rounded-3 shadow-none" required>
                                        @foreach(['Office Supplies', 'IT Equipment', 'Janitorial', 'Furniture', 'Laboratory'] as $cat)
                                            <option value="{{ $cat }}" {{ old('category', $item->category) == $cat ? 'selected' : '' }}>{{ $cat }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold text-dark text-uppercase">Model / Series #</label>
                                    <input type="text" name="model_number" class="form-control border-0 bg-light py-2 rounded-3 shadow-none" value="{{ old('model_number', $item->model_number) }}">
                                </div>
                            </div>
                        </div>

                        <!-- Section 2: Detailed Specs -->
                        <div class="card-body p-4 p-md-5 border-bottom" style="background-color: #fafbfc;">
                            <div class="d-flex align-items-center mb-4">
                                <div class="p-2 bg-primary-subtle text-primary rounded-3 me-3">
                                    <i data-lucide="align-left" style="width:20px"></i>
                                </div>
                                <h6 class="fw-bold mb-0">Updated Specifications</h6>
                            </div>
                            <textarea name="physical_description" class="form-control border-0 bg-white py-3 rounded-4 shadow-sm" rows="3" required>{{ old('physical_description', $item->physical_description) }}</textarea>
                        </div>

                        <!-- Section 3: Values -->
                        <div class="card-body p-4 p-md-5">
                            <div class="row g-4">
                                <div class="col-md-3">
                                    <label class="form-label small fw-bold text-dark text-uppercase">Current Qty <span class="text-danger">*</span></label>
                                    <input type="number" name="quantity" class="form-control border-0 bg-light py-2 rounded-3 shadow-none" value="{{ old('quantity', $item->quantity) }}" required>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label small fw-bold text-dark text-uppercase">Unit</label>
                                    <select name="unit" class="form-select border-0 bg-light py-2 rounded-3 shadow-none" required>
                                        @foreach(['Ream', 'Box', 'Piece', 'Set', 'Bottle', 'Pack'] as $u)
                                            <option value="{{ $u }}" {{ old('unit', $item->unit) == $u ? 'selected' : '' }}>{{ $u }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label small fw-bold text-dark text-uppercase">Price (₱)</label>
                                    <input type="number" step="0.01" name="unit_price" class="form-control border-0 bg-light py-2 shadow-none fw-bold text-success" value="{{ old('unit_price', $item->unit_price) }}" required>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label small fw-bold text-dark text-uppercase text-danger">Alert Level</label>
                                    <input type="number" name="min_stock_level" class="form-control border-0 bg-danger-subtle py-2 rounded-3 shadow-none fw-bold text-danger" value="{{ old('min_stock_level', $item->min_stock_level) }}" required>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="d-flex gap-3 justify-content-end mb-5">
                        <a href="{{ route('inventory.index') }}" class="btn btn-light px-5 py-3 rounded-pill fw-bold border">Discard Changes</a>
                        <button type="submit" class="btn btn-htc px-5 py-3 rounded-pill fw-bold shadow-lg d-flex align-items-center">
                            <i data-lucide="save" class="me-2"></i> UPDATE REGISTRY
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
