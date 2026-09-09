<x-app-layout>
    <x-slot name="header">Institutional Personnel Registry</x-slot>

    <div class="container-fluid py-2" x-data="{
        editModal: null,
        selectedUser: { id: '', name: '', role: '', department: '' },
        openEdit(user) {
            this.selectedUser = user;
            if (!this.editModal) {
                this.editModal = new bootstrap.Modal(document.getElementById('editRoleModal'));
            }
            this.editModal.show();
        }
    }">
        <!-- TOP TOOLBAR & STATS -->
        <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
            <div>
                <h4 class="fw-black text-dark mb-0">Institutional Personnel</h4>
                <p class="text-muted small mb-0">Manage roles, signatories, and department rosters across HTC.</p>
            </div>

            <!-- SEARCH BAR -->
            <form action="{{ route('admin.users.manage') }}" method="GET" class="d-flex align-items-center gap-2">
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-white border-end-0 rounded-start-pill text-muted">
                        <i data-lucide="search" style="width: 14px;"></i>
                    </span>
                    <input type="text" name="search" value="{{ request('search') }}"
                           class="form-control border-start-0 rounded-end-pill bg-white shadow-none"
                           placeholder="Search name, ID, or email..." style="font-size: 11px; min-width: 220px;">
                </div>
                @if(request('search'))
                    <a href="{{ route('admin.users.manage') }}" class="btn btn-sm btn-link text-danger text-decoration-none">Clear</a>
                @endif
            </form>
        </div>

        @if(session('success'))
            <div class="alert alert-success border-0 shadow-sm mb-4 rounded-4 d-flex align-items-center">
                <i data-lucide="check-circle" class="me-2" style="width: 18px;"></i>
                <div>{{ session('success') }}</div>
            </div>
        @endif

        <!-- CATEGORY NAVIGATION TABS -->
        <ul class="nav nav-pills gap-2 mb-4" id="personnelTabs" role="tablist">
            <li class="nav-item">
                <button class="nav-link active rounded-pill px-4 fw-bold small text-uppercase" data-bs-toggle="pill" data-bs-target="#executives" type="button">
                    <i data-lucide="award" class="me-1" style="width: 14px; vertical-align: middle;"></i> Key Signatories ({{ $executives->count() }})
                </button>
            </li>
            <li class="nav-item">
                <button class="nav-link rounded-pill px-4 fw-bold small text-uppercase" data-bs-toggle="pill" data-bs-target="#deptHeads" type="button">
                    <i data-lucide="briefcase" class="me-1" style="width: 14px; vertical-align: middle;"></i> Deans & Dept Heads ({{ $deptHeads->count() }})
                </button>
            </li>
            <li class="nav-item">
                <button class="nav-link rounded-pill px-4 fw-bold small text-uppercase" data-bs-toggle="pill" data-bs-target="#facultyStaff" type="button">
                    <i data-lucide="users" class="me-1" style="width: 14px; vertical-align: middle;"></i> Faculty & Staff by Dept
                </button>
            </li>
        </ul>

        <!-- TAB CONTENT PANES -->
        <div class="tab-content">

            <!-- TAB 1: KEY SIGNATORIES & EXECUTIVES -->
            <div class="tab-pane fade show active" id="executives">
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-white">
                    <div class="card-header bg-white border-0 p-4 pb-2">
                        <h6 class="fw-bold text-dark mb-0">Executive Council & Supply Officers</h6>
                        <small class="text-muted">Users possessing institutional approval and release authority.</small>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light small text-muted uppercase">
                                <tr>
                                    <th class="ps-4 py-3">Official</th>
                                    <th>Designation / Role</th>
                                    <th>Department</th>
                                    <th class="text-end pe-4">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($executives as $u)
                                <tr>
                                    <td class="ps-4">
                                        <div class="fw-bold text-dark">{{ $u->name }}</div>
                                        <small class="text-muted">{{ $u->school_id }} • {{ $u->email }}</small>
                                    </td>
                                    <td>
                                        @php
                                            $roleBadge = match($u->role) {
                                                'president'  => 'bg-dark text-white',
                                                'provost'    => 'bg-primary text-white',
                                                'vp_admin'   => 'bg-info-subtle text-info border border-info',
                                                'vp_finance' => 'bg-warning-subtle text-warning border border-warning',
                                                'smo'        => 'bg-success text-white',
                                                default      => 'bg-secondary text-white'
                                            };
                                        @endphp
                                        <span class="badge {{ $roleBadge }} rounded-pill px-3 py-1" style="font-size: 9px;">
                                            {{ strtoupper(str_replace('_', ' ', $u->role)) }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="small text-muted">{{ $u->department ?? 'Institutional' }}</span>
                                    </td>
                                    <td class="pe-4 text-end">
                                        <button type="button" class="btn btn-sm btn-light border rounded-pill px-3 fw-bold" style="font-size: 10px;" @click="openEdit({{ json_encode($u) }})">
                                            Modify Role →
                                        </button>
                                    </td>
                                </tr>
                                @empty
                                <tr><td colspan="4" class="text-center py-4 text-muted small">No executive signatories registered.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- TAB 2: DEANS & DEPT HEADS -->
            <div class="tab-pane fade" id="deptHeads">
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-white">
                    <div class="card-header bg-white border-0 p-4 pb-2">
                        <h6 class="fw-bold text-dark mb-0">Department Deans & Heads</h6>
                        <small class="text-muted">First-tier approval authorities for departmental supply requisitions.</small>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light small text-muted uppercase">
                                <tr>
                                    <th class="ps-4 py-3">Dean / Head</th>
                                    <th>Assigned Department</th>
                                    <th>Institutional Email</th>
                                    <th class="text-end pe-4">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($deptHeads as $u)
                                <tr>
                                    <td class="ps-4">
                                        <div class="fw-bold text-dark">{{ $u->name }}</div>
                                        <small class="text-muted">{{ $u->school_id }}</small>
                                    </td>
                                    <td>
                                        <span class="badge bg-success-subtle text-success border border-success rounded-pill px-3 py-1" style="font-size: 9px;">
                                            {{ $u->department }} DEAN / HEAD
                                        </span>
                                    </td>
                                    <td class="small text-muted">{{ $u->email }}</td>
                                    <td class="pe-4 text-end">
                                        <button type="button" class="btn btn-sm btn-light border rounded-pill px-3 fw-bold" style="font-size: 10px;" @click="openEdit({{ json_encode($u) }})">
                                            Modify Role →
                                        </button>
                                    </td>
                                </tr>
                                @empty
                                <tr><td colspan="4" class="text-center py-4 text-muted small">No department heads assigned yet.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- TAB 3: FACULTY & STAFF GROUPED BY DEPARTMENT -->
            <div class="tab-pane fade" id="facultyStaff">
                @php
                    $departments = ['CETE', 'CTE', 'CBMA', 'CCJE', 'CAS', 'General'];
                @endphp

                @foreach($departments as $dept)
                    @php
                        $deptUsers = $employeesByDept->get($dept, collect());
                    @endphp
                    <div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-white mb-4">
                        <div class="card-header bg-light border-0 px-4 py-3 d-flex justify-content-between align-items-center">
                            <div class="d-flex align-items-center">
                                <span class="badge bg-dark rounded-pill px-3 me-2" style="font-size: 10px;">{{ $dept }}</span>
                                <span class="fw-bold text-dark small">Department Faculty & Staff</span>
                            </div>
                            <small class="text-muted fw-bold">{{ $deptUsers->count() }} Member(s)</small>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="small text-muted uppercase" style="font-size: 9px;">
                                    <tr>
                                        <th class="ps-4 py-2">Personnel Name</th>
                                        <th>School ID</th>
                                        <th>Email</th>
                                        <th class="text-end pe-4">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($deptUsers as $u)
                                    <tr>
                                        <td class="ps-4">
                                            <div class="fw-semibold text-dark">{{ $u->name }}</div>
                                        </td>
                                        <td class="small text-muted fw-mono">{{ $u->school_id }}</td>
                                        <td class="small text-muted">{{ $u->email }}</td>
                                        <td class="pe-4 text-end">
                                            <button type="button" class="btn btn-sm btn-light border rounded-pill px-3 fw-bold" style="font-size: 10px;" @click="openEdit({{ json_encode($u) }})">
                                                Edit →
                                            </button>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr><td colspan="4" class="text-center py-3 text-muted small italic">No faculty/staff registered under {{ $dept }}.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- EDIT ROLE MODAL -->
        <div class="modal fade" id="editRoleModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-sm">
                <div class="modal-content border-0 shadow-lg rounded-4">
                    <div class="modal-header border-0 bg-light p-3">
                        <h6 class="modal-title fw-bold">Update Role</h6>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form :action="'/admin/users/' + selectedUser.id + '/approve'" method="POST">
                        @csrf
                        <div class="modal-body p-4">
                            <p class="small text-muted mb-3">Change authority designation for <strong class="text-dark" x-text="selectedUser.name"></strong>:</p>

                            <label class="form-label mini-label">Designated Role</label>
                            <select name="role" class="form-select border bg-light py-2 rounded-3 shadow-none fw-bold" style="font-size: 11px;" x-model="selectedUser.role" required>
                                <option value="employee">Faculty / Staff</option>
                                <option value="dept_head">Department Head (Dean)</option>
                                <option value="vp_finance">VP for Finance</option>
                                <option value="vp_admin">VP for Administration</option>
                                <option value="provost">School Provost</option>
                                <option value="president">School President</option>
                                <option value="smo">SMO In-Charge</option>
                            </select>
                        </div>
                        <div class="modal-footer border-0 p-3 pt-0">
                            <button type="submit" class="btn btn-htc w-100 py-2 rounded-3 fw-bold">Save Designation</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <style>
        .mini-label { font-size: 9px; font-weight: 800; text-transform: uppercase; color: #94a3b8; letter-spacing: 1px; margin-bottom: 5px; }
        .btn-htc { background-color: var(--htc-green); color: white; border: none; }
        .btn-htc:hover { background-color: #0d2e16; color: white; }
        #personnelTabs .nav-link { color: #64748b; border: 1px solid #e2e8f0; background: white; }
        #personnelTabs .nav-link.active { background-color: var(--htc-green) !important; color: white !important; border-color: var(--htc-green) !important; box-shadow: 0 4px 12px rgba(24, 91, 59, 0.2); }
    </style>
</x-app-layout>
