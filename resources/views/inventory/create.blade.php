<x-app-layout>
    <x-slot name="header">
        {{ __('New Supply Specification') }}
    </x-slot>

    <div class="container-fluid py-2">
        <div class="row justify-content-center">
            <div class="col-xl-9 col-lg-11">
                <!-- Header Actions -->
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <a href="{{ route('inventory.index') }}" class="btn btn-link text-decoration-none text-muted p-0 d-flex align-items-center">
                        <i data-lucide="chevron-left" class="me-1" style="width:18px"></i> Back to Inventory
                    </a>
                    <div class="text-end">
                        <span class="badge bg-white text-dark border shadow-sm px-3 py-2 rounded-pill small">
                            <span class="text-danger me-1">*</span> Mandatory Fields
                        </span>
                    </div>
                </div>

                <form action="{{ route('inventory.store') }}" method="POST">
                    @csrf

                    <!-- MAIN FORM ISLAND -->
                    <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
                        <!-- Step 1: Basic Identity -->
                        <div class="card-body p-4 p-md-5 border-bottom">
                            <div class="d-flex align-items-center mb-4">
                                <div class="p-2 bg-success-subtle text-success rounded-3 me-3">
                                    <i data-lucide="tag" style="width:20px"></i>
                                </div>
                                <div>
                                    <h6 class="fw-bold mb-0">Item Identity</h6>
                                    <p class="text-muted small mb-0">Define the primary classification of the supply.</p>
                                </div>
                            </div>

                            <div class="row g-4">
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold text-dark text-uppercase">Item Name <span class="text-danger">*</span></label>
                                    <input type="text" name="item_name" class="form-control border-0 bg-light py-2 rounded-3 shadow-none" placeholder="e.g. Bond Paper" required value="{{ old('item_name') }}">
                                    <x-input-error :messages="$errors->get('item_name')" class="mt-1" />
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold text-dark text-uppercase">Brand Name <span class="text-danger">*</span></label>
                                    <input type="text" name="brand" class="form-control border-0 bg-light py-2 rounded-3 shadow-none" placeholder="e.g. Hard Copy" required value="{{ old('brand') }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold text-dark text-uppercase">Institutional Category <span class="text-danger">*</span></label>
                                    <select name="category" class="form-select border-0 bg-light py-2 rounded-3 shadow-none" required>
                                        <option value="">-- Choose Category --</option>
                                        @foreach(['Office Supplies', 'IT Equipment', 'Janitorial', 'Furniture', 'Laboratory'] as $cat)
                                            <option value="{{ $cat }}" {{ old('category') == $cat ? 'selected' : '' }}>{{ $cat }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold text-dark text-uppercase">Model / Series #</label>
                                    <input type="text" name="model_number" class="form-control border-0 bg-light py-2 rounded-3 shadow-none" placeholder="e.g. G-Series / 80GSM" value="{{ old('model_number') }}">
                                </div>
                            </div>
                        </div>

                        <!-- Step 2: Core Specifications -->
                        <div class="card-body p-4 p-md-5 border-bottom" style="background-color: #fafbfc;">
                            <div class="d-flex align-items-center mb-4">
                                <div class="p-2 bg-primary-subtle text-primary rounded-3 me-3">
                                    <i data-lucide="list-checks" style="width:20px"></i>
                                </div>
                                <div>
                                    <h6 class="fw-bold mb-0">Detailed Specifications</h6>
                                    <p class="text-muted small mb-0">Specify physical dimensions, weight, and properties.</p>
                                </div>
                            </div>

                            <div class="mb-0">
                                <label class="form-label small fw-bold text-dark text-uppercase">Physical Description <span class="text-danger">*</span></label>
                                <textarea name="physical_description" class="form-control border-0 bg-white py-3 rounded-4 shadow-sm" rows="3" placeholder="Enter size, color, weight, and packaging details (e.g. A4 Size, 500 sheets per ream, Ultra White)..." required>{{ old('physical_description') }}</textarea>
                            </div>
                        </div>

                        <!-- Step 3: Logistics & Pricing -->
                        <div class="card-body p-4 p-md-5">
                            <div class="d-flex align-items-center mb-4">
                                <div class="p-2 bg-warning-subtle text-warning rounded-3 me-3">
                                    <i data-lucide="database" style="width:20px"></i>
                                </div>
                                <div>
                                    <h6 class="fw-bold mb-0">Stock & Financials</h6>
                                    <p class="text-muted small mb-0">Set initial quantity and restock thresholds.</p>
                                </div>
                            </div>

                            <div class="row g-4">
                                <div class="col-md-3">
                                    <label class="form-label small fw-bold text-dark text-uppercase">Initial Qty <span class="text-danger">*</span></label>
                                    <input type="number" name="quantity" class="form-control border-0 bg-light py-2 rounded-3 shadow-none" min="1" required value="{{ old('quantity') }}">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label small fw-bold text-dark text-uppercase">Unit <span class="text-danger">*</span></label>
                                    <select name="unit" class="form-select border-0 bg-light py-2 rounded-3 shadow-none" required>
                                        <option value="">-- Unit --</option>
                                        @foreach(['Ream', 'Box', 'Piece', 'Set', 'Bottle', 'Pack'] as $unit)
                                            <option value="{{ $unit }}" {{ old('unit') == $unit ? 'selected' : '' }}>{{ $unit }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label small fw-bold text-dark text-uppercase">Unit Price <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text border-0 bg-light pe-0 text-muted">₱</span>
                                        <input type="number" step="0.01" name="unit_price" class="form-control border-0 bg-light py-2 shadow-none fw-bold" required value="{{ old('unit_price') }}">
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label small fw-bold text-dark text-uppercase text-danger">Restock Alert <span class="text-danger">*</span></label>
                                    <input type="number" name="min_stock_level" class="form-control border-0 bg-danger-subtle py-2 rounded-3 shadow-none fw-bold text-danger" value="5" required>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Submit Actions -->
                    <div class="d-flex gap-3 justify-content-end mb-5">
                        <button type="reset" class="btn btn-light px-5 py-3 rounded-pill fw-bold border shadow-sm">Reset</button>
                        <button type="submit" class="btn btn-htc px-5 py-3 rounded-pill fw-bold shadow-lg d-flex align-items-center">
                            <i data-lucide="file-check-2" class="me-2"></i> VERIFY AND REGISTER ITEM
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
