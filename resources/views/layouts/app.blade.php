<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name') }}</title>
    <link rel="icon" type="image/png" href="{{ asset('images/favicon-32x32.png') }}">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- 1. Alpine.js (Must be in head with defer) -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <!-- 2. Lucide Icons (Must be in head) -->
    <script src="https://unpkg.com/lucide@latest"></script>

    <style>
        :root {
            --htc-green: #185b3b;
            --htc-light-green: #f0f7f1;
            --sidebar-width: 300px;
        }

        body { background-color: #f4f7f6; font-family: 'Inter', sans-serif; color: #334155; }

        /* Sidebar Styling */
        .sidebar { width: var(--sidebar-width); height: 100vh; position: fixed; background: white; border-right: 1px solid #e2e8f0; z-index: 1000; padding: 1.5rem; display: flex; flex-direction: column; }
        .brand-section { display: flex; align-items: center; padding-bottom: 2rem; margin-bottom: 1rem; border-bottom: 1px solid #f1f5f9; }
        .brand-logo { width: 50px; height: 50px; object-fit: contain; margin-right: 12px; }
        .brand-title { font-size: 1.1rem; font-weight: 800; letter-spacing: 0.5px; line-height: 1.2; color: var(--htc-green); text-transform: uppercase; }

        .nav-section-label { font-size: 0.7rem; font-weight: 700; color: #94a3b8; text-transform: uppercase; letter-spacing: 1px; margin: 1.5rem 0 0.75rem 0.75rem; }
        .nav-link-custom { display: flex; align-items: center; padding: 0.75rem 1rem; color: #64748b; text-decoration: none; border-radius: 12px; margin-bottom: 4px; font-weight: 500; font-size: 0.9rem; transition: all 0.2s ease; }
        .nav-link-custom:hover { background-color: var(--htc-light-green); color: var(--htc-green); transform: translateX(4px); }
        .nav-link-custom.active { background-color: var(--htc-green); color: white; box-shadow: 0 4px 12px rgba(24, 91, 59, 0.2); }
        .nav-link-custom [data-lucide] { width: 18px; height: 18px; margin-right: 12px; stroke-width: 2.5px; }

        /* Main Content & Top Bar */
        .main-content { margin-left: var(--sidebar-width); width: calc(100% - var(--sidebar-width)); }
        .top-bar { height: 80px; background: white; border-bottom: 1px solid #e2e8f0; display: flex; align-items: center; justify-content: space-between; padding: 0 2.5rem; position: sticky; top: 0; z-index: 1010; }
        .page-title { font-weight: 800; font-size: 1.3rem; color: var(--htc-green); }

        .role-badge { font-size: 0.65rem; font-weight: 800; text-transform: uppercase; letter-spacing: 1px; padding: 5px 12px; border-radius: 20px; background-color: var(--htc-light-green); color: var(--htc-green); margin-right: 15px; border: 1px solid rgba(24, 91, 59, 0.1); }
        .main-content-inner { background: white; border-radius: 24px; box-shadow: 0 4px 20px rgba(0,0,0,0.03); min-height: calc(100vh - 160px); border: 1px solid #e2e8f0; }

        .btn-new-request { background-color: var(--htc-green); color: white; border-radius: 12px; padding: 0.8rem; font-weight: 700; border: none; transition: all 0.3s ease; }
        .btn-new-request:hover { background-color: #0d2e16; color: white; transform: translateY(-2px); }

        .dropdown-menu { z-index: 1050 !important; border-radius: 15px; border: none; box-shadow: 0 10px 30px rgba(0,0,0,0.1) !important; }

        /* --- UNIVERSAL STEPPER (Shopee-style) --- */
        .tracking-stepper {
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: relative;
            width: 100%;
            margin-bottom: 2rem;
        }

        .step-item {
            flex: 1;
            position: relative;
            display: flex;
            flex-direction: column;
            align-items: center;
            z-index: 1;
        }

        /* The Connecting Line */
        .step-item::after {
            content: "";
            position: absolute;
            top: 20px; /* Centers the line vertically with the icon */
            left: 50%;
            width: 100%;
            height: 3px;
            background-color: #e2e8f0;
            z-index: -1;
        }

        .step-item:last-child::after {
            display: none;
        }

        /* The Circle Icon */
        .step-icon {
            width: 40px;
            height: 40px;
            background: white;
            border: 3px solid #e2e8f0;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 8px;
            transition: all 0.3s ease;
        }

        .step-icon [data-lucide] {
            width: 18px;
            height: 18px;
            color: #94a3b8;
        }

        /* Completed State (Green) */
        .step-item.completed .step-icon {
            background-color: var(--htc-green);
            border-color: var(--htc-green);
        }

        .step-item.completed .step-icon [data-lucide] {
            color: white;
        }

        .step-item.completed::after {
            background-color: var(--htc-green);
        }

        /* Active State (Yellow/Current) */
        .step-item.active .step-icon {
            border-color: #ffc107;
            color: #ffc107;
            box-shadow: 0 0 0 4px rgba(255, 193, 7, 0.1);
        }

        .step-item.active .step-icon [data-lucide] {
            color: #ffc107;
        }

        /* Labels */
        .step-label {
            font-size: 0.65rem;
            font-weight: 700;
            text-transform: uppercase;
            color: #94a3b8;
            letter-spacing: 0.5px;
            text-align: center;
        }

        .completed .step-label {
            color: var(--htc-green);
        }
    </style>
</head>
<body>
    <div class="d-flex">
        <!-- SIDEBAR -->
        <aside class="sidebar">
            <div class="brand-section">
                <img src="{{ asset('images/android-chrome-512x5122.png') }}" alt="Logo" class="brand-logo">
                <div class="brand-title">SUPPLY<br>MANAGEMENT</div>
            </div>

            <div class="flex-grow-1">
                <div class="nav-section-label">General</div>
                <a href="{{ route('dashboard') }}" class="nav-link-custom {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <i data-lucide="layout-dashboard"></i> Dashboard
                </a>
                <a href="{{ route('requests.index') }}" class="nav-link-custom {{ request()->routeIs('requests.index') ? 'active' : '' }}">
                    <i data-lucide="clipboard-list"></i> My Requests
                </a>
                <a href="{{ route('notifications') }}" class="nav-link-custom {{ request()->routeIs('notifications') ? 'active' : '' }}">
                    <div class="d-flex justify-content-between align-items-center w-100">
                        <div><i data-lucide="bell"></i> Notifications</div>
                        @if($unreadCount > 0) <span class="badge rounded-pill bg-danger" style="font-size: 10px;">{{ $unreadCount }}</span> @endif
                    </div>
                </a>

                @if(in_array(Auth::user()->role, ['dept_head', 'vp', 'provost', 'president']))
                    <div class="nav-section-label">Management</div>
                    <a href="{{ route('admin.approvals') }}" class="nav-link-custom {{ request()->routeIs('admin.approvals') ? 'active' : '' }}">
                        <i data-lucide="user-check"></i> Pending Approvals
                    </a>
                @endif

                @if(Auth::user()->role == 'smo')
                    <div class="nav-section-label">Inventory & Logistics</div>
                    <a href="{{ route('admin.approvals') }}" class="nav-link-custom">
                        <i data-lucide="truck"></i> Release Queue
                    </a>
                    <a href="{{ route('inventory.index') }}" class="nav-link-custom {{ request()->routeIs('inventory.*') ? 'active' : '' }}">
                        <i data-lucide="package"></i> Manage Stock
                    </a>
                @endif

                @if(Auth::user()->role == 'smo')
                    <a href="{{ route('admin.users.pending') }}" class="nav-link-custom {{ request()->routeIs('admin.users.pending') ? 'active' : '' }}">
                        <i data-lucide="users"></i> User Requests
                    </a>
                @endif
            </div>

            <div class="pt-4 mt-auto border-top">
                <button type="button" class="btn btn-new-request w-100 shadow-sm" data-bs-toggle="modal" data-bs-target="#globalRequestModal">
                    + NEW REQUISITION
                </button>
            </div>
        </aside>

        <!-- MAIN CONTENT AREA -->
        <main class="main-content">
            <header class="top-bar">
                <div class="page-title">{{ $header ?? 'System Dashboard' }}</div>

                <div class="d-flex align-items-center">
                    @php
                        $position = match(Auth::user()->role) {
                            'employee' => 'Faculty / Staff',
                            'dept_head' => 'Department Head',
                            'vp' => 'Vice President',
                            'provost' => 'School Provost',
                            'president' => 'School President',
                            'smo' => 'SMO In-Charge',
                            default => 'User'
                        };
                    @endphp
                    <div class="role-badge d-none d-md-block">{{ $position }}</div>

                    <div class="dropdown">
                        <button class="btn border-0 d-flex align-items-center p-2 rounded-3" type="button" data-bs-toggle="dropdown">
                            <div class="text-end me-3 d-none d-lg-block">
                                <div class="fw-bold small text-dark" style="line-height: 1;">{{ Auth::user()->name }}</div>
                                <small class="text-muted" style="font-size: 10px;">{{ Auth::user()->school_id }}</small>
                            </div>
                            <div class="rounded-circle d-flex align-items-center justify-content-center font-bold text-white shadow-sm overflow-hidden"
                                style="width: 40px; height: 40px; background-color: var(--htc-green);">
                                @if(Auth::user()->profile_photo)
                                    <img src="{{ asset('storage/' . Auth::user()->profile_photo) }}" style="width: 100%; height: 100%; object-fit: cover;">
                                @else
                                    {{ substr(Auth::user()->name, 0, 1) }}
                                @endif
                            </div>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end p-2 mt-2">
                            <li><a class="dropdown-item rounded-3 py-2 small" href="{{ route('profile.edit') }}"><i data-lucide="settings" class="me-2" style="width:14px"></i> Account Setting</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="dropdown-item text-danger rounded-3 py-2 small"><i data-lucide="log-out" class="me-2" style="width:14px"></i> Sign Out</button>
                                </form>
                            </li>
                        </ul>
                    </div>
                </div>
            </header>

            <div class="p-4 p-lg-5">
                <div class="main-content-inner p-4 p-md-5">
                    {{ $slot }}
                </div>
            </div>
        </main>
    </div>

    <!-- GLOBAL REQUEST MODAL -->
    <div class="modal fade" id="globalRequestModal" tabindex="-1" aria-hidden="true" x-data="cartSystem()">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">

                <!-- VIEW 1: TIER SELECTION -->
                <div id="tierSelectionView" class="p-5">
                    <div class="text-center mb-5">
                        <h3 class="fw-bold">Initialize Procurement</h3>
                        <p class="text-muted">Select the appropriate request tier to proceed</p>
                    </div>
                    <div class="row g-4">
                        <div class="col-md-6">
                            <div class="card h-100 border-0 shadow-sm p-4 text-center" style="cursor:pointer" onclick="showRequestForm('minor')">
                                <div class="rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 60px; height: 60px; background-color: #f0f7f1; color: #144521; font-size: 1.5rem;">📦</div>
                                <h5 class="fw-bold">Minor Request</h5>
                                <small class="text-muted">Standard office supplies.</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card h-100 border-0 shadow-sm p-4 text-center" style="cursor:pointer" onclick="showRequestForm('major')">
                                <div class="rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 60px; height: 60px; background-color: #fff4e6; color: #fd7e14; font-size: 1.5rem;">🏢</div>
                                <h5 class="fw-bold">Major Request</h5>
                                <small class="text-muted">High-value equipment.</small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- VIEW 2: FORM -->
                <div id="formView" class="p-4 d-none">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <button type="button" class="btn btn-sm btn-light rounded-pill px-3" onclick="showTiers()">← Back</button>
                        <h5 class="fw-bold m-0 text-uppercase"><span x-text="tier"></span> Requisition</h5>
                        <div class="fw-bold text-success">₱<span x-text="formatMoney(grandTotal)"></span></div>
                    </div>

                    <form action="{{ route('requisitions.store') }}" method="POST">
                        @csrf
                        <input type="hidden" name="request_type" :value="tier">
                        <div class="table-responsive mb-3" style="max-height: 350px;">
                            <table class="table table-sm align-middle">
                                <thead class="bg-light small fw-bold">
                                    <tr>
                                        <th width="45%">Item Details</th>
                                        <th width="20%">Qty / Unit</th>
                                        <th width="20%">Price</th>
                                        <th width="15%" class="text-end">Total</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <template x-for="(item, index) in cart" :key="index">
                                        <tr>
                                            <td>
                                                <input type="text" :name="'items['+index+'][name]'" x-model="item.name" class="form-control form-control-sm border-0 bg-light mb-1" placeholder="Item name" required>
                                                <input type="text" :name="'items['+index+'][specs]'" x-model="item.specs" class="form-control form-control-sm border-0 bg-light" style="font-size: 10px" placeholder="Specifications">
                                            </td>
                                            <td>
                                                <div class="input-group input-group-sm">
                                                    <input type="number" :name="'items['+index+'][qty]'" x-model.number="item.qty" class="form-control border-0 bg-light" min="1" required>
                                                    <input type="text" :name="'items['+index+'][unit]'" x-model="item.unit" class="form-control border-0 bg-light border-start" placeholder="unit" required>
                                                </div>
                                            </td>
                                            <td><input type="number" :name="'items['+index+'][price]'" x-model.number="item.price" step="0.01" class="form-control form-control-sm border-0 bg-light" required></td>
                                            <td class="text-end fw-bold small text-success">₱<span x-text="formatMoney(item.qty * item.price)"></span></td>
                                            <td><button type="button" @click="removeItem(index)" class="btn btn-sm text-danger" x-show="cart.length > 1"><i data-lucide="trash-2" style="width:14px"></i></button></td>
                                        </tr>
                                    </template>
                                </tbody>
                            </table>
                        </div>
                        <button type="button" @click="addItem()" class="btn btn-outline-success btn-sm w-100 mb-4 fw-bold">+ Add Another Item</button>
                        <button type="submit" class="btn btn-lg w-100 text-white fw-bold shadow-sm" style="background-color: var(--htc-green);">Submit Requisition</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Initialize Icons
        lucide.createIcons();

        // Cart Engine
        function cartSystem() {
            return {
                tier: 'minor',
                cart: [{ name: '', specs: '', qty: 1, unit: 'pc', price: 0 }],
                addItem() { this.cart.push({ name: '', specs: '', qty: 1, unit: 'pc', price: 0 }); setTimeout(() => lucide.createIcons(), 10); },
                removeItem(index) { if(this.cart.length > 1) this.cart.splice(index, 1); },
                get grandTotal() { return this.cart.reduce((sum, item) => sum + (item.qty * item.price), 0); },
                formatMoney(val) { return parseFloat(val || 0).toLocaleString(undefined, {minimumFractionDigits: 2}); }
            }
        }

        // Modal Controls
        function showRequestForm(tier) {
            document.getElementById('tierSelectionView').classList.add('d-none');
            document.getElementById('formView').classList.remove('d-none');
            const alpine = document.querySelector('[x-data="cartSystem()"]').__x.$data;
            alpine.tier = tier;
        }

        function showTiers() {
            document.getElementById('formView').classList.add('d-none');
            document.getElementById('tierSelectionView').classList.remove('d-none');
        }
    </script>
</body>
</html>
