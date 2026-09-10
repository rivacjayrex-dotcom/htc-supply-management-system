<x-app-layout>
    <x-slot name="header">Institutional Report Generation Engine</x-slot>

    <div class="container-fluid py-2">
        <div class="mb-4">
            <h4 class="fw-black text-dark mb-1">Institutional Report Builder</h4>
            <p class="text-muted small mb-0">Customize, filter, preview, and generate customized procurement and inventory documentation.</p>
        </div>

        <form action="{{ route('admin.reports.index') }}" method="GET" id="reportForm">
            <!-- 1. REPORT TYPE SELECTOR (CARDS) -->
            <div class="row g-3 mb-4">
                <div class="col-md-4">
                    <label class="card border-0 shadow-sm rounded-4 p-3 w-100 cursor-pointer {{ request('report_type', 'requisitions') === 'requisitions' ? 'border-success border-2 bg-success-subtle' : 'bg-white' }}">
                        <div class="d-flex align-items-center">
                            <input type="radio" name="report_type" value="requisitions" class="form-check-input me-3" {{ request('report_type', 'requisitions') === 'requisitions' ? 'checked' : '' }} onchange="this.form.submit()">
                            <div>
                                <div class="fw-bold text-dark">Requisition Transactions</div>
                                <small class="text-muted">Itemized requests, approvals, and order records</small>
                            </div>
                        </div>
                    </label>
                </div>

                <div class="col-md-4">
                    <label class="card border-0 shadow-sm rounded-4 p-3 w-100 cursor-pointer {{ request('report_type') === 'inventory' ? 'border-success border-2 bg-success-subtle' : 'bg-white' }}">
                        <div class="d-flex align-items-center">
                            <input type="radio" name="report_type" value="inventory" class="form-check-input me-3" {{ request('report_type') === 'inventory' ? 'checked' : '' }} onchange="this.form.submit()">
                            <div>
                                <div class="fw-bold text-dark">Inventory & Asset Valuation</div>
                                <small class="text-muted">Stock quantities, unit values, and restock levels</small>
                            </div>
                        </div>
                    </label>
                </div>

                <div class="col-md-4">
                    <label class="card border-0 shadow-sm rounded-4 p-3 w-100 cursor-pointer {{ request('report_type') === 'dept_spending' ? 'border-success border-2 bg-success-subtle' : 'bg-white' }}">
                        <div class="d-flex align-items-center">
                            <input type="radio" name="report_type" value="dept_spending" class="form-check-input me-3" {{ request('report_type') === 'dept_spending' ? 'checked' : '' }} onchange="this.form.submit()">
                            <div>
                                <div class="fw-bold text-dark">Departmental Expenditure</div>
                                <small class="text-muted">Cost distribution across academic colleges</small>
                            </div>
                        </div>
                    </label>
                </div>
            </div>

            <!-- 2. FILTER & CUSTOMIZATION CONFIGURATOR -->
            <div class="card border-0 shadow-sm rounded-4 mb-4 bg-white">
                <div class="card-header bg-white border-bottom p-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <h6 class="fw-bold mb-0 text-dark d-flex align-items-center">
                            <i data-lucide="sliders" class="me-2 text-success" style="width: 18px;"></i>
                            Configure Filter Parameters
                        </h6>
                        <a href="{{ route('admin.reports.index') }}" class="btn btn-sm btn-link text-danger text-decoration-none fw-bold" style="font-size: 11px;">Reset All Filters</a>
                    </div>
                </div>

                <div class="card-body p-4">
                    <!-- REQUISITION SPECIFIC FILTERS -->
                    @if(request('report_type', 'requisitions') === 'requisitions')
                    <div class="row g-3 mb-4">
                        <div class="col-md-3">
                            <label class="form-label mini-label">Department</label>
                            <select name="dept" class="form-select border-0 bg-light rounded-3 shadow-none">
                                <option value="">All Departments</option>
                                <option value="CETE" {{ request('dept') == 'CETE' ? 'selected' : '' }}>CETE</option>
                                <option value="CTE" {{ request('dept') == 'CTE' ? 'selected' : '' }}>CTE</option>
                                <option value="CBMA" {{ request('dept') == 'CBMA' ? 'selected' : '' }}>CBMA</option>
                                <option value="CCJE" {{ request('dept') == 'CCJE' ? 'selected' : '' }}>CCJE</option>
                                <option value="CAS" {{ request('dept') == 'CAS' ? 'selected' : '' }}>CAS</option>
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label mini-label">Requisition Tier</label>
                            <select name="tier" class="form-select border-0 bg-light rounded-3 shadow-none">
                                <option value="">All Tiers</option>
                                <option value="minor" {{ request('tier') == 'minor' ? 'selected' : '' }}>Minor (≤ ₱1,000)</option>
                                <option value="major" {{ request('tier') == 'major' ? 'selected' : '' }}>Major (> ₱1,000)</option>
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label mini-label">Transaction Status</label>
                            <select name="status" class="form-select border-0 bg-light rounded-3 shadow-none">
                                <option value="">All Statuses</option>
                                <option value="released" {{ request('status') == 'released' ? 'selected' : '' }}>Released / Completed</option>
                                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending Approvals</option>
                                <option value="for_clarification" {{ request('status') == 'for_clarification' ? 'selected' : '' }}>Office Clarification</option>
                                <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Disapproved / Rejected</option>
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label mini-label">Date Range</label>
                            <div class="input-group input-group-sm">
                                <input type="date" name="date_from" value="{{ request('date_from') }}" class="form-control border-0 bg-light">
                                <input type="date" name="date_to" value="{{ request('date_to') }}" class="form-control border-0 bg-light">
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- INVENTORY SPECIFIC FILTERS -->
                    @if(request('report_type') === 'inventory')
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label mini-label">Category Filter</label>
                            <select name="category" class="form-select border-0 bg-light rounded-3 shadow-none">
                                <option value="">All 8 Categories</option>
                                @foreach([
                                    'Cleaning Supplies', 'Construction Supplies', 'Drugs and Medicines',
                                    'HDMI', 'Maintenance Supplies', 'Medical Supplies',
                                    'Non-Medical Supplies', 'Office Supplies'
                                ] as $cat)
                                    <option value="{{ $cat }}" {{ request('category') == $cat ? 'selected' : '' }}>{{ $cat }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label mini-label">Stock Health Filter</label>
                            <select name="stock_filter" class="form-select border-0 bg-light rounded-3 shadow-none">
                                <option value="">All Stock Levels</option>
                                <option value="low" {{ request('stock_filter') == 'low' ? 'selected' : '' }}>Below Restock Threshold (Low Stock)</option>
                                <option value="out" {{ request('stock_filter') == 'out' ? 'selected' : '' }}>Zero Stock (Out of Stock)</option>
                            </select>
                        </div>
                    </div>
                    @endif

                    <!-- EXPENDITURE FILTERS -->
                    @if(request('report_type') === 'dept_spending')
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label mini-label">Fiscal Date Range</label>
                            <div class="input-group">
                                <input type="date" name="date_from" value="{{ request('date_from') }}" class="form-control border-0 bg-light">
                                <input type="date" name="date_to" value="{{ request('date_to') }}" class="form-control border-0 bg-light">
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- 3. SELECTABLE PDF DATA COLUMNS (CHECKBOXES) -->
                    <div class="pt-3 border-top">
                        <div class="nav-section-label mb-2 text-primary">Select Columns to Include in Generated Document</div>
                        <div class="d-flex flex-wrap gap-3">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="checkbox" name="columns[]" value="specs" id="col_specs" checked>
                                <label class="form-check-label small" for="col_specs">Technical Specifications</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="checkbox" name="columns[]" value="costs" id="col_costs" checked>
                                <label class="form-check-label small" for="col_costs">Unit Pricing & Totals</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="checkbox" name="columns[]" value="remarks" id="col_remarks" checked>
                                <label class="form-check-label small" for="col_remarks">Official Purpose / Remarks</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="checkbox" name="columns[]" value="audit" id="col_audit" checked>
                                <label class="form-check-label small" for="col_audit">Signatory Audit Timeline</label>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card-footer bg-light border-0 p-4 d-flex justify-content-between align-items-center">
                    <button type="submit" class="btn btn-outline-dark px-4 rounded-pill fw-bold">
                        <i data-lucide="filter" class="me-1" style="width: 14px;"></i> Apply Filter & Preview Screen
                    </button>

                    <button type="submit" name="download_pdf" value="1" class="btn btn-htc px-5 rounded-pill fw-bold shadow-sm d-flex align-items-center">
                        <i data-lucide="file-down" class="me-2" style="width: 18px;"></i> Generate Official Document (PDF)
                    </button>
                </div>
            </div>
        </form>

        <!-- 4. LIVE SCREEN PREVIEW -->
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-white">
            <div class="card-header bg-white border-bottom p-4 d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="fw-bold mb-0">Document Live Data Preview</h6>
                    <small class="text-muted">{{ $records->count() }} records matched current filter criteria</small>
                </div>
                <div class="text-end">
                    <small class="text-muted text-uppercase fw-bold" style="font-size: 9px;">Calculated Aggregate Total</small>
                    <h5 class="fw-black text-success mb-0">₱{{ number_format($totalValue, 2) }}</h5>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" style="font-size: 0.88rem;">
                    @if($reportType === 'requisitions')
                        <thead class="bg-light small text-muted uppercase">
                            <tr>
                                <th class="ps-4 py-3">Req #</th>
                                <th>Requestor</th>
                                <th>Dept</th>
                                <th>Tier</th>
                                <th>Primary Item(s)</th>
                                <th class="text-center">Status</th>
                                <th class="text-end pe-4">Total Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($records as $r)
                            <tr>
                                <td class="ps-4 fw-mono text-muted">#{{ str_pad($r->id, 4, '0', STR_PAD_LEFT) }}</td>
                                <td class="fw-bold text-dark">{{ $r->user->name }}</td>
                                <td><span class="badge bg-light text-dark border">{{ $r->user->department }}</span></td>
                                <td><span class="badge {{ $r->request_type === 'major' ? 'bg-primary' : 'bg-secondary' }}">{{ strtoupper($r->request_type) }}</span></td>
                                <td>
                                    <div>{{ $r->items->first()->item_name ?? 'N/A' }}</div>
                                    @if($r->items->count() > 1)
                                        <small class="text-muted">+{{ $r->items->count() - 1 }} other item(s)</small>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-light text-dark border px-2 py-1">{{ str_replace('_', ' ', $r->status) }}</span>
                                </td>
                                <td class="text-end pe-4 fw-bold">₱{{ number_format($r->grand_total, 2) }}</td>
                            </tr>
                            @empty
                            <tr><td colspan="7" class="text-center py-5 text-muted">No records match the configured parameters.</td></tr>
                            @endforelse
                        </tbody>
                    @elseif($reportType === 'inventory')
                        <thead class="bg-light small text-muted uppercase">
                            <tr>
                                <th class="ps-4 py-3">Item Name</th>
                                <th>Category</th>
                                <th>Stock Level</th>
                                <th>Unit Price</th>
                                <th class="text-end pe-4">Asset Value</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($records as $item)
                            <tr>
                                <td class="ps-4">
                                    <div class="fw-bold text-dark">{{ $item->item_name }}</div>
                                    <small class="text-muted">{{ $item->brand }}</small>
                                </td>
                                <td><span class="badge bg-light text-dark border">{{ $item->category }}</span></td>
                                <td>{{ $item->quantity }} {{ $item->unit }}</td>
                                <td>₱{{ number_format($item->unit_price, 2) }}</td>
                                <td class="text-end pe-4 fw-bold">₱{{ number_format($item->quantity * $item->unit_price, 2) }}</td>
                            </tr>
                            @empty
                            <tr><td colspan="5" class="text-center py-5 text-muted">No inventory records found.</td></tr>
                            @endforelse
                        </tbody>
                    @elseif($reportType === 'dept_spending')
                        <thead class="bg-light small text-muted uppercase">
                            <tr>
                                <th class="ps-4 py-3">Academic Department</th>
                                <th class="text-center">Total Completed Requisitions</th>
                                <th class="text-end">Average Spend / Order</th>
                                <th class="text-end pe-4">Total Expenditure</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($records as $dept)
                            <tr>
                                <td class="ps-4 fw-bold text-dark">{{ $dept->department }}</td>
                                <td class="text-center">{{ $dept->total_requests }}</td>
                                <td class="text-end">₱{{ number_format($dept->avg_cost, 2) }}</td>
                                <td class="text-end pe-4 fw-bold text-success">₱{{ number_format($dept->total_spent, 2) }}</td>
                            </tr>
                            @empty
                            <tr><td colspan="4" class="text-center py-5 text-muted">No departmental expenditure records found.</td></tr>
                            @endforelse
                        </tbody>
                    @endif
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
