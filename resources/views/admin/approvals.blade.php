<x-app-layout>
    <x-slot name="header">
        {{ Auth::user()->role == 'smo' ? __('Release Queue') : __('Pending Request Approvals') }}
    </x-slot>

    <div class="container-fluid py-2" x-data="approvalManager()">
        <!-- ALERTS (Keep existing) -->
        @if(session('success'))
            <div class="alert alert-success border-0 shadow-sm mb-4 rounded-4 d-flex align-items-center">
                <i data-lucide="check-circle" class="me-2"></i> {{ session('success') }}
            </div>
        @endif

        <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
            <div class="card-header bg-white border-0 p-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <h5 class="fw-bold mb-0">Requisition Queue</h5>
                        <p class="text-muted small mb-0">Monitor and process procurement requests.</p>
                    </div>
                </div>

                <!-- TABS FOR SMO -->
                @if(Auth::user()->role == 'smo')
                    <ul class="nav nav-pills gap-2" id="smoTabs" role="tablist">
                        <li class="nav-item">
                            <button class="nav-link active rounded-pill px-4 fw-bold small text-uppercase" id="minor-tab" data-bs-toggle="pill" data-bs-target="#minor" type="button">
                                Minor Requests <span class="badge bg-white text-primary ms-2">{{ $pendingMinor->count() }}</span>
                            </button>
                        </li>
                        <li class="nav-item">
                            <button class="nav-link rounded-pill px-4 fw-bold small text-uppercase" id="major-tab" data-bs-toggle="pill" data-bs-target="#major" type="button">
                                Major Requests <span class="badge bg-white text-warning ms-2">{{ $pendingMajor->count() }}</span>
                            </button>
                        </li>
                    </ul>
                @endif
            </div>

            <div class="card-body p-0">
                <div class="tab-content">
                    @if(Auth::user()->role == 'smo')
                        <!-- TAB 1: MINOR (SMO ONLY) -->
                        <div class="tab-pane fade show active" id="minor">
                            @include('admin.partials.approvals-table', ['requests' => $pendingMinor])
                        </div>
                        <!-- TAB 2: MAJOR (SMO ONLY) -->
                        <div class="tab-pane fade" id="major">
                            @include('admin.partials.approvals-table', ['requests' => $pendingMajor])
                        </div>
                    @else
                        <!-- STANDARD VIEW FOR BOSSES -->
                        <div class="tab-pane fade show active">
                            @include('admin.partials.approvals-table', ['requests' => $pendingRequests])
                        </div>
                    @endif
                </div>
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

                        <!-- DYNAMIC TIMELINE (Alpine Only) -->
                        <div class="mb-5 px-2">
                            <div class="tracking-stepper">
                                <!-- Step 1 -->
                                <div class="step-item completed" :class="selectedReq.status == 'pending' ? 'in-progress' : ''">
                                    <div class="step-icon"><i data-lucide="send"></i></div>
                                    <div class="step-label">Submitted</div>
                                </div>

                                <!-- Step 2: Dept Head -->
                                <div class="step-item"
                                    :class="{
                                        'completed': ['approved_dept', 'approved_vp', 'approved_provost', 'approved_president', 'released'].includes(selectedReq.status),
                                        'active': selectedReq.status == 'pending',
                                        'in-progress': selectedReq.status == 'approved_dept'
                                    }">
                                    <div class="step-icon"><i data-lucide="user-check"></i></div>
                                    <div class="step-label">Dept Head</div>
                                </div>

                                <!-- Step 3: VP -->
                                <div class="step-item" :class="['approved_vp', 'approved_provost', 'approved_president', 'released'].includes(selectedReq.status) ? 'completed' : (selectedReq.status == 'approved_dept' ? 'active' : '')">
                                    <div class="step-icon"><i data-lucide="shield-check"></i></div>
                                    <div class="step-label" x-text="selectedReq.request_type == 'minor' ? 'VP Finance' : 'VP Admin'"></div>
                                </div>

                                <!-- Step 4: Provost (Major Only) -->
                                <template x-if="selectedReq.request_type == 'major'">
                                    <div class="step-item" :class="['approved_provost', 'approved_president', 'released'].includes(selectedReq.status) ? 'completed' : (selectedReq.status == 'approved_vp' ? 'active' : '')">
                                        <div class="step-icon"><i data-lucide="graduation-cap"></i></div>
                                        <div class="step-label">Provost</div>
                                    </div>
                                </template>

                                <!-- Step 5: President (Major Only) -->
                                <template x-if="selectedReq.request_type == 'major'">
                                    <div class="step-item" :class="['approved_president', 'released'].includes(selectedReq.status) ? 'completed' : (selectedReq.status == 'approved_provost' ? 'active' : '')">
                                        <div class="step-icon"><i data-lucide="award"></i></div>
                                        <div class="step-label">President</div>
                                    </div>
                                </template>

                                <!-- Step 6: Released -->
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
                                            <td class="text-end pe-3 fw-bold" x-text="'₱' + parseFloat(item.subtotal).toLocaleString(undefined, {minimumFractionDigits:2})"></td>
                                        </tr>
                                    </template>
                                </tbody>
                                <tfoot class="bg-light fw-bold">
                                    <tr>
                                        <td colspan="2" class="ps-3 text-uppercase small">Grand Total</td>
                                        <td class="text-end pe-3 text-success" x-text="'₱' + parseFloat(selectedReq.grand_total).toLocaleString(undefined, {minimumFractionDigits:2})"></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>

                        <!-- ACTION FORMS -->
                        <div class="mt-4">
                            @if(Auth::user()->role == 'smo')
                                <form :action="'{{ url('/admin/requests') }}/' + selectedReq.id + '/release'" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-primary w-100 py-3 fw-bold rounded-3 shadow-sm">
                                        <i data-lucide="package-check" class="me-2" style="width:18px; vertical-align:middle"></i> CONFIRM RELEASE
                                    </button>
                                </form>
                            @else
                                <form :action="'{{ url('/admin/requests') }}/' + selectedReq.id + '/status'" method="POST">
                                    @csrf
                                    <div class="mb-3">
                                        <label class="form-label small fw-bold text-muted uppercase">Signatory Remarks</label>
                                        <textarea name="remarks" class="form-control border-0 bg-light rounded-3" rows="2" placeholder="Optional note..."></textarea>
                                    </div>
                                    <div class="d-flex gap-2">
                                        <button name="status" value="rejected" class="btn btn-outline-danger fw-bold flex-grow-1 py-2 rounded-3">Reject</button>
                                        <button name="status" value="approved" class="btn btn-success fw-bold flex-grow-1 py-2 shadow-sm rounded-3">Approve</button>
                                    </div>
                                </form>
                            @endif
                        </div>
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

                    setTimeout(() => lucide.createIcons(), 150);
                }
            }
        }
    </script>
</x-app-layout>
