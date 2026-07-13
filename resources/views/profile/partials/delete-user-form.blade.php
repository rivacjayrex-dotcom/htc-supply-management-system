<section>
    <!-- Trigger Button -->
    <button type="button" class="btn btn-outline-danger btn-sm fw-bold px-4 py-2"
            style="border-radius: 10px;"
            data-bs-toggle="modal" data-bs-target="#confirmUserDeletionModal">
        <i data-lucide="trash-2" class="me-1" style="width: 14px; vertical-align: middle;"></i>
        Permanently Delete Account
    </button>

    <!-- Bootstrap Deletion Modal -->
    <div class="modal fade" id="confirmUserDeletionModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg rounded-4">
                <form method="post" action="{{ route('profile.destroy') }}" class="p-4">
                    @csrf
                    @method('delete')

                    <div class="modal-header border-0 pb-0">
                        <h5 class="modal-title fw-bold text-danger" id="deleteModalLabel">Confirm Account Deletion</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <div class="modal-body">
                        <p class="text-muted small">
                            {{ __('Once your account is deleted, all of its resources and data will be permanently deleted. Please enter your password to confirm you would like to permanently delete your account.') }}
                        </p>

                        <!-- Password Input -->
                        <div class="mt-4">
                            <label class="form-label small fw-bold text-muted text-uppercase tracking-widest" style="font-size: 10px;">Verify Password</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light border-0"><i data-lucide="lock" style="width: 14px;"></i></span>
                                <input id="password" name="password" type="password"
                                    class="form-control border-0 bg-light py-2 px-3 rounded-end-3 shadow-none"
                                    placeholder="Enter password to confirm" required>
                            </div>
                            <x-input-error :messages="$errors->userDeletion->get('password')" class="mt-2" />
                        </div>
                    </div>

                    <div class="modal-footer border-0 pt-0 mt-4 d-flex gap-2">
                        <button type="button" class="btn btn-light fw-bold px-4 rounded-3 border flex-grow-1" data-bs-toggle="modal" data-bs-target="#confirmUserDeletionModal">
                            Cancel
                        </button>
                        <button type="submit" class="btn btn-danger fw-bold px-4 rounded-3 flex-grow-1 shadow-sm">
                            Delete My Account
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Script to automatically show modal if there are validation errors -->
    @if($errors->userDeletion->isNotEmpty())
        <script>
            window.addEventListener('DOMContentLoaded', () => {
                const deleteModal = new bootstrap.Modal('#confirmUserDeletionModal');
                deleteModal.show();
            });
        </script>
    @endif
</section>
