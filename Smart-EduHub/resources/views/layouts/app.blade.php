<!DOCTYPE html>
<html class="light" lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover"/>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#ea580c">
    <title>{{ config('app.name', 'EduAdmin Pro') }} - @yield('title')</title>
    <link rel="icon" type="image/png" href="{{ asset('images/SEHmob.png') }}">

    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>

    <!-- Alpine.js (plugin harus dimuat sebelum core) -->
    <script defer src="https://cdn.jsdelivr.net/npm/@alpinejs/collapse@3.x.x/dist/cdn.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <script id="tailwind-config">
        tailwind.config = {
            darkMode: "class",
            theme: {
                extend: {
                    screens: { "3xl": "1920px" },
                    colors: {
                        "on-primary-fixed": "#7c2d12", "inverse-primary": "#fed7aa", "surface-container-low": "#fff7ed",
                        "error-container": "#ffdad6", "surface-tint": "#f97316", "secondary": "#006591",
                        "on-tertiary-container": "#ffd2be", "primary-fixed-dim": "#fed7aa", "on-tertiary": "#ffffff",
                        "on-background": "#0b1c30", "tertiary-container": "#a44100", "tertiary-fixed": "#ffdbcc",
                        "surface-variant": "#d3e4fe", "error": "#ba1a1a", "background": "#f8f9ff", "on-error": "#ffffff",
                        "on-secondary-fixed-variant": "#004c6e", "outline-variant": "#c7c4d8", "on-primary": "#ffffff",
                        "secondary-container": "#39b8fd", "tertiary-fixed-dim": "#ffb695", "secondary-fixed": "#c9e6ff",
                        "inverse-on-surface": "#eaf1ff", "on-tertiary-fixed": "#351000", "on-primary-container": "#7c2d12",
                        "surface-container-highest": "#d3e4fe", "secondary-fixed-dim": "#89ceff", "on-surface": "#0b1c30",
                        "on-secondary": "#ffffff", "primary-fixed": "#ffedd5", "on-tertiary-fixed-variant": "#7b2f00",
                        "primary-container": "#fb923c", "surface-container": "#e5eeff", "on-error-container": "#93000a",
                        "on-secondary-fixed": "#001e2f", "tertiary": "#7e3000", "surface-dim": "#cbdbf5",
                        "inverse-surface": "#213145", "on-surface-variant": "#464555", "outline": "#777587",
                        "on-secondary-container": "#004666", "on-primary-fixed-variant": "#c2410c", "primary": "#ea580c",
                        "surface-container-lowest": "#ffffff", "surface": "#f8f9ff", "surface-bright": "#f8f9ff",
                        "surface-container-high": "#dce9ff"
                    },
                    borderRadius: { DEFAULT: "0.25rem", lg: "0.5rem", xl: "0.75rem", full: "9999px" },
                    spacing: {
                        "unit": "4px", "stack-sm": "8px", "margin-page": "32px", "gutter": "24px",
                        "stack-md": "16px", "stack-lg": "24px", "container-max": "1440px"
                    },
                    fontFamily: {
                        "headline-md": ["Inter"], "label-md": ["Inter"], "label-sm": ["Inter"], "display-xl": ["Inter"],
                        "body-md": ["Inter"], "headline-lg": ["Inter"], "body-lg": ["Inter"]
                    },
                    fontSize: {
                        "headline-md": ["20px", {"lineHeight": "28px", "letterSpacing": "-0.01em", "fontWeight": "600"}],
                        "label-md": ["12px", {"lineHeight": "16px", "letterSpacing": "0.02em", "fontWeight": "600"}],
                        "label-sm": ["11px", {"lineHeight": "14px", "letterSpacing": "0.03em", "fontWeight": "500"}],
                        "display-xl": ["36px", {"lineHeight": "44px", "letterSpacing": "-0.02em", "fontWeight": "700"}],
                        "body-md": ["14px", {"lineHeight": "20px", "letterSpacing": "0em", "fontWeight": "400"}],
                        "headline-lg": ["24px", {"lineHeight": "32px", "letterSpacing": "-0.01em", "fontWeight": "600"}],
                        "body-lg": ["16px", {"lineHeight": "24px", "letterSpacing": "0em", "fontWeight": "400"}]
                    }
                },
            },
        }
    </script>
    <style>
        @keyframes fade-in-down { 0% { opacity: 0; transform: translateY(-10px); } 100% { opacity: 1; transform: translateY(0); } }
        @keyframes slide-up { 0% { transform: translateY(100%); } 100% { transform: translateY(0); } }
        .animate-fade-in-down { animation: fade-in-down 0.3s ease-out; }
        .animate-slide-up { animation: slide-up 0.4s cubic-bezier(0.16, 1, 0.3, 1); }
        [x-cloak] { display: none !important; }
        html { -webkit-text-size-adjust: 100%; scroll-behavior: smooth; }
        .material-symbols-outlined { font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24; }
        body {
            font-family: 'Inter', system-ui, -apple-system, 'Segoe UI', sans-serif;
            background-color: #f8f9ff;
            background-image:
                radial-gradient(900px 420px at 100% -10%, rgba(249,115,22,.07), transparent 60%),
                radial-gradient(700px 380px at -10% 110%, rgba(57,184,253,.06), transparent 60%);
            background-attachment: fixed;
            -webkit-font-smoothing: antialiased;
            overflow-x: hidden;
        }
        .mobile-sheet-transition { transition: transform 0.4s cubic-bezier(0.16, 1, 0.3, 1); }
        .bento-card {
            background-color: #ffffff; border-radius: 16px;
            box-shadow: 0 1px 3px 0 rgba(0,0,0,.1), 0 1px 2px -1px rgba(0,0,0,.1);
            transition: all 0.2s ease-in-out;
        }
        .bento-card:hover { transform: translateY(-1px); box-shadow: 0 4px 6px -1px rgba(0,0,0,.1), 0 2px 4px -1px rgba(0,0,0,.06); }
        .custom-scrollbar::-webkit-scrollbar { width: 4px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 10px; }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #cbd5e1; }
        .glass { background: rgba(255,255,255,.78); -webkit-backdrop-filter: blur(16px); backdrop-filter: blur(16px); }
        :focus-visible { outline: 2px solid #f97316; outline-offset: 2px; }
        main img, main video { max-width: 100%; height: auto; }
        main table { max-width: 100%; }
        @media (max-width: 1023px) {
            /* Halaman dengan header hero (#pageHero) menempel penuh ke ujung atas layar */
            body:has(#pageHero) > main { padding-top: 0 !important; }
        }
        @media (prefers-reduced-motion: reduce) {
            *, *::before, *::after { animation-duration: .01ms !important; transition-duration: .01ms !important; scroll-behavior: auto !important; }
        }
    </style>
    @yield('styles')
</head>
<body class="text-on-surface min-h-screen">
@php
    // User aman: kalau belum login, pakai model kosong supaya tidak 500
    $authUser = auth()->user() ?? new \App\Models\User();

    // Pemanggil method aman: false jika method tidak ada di model User
    $can = fn($m) => method_exists($authUser, $m) && (bool) $authUser->$m();

    // Helper route aman: tidak 500 jika route/parameter salah, error tetap dicatat di laravel.log
    $rt = function ($name, $params = []) {
        try {
            return \Illuminate\Support\Facades\Route::has($name) ? route($name, $params) : '#';
        } catch (\Throwable $e) {
            report($e);
            return '#';
        }
    };

    $isAdmin    = $can('isAdmin');
    $isGuru     = $can('isGuru');
    $isFinance  = $can('isFinanceStaff');
    $isWali     = $can('isWaliKelas');
    $isWakasis  = $can('isWakasis');
    $isBK       = $can('isBK');
    $showOps    = $isAdmin || (!$isFinance && !$isGuru);
    $viewYear   = $currentViewYear ?? null;
    $years      = $globalAcademicYears ?? [];
    $userName   = $authUser->name ?? 'Admin';
    $roleLabel  = $authUser->role_label ?? 'Admin';

    // Kelas tombol dipakai ulang agar markup ringkas & konsisten
    $navCls  = fn($on) => 'group relative flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-semibold transition-all duration-200 '
                . ($on ? 'bg-orange-50 text-orange-700 shadow-sm shadow-orange-100' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900');
    $tileCls = fn($on) => 'flex flex-col items-center gap-2 p-3 rounded-2xl border transition-all active:scale-95 '
                . ($on ? 'bg-orange-50 border-orange-200 text-orange-600' : 'bg-slate-50 border-slate-100 text-slate-700 hover:border-orange-200');
    $tileIco = fn($on) => 'material-symbols-outlined text-[26px] ' . ($on ? 'text-orange-500' : 'text-slate-500');
    $botCls  = fn($on) => 'flex flex-col items-center gap-1 min-w-[4rem] ' . ($on ? 'text-orange-600' : 'text-slate-400');

    $financeItems = [];
    if ($isFinance) {
        $financeItems = array_values(array_filter([
            ['ok' => $can('canAccessFinanceDashboard'),          'route' => 'admin.finance.dashboard',            'match' => ['admin.finance.dashboard'],            'icon' => 'monitoring',               'label' => 'Dashboard Keuangan',  'short' => 'Dashboard',   'tint' => 'bg-emerald-100 text-emerald-600'],
            ['ok' => $can('canManageFinanceRequests'),           'route' => 'admin.finance.requests',             'match' => ['admin.finance.requests*'],            'icon' => 'mark_email_read',          'label' => 'Pengajuan Kartu',     'short' => 'Pengajuan',   'tint' => 'bg-orange-100 text-orange-600'],
            ['ok' => $can('canProcessFinancePayment'),           'route' => 'admin.finance.fast-payment',         'match' => ['admin.finance.fast-payment'],         'icon' => 'speed',                    'label' => 'Pembayaran Parkir',    'short' => 'Parkir Cepat', 'tint' => 'bg-red-100 text-red-600'],
            ['ok' => $can('canTopupFinance'),                    'route' => 'admin.finance.fast-transaction',     'match' => ['admin.finance.fast-transaction*'],    'icon' => 'bolt',                     'label' => 'Transaksi Cepat',      'short' => 'Cepat',       'tint' => 'bg-orange-100 text-orange-600'],
            ['ok' => $can('canViewFinanceCards'),                'route' => 'admin.finance.cards',                'match' => ['admin.finance.cards*'],               'icon' => 'credit_card',              'label' => 'Transaksi Kartu',     'short' => 'Kartu',       'tint' => 'bg-orange-100 text-orange-600'],
            ['ok' => $can('canViewFinancePaymentHistory'),       'route' => 'admin.finance.payments.history',     'match' => ['admin.finance.payments.history'],     'icon' => 'receipt_long',             'label' => 'Riwayat Pembayaran',  'short' => 'Riwayat',     'tint' => 'bg-amber-100 text-amber-600'],
            ['ok' => $can('canManageFinanceTransactions'),       'route' => 'admin.finance.transactions.index',   'match' => ['admin.finance.transactions*'],        'icon' => 'account_balance_wallet',   'label' => 'Kelola Transaksi',    'short' => 'Transaksi',   'tint' => 'bg-orange-100 text-orange-600'],
            ['ok' => $can('canViewFinanceTransactionHistory'),   'route' => 'admin.finance.transaction-history.index', 'match' => ['admin.finance.transaction-history*'], 'icon' => 'history', 'label' => 'Riwayat Perubahan', 'short' => 'Audit', 'tint' => 'bg-violet-100 text-violet-600'],
            ['ok' => $can('canAccessFinanceRekap'),              'route' => 'admin.finance.rekap',                'match' => ['admin.finance.rekap'],                'icon' => 'bar_chart',                'label' => 'Rekap Keuangan',      'short' => 'Rekap',       'tint' => 'bg-violet-100 text-violet-600'],
        ], fn($i) => $i['ok']));
    }
@endphp

    <!-- Loading bar -->
    <div id="adminLoadingBar" class="fixed top-0 left-0 right-0 h-[3px] w-0 opacity-0 z-[9999] transition-all duration-300"
         style="background: linear-gradient(90deg, #f97316, #ea580c, #c2410c);"></div>

    <!-- ===================== SIDEBAR (Desktop) ===================== -->
    <aside class="hidden lg:flex fixed left-0 top-0 h-screen w-64 2xl:w-72 flex-col bg-white/90 glass border-r border-slate-100 p-4 z-40">
        <div class="px-2 pb-5 mb-2 border-b border-slate-100">
            <img src="{{ asset('images/SEH.png') }}" alt="Smart Axioo Logo" class="h-10 w-auto object-contain">
            <p class="mt-2 text-[11px] font-bold text-slate-400 tracking-wide">Portal Admin</p>
        </div>

        <nav class="flex-1 space-y-1 overflow-y-auto pr-1 custom-scrollbar" aria-label="Menu utama">
            @if(!$isGuru)
                <a class="{{ $navCls(request()->routeIs('dashboard')) }}" href="{{ $rt('dashboard') }}">
                    <span class="material-symbols-outlined">dashboard</span><span>{{ __('Dashboard') }}</span>
                </a>
            @else
                <a class="{{ $navCls(request()->routeIs('guru.dashboard')) }}" href="{{ $rt('guru.dashboard') }}">
                    <span class="material-symbols-outlined">school</span><span>Dashboard Guru</span>
                </a>
                <a class="{{ $navCls(request()->routeIs('admin.courses*') && !request()->routeIs('admin.courses.all-rekap', 'admin.courses.rekap-nilai*')) }}" href="{{ $rt('admin.courses.index') }}">
                    <span class="material-symbols-outlined">auto_stories</span><span>Kursus &amp; Modul</span>
                </a>
                <a class="{{ $navCls(request()->routeIs('admin.courses.all-rekap', 'admin.courses.rekap-nilai*')) }}" href="{{ $rt('admin.courses.all-rekap') }}">
                    <span class="material-symbols-outlined">grading</span><span>Penilaian</span>
                </a>
            @endif

            @if($isFinance && count($financeItems))
            <div x-data="{ open: {{ request()->routeIs('admin.finance*') ? 'true' : 'false' }} }" class="space-y-1">
                <button type="button" @click="open = !open" :aria-expanded="open" class="w-full flex items-center justify-between px-3.5 py-2.5 rounded-xl text-sm font-semibold text-slate-600 hover:bg-slate-50 hover:text-slate-900 transition-all">
                    <span class="flex items-center gap-3"><span class="material-symbols-outlined">account_balance_wallet</span><span>Keuangan</span></span>
                    <span class="material-symbols-outlined transition-transform duration-200" :class="open ? 'rotate-180' : ''">expand_more</span>
                </button>
                <div x-show="open" x-cloak x-collapse class="ml-3 pl-2 border-l border-slate-100 space-y-1">
                    @foreach($financeItems as $fi)
                        <a class="{{ $navCls(request()->routeIs(...$fi['match'])) }}" href="{{ $rt($fi['route']) }}">
                            <span class="material-symbols-outlined text-[20px]">{{ $fi['icon'] }}</span><span>{{ $fi['label'] }}</span>
                        </a>
                    @endforeach
                </div>
            </div>
            @endif

            @if($isAdmin)
                <a class="{{ $navCls(request()->routeIs('admin.notifications*')) }}" href="{{ $rt('admin.notifications.index') }}">
                    <span class="material-symbols-outlined">notifications</span><span>Notifikasi</span>
                </a>

                <div x-data="{ open: {{ request()->routeIs('students*', 'management-users*', 'parents*', 'registrations*', 'admin.courses*', 'angkatans*') ? 'true' : 'false' }} }" class="space-y-1">
                    <button type="button" @click="open = !open" :aria-expanded="open" class="w-full flex items-center justify-between px-3.5 py-2.5 rounded-xl text-sm font-semibold text-slate-600 hover:bg-slate-50 hover:text-slate-900 transition-all">
                        <span class="flex items-center gap-3"><span class="material-symbols-outlined">database</span><span>Data Master</span></span>
                        <span class="material-symbols-outlined transition-transform duration-200" :class="open ? 'rotate-180' : ''">expand_more</span>
                    </button>
                    <div x-show="open" x-cloak x-collapse class="ml-3 pl-2 border-l border-slate-100 space-y-1">
                        <a class="{{ $navCls(request()->routeIs('students*')) }}" href="{{ $rt('students') }}"><span class="material-symbols-outlined text-[20px]">group</span><span>Data Siswa</span></a>
                        <a class="{{ $navCls(request()->routeIs('management-users*')) }}" href="{{ $rt('management-users.index') }}"><span class="material-symbols-outlined text-[20px]">manage_accounts</span><span>Manajemen User</span></a>
                        <a class="{{ $navCls(request()->routeIs('parents*')) }}" href="{{ $rt('parents.index') }}"><span class="material-symbols-outlined text-[20px]">family_restroom</span><span>Data Orang Tua</span></a>
                        <a class="{{ $navCls(request()->routeIs('angkatans*')) }}" href="{{ $rt('angkatans.index') }}"><span class="material-symbols-outlined text-[20px]">sort</span><span>Data Angkatan</span></a>
                        <a class="{{ $navCls(request()->routeIs('registrations*')) }}" href="{{ $rt('registrations') }}"><span class="material-symbols-outlined text-[20px]">how_to_reg</span><span>Pendaftaran</span></a>
                        <a class="{{ $navCls(request()->routeIs('admin.courses*')) }}" href="{{ $rt('admin.courses.index') }}"><span class="material-symbols-outlined text-[20px]">auto_stories</span><span>Kursus &amp; Modul</span></a>
                    </div>
                </div>
            @endif

            @if($showOps)
            <div x-data="{ open: {{ request()->routeIs('attendance*', 'points*', 'letters*', 'admin.exams*', 'admin.permits*', 'holidays*', 'point-rules*', 'raport*', 'admin.complaints*') ? 'true' : 'false' }} }" class="space-y-1">
                <button type="button" @click="open = !open" :aria-expanded="open" class="w-full flex items-center justify-between px-3.5 py-2.5 rounded-xl text-sm font-semibold text-slate-600 hover:bg-slate-50 hover:text-slate-900 transition-all">
                    <span class="flex items-center gap-3"><span class="material-symbols-outlined">settings_accessibility</span><span>Operasional</span></span>
                    <span class="material-symbols-outlined transition-transform duration-200" :class="open ? 'rotate-180' : ''">expand_more</span>
                </button>
                <div x-show="open" x-cloak x-collapse class="ml-3 pl-2 border-l border-slate-100 space-y-1">
                    {{-- Raport Industri & Surat Resmi dinonaktifkan --}}

                    @if($isAdmin)
                        <a class="{{ $navCls(request()->routeIs('attendance')) }}" href="{{ $rt('attendance') }}"><span class="material-symbols-outlined text-[20px]">event_available</span><span>{{ __('Attendance') }}</span></a>
                    @endif
                    @if($isAdmin || $isWali)
                        <a class="{{ $navCls(request()->routeIs('attendance.report')) }}" href="{{ $rt('attendance.report') }}"><span class="material-symbols-outlined text-[20px]">analytics</span><span>Laporan Presensi</span></a>
                    @endif
                    @if($isAdmin || $isWali || $isWakasis || $isBK)
                        <a class="{{ $navCls(request()->routeIs('points')) }}" href="{{ $rt('points') }}"><span class="material-symbols-outlined text-[20px]">military_tech</span><span>{{ __('Merit & Discipline') }}</span></a>
                    @endif
                    @if($isAdmin)
                        <a class="{{ $navCls(request()->routeIs('admin.exams.index')) }}" href="{{ $rt('admin.exams.index') }}"><span class="material-symbols-outlined text-[20px]">quiz</span><span>Manajemen Ujian</span></a>
                        <a class="{{ $navCls(request()->routeIs('admin.exams.results')) }}" href="{{ $rt('admin.exams.results') }}"><span class="material-symbols-outlined text-[20px]">grade</span><span>Manajemen Nilai</span></a>
                        <a class="{{ $navCls(request()->routeIs('admin.permits*')) }}" href="{{ $rt('admin.permits.index') }}"><span class="material-symbols-outlined text-[20px]">assignment_late</span><span>Izin &amp; Sakit</span></a>
                        <a class="{{ $navCls(request()->routeIs('holidays*')) }}" href="{{ $rt('holidays') }}"><span class="material-symbols-outlined text-[20px]">event_busy</span><span>Hari Libur</span></a>
                        <a class="{{ $navCls(request()->routeIs('point-rules*')) }}" href="{{ $rt('point-rules.index') }}"><span class="material-symbols-outlined text-[20px]">rule</span><span>Peraturan Poin</span></a>
                        <a class="{{ $navCls(request()->routeIs('admin.complaints*')) }}" href="{{ $rt('admin.complaints.index') }}"><span class="material-symbols-outlined text-[20px]">report</span><span>Aduan &amp; Laporan</span></a>
                    @endif
                </div>
            </div>
            @endif
        </nav>

        <div class="pt-4 mt-2 border-t border-slate-100 space-y-1">
            @if($isAdmin)
                <a class="{{ $navCls(request()->routeIs('settings*')) }}" href="{{ $rt('settings') }}"><span class="material-symbols-outlined">settings</span><span>{{ __('Settings') }}</span></a>
            @endif
            <form method="POST" action="{{ $rt('logout') }}">
                @csrf
                <button type="submit" class="w-full flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-semibold text-slate-600 hover:bg-rose-50 hover:text-rose-600 transition-all">
                    <span class="material-symbols-outlined">logout</span><span>Sign Out</span>
                </button>
            </form>
        </div>
    </aside>

    <!-- ===================== TOP BAR (Desktop) ===================== -->
    <header class="hidden lg:flex fixed top-0 right-0 left-64 2xl:left-72 h-16 glass items-center justify-between gap-4 px-8 2xl:px-12 z-30 border-b border-slate-100">
        <div class="flex items-center gap-4 flex-1 min-w-0">
            <h1 class="hidden xl:block text-base font-extrabold text-slate-900 truncate max-w-[16rem]">@yield('title', 'Dashboard')</h1>
            <div class="relative w-full max-w-md">
                <span class="material-symbols-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-[20px]">search</span>
                <input class="w-full bg-slate-100/70 border-none rounded-full py-2 pl-10 pr-4 text-sm focus:ring-2 focus:ring-orange-500/30 focus:bg-white transition-all" placeholder="{{ __('Search...') }}" type="text" aria-label="Search"/>
            </div>
        </div>
        <div class="flex items-center gap-2 shrink-0">
            @auth
            <form action="{{ $rt('settings.set-academic-year') }}" method="POST" id="yearSwitcherForm">
                @csrf
                <select name="academic_year" onchange="this.form.submit()" aria-label="Tahun ajaran" class="bg-slate-100/70 border-none rounded-full py-1.5 pl-4 pr-8 text-[12px] font-bold text-slate-600 focus:ring-2 focus:ring-orange-500/30 cursor-pointer">
                    @foreach($years as $year)
                        <option value="{{ $year->name }}" {{ $viewYear == $year->name ? 'selected' : '' }}>AY {{ $year->name }}</option>
                    @endforeach
                </select>
            </form>
            @endauth
            <button type="button" onclick="window.location.reload()" class="p-2 text-slate-500 hover:bg-slate-100 rounded-full transition-all" title="Refresh" aria-label="Refresh">
                <span class="material-symbols-outlined text-[22px]">refresh</span>
            </button>
            <a href="{{ $rt('profile') }}" class="flex items-center gap-3 pl-2">
                <div class="text-right hidden xl:block">
                    <p class="text-sm font-bold text-slate-900 leading-none">{{ $userName }}</p>
                    <p class="text-[11px] text-slate-500 font-medium mt-1">{{ $roleLabel }}</p>
                </div>
                <img alt="Profil" class="w-10 h-10 rounded-full object-cover border-2 border-white shadow-sm ring-1 ring-orange-100"
                     src="https://ui-avatars.com/api/?name={{ urlencode($userName) }}&background=ea580c&color=fff"/>
            </a>
        </div>
    </header>

    <!-- ===================== MAIN ===================== -->
    <main class="min-h-screen pt-4 lg:pt-24 lg:ml-64 2xl:ml-72 px-4 sm:px-6 lg:px-8 2xl:px-12 pb-32 lg:pb-12">
        <div class="w-full max-w-[2200px] mx-auto">
            @if(session('success'))
                <div data-autodismiss class="mb-5 p-4 bg-green-50 border border-green-200 text-green-800 rounded-2xl flex items-center justify-between gap-3 animate-fade-in-down" role="status">
                    <div class="flex items-center gap-3 min-w-0">
                        <span class="material-symbols-outlined text-green-600">check_circle</span>
                        <p class="text-sm font-semibold break-words">{{ session('success') }}</p>
                    </div>
                    <button type="button" onclick="this.parentElement.remove()" class="text-green-600 hover:text-green-800 transition-colors" aria-label="Tutup">
                        <span class="material-symbols-outlined text-lg">close</span>
                    </button>
                </div>
            @endif
            @if(session('error'))
                <div data-autodismiss class="mb-5 p-4 bg-red-50 border border-red-200 text-red-800 rounded-2xl flex items-center justify-between gap-3 animate-fade-in-down" role="alert">
                    <div class="flex items-center gap-3 min-w-0">
                        <span class="material-symbols-outlined text-red-600">error</span>
                        <p class="text-sm font-semibold break-words">{{ session('error') }}</p>
                    </div>
                    <button type="button" onclick="this.parentElement.remove()" class="text-red-600 hover:text-red-800 transition-colors" aria-label="Tutup">
                        <span class="material-symbols-outlined text-lg">close</span>
                    </button>
                </div>
            @endif

            @yield('content')
        </div>
    </main>

    <!-- ===================== BOTTOM NAV (Mobile & Tablet) ===================== -->
    <nav class="lg:hidden fixed bottom-0 left-0 right-0 glass border-t border-slate-100 flex justify-around items-center px-2 pt-3 pb-[calc(0.5rem+env(safe-area-inset-bottom))] z-50 shadow-[0_-8px_30px_rgba(0,0,0,0.06)]" aria-label="Navigasi bawah">
        @if($isGuru)
            <a href="{{ $rt('guru.dashboard') }}" class="{{ $botCls(request()->routeIs('guru.dashboard')) }}">
                <span class="material-symbols-outlined text-[24px]">school</span><span class="text-[10px] font-bold">Home</span>
            </a>
            <a href="{{ $rt('admin.courses.index') }}" class="{{ $botCls(request()->routeIs('admin.courses*') && !request()->routeIs('admin.courses.all-rekap', 'admin.courses.rekap-nilai*')) }}">
                <span class="material-symbols-outlined text-[24px]">auto_stories</span><span class="text-[10px] font-bold">Kursus</span>
            </a>
        @elseif($isFinance && !$isAdmin)
            <a href="{{ $rt('admin.finance.dashboard') }}" class="{{ $botCls(request()->routeIs('admin.finance.dashboard')) }}">
                <span class="material-symbols-outlined text-[24px]">monitoring</span><span class="text-[10px] font-bold">Dashboard</span>
            </a>
            <a href="{{ $rt('admin.finance.fast-payment') }}" class="{{ $botCls(request()->routeIs('admin.finance.fast-payment')) }}">
                <span class="material-symbols-outlined text-[24px]">speed</span><span class="text-[10px] font-bold">Bayar</span>
            </a>
            <a href="{{ $rt('admin.finance.fast-transaction') }}" class="{{ $botCls(request()->routeIs('admin.finance.fast-transaction*')) }}">
                <span class="material-symbols-outlined text-[24px]">bolt</span><span class="text-[10px] font-bold">Cepat</span>
            </a>
        @else
            <a href="{{ $rt('dashboard') }}" class="{{ $botCls(request()->routeIs('dashboard')) }}">
                <span class="material-symbols-outlined text-[24px]">dashboard</span><span class="text-[10px] font-bold">Home</span>
            </a>
            <a href="{{ $rt('points') }}" class="{{ $botCls(request()->routeIs('points*')) }}">
                <span class="material-symbols-outlined text-[24px]">military_tech</span><span class="text-[10px] font-bold">Poin</span>
            </a>
        @endif

        <button type="button" onclick="toggleMobileMaster()" aria-controls="mobileMasterMenu"
                class="{{ $botCls(request()->routeIs('management-users*','registrations*','students*','attendance*','admin.exams*','admin.permits*','angkatans*','admin.finance*','admin.courses*')) }}">
            <div class="w-12 h-12 -mt-7 rounded-full bg-gradient-to-tr from-orange-500 to-orange-600 flex items-center justify-center shadow-lg shadow-orange-300 border-4 border-white">
                <span class="material-symbols-outlined text-white text-[22px]">apps</span>
            </div>
            <span class="text-[10px] font-bold">Menu</span>
        </button>

        @if($isGuru)
            <a href="{{ $rt('admin.courses.all-rekap') }}" class="{{ $botCls(request()->routeIs('admin.courses.all-rekap','admin.courses.rekap-nilai*')) }}">
                <span class="material-symbols-outlined text-[24px]">grading</span><span class="text-[10px] font-bold">Nilai</span>
            </a>
        @elseif($isFinance && !$isAdmin)
            <a href="{{ $rt('admin.finance.cards') }}" class="{{ $botCls(request()->routeIs('admin.finance.cards*')) }}">
                <span class="material-symbols-outlined text-[24px]">credit_card</span><span class="text-[10px] font-bold">Kartu</span>
            </a>
        @else
            <a href="{{ $rt('attendance') }}" class="{{ $botCls(request()->routeIs('attendance*')) }}">
                <span class="material-symbols-outlined text-[24px]">fingerprint</span><span class="text-[10px] font-bold">Presensi</span>
            </a>
        @endif

        <a href="{{ $rt('profile') }}" class="{{ $botCls(request()->routeIs('profile*')) }}">
            <span class="material-symbols-outlined text-[24px]">person</span><span class="text-[10px] font-bold">Profil</span>
        </a>
    </nav>

    @if(request()->routeIs('admin.finance*') && $isFinance && count($financeItems))
    <!-- FAB Menu Keuangan (Mobile & Tablet) -->
    <div x-data="{ openFinanceMenu: false }" @keydown.escape.window="openFinanceMenu = false" class="lg:hidden">
        <div x-show="openFinanceMenu" x-cloak
             x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
             @click="openFinanceMenu = false" class="fixed inset-0 bg-slate-950/60 backdrop-blur-sm z-[70]"></div>

        <div x-show="openFinanceMenu" x-cloak
             x-transition:enter="transition ease-out duration-300 transform" x-transition:enter-start="translate-y-full" x-transition:enter-end="translate-y-0"
             x-transition:leave="transition ease-in duration-200 transform" x-transition:leave-start="translate-y-0" x-transition:leave-end="translate-y-full"
             class="fixed bottom-0 left-0 right-0 md:max-w-2xl md:mx-auto bg-white rounded-t-[2rem] shadow-2xl z-[80] p-6 pb-28 border-t border-slate-100 max-h-[80vh] overflow-y-auto">
            <div class="w-12 h-1.5 bg-slate-200 rounded-full mx-auto mb-6"></div>
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h3 class="text-lg font-black text-slate-900 tracking-tight">Menu Keuangan</h3>
                    <p class="text-xs text-slate-500 font-medium">Navigasi cepat modul keuangan</p>
                </div>
                <button type="button" @click="openFinanceMenu = false" class="p-2 text-slate-400 hover:text-slate-600 rounded-xl bg-slate-50 transition-all" aria-label="Tutup">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>
            <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
                @foreach($financeItems as $fi)
                    @php $on = request()->routeIs(...$fi['match']); @endphp
                    <a href="{{ $rt($fi['route']) }}" class="flex flex-col items-center gap-3 p-4 rounded-2xl border transition-colors {{ $on ? 'bg-orange-50 border-orange-200 text-orange-600' : 'bg-slate-50 border-slate-100 text-slate-700 hover:bg-slate-100' }}">
                        <div class="w-12 h-12 rounded-xl flex items-center justify-center {{ $on ? 'bg-orange-100 text-orange-600' : $fi['tint'] }}">
                            <span class="material-symbols-outlined">{{ $fi['icon'] }}</span>
                        </div>
                        <span class="text-xs font-bold text-center">{{ $fi['label'] }}</span>
                    </a>
                @endforeach
            </div>
        </div>

        <button type="button" @click="openFinanceMenu = true" aria-label="Buka menu keuangan"
                class="fixed bottom-24 right-5 w-14 h-14 bg-gradient-to-tr from-orange-600 to-orange-700 hover:from-orange-700 hover:to-orange-800 text-white rounded-full flex items-center justify-center shadow-2xl z-[65] hover:scale-105 active:scale-95 transition-all border-4 border-white/20">
            <span class="material-symbols-outlined text-[26px]">account_balance_wallet</span>
        </button>
    </div>
    @endif

    <!-- Tombol keluar dari impersonate / quick login -->
    @if(session()->has('impersonate_admin_id') || session()->has('quick_login_from_welcome'))
        @php $fromWelcome = session()->has('quick_login_from_welcome'); @endphp
        <form method="POST" action="{{ $rt('impersonate.guru.stop') }}" class="fixed bottom-24 left-5 z-[75]">
            @csrf
            <button type="submit"
                    onclick="return confirm('{{ $fromWelcome ? 'Keluar & kembali ke halaman utama?' : 'Kembali ke akun Administrator?' }}')"
                    class="pl-3 pr-5 h-14 text-white rounded-full flex items-center gap-2 shadow-2xl hover:scale-105 active:scale-95 transition-all border-4 border-white/20 animate-pulse bg-gradient-to-tr {{ $fromWelcome ? 'from-slate-700 to-slate-900 shadow-slate-300' : 'from-rose-500 to-pink-600 shadow-rose-200' }}">
                <span class="material-symbols-outlined text-[22px]">{{ $fromWelcome ? 'home' : 'admin_panel_settings' }}</span>
                <span class="text-xs md:text-sm font-black tracking-tight">{{ $fromWelcome ? 'Kembali ke Halaman Utama' : 'Kembali ke Admin' }}</span>
            </button>
        </form>
    @endif

    <!-- ===================== MOBILE MASTER MENU (bottom sheet) ===================== -->
    <div id="mobileMasterOverlay" onclick="toggleMobileMaster()" class="lg:hidden fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-[100] hidden opacity-0 transition-opacity duration-300"></div>

    <div id="mobileMasterMenu" role="dialog" aria-modal="true" aria-label="Menu"
         class="lg:hidden fixed bottom-0 left-0 right-0 md:max-w-2xl md:mx-auto max-h-[88vh] bg-white rounded-t-[32px] z-[101] translate-y-full mobile-sheet-transition shadow-[0_-10px_40px_rgba(0,0,0,0.15)] flex flex-col overflow-hidden">

        <div class="w-full flex items-center justify-between px-6 py-4 bg-white border-b border-slate-50 shrink-0 cursor-pointer" onclick="toggleMobileMaster()">
            <div class="flex items-center gap-3 min-w-0">
                <img alt="Profil" class="w-10 h-10 rounded-full border-2 border-orange-100" src="https://ui-avatars.com/api/?name={{ urlencode($userName) }}&background=ea580c&color=fff"/>
                <div class="min-w-0">
                    <p class="text-sm font-black text-slate-900 leading-tight truncate">{{ $userName }}</p>
                    <p class="text-[11px] text-slate-400 font-semibold mt-0.5">{{ $roleLabel }}</p>
                </div>
            </div>
            <button type="button" onclick="event.stopPropagation(); toggleMobileMaster()" class="w-8 h-8 rounded-full bg-slate-100 text-slate-500 flex items-center justify-center" aria-label="Tutup menu">
                <span class="material-symbols-outlined text-lg">close</span>
            </button>
        </div>

        <div class="overflow-y-auto custom-scrollbar p-5 space-y-6 pb-28">

            @if(count($years))
            <form action="{{ $rt('settings.set-academic-year') }}" method="POST" class="flex items-center justify-between gap-3 p-3 bg-slate-50 rounded-2xl">
                @csrf
                <label for="mobileYearSelect" class="flex items-center gap-2 text-sm font-bold text-slate-700">
                    <span class="material-symbols-outlined text-slate-500">calendar_month</span>Tahun ajaran
                </label>
                <select id="mobileYearSelect" name="academic_year" onchange="this.form.submit()" class="bg-white border border-slate-200 rounded-full py-1.5 pl-4 pr-8 text-xs font-bold text-slate-600 focus:ring-2 focus:ring-orange-500/30">
                    @foreach($years as $year)
                        <option value="{{ $year->name }}" {{ $viewYear == $year->name ? 'selected' : '' }}>{{ $year->name }}</option>
                    @endforeach
                </select>
            </form>
            @endif

            @if($isAdmin)
            <div>
                <p class="text-xs font-extrabold text-slate-400 mb-3">Data master</p>
                <div class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-5 gap-3">
                    @foreach([
                        ['students*', 'students', 'group', 'Siswa'],
                        ['management-users*', 'management-users.index', 'manage_accounts', 'Users'],
                        ['parents*', 'parents.index', 'family_restroom', 'Orang Tua'],
                        ['angkatans*', 'angkatans.index', 'sort', 'Angkatan'],
                        ['registrations*', 'registrations', 'how_to_reg', 'Pendaftaran'],
                        ['admin.courses*', 'admin.courses.index', 'auto_stories', 'Kursus'],
                    ] as [$m, $r, $ic, $lb])
                        @php $on = request()->routeIs($m); @endphp
                        <a href="{{ $rt($r) }}" class="{{ $tileCls($on) }}">
                            <span class="{{ $tileIco($on) }}">{{ $ic }}</span>
                            <span class="text-[11px] font-bold text-center">{{ $lb }}</span>
                        </a>
                    @endforeach
                </div>
            </div>
            @endif

            @if($isGuru)
            <div>
                <p class="text-xs font-extrabold text-slate-400 mb-3">Portal guru</p>
                <div class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-5 gap-3">
                    @foreach([
                        ['guru.dashboard', 'guru.dashboard', 'school', 'Dashboard'],
                        ['admin.courses.index', 'admin.courses.index', 'auto_stories', 'Kursus'],
                        ['admin.courses.all-rekap', 'admin.courses.all-rekap', 'grading', 'Penilaian'],
                    ] as [$m, $r, $ic, $lb])
                        @php $on = request()->routeIs($m); @endphp
                        <a href="{{ $rt($r) }}" class="{{ $tileCls($on) }}">
                            <span class="{{ $tileIco($on) }}">{{ $ic }}</span>
                            <span class="text-[11px] font-bold text-center">{{ $lb }}</span>
                        </a>
                    @endforeach
                </div>
            </div>
            @endif

            @if($isFinance && count($financeItems))
            <div>
                <p class="text-xs font-extrabold text-slate-400 mb-3">Keuangan</p>
                <div class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-5 gap-3">
                    @foreach($financeItems as $fi)
                        @php $on = request()->routeIs(...$fi['match']); @endphp
                        <a href="{{ $rt($fi['route']) }}" class="{{ $tileCls($on) }}">
                            <span class="{{ $tileIco($on) }}">{{ $fi['icon'] }}</span>
                            <span class="text-[11px] font-bold text-center">{{ $fi['short'] }}</span>
                        </a>
                    @endforeach
                </div>
            </div>
            @endif

            @if($showOps)
            <div>
                <p class="text-xs font-extrabold text-slate-400 mb-3">Operasional</p>
                <div class="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-5 gap-3">
                    @if($isAdmin)
                        <a href="{{ $rt('attendance') }}" class="{{ $tileCls(request()->routeIs('attendance')) }}"><span class="{{ $tileIco(request()->routeIs('attendance')) }}">event_available</span><span class="text-[11px] font-bold text-center">Presensi</span></a>
                    @endif
                    @if($isAdmin || $isWali)
                        <a href="{{ $rt('attendance.report') }}" class="{{ $tileCls(request()->routeIs('attendance.report')) }}"><span class="{{ $tileIco(request()->routeIs('attendance.report')) }}">analytics</span><span class="text-[11px] font-bold text-center">Lap. Presensi</span></a>
                    @endif
                    @if($isAdmin || $isWali || $isWakasis || $isBK)
                        <a href="{{ $rt('points') }}" class="{{ $tileCls(request()->routeIs('points*')) }}"><span class="{{ $tileIco(request()->routeIs('points*')) }}">military_tech</span><span class="text-[11px] font-bold text-center">Poin</span></a>
                    @endif
                    @if($isAdmin)
                        <a href="{{ $rt('admin.exams.index') }}" class="{{ $tileCls(request()->routeIs('admin.exams.index')) }}"><span class="{{ $tileIco(request()->routeIs('admin.exams.index')) }}">quiz</span><span class="text-[11px] font-bold text-center">Ujian</span></a>
                        <a href="{{ $rt('admin.exams.results') }}" class="{{ $tileCls(request()->routeIs('admin.exams.results')) }}"><span class="{{ $tileIco(request()->routeIs('admin.exams.results')) }}">grade</span><span class="text-[11px] font-bold text-center">Nilai</span></a>
                        <a href="{{ $rt('admin.permits.index') }}" class="{{ $tileCls(request()->routeIs('admin.permits*')) }}"><span class="{{ $tileIco(request()->routeIs('admin.permits*')) }}">assignment_late</span><span class="text-[11px] font-bold text-center">Izin &amp; Sakit</span></a>
                        <a href="{{ $rt('holidays') }}" class="{{ $tileCls(request()->routeIs('holidays*')) }}"><span class="{{ $tileIco(request()->routeIs('holidays*')) }}">event_busy</span><span class="text-[11px] font-bold text-center">Hari Libur</span></a>
                        <a href="{{ $rt('point-rules.index') }}" class="{{ $tileCls(request()->routeIs('point-rules*')) }}"><span class="{{ $tileIco(request()->routeIs('point-rules*')) }}">rule</span><span class="text-[11px] font-bold text-center">Aturan Poin</span></a>
                        <a href="{{ $rt('admin.complaints.index') }}" class="{{ $tileCls(request()->routeIs('admin.complaints*')) }}"><span class="{{ $tileIco(request()->routeIs('admin.complaints*')) }}">report</span><span class="text-[11px] font-bold text-center">Aduan</span></a>
                    @endif
                </div>
            </div>
            @endif

            @if($isAdmin)
            <a href="{{ $rt('admin.notifications.index') }}" class="flex items-center justify-between p-4 bg-slate-50 hover:bg-orange-50 rounded-2xl transition-all">
                <span class="flex items-center gap-3"><span class="material-symbols-outlined text-slate-500">notifications</span><span class="text-sm font-bold text-slate-700">Notifikasi siswa</span></span>
                <span class="material-symbols-outlined text-slate-400 text-sm">chevron_right</span>
            </a>
            @endif

            <div class="space-y-2 pt-1">
                <p class="text-xs font-extrabold text-slate-400 mb-3">Akun &amp; sesi</p>
                <a href="{{ $rt('profile') }}" class="flex items-center justify-between p-4 bg-slate-50 hover:bg-orange-50 rounded-2xl transition-all">
                    <span class="flex items-center gap-3"><span class="material-symbols-outlined text-slate-500">manage_accounts</span><span class="text-sm font-bold text-slate-700">Profil saya</span></span>
                    <span class="material-symbols-outlined text-slate-400 text-sm">chevron_right</span>
                </a>
                @if($isAdmin)
                <a href="{{ $rt('settings') }}" class="flex items-center justify-between p-4 bg-slate-50 hover:bg-orange-50 rounded-2xl transition-all">
                    <span class="flex items-center gap-3"><span class="material-symbols-outlined text-slate-500">settings</span><span class="text-sm font-bold text-slate-700">Pengaturan</span></span>
                    <span class="material-symbols-outlined text-slate-400 text-sm">chevron_right</span>
                </a>
                @endif
                <form method="POST" action="{{ $rt('logout') }}">
                    @csrf
                    <button type="submit" class="w-full flex items-center gap-3 p-4 bg-rose-50 hover:bg-rose-100 text-rose-600 rounded-2xl transition-all font-bold text-sm mt-2">
                        <span class="material-symbols-outlined">logout</span>Keluar sistem
                    </button>
                </form>
            </div>

            <p class="text-center text-[11px] text-slate-400 font-semibold pt-2">© {{ date('Y') }} Smart Edu Hub</p>
        </div>
    </div>

    @yield('scripts')
    @stack('scripts')
    <script>
        // ---------- Bottom sheet menu ----------
        function toggleMobileMaster() {
            const overlay = document.getElementById('mobileMasterOverlay');
            const sheet = document.getElementById('mobileMasterMenu');
            if (!overlay || !sheet) return;
            const isOpen = !sheet.classList.contains('translate-y-full');
            if (isOpen) {
                sheet.classList.add('translate-y-full');
                overlay.classList.remove('opacity-100');
                setTimeout(() => {
                    if (sheet.classList.contains('translate-y-full')) overlay.classList.add('hidden');
                    document.body.style.overflow = '';
                }, 350);
            } else {
                overlay.classList.remove('hidden');
                setTimeout(() => overlay.classList.add('opacity-100'), 10);
                sheet.classList.remove('translate-y-full');
                document.body.style.overflow = 'hidden';
            }
        }
        document.addEventListener('keydown', (e) => {
            const sheet = document.getElementById('mobileMasterMenu');
            if (e.key === 'Escape' && sheet && !sheet.classList.contains('translate-y-full')) toggleMobileMaster();
        });
        // Tutup sheet otomatis bila layar dibesarkan ke desktop
        window.matchMedia('(min-width: 1024px)').addEventListener?.('change', (e) => {
            const sheet = document.getElementById('mobileMasterMenu');
            if (e.matches && sheet && !sheet.classList.contains('translate-y-full')) toggleMobileMaster();
        });

        // ---------- Auto-dismiss flash message ----------
        document.querySelectorAll('[data-autodismiss]').forEach((el) => {
            setTimeout(() => {
                el.style.transition = 'opacity .3s ease, transform .3s ease';
                el.style.opacity = '0';
                el.style.transform = 'translateY(-6px)';
                setTimeout(() => el.remove(), 320);
            }, 5000);
        });

        // ---------- Loading bar ----------
        (function () {
            const bar = document.getElementById('adminLoadingBar');
            if (!bar) return;
            const start = () => { bar.style.opacity = '1'; bar.style.width = '70%'; };
            const done = () => {
                bar.style.width = '100%';
                setTimeout(() => { bar.style.opacity = '0'; bar.style.width = '0%'; }, 300);
            };
            document.addEventListener('click', (e) => {
                const a = e.target.closest && e.target.closest('a[href]');
                if (!a || e.defaultPrevented || e.metaKey || e.ctrlKey || e.shiftKey || e.button !== 0) return;
                const href = a.getAttribute('href') || '';
                if (href.startsWith('#') || href.startsWith('javascript:') || a.target === '_blank' || a.hasAttribute('download')) return;
                start();
            });
            window.addEventListener('load', done);
            window.addEventListener('pageshow', (e) => { if (e.persisted) done(); }); // tombol back/forward
        })();

        // ---------- Notifikasi admin (toast + polling) ----------
        const NOTIF_STORAGE_KEY = 'lastAdminNotificationId';
        let lastShownNotificationId = 0;
        try { lastShownNotificationId = parseInt(localStorage.getItem(NOTIF_STORAGE_KEY) || '0', 10) || 0; } catch (e) {}

        function escapeHtml(value) {
            const div = document.createElement('div');
            div.textContent = value == null ? '' : String(value);
            return div.innerHTML;
        }

        async function markNotificationAsRead(notificationId) {
            try {
                const token = document.querySelector('meta[name="csrf-token"]').content;
                const url = @json($rt('admin.notifications.read', ['id' => 'NOTIFICATION_ID'])).replace('NOTIFICATION_ID', encodeURIComponent(notificationId));
                if (url === '#') return;
                await fetch(url, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded', 'X-CSRF-TOKEN': token },
                    body: new URLSearchParams({ '_token': token })
                });
            } catch (e) { /* abaikan */ }
        }

        let activeAdminNotificationToast = null;

        function showAdminNotificationToast(title, message, notificationId = null, type = 'general', notificationData = null, additionalCount = 0) {
            const icons = {
                new_student:   ['person_add', 'text-orange-600 bg-orange-100'],
                new_complaint: ['report', 'text-orange-600 bg-orange-100'],
                finance:       ['account_balance_wallet', 'text-emerald-600 bg-emerald-100'],
            };
            const [iconName, iconClass] = icons[type] || ['notifications', 'text-orange-600 bg-orange-100'];

            if (activeAdminNotificationToast) activeAdminNotificationToast.remove();

            const toast = document.createElement('div');
            toast.className = 'fixed top-3 right-3 left-auto w-[calc(100%-1.5rem)] max-w-[20rem] z-[110] bg-white rounded-xl shadow-xl p-3 border border-slate-100 animate-fade-in-down cursor-pointer lg:top-20 lg:right-6 lg:w-96 lg:max-w-none lg:rounded-2xl lg:p-4';
            activeAdminNotificationToast = toast;
            toast.innerHTML = `
                <div class="flex items-start gap-2 lg:gap-3">
                    <div class="w-8 h-8 rounded-lg ${iconClass} flex items-center justify-center shrink-0 lg:w-10 lg:h-10 lg:rounded-xl">
                        <span class="material-symbols-outlined text-[18px] lg:text-2xl">${iconName}</span>
                    </div>
                    <div class="flex-1 min-w-0">
                        <h4 class="font-bold text-slate-800 text-xs lg:text-sm">${escapeHtml(title)}</h4>
                        <p class="text-[11px] lg:text-xs text-slate-600 mt-0.5 lg:mt-1 break-words" style="display:-webkit-box;-webkit-line-clamp:3;-webkit-box-orient:vertical;overflow:hidden">${escapeHtml(message)}${additionalCount ? ` (+${additionalCount} notifikasi lainnya)` : ''}</p>
                    </div>
                    <button type="button" data-close class="shrink-0 p-0.5 lg:p-1 text-slate-400 hover:text-slate-600 rounded-lg hover:bg-slate-100" aria-label="Tutup">
                        <span class="material-symbols-outlined text-sm">close</span>
                    </button>
                </div>`;

            const removeToast = () => {
                toast.remove();
                if (activeAdminNotificationToast === toast) activeAdminNotificationToast = null;
            };
            toast.querySelector('[data-close]').addEventListener('click', (e) => { e.stopPropagation(); removeToast(); });
            toast.addEventListener('click', async () => {
                if (notificationData) {
                    if (type === 'new_student' && notificationData.student_id) {
                        window.location.href = @json($rt('students'));
                        return;
                    } else if (type === 'new_complaint' && notificationData.complaint_id) {
                        window.location.href = @json($rt('admin.complaints.show', ['complaint' => 'COMPLAINT_ID'])).replace('COMPLAINT_ID', encodeURIComponent(notificationData.complaint_id));
                        return;
                    } else if (type === 'finance' && notificationData.transaction_id) {
                        window.location.href = @json($rt('admin.finance.transactions.index'));
                        return;
                    }
                }
                removeToast();
            });

            document.body.appendChild(toast);
            if (notificationId) markNotificationAsRead(notificationId);

            setTimeout(() => {
                if (activeAdminNotificationToast !== toast) return;
                toast.style.transition = 'all 0.3s ease';
                toast.style.opacity = '0';
                toast.style.transform = 'translateY(-10px)';
                setTimeout(removeToast, 300);
            }, 5000);
        }

        // Anti-spam: maksimal 1 toast tampil, jeda minimal antar toast, dan sinkron antar tab
        const NOTIF_TOAST_AT_KEY = 'lastAdminToastAt';
        const NOTIF_TOAST_COOLDOWN_MS = 30000; // 30 detik

        let notifBusy = false;
        async function checkNewAdminNotifications() {
            if (notifBusy || document.hidden) return;
            if (activeAdminNotificationToast) return; // toast masih tampil, jangan ditumpuk

            const notifUrl = @json($rt('admin.notifications.new'));
            if (notifUrl === '#') return;

            // Sinkronkan dengan tab lain: ambil ID terakhir & waktu toast terakhir
            let lastToastAt = 0;
            try {
                lastShownNotificationId = Math.max(lastShownNotificationId, parseInt(localStorage.getItem(NOTIF_STORAGE_KEY) || '0', 10) || 0);
                lastToastAt = parseInt(localStorage.getItem(NOTIF_TOAST_AT_KEY) || '0', 10) || 0;
            } catch (e) {}
            // Masih dalam jeda: notifikasi baru tidak hilang, akan digabung pada toast berikutnya
            if (Date.now() - lastToastAt < NOTIF_TOAST_COOLDOWN_MS) return;

            notifBusy = true;
            try {
                const response = await fetch(notifUrl + '?last_shown_id=' + encodeURIComponent(lastShownNotificationId), {
                    headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
                });
                if (!response.ok) return;
                const data = await response.json();

                // Saring di sisi klien: hanya yang benar-benar baru
                const fresh = (data.notifications || []).filter((n) => n.id > lastShownNotificationId);
                if (!fresh.length) return;

                const latest = fresh.reduce((a, b) => (b.id > a.id ? b : a));

                // Simpan dulu sebelum tampil, supaya polling/tab lain tidak menampilkannya lagi
                lastShownNotificationId = latest.id;
                try {
                    localStorage.setItem(NOTIF_STORAGE_KEY, String(latest.id));
                    localStorage.setItem(NOTIF_TOAST_AT_KEY, String(Date.now()));
                } catch (e) {}

                // Notifikasi lain digabung (ditandai terbaca), hanya 1 toast yang muncul
                fresh.forEach((n) => { if (n.id !== latest.id) markNotificationAsRead(n.id); });

                showAdminNotificationToast(
                    latest.title,
                    latest.message,
                    latest.id,
                    latest.type,
                    latest.data,
                    fresh.length - 1
                );
            } catch (e) { /* jaringan putus: coba lagi pada interval berikutnya */ }
            finally { notifBusy = false; }
        }

        document.addEventListener('DOMContentLoaded', () => {
            checkNewAdminNotifications();
            setInterval(checkNewAdminNotifications, 10000);
            document.addEventListener('visibilitychange', () => { if (!document.hidden) checkNewAdminNotifications(); });
        });

        // ---------- Riwayat login (semua role) ke localStorage ----------
        @auth
            @php
                $u = auth()->user();
                $role = $u->role ?? null;
                $guard = config('auth.defaults.guard');
                $type = 'user';
                $label = 'User';
                $secondary = $u->email ?? '';
                $definedGuards = array_keys(config('auth.guards', []));

                if (in_array('student', $definedGuards) && auth()->guard('student')->check()) {
                    $guard = 'student'; $type = 'student'; $label = 'Siswa';
                    $secondary = $u->nisn ?? $u->username ?? '';
                } elseif (in_array('parent', $definedGuards) && auth()->guard('parent')->check()) {
                    $guard = 'parent'; $type = 'parent'; $label = 'Wali Murid';
                    $secondary = $u->email ?? '';
                } elseif ($u instanceof \App\Models\User) {
                    $label = $u->role_label ?? ucfirst(str_replace('_', ' ', $role ?? 'User'));
                }
            @endphp
            (function () {
                try {
                    const KEY = 'smart_acp_login_history';
                    const MAX = 20;
                    const currentUser = {
                        id: @json($u->id),
                        type: @json($type),
                        guard: @json($guard),
                        role: @json($role),
                        role_label: @json($label),
                        name: @json($u->name),
                        secondary: @json($secondary),
                        last_login: new Date().toISOString()
                    };
                    let list = [];
                    try { list = JSON.parse(localStorage.getItem(KEY) || '[]'); } catch (e) { list = []; }
                    if (!Array.isArray(list)) list = [];
                    list = list.filter(x => x && !(x.id === currentUser.id && x.type === currentUser.type));
                    list.unshift(currentUser);
                    localStorage.setItem(KEY, JSON.stringify(list.slice(0, MAX)));
                } catch (e) { /* localStorage nonaktif */ }
            })();
        @endauth
    </script>
</body>
</html>