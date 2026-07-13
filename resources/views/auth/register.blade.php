<x-guest-layout>
    <div class="container-fluid p-0 h-100">
        <div class="row g-0 h-100">

            <!-- LEFT SIDE: GREEN PANEL -->
            <div class="col-md-5 d-flex flex-column justify-content-between p-4 text-white" style="background-color: #144521;">
                <div>
                    <div class="d-flex align-items-center mb-4">
                        <div class="bg-white rounded-circle p-1 me-2" style="width: 30px; height: 30px;">
                            <img src="{{ asset('images/school_seal.png') }}" class="w-100" onerror="this.src='https://via.placeholder.com/25'">
                        </div>
                        <small class="fw-bold text-uppercase tracking-widest" style="font-size: 10px;">Supply Management</small>
                    </div>
                    <h2 class="fw-bold lh-sm mb-3" style="font-size: 1.8rem;">Join the <br> GoodHoly <br> Experience!</h2>
                    <p class="opacity-75 small" style="max-width: 220px; line-height: 1.4;">
                        Create your institutional account to begin managing supply requests and inventory.
                    </p>
                </div>
                <div class="opacity-50 text-uppercase tracking-widest" style="font-size: 9px;">
                    Holy Trinity College <br> of General Santos City
                </div>
            </div>

            <!-- RIGHT SIDE: REGISTRATION FORM -->
            <!-- Changed 'justify-content-center' to 'justify-content-start' and added 'pt-4' -->
            <div class="col-md-7 d-flex flex-column justify-content-start p-4 bg-white overflow-auto h-100 shadow-inner">

                <!-- HEADER SECTION (Now visible at the top) -->
                <div class="mb-3 mt-1">
                    <h3 class="fw-black text-uppercase tracking-tighter m-0" style="font-size: 1.4rem; color: #1a1a1a;"><b>REQUEST ACCESS</b></h3>
                    <p class="text-muted" style="font-size: 0.75rem;">Fill in your institutional details</p>
                </div>

                <form method="POST" action="{{ route('register') }}">
                    @csrf

                    <!-- Full Name -->
                    <div class="mb-2">
                        <label class="form-label fw-bold text-muted text-uppercase tracking-widest" style="font-size: 9px; margin-bottom: 2px;">Full Name</label>
                        <div class="input-group input-group-sm">
                            <span class="input-group-text bg-light border-0 text-muted"><i data-lucide="user" style="width: 12px;"></i></span>
                            <input type="text" name="name" class="form-control bg-light border-0 shadow-none text-muted" style="font-size: 0.8rem;" placeholder="Juan Dela Cruz" value="{{ old('name') }}" required autofocus>
                        </div>
                    </div>

                    <!-- Username -->
                    <div class="mb-2">
                        <label class="form-label fw-bold text-muted text-uppercase tracking-widest" style="font-size: 9px; margin-bottom: 2px;">Username</label>
                        <div class="input-group input-group-sm">
                            <span class="input-group-text bg-light border-0 text-muted" style="font-size: 10px;">@</span>
                            <input type="text" name="username" class="form-control bg-light border-0 shadow-none text-muted" style="font-size: 0.8rem;" placeholder="unique_username" value="{{ old('username') }}" required>
                        </div>
                    </div>

                    <!-- School ID -->
                    <div class="mb-2">
                        <label class="form-label fw-bold text-muted text-uppercase tracking-widest" style="font-size: 9px; margin-bottom: 2px;">HTC School ID</label>
                        <div class="input-group input-group-sm">
                            <span class="input-group-text bg-light border-0 text-muted" style="font-size: 9px;">ID</span>
                            <input type="text" name="school_id" class="form-control bg-light border-0 shadow-none text-muted" style="font-size: 0.8rem;" placeholder="00-0000-00" value="{{ old('school_id') }}" required>
                        </div>
                    </div>

                    <!-- Email Address -->
                    <div class="mb-2">
                        <label class="form-label fw-bold text-muted text-uppercase tracking-widest" style="font-size: 9px; margin-bottom: 2px;">Email Address</label>
                        <div class="input-group input-group-sm">
                            <span class="input-group-text bg-light border-0 text-muted"><i data-lucide="mail" style="width: 12px;"></i></span>
                            <input type="email" name="email" class="form-control bg-light border-0 shadow-none text-muted" style="font-size: 0.8rem;" placeholder="email@htcsgen.edu.ph" value="{{ old('email') }}" required>
                        </div>
                    </div>

                    <!-- Passwords Row -->
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label fw-bold text-muted text-uppercase tracking-widest" style="font-size: 9px; margin-bottom: 2px;">Password</label>
                            <div class="input-group input-group-sm">
                                <input id="password" type="password" name="password" class="form-control bg-light border-0 shadow-none text-muted" style="font-size: 0.8rem;" placeholder="••••••••" required>
                                <button class="btn bg-light border-0" type="button" id="toggleRegPassword">
                                    <i data-lucide="eye" id="eyeIconReg" style="width: 14px; color: #6c757d;"></i>
                                </button>
                            </div>
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-bold text-muted text-uppercase tracking-widest" style="font-size: 9px; margin-bottom: 2px;">Confirm</label>
                            <div class="input-group input-group-sm">
                                <input id="password_confirmation" type="password" name="password_confirmation" class="form-control bg-light border-0 shadow-none text-muted" style="font-size: 0.8rem;" placeholder="••••••••" required>
                                <button class="btn bg-light border-0" type="button" id="toggleConfirmPassword">
                                    <i data-lucide="eye" id="eyeIconConfirm" style="width: 14px; color: #6c757d;"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-sm w-100 text-white fw-bold py-2 shadow-sm" style="background-color: #144521; border-radius: 6px; font-size: 0.85rem;">
                        Register Account →
                    </button>

                    <div class="mt-3 pt-2 border-top text-center mb-2">
                        <p class="text-muted" style="font-size: 0.7rem;">Already registered? <a href="{{ route('login') }}" class="fw-bold text-decoration-none" style="color: #144521;">Log In</a></p>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script>
        lucide.createIcons();

        const toggleRegPassword = document.querySelector('#toggleRegPassword');
        const passwordInput = document.querySelector('#password');
        const eyeIconReg = document.querySelector('#eyeIconReg');

        toggleRegPassword.addEventListener('click', function () {
            const isPassword = passwordInput.getAttribute('type') === 'password';
            passwordInput.setAttribute('type', isPassword ? 'text' : 'password');
            eyeIconReg.setAttribute('data-lucide', isPassword ? 'eye-off' : 'eye');
            lucide.createIcons();
        });

        const toggleConfirmPassword = document.querySelector('#toggleConfirmPassword');
        const confirmInput = document.querySelector('#password_confirmation');
        const eyeIconConfirm = document.querySelector('#eyeIconConfirm');

        toggleConfirmPassword.addEventListener('click', function () {
            const isPassword = confirmInput.getAttribute('type') === 'password';
            confirmInput.setAttribute('type', isPassword ? 'text' : 'password');
            eyeIconConfirm.setAttribute('data-lucide', isPassword ? 'eye-off' : 'eye');
            lucide.createIcons();
        });
    </script>

    <style>
        .overflow-auto::-webkit-scrollbar { width: 4px; }
        .overflow-auto::-webkit-scrollbar-thumb { background: #eee; border-radius: 10px; }
        .overflow-auto::-webkit-scrollbar-track { background: transparent; }
        /* Hide scrollbar for Chrome, Safari and Opera */
        .shadow-inner::-webkit-scrollbar { display: none; }
        /* Hide scrollbar for IE, Edge and Firefox */
        .shadow-inner { -ms-overflow-style: none; scrollbar-width: none; }
    </style>
</x-guest-layout>
