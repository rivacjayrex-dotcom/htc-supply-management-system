<x-guest-layout>
    <div class="container-fluid p-0 h-100">
        <div class="row g-0 h-100">

            <!-- LEFT SIDE: GREEN PANEL (Scaled Down) -->
            <div class="col-md-5 d-flex flex-column justify-content-between p-3 text-white" style="background-color: #144521;">
                <div>
                    <div class="d-flex align-items-center mb-3">
                        <div class="bg-white rounded-circle p-1 me-2" style="width: 25px; height: 25px;">
                            <img src="{{ asset('images/android-chrome-192x192.png') }}" class="w-100" onerror="this.src='https://via.placeholder.com/20'">
                        </div>
                        <small class="fw-bold text-uppercase tracking-widest" style="font-size: 8px;">Supply Management</small>
                    </div>

                    <h2 class="fw-bold lh-sm mb-2" style="font-size: 1.3rem;">Institutional <br> Resource Control.</h2>
                    <p class="opacity-75" style="max-width: 180px; line-height: 1.3; font-size: 0.75rem;">
                        Secure access to the college's unified procurement ecosystem.
                    </p>
                </div>

                <div class="opacity-50 text-uppercase tracking-widest" style="font-size: 8px;">
                    Holy Trinity College <br> General Santos City
                </div>
            </div>

            <!-- RIGHT SIDE: LOGIN FORM (Compact) -->
            <div class="col-md-7 d-flex flex-column justify-content-center p-4 bg-white">
                <div class="mb-3">
                    <h3 class="fw-black text-uppercase tracking-tighter m-0" style="font-size: 1.4rem;"><b>GOODHOLY!</b></h3>
                    <p class="text-muted" style="font-size: 0.7rem;">Enter your credentials to continue</p>
                </div>

                <form method="POST" action="{{ route('login') }}">
                    @csrf

                    <!-- Username/Login -->
                    <div class="mb-2">
                        <label class="form-label fw-bold text-muted text-uppercase tracking-widest" style="font-size: 9px; margin-bottom: 2px;">Username or School ID</label>
                        <div class="input-group input-group-sm">
                            <span class="input-group-text bg-light border-0 text-muted" style="font-size: 0.7rem;">@</span>
                            <input type="text" name="login" value="{{ old('login') }}" required
                                class="form-control bg-light border-0 shadow-none text-muted" style="font-size: 0.8rem;" placeholder="Username or ID">
                        </div>
                        <x-input-error :messages="$errors->get('login')" class="mt-1" style="font-size: 10px;" />
                    </div>

                    <!-- Password -->
                    <div class="mb-3">
                        <label class="form-label fw-bold text-muted text-uppercase tracking-widest" style="font-size: 9px; margin-bottom: 2px;">Password</label>
                        <div class="input-group input-group-sm">
                            <span class="input-group-text bg-light border-0">
                                <i data-lucide="lock" style="width: 12px; color: #6c757d;"></i>
                            </span>
                            <input id="password" type="password" name="password" required
                                class="form-control bg-light border-0 shadow-none text-muted"
                                style="font-size: 0.8rem;" placeholder="Enter Password">

                            <button class="btn bg-light border-0" type="button" id="btnToggle">
                                <i data-lucide="eye" id="eyeIcon" style="width: 12px; color: #6c757d;"></i>
                            </button>
                        </div>
                        <x-input-error :messages="$errors->get('password')" class="mt-1" style="font-size: 10px;" />
                    </div>

                    <button type="submit" class="btn btn-sm w-100 text-white fw-bold py-2 shadow-sm" style="background-color: #144521; border-radius: 6px; font-size: 0.8rem;">
                        Log In →
                    </button>

                    <div class="mt-3 pt-2 border-top text-center">
                        <p class="text-muted" style="font-size: 0.65rem;">New staff member? <a href="{{ route('register') }}" class="fw-bold text-decoration-none" style="color: #144521;">Request Access</a></p>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        lucide.createIcons();

        const btnToggle = document.querySelector('#btnToggle');
        const passwordInput = document.querySelector('#password');
        const eyeIcon = document.querySelector('#eyeIcon');

        btnToggle.addEventListener('click', function () {
            const isPassword = passwordInput.getAttribute('type') === 'password';
            passwordInput.setAttribute('type', isPassword ? 'text' : 'password');
            eyeIcon.setAttribute('data-lucide', isPassword ? 'eye-off' : 'eye');
            lucide.createIcons();
        });
    </script>
</x-guest-layout>
