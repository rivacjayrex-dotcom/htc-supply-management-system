<x-app-layout>
    <x-slot name="header">Institutional User Registry</x-slot>

    <div class="container-fluid py-2">
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-white">
            <div class="card-header bg-white border-0 p-4">
                <h6 class="fw-bold mb-0">Manage Verified Personnel</h6>
                <p class="text-muted extra-small mb-0">Update institutional roles or manage system access for all faculty and staff.</p>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light small text-muted uppercase">
                        <tr>
                            <th class="ps-4 py-3">Personnel</th>
                            <th>Current Role</th>
                            <th class="text-end pe-4">Manage Access</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($users as $u)
                        <tr>
                            <td class="ps-4">
                                <div class="fw-bold small text-dark">{{ $u->name }}</div>
                                <div class="text-muted" style="font-size: 9px;">{{ $u->school_id }} • {{ $u->department }}</div>
                            </td>
                            <td>
                                <span class="badge bg-success-subtle text-success border border-success rounded-pill px-3" style="font-size: 9px;">
                                    {{ strtoupper(str_replace('_', ' ', $u->role)) }}
                                </span>
                            </td>
                            <td class="pe-4 text-end">
                                <!-- Quick Role Edit Button -->
                                <button class="btn btn-sm btn-light border rounded-pill px-3 fw-bold" style="font-size: 10px;">
                                    Edit Role
                                </button>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
