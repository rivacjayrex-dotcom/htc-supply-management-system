<section>
    <div class="d-flex align-items-center mb-4">
        <div class="position-relative">
            @if(Auth::user()->profile_photo)
                <img src="{{ asset('storage/' . Auth::user()->profile_photo) }}" class="rounded-circle shadow-sm border" style="width: 80px; height: 80px; object-fit: cover;">
            @else
                <div class="rounded-circle bg-light d-flex align-items-center justify-content-center border" style="width: 80px; height: 80px; color: var(--htc-green);">
                    <i data-lucide="user" style="width: 40px;"></i>
                </div>
            @endif
        </div>

        <div class="ms-4">
            <label class="form-label small fw-bold text-muted uppercase d-block">Change Profile Photo</label>
            <input type="file" name="profile_photo" class="form-control form-control-sm border-0 bg-light shadow-none">
            <small class="text-muted" style="font-size: 10px;">Recommended: Square JPG or PNG, max 2MB.</small>
        </div>
    </div>

    <form method="post" action="{{ route('profile.update') }}" class="mt-2" enctype="multipart/form-data">
        @csrf
        @method('patch')

        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label small fw-bold text-muted uppercase">Full Name</label>
                <input name="name" type="text" class="form-control border-0 bg-light py-2 px-3 rounded-3 shadow-none" value="{{ old('name', $user->name) }}" required>
            </div>

            <div class="col-md-6">
                <label class="form-label small fw-bold text-muted uppercase">Email Address</label>
                <input name="email" type="email" class="form-control border-0 bg-light py-2 px-3 rounded-3 shadow-none" value="{{ old('email', $user->email) }}" required>
            </div>

            <div class="col-md-12">
                <label class="form-label small fw-bold text-muted uppercase">School ID (Read-only)</label>
                <input type="text" class="form-control border-0 bg-light py-2 px-3 rounded-3" value="{{ $user->school_id }}" readonly style="cursor: not-allowed;">
            </div>
        </div>

        <div class="mt-4 d-flex align-items-center">
            <button type="submit" class="btn fw-bold px-4 py-2 text-white" style="background-color: var(--htc-green); border-radius: 10px;">
                Save Changes
            </button>

            @if (session('status') === 'profile-updated')
                <p class="text-success small ms-3 mb-0 fw-bold">Saved successfully!</p>
            @endif
        </div>
    </form>
</section>
