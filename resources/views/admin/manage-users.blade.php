<x-app-layout>
    <x-slot name="header">Institutional User Registry</x-slot>

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
        @if(session('success'))
            <div class="alert alert-success border-0 shadow-sm mb-4 rounded-4">{{ session('success') }}</div>
        @endif

        <div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-white">
            <div class="card-header bg-white border-0 p-4">
                <h6 class="fw-bold mb-0">Manage Verified Personnel</h6>
                <p class="text-muted small mb-0">Update institutional roles or modify system access for faculty and staff.</p>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light small text-muted uppercase">
                        <tr>
                            <th class="ps-4 py-3">Personnel</th>
                            <th>Current Role</th>
                            <th>Department</th>
                            <th class="text-end pe-4">Manage Access</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $u)
                        <tr>
                            <td class="ps-4">
                                <div class="fw-bold small text-dark">{{ $u->name }}</div>
                                <div class="text-muted" style="font-size: 10px;">{{ $u->school_id }} • {{ $u->email }}</div>
                            </td>
                            <td>
                                <span class="badge bg-success-subtle text-success border border-success rounded-pill px-3 py-1" style="font-size: 9px;">
                                    {{ strtoupper(str_replace('_', ' ', $u->role)) }}
                                </span>
                            </td>
                            <td>
                                <span class="small fw-semibold text-muted">{{ $u->department ?? 'N/A' }}</span>
                            </td>
                            <td class="pe-4 text-end">
                                <button type="button"
                                        class="btn btn-sm btn-light border rounded-pill px-3 fw-bold"
                                        style="font-size: 10px;"
                                        @click="openEdit({{ json_encode($u) }})">
                                    Edit Role →
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center py-5 text-muted">No users found.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
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
                            <p class="small text-muted mb-3">Change designation for <strong class="text-dark" x-text="selectedUser.name"></strong>:</p>

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
                            <button type="submit" class="btn btn-htc w-100 py-2 rounded-3 fw-bold">Save Role Changes</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
