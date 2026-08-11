<x-guest-layout>
    <div class="container-fluid p-0 h-100">
        <div class="row g-0 h-100">

            <!-- LEFT SIDE: GREEN PANEL (Scaled Down) -->
            <div class="col-md-5 d-flex flex-column justify-content-between p-3 text-white" style="background-color: #144521;">
                <div>
                    <div class="d-flex align-items-center mb-3">
                        <div class="bg-white rounded-circle p-1 me-2" style="width: 25px; height: 25px;">
                            <img src="{{ asset('images/school_seal.png') }}" class="w-100" onerror="this.src='https://via.placeholder.com/20'">
                        </div>
                        <small class="fw-bold text-uppercase tracking-widest" style="font-size: 8px;">Supply Management</small>
                    </div>

                    <h2 class="fw-bold lh-sm mb-2" style="font-size: 1.3rem;">Join the <br> GoodHoly <br> Experience!</h2>
                    <p class="opacity-75" style="max-width: 180px; line-height: 1.3; font-size: 0.7rem;">
                        Create your account to manage institutional requisitions.
                    </p>
                </div>

                <div class="opacity-50 text-uppercase tracking-widest" style="font-size: 8px;">
                    Holy Trinity College <br> General Santos City
                </div>
            </div>

            <!-- RIGHT SIDE: REGISTRATION FORM (Extra Compact) -->
            <div class="col-md-7 d-flex flex-column justify-content-start p-4 bg-white h-100 shadow-inner">

                <div class="mb-2 mt-1">
                    <h3 class="fw-black text-uppercase tracking-tighter m-0" style="font-size: 1.2rem; color: #1a1a1a;"><b>REQUEST ACCESS</b></h3>
                    <p class="text-muted" style="font-size: 0.65rem; margin-bottom: 5px;">Fill in your institutional details</p>
                </div>

                <form method="POST" action="{{ route('register') }}">
                    @csrf

                    <div class="row g-2">
                        <!-- Full Name -->
                        <div class="col-12">
                            <label class="form-label fw-bold text-muted text-uppercase tracking-widest" style="font-size: 8px; margin-bottom: 2px;">Full Name</label>
                            <div class="input-group input-group-sm">
                                <span class="input-group-text bg-light border-0 text-muted"><i data-lucide="user" style="width: 12px;"></i></span>
                                <input type="text" name="name" class="form-control bg-light border-0 shadow-none text-muted" style="font-size: 0.75rem;" placeholder="Juan Dela Cruz" value="{{ old('name') }}" required autofocus>
                            </div>
                        </div>

                        <!-- Username -->
                        <div class="col-6">
                            <label class="form-label fw-bold text-muted text-uppercase tracking-widest" style="font-size: 8px; margin-bottom: 2px;">Username</label>
                            <div class="input-group input-group-sm">
                                <span class="input-group-text bg-light border-0 text-muted" style="font-size: 9px;">@</span>
                                <input type="text" name="username" class="form-control bg-light border-0 shadow-none text-muted" style="font-size: 0.75rem;" placeholder="user" value="{{ old('username') }}" required>
                            </div>
                        </div>

                        <!-- School ID -->
                        <div class="col-6">
                            <label class="form-label fw-bold text-muted text-uppercase tracking-widest" style="font-size: 8px; margin-bottom: 2px;">School ID</label>
                            <div class="input-group input-group-sm">
                                <span class="input-group-text bg-light border-0 text-muted" style="font-size: 8px;">ID</span>
                                <input type="text" name="school_id" class="form-control bg-light border-0 shadow-none text-muted" style="font-size: 0.75rem;" placeholder="00-00" value="{{ old('school_id') }}" required>
                            </div>
                        </div>

                        <!-- Email Address -->
                        <div class="col-12">
                            <label class="form-label fw-bold text-muted text-uppercase tracking-widest" style="font-size: 8px; margin-bottom: 2px;">Institutional Email</label>
                            <div class="input-group input-group-sm">
                                <span class="input-group-text bg-light border-0 text-muted"><i data-lucide="mail" style="width: 12px;"></i></span>
                                <input type="email" name="email" class="form-control bg-light border-0 shadow-none text-muted" style="font-size: 0.75rem;" placeholder="email@htc.edu.ph" value="{{ old('email') }}" required>
                            </div>
                        </div>

                        <!-- Password Row -->
                        <div class="col-6">
                            <label class="form-label fw-bold text-muted text-uppercase tracking-widest" style="font-size: 8px; margin-bottom: 2px;">Password</label>
                            <div class="input-group input-group-sm">
                                <input id="password" type="password" name="password" class="form-control bg-light border-0 shadow-none text-muted" style="font-size: 0.75rem;" placeholder="••••••" required>
                                <button class="btn bg-light border-0" type="button" id="toggleRegPassword">
                                    <i data-lucide="eye" id="eyeIconReg" style="width: 12px; color: #6c757d;"></i>
                                </button>
                            </div>
                        </div>

                        <div class="col-6">
                            <label class="form-label fw-bold text-muted text-uppercase tracking-widest" style="font-size: 8px; margin-bottom: 2px;">Confirm</label>
                            <div class="input-group input-group-sm">
                                <input id="password_confirmation" type="password" name="password_confirmation" class="form-control bg-light border-0 shadow-none text-muted" style="font-size: 0.75rem;" placeholder="••••••" required>
                                <button class="btn bg-light border-0" type="button" id="toggleConfirmPassword">
                                    <i data-lucide="eye" id="eyeIconConfirm" style="width: 12px; color: #6c757d;"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-sm w-100 text-white fw-bold py-2 shadow-sm mt-3" style="background-color: #144521; border-radius: 6px; font-size: 0.8rem;">
                        Register Account →
                    </button>

                    <div class="mt-2 pt-2 border-top text-center">
                        <p class="text-muted" style="font-size: 0.65rem;">Registered? <a href="{{ route('login') }}" class="fw-bold text-decoration-none" style="color: #144521;">Log In</a></p>
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
        .shadow-inner::-webkit-scrollbar { width: 3px; }
        .shadow-inner::-webkit-scrollbar-thumb { background: #eee; border-radius: 10px; }
        .shadow-inner { scrollbar-width: thin; scrollbar-color: #eee transparent; }
    </style>
</x-guest-layout>
