<x-app-layout>
    <x-slot name="header">Inventory Specification Registry</x-slot>

    <div class="container-fluid py-4">
        <div class="row justify-content-center">
            <div class="col-xl-12 col-lg-12">
                <form action="{{ route('inventory.store') }}" method="POST">
                    @csrf

                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <a href="{{ route('inventory.index') }}" class="btn btn-link text-decoration-none text-muted p-0 d-flex align-items-center">
                                <i data-lucide="chevron-left" class="me-1" style="width:18px"></i> Back to Inventory
                            </a>
                        </div>

                        <!-- SECTION 1: IDENTITY -->
                        <div class="card-body p-4 border-bottom">
                            <div class="nav-section-label mb-3 text-primary">I. Institutional Identity</div>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label mini-label">Primary Item Name <span class="text-danger">*</span></label>
                                    <input type="text" name="item_name" class="form-control border-0 bg-light py-2 shadow-none" placeholder="e.g. A4 Bond Paper" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label mini-label">Brand / Manufacturer <span class="text-danger">*</span></label>
                                    <input type="text" name="brand" class="form-control border-0 bg-light py-2 shadow-none" placeholder="e.g. Hard Copy" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label mini-label">Institutional Category <span class="text-danger">*</span></label>
                                    <select name="category" class="form-select border-0 bg-light py-2 shadow-none" required>
                                        <option value="">-- Select Category --</option>
                                        <option>Office Supplies</option>
                                        <option>IT Equipment</option>
                                        <option>Janitorial</option>
                                        <option>Laboratory</option>
                                        <option>Furniture</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label mini-label">Model / Part Number</label>
                                    <input type="text" name="model_number" class="form-control border-0 bg-light py-2 shadow-none" placeholder="e.g. L-3210 or 80GSM">
                                </div>
                            </div>
                        </div>

                        <!-- SECTION 2: TECHNICAL CORE -->
                        <div class="card-body p-4 border-bottom bg-light bg-opacity-50">
                            <div class="nav-section-label mb-3 text-primary">II. Technical Specifications</div>
                            <div class="mb-0">
                                <label class="form-label mini-label">Detailed Physical Description (Size, Weight, Color) <span class="text-danger">*</span></label>
                                <textarea name="specifications" class="form-control border-0 bg-white py-3 shadow-sm rounded-4" rows="3" placeholder="Specify all core attributes to avoid misconceptions..."></textarea>
                            </div>
                        </div>

                        <!-- SECTION 3: FINANCIALS -->
                        <div class="card-body p-4">
                            <div class="nav-section-label mb-3 text-primary">III. Stock & Financial Parameters</div>
                            <div class="row g-3">
                                <div class="col-md-3">
                                    <label class="form-label mini-label text-success">Unit Price (₱) <span class="text-danger">*</span></label>
                                    <input type="number" step="0.01" name="unit_price" class="form-control border-0 bg-light py-2 fw-bold text-success" required>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label mini-label">Initial Quantity <span class="text-danger">*</span></label>
                                    <input type="number" name="quantity" class="form-control border-0 bg-light py-2" value="0" required>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label mini-label">Unit <span class="text-danger">*</span></label>
                                    <select name="unit" class="form-select border-0 bg-light py-2" required>
                                        <option value="">-- Unit --</option>
                                        <option>Ream</option><option>Box</option><option>Piece</option>
                                        <option>Set</option><option>Roll</option><option>Bottle</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label mini-label text-danger">Restock Threshold <span class="text-danger">*</span></label>
                                    <input type="number" name="min_stock_level" class="form-control border-0 bg-danger-subtle py-2 text-danger fw-bold" value="5" required>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex gap-3 justify-content-end">
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
