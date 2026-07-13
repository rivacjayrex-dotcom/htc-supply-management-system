<x-app-layout>
    <x-slot name="header">User Access Requests</x-slot>

    <div class="container-fluid py-2">
        <div class="card border-0 shadow-sm rounded-4">
            <div class="table-responsive p-4">
                <table class="table align-middle">
                    <thead>
                        <tr class="text-muted small uppercase">
                            <th>School ID</th>
                            <th>Full Name</th>
                            <th>Email</th>
                            <th class="text-end">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $u)
                        <tr>
                            <td class="fw-bold">{{ $u->school_id }}</td>
                            <td>{{ $u->name }}</td>
                            <td>{{ $u->email }}</td>
                            <td class="text-end">
                                <form action="{{ route('admin.users.approve', $u->id) }}" method="POST">
                                    @csrf
                                    <button class="btn btn-sm btn-success fw-bold px-3 rounded-pill">
                                        Approve Access
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="4" class="text-center py-4 text-muted">No pending requests.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
