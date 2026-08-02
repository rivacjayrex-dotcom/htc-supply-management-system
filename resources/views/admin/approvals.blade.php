<x-app-layout>
    <x-slot name="header">
        {{ __('Pending Request Approvals') }}
    </x-slot>

    <div class="container-fluid py-2" x-data="approvalManager()">
        <!-- ALERTS -->
        @if(session('success'))
            <div class="alert alert-success border-0 shadow-sm mb-4 rounded-4 d-flex align-items-center">
                <i data-lucide="check-circle" class="me-2"></i> {{ session('success') }}
            </div>
        @endif

        <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
            <div class="card-header bg-white border-0 p-4">
                <h5 class="fw-bold mb-0">Requisition Queue</h5>
                <p class="text-muted small mb-0">Review and act on pending procurement requests.</p>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light text-muted small text-uppercase">
                        <tr>
                            <th class="ps-4 py-3">Req #</th>
                            <th class="py-3">Requestor</th>
                            <th class="py-3">Department</th>
                            <th class="py-3">Items</th>
                            <th class="py-3">Total Amount</th>
                            <th class="pe-4 py-3 text-end">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($pendingRequests as $req)
                            <tr>
                                <td class="ps-4 fw-mono text-muted">#{{ $req->id }}</td>
                                <td>
                                    <div class="fw-bold text-dark">{{ $req->user->name }}</div>
                                    <div class="small text-muted" style="font-size: 10px;">ID: {{ $req->user->school_id }}</div>
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark border px-2">{{ $req->user->department ?? 'General' }}</span>
                                </td>
                                <td>
                                    <span class="fw-semibold">{{ $req->items->first()->item_name }}</span>
                                    @if($req->items->count() > 1)
                                        <span class="text-muted small"> (+{{ $req->items->count() - 1 }} others)</span>
                                    @endif
                                </td>
                                <td class="fw-bold text-success">₱{{ number_format($req->grand_total, 2) }}</td>
                                <td class="pe-4 text-end">
                                    <button class="btn btn-sm btn-htc px-3 rounded-pill shadow-sm"
                                            @click="openDetails({{ $req }}, {{ $req->items }}, '{{ $req->user->name }}', '{{ $req->user->department }}')">
                                        See More & Review
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted italic">No pending requisitions for your approval.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- REVIEW MODAL -->
        <div class="modal fade" id="reviewModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
                    <div class="modal-header border-0 bg-light p-4">
                        <h5 class="modal-title fw-bold">Review Requisition <span class="text-muted" x-text="'#' + selectedReq.id"></span></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body p-4">
                        <!-- REQUESTOR INFO -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <small class="text-muted text-uppercase fw-bold" style="font-size: 10px;">Requestor</small>
                                <div class="fw-bold fs-5" x-text="requestorName"></div>
                            </div>
                            <div class="col-md-6 text-md-end">
                                <small class="text-muted text-uppercase fw-bold" style="font-size: 10px;">Department</small>
                                <div class="fw-bold fs-5 text-success" x-text="requestorDept"></div>
                            </div>
                        </div>

                        <!-- TIMELINE (The Shopee-style bar inside the modal) -->
                        <!-- DYNAMIC TIMELINE -->
                        <div class="mb-5 px-2">
                            <div class="tracking-stepper">
                                <!-- Step 1: Submission (Always there) -->
                                <div class="step-item completed">
                                    <div class="step-icon"><i data-lucide="send"></i></div>
                                    <div class="step-label">Submitted</div>
                                </div>

                                <!-- Step 2: Dept Head (Always there) -->
                                <div class="step-item" :class="['approved_dept', 'approved_vp', 'approved_provost', 'approved_president', 'released'].includes(selectedReq.status) ? 'completed' : (selectedReq.status == 'pending' ? 'active' : '')">
                                    <div class="step-icon"><i data-lucide="user-check"></i></div>
                                    <div class="step-label">Dept Head</div>
                                </div>

                                <!-- Step 3: VP (Always there - Finance for Minor, Admin for Major) -->
                                <div class="step-item" :class="['approved_vp', 'approved_provost', 'approved_president', 'released'].includes(selectedReq.status) ? 'completed' : (selectedReq.status == 'approved_dept' ? 'active' : '')">
                                    <div class="step-icon"><i data-lucide="shield-check"></i></div>
                                    <div class="step-label" x-text="selectedReq.request_type == 'minor' ? 'VP Finance' : 'VP Admin'"></div>
                                </div>

                                <!-- Step 4: Provost (ONLY FOR MAJOR) -->
                                <template x-if="selectedReq.request_type == 'major'">
                                    <div class="step-item" :class="['approved_provost', 'approved_president', 'released'].includes(selectedReq.status) ? 'completed' : (selectedReq.status == 'approved_vp' ? 'active' : '')">
                                        <div class="step-icon"><i data-lucide="graduation-cap"></i></div>
                                        <div class="step-label">Provost</div>
                                    </div>
                                </template>

                                <!-- Step 5: President (ONLY FOR MAJOR) -->
                                <template x-if="selectedReq.request_type == 'major'">
                                    <div class="step-item" :class="['approved_president', 'released'].includes(selectedReq.status) ? 'completed' : (selectedReq.status == 'approved_provost' ? 'active' : '')">
                                        <div class="step-icon"><i data-lucide="award"></i></div>
                                        <div class="step-label">President</div>
                                    </div>
                                </template>

                                <!-- Step 6: Released (Always the final goal) -->
                                <div class="step-item" :class="selectedReq.status == 'released' ? 'completed' : ''">
                                    <div class="step-icon"><i data-lucide="package"></i></div>
                                    <div class="step-label">Released</div>
                                </div>
                            </div>
                        </div>

                        <!-- ITEMS TABLE -->
                        <h6 class="fw-bold mb-3">Requested Items</h6>
                        <div class="table-responsive border rounded-3 mb-4">
                            <table class="table table-sm mb-0">
                                <thead class="bg-light">
                                    <tr>
                                        <th class="ps-3">Item</th>
                                        <th>Qty</th>
                                        <th class="text-end pe-3">Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <template x-for="item in selectedItems" :key="item.id">
                                        <tr>
                                            <td class="ps-3">
                                                <div class="fw-bold" x-text="item.item_name"></div>
                                                <small class="text-muted" x-text="item.specifications"></small>
                                            </td>
                                            <td x-text="item.quantity + ' ' + item.unit"></td>
                                            <td class="text-end pe-3 fw-bold" x-text="'₱' + parseFloat(item.subtotal).toLocaleString()"></td>
                                        </tr>
                                    </template>
                                </tbody>
                                <tfoot class="bg-light fw-bold">
                                    <tr>
                                        <td colspan="2" class="ps-3 text-uppercase small">Grand Total</td>
                                        <td class="text-end pe-3 text-success" x-text="'₱' + parseFloat(selectedReq.grand_total).toLocaleString()"></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>

                        <!-- DECISION FORM -->
                        <form :action="'/admin/requests/' + selectedReq.id + '/status'" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label small fw-bold text-muted uppercase">Signatory Remarks (Optional)</label>
                                <textarea name="remarks" class="form-control border-0 bg-light rounded-3" rows="2" placeholder="Explain the reason for approval or rejection..."></textarea>
                            </div>

                            <div class="d-flex gap-2">
                                <button name="status" value="rejected" class="btn btn-outline-danger fw-bold flex-grow-1 py-2">Reject Request</button>
                                <button name="status" value="approved" class="btn btn-success fw-bold flex-grow-1 py-2 shadow-sm">Approve Requisition</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function approvalManager() {
            return {
                selectedReq: {},
                selectedItems: [],
                requestorName: '',
                requestorDept: '',
                openDetails(req, items, name, dept) {
                    this.selectedReq = req;
                    this.selectedItems = items;
                    this.requestorName = name;
                    this.requestorDept = dept;

                    const modal = new bootstrap.Modal(document.getElementById('reviewModal'));
                    modal.show();

                    // Re-render icons inside modal
                    setTimeout(() => lucide.createIcons(), 100);
                }
            }
        }
    </script>
</x-app-layout>
