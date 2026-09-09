<x-app-layout>
    <x-slot name="header">Inventory Specification Registry</x-slot>

    <div class="container-fluid py-4">
        <div class="row justify-content-center">
            <div class="col-xl-10 col-lg-11">

                <!-- ANY VALIDATION ERRORS DISPLAY -->
                @if($errors->any())
                    <div class="alert alert-danger border-0 shadow-sm rounded-4 mb-4 p-3">
                        <div class="d-flex align-items-center mb-2">
                            <i data-lucide="alert-triangle" class="me-2 text-danger" style="width: 20px;"></i>
                            <strong class="text-danger">Please correct the following errors:</strong>
                        </div>
                        <ul class="mb-0 small ps-4 text-danger">
                            @foreach($errors->all() as $err)
                                <li>{{ $err }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('inventory.store') }}" method="POST">
                    @csrf

                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <a href="{{ route('inventory.index') }}" class="btn btn-link text-decoration-none text-muted p-0 d-flex align-items-center">
                            <i data-lucide="chevron-left" class="me-1" style="width:18px"></i> Back to Inventory
                        </a>
                    </div>

                    <div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-white mb-4">
                        <!-- SECTION 1: IDENTITY -->
                        <div class="card-body p-4 border-bottom">
                            <div class="nav-section-label mb-3 text-primary">I. Institutional Identity</div>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label mini-label">Primary Item Name <span class="text-danger">*</span></label>
                                    <input type="text" name="item_name" value="{{ old('item_name') }}" class="form-control border-0 bg-light py-2 shadow-none @error('item_name') is-invalid @enderror" placeholder="e.g. A4 Bond Paper" required>
                                    @error('item_name') <span class="text-danger small">{{ $message }}</span> @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label mini-label">Brand / Manufacturer <span class="text-danger">*</span></label>
                                    <input type="text" name="brand" value="{{ old('brand') }}" class="form-control border-0 bg-light py-2 shadow-none @error('brand') is-invalid @enderror" placeholder="e.g. Hard Copy" required>
                                    @error('brand') <span class="text-danger small">{{ $message }}</span> @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label mini-label">Institutional Category <span class="text-danger">*</span></label>
                                    <select name="category" class="form-select border-0 bg-light py-2 shadow-none @error('category') is-invalid @enderror" required>
                                        <option value="">-- Select Category --</option>
                                        <option value="Office Supplies" {{ old('category') == 'Office Supplies' ? 'selected' : '' }}>Office Supplies</option>
                                        <option value="IT Equipment" {{ old('category') == 'IT Equipment' ? 'selected' : '' }}>IT Equipment</option>
                                        <option value="Janitorial" {{ old('category') == 'Janitorial' ? 'selected' : '' }}>Janitorial</option>
                                        <option value="Laboratory" {{ old('category') == 'Laboratory' ? 'selected' : '' }}>Laboratory</option>
                                        <option value="Furniture" {{ old('category') == 'Furniture' ? 'selected' : '' }}>Furniture</option>
                                    </select>
                                    @error('category') <span class="text-danger small">{{ $message }}</span> @enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label mini-label">Model / Part Number</label>
                                    <input type="text" name="model_number" value="{{ old('model_number') }}" class="form-control border-0 bg-light py-2 shadow-none" placeholder="e.g. L-3210 or 80GSM">
                                </div>
                            </div>
                        </div>

                        <!-- SECTION 2: TECHNICAL CORE -->
                        <div class="card-body p-4 border-bottom bg-light bg-opacity-50">
                            <div class="nav-section-label mb-3 text-primary">II. Technical Specifications</div>
                            <div class="mb-0">
                                <label class="form-label mini-label">Detailed Physical Description (Size, Weight, Color) <span class="text-danger">*</span></label>
                                <textarea name="specifications" class="form-control border-0 bg-white py-3 shadow-sm rounded-4 @error('specifications') is-invalid @enderror" rows="3" placeholder="Specify all core attributes..." required>{{ old('specifications') }}</textarea>
                                @error('specifications') <span class="text-danger small">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        <!-- SECTION 3: FINANCIALS -->
                        <div class="card-body p-4">
                            <div class="nav-section-label mb-3 text-primary">III. Stock & Financial Parameters</div>
                            <div class="row g-3">
                                <div class="col-md-3">
                                    <label class="form-label mini-label text-success">Unit Price (₱) <span class="text-danger">*</span></label>
                                    <input type="number" step="0.01" name="unit_price" value="{{ old('unit_price') }}" class="form-control border-0 bg-light py-2 fw-bold text-success shadow-none @error('unit_price') is-invalid @enderror" placeholder="0.00" required>
                                    @error('unit_price') <span class="text-danger small">{{ $message }}</span> @enderror
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label mini-label">Initial Quantity <span class="text-danger">*</span></label>
                                    <input type="number" name="quantity" value="{{ old('quantity', 0) }}" class="form-control border-0 bg-light py-2 shadow-none @error('quantity') is-invalid @enderror" min="0" required>
                                    @error('quantity') <span class="text-danger small">{{ $message }}</span> @enderror
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label small fw-bold text-dark text-uppercase">Unit <span class="text-danger">*</span></label>
                                    <select name="unit" class="form-select border-0 bg-light py-2 rounded-3 shadow-none @error('unit') is-invalid @enderror" required>
                                        <option value="">-- Select Unit --</option>
                                        @foreach(['Bottle', 'Box(es)', 'Can(s)', 'Gallon(s)', 'Kilogram(s)', 'Liter(s)', 'Meter(s)', 'Pack(s)', 'PC/PCS', 'Piece(s)', 'Ream(s)', 'Roll(s)', 'Set', 'Unit(s)'] as $u)
                                            <option value="{{ $u }}" {{ old('unit') == $u ? 'selected' : '' }}>{{ $u }}</option>
                                        @endforeach
                                    </select>
                                    @error('unit') <span class="text-danger small">{{ $message }}</span> @enderror
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label mini-label text-danger">Restock Threshold <span class="text-danger">*</span></label>
                                    <input type="number" name="min_stock_level" value="{{ old('min_stock_level', 5) }}" class="form-control border-0 bg-danger-subtle py-2 text-danger fw-bold shadow-none @error('min_stock_level') is-invalid @enderror" min="0" required>
                                    @error('min_stock_level') <span class="text-danger small">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex gap-3 justify-content-end mb-5">
                        <a href="{{ route('inventory.index') }}" class="btn btn-light px-5 py-3 rounded-pill fw-bold border">Discard</a>
                        <button type="submit" class="btn btn-htc px-5 py-3 rounded-pill fw-bold shadow-lg">
                            VERIFY AND REGISTER ASSET
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
