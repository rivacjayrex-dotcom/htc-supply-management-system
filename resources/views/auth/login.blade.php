<x-guest-layout>
    <div class="container-fluid p-0 h-100">
        <div class="row g-0 h-100">

            <!-- LEFT SIDE: GREEN PANEL -->
            <div class="col-md-5 d-flex flex-column justify-content-between p-4 text-white" style="background-color: #144521;">
                <div>
                    <div class="d-flex align-items-center mb-4">
                        <div class="bg-white rounded-circle p-1 me-2" style="width: 30px; height: 30px;">
                            <img src="{{ asset('images/android-chrome-192x192.png') }}" class="w-100" onerror="this.src='https://via.placeholder.com/30'">
                        </div>
                        <small class="fw-bold text-uppercase tracking-widest" style="font-size: 10px;">Supply Management</small>
                    </div>

                    <h2 class="fw-bold lh-sm mb-3" style="font-size: 1.8rem;">Institutional <br> Resource Control.</h2>
                    <p class="opacity-75 small" style="max-width: 220px; line-height: 1.4;">
                        Secure access to the college's unified procurement and inventory management ecosystem.
                    </p>
                </div>

                <div class="opacity-50 text-uppercase tracking-widest" style="font-size: 9px;">
                    Holy Trinity College <br> of General Santos City
                </div>
            </div>

            <!-- RIGHT SIDE: LOGIN FORM -->
            <div class="col-md-7 d-flex flex-column justify-content-center p-4 bg-white">
                <div class="mb-4">
                    <h3 class="fw-black text-uppercase tracking-tighter m-0" style="font-size: 1.8rem;"><b>GOODHOLY!</b></h3>
                    <p class="text-muted" style="font-size: 0.75rem;">Please enter your credentials to continue</p>
                </div>

                <form method="POST" action="{{ route('login') }}">
                    @csrf

                    <!-- Username/Login -->
                    <div class="mb-3">
                        <label class="form-label fw-bold text-muted text-uppercase tracking-widest" style="font-size: 10px;">Username or School ID</label>
                        <div class="input-group input-group-sm">
                            <span class="input-group-text bg-light border-0 text-muted">@</span>
                            <input type="text" name="login" value="{{ old('login') }}" required
                                class="form-control bg-light border-0 shadow-none text-muted" style="font-size: 0.85rem;" placeholder="Username or School ID">
                        </div>
                        <x-input-error :messages="$errors->get('login')" class="mt-1" />
                    </div>

                    <!-- Password Input with Toggle -->
                    <div class="mb-4">
                        <label class="form-label fw-bold text-muted text-uppercase tracking-widest" style="font-size: 10px;">Password</label>
                        <div class="input-group input-group-sm">
                            <span class="input-group-text bg-light border-0">
                                <i data-lucide="lock" style="width: 14px; color: #6c757d;"></i>
                            </span>
                            <input id="password" type="password" name="password" required
                                class="form-control bg-light border-0 shadow-none text-muted"
                                style="font-size: 0.85rem;" placeholder="Enter Password">

                            <!-- THE TOGGLE BUTTON -->
                            <button class="btn bg-light border-0" type="button" id="btnToggle">
                                <i data-lucide="eye" id="eyeIcon" style="width: 14px; color: #6c757d;"></i>
                            </button>
                        </div>
                        <x-input-error :messages="$errors->get('password')" class="mt-1" />
                    </div>

                    <button type="submit" class="btn btn-sm w-100 text-white fw-bold py-2 shadow-sm" style="background-color: #144521; border-radius: 6px; font-size: 0.9rem;">
                        Log In →
                    </button>

                    <div class="mt-4 pt-3 border-top text-center">
                        <p class="text-muted" style="font-size: 0.7rem;">New staff member? <a href="{{ route('register') }}" class="fw-bold text-decoration-none" style="color: #144521;">Request Access</a></p>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Initialize Lucide Icons for the Guest page
        lucide.createIcons();

        // Select elements
        const btnToggle = document.querySelector('#btnToggle');
        const passwordInput = document.querySelector('#password');
        const eyeIcon = document.querySelector('#eyeIcon');

        btnToggle.addEventListener('click', function () {
            // 1. Toggle the input type
            const isPassword = passwordInput.getAttribute('type') === 'password';
            passwordInput.setAttribute('type', isPassword ? 'text' : 'password');

            // 2. Toggle the icon name
            eyeIcon.setAttribute('data-lucide', isPassword ? 'eye-off' : 'eye');

            // 3. Re-render icons to show the change
            lucide.createIcons();
        });
    </script>
</x-guest-layout>
