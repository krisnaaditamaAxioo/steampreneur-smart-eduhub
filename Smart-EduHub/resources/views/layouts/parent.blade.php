@php
    $parentUser = Auth::guard('parent')->user();
    $unread     = $parentUser ? $parentUser->unreadNotifications()->count() : 0;

    // [route, ikon, label desktop, label mobile (null = tidak tampil di bottom nav)]
    $nav = [
        ['parent.dashboard',     'dashboard',            'Dashboard',            'Home'],
        ['parent.attendance',    'event_available',      'Presensi Anak',        'Absen'],
        ['parent.finance',       'account_balance_wallet', 'Keuangan Siswa',     'Keuangan'],
        ['parent.points',        'military_tech',        'Poin Kedisiplinan',    'Poin'],
        ['parent.permit',        'assignment_late',      'Pengajuan Izin Sakit', null],
        ['parent.notifications', 'notifications',        'Notifikasi',           null],
        ['parent.complaints',    'report',               'Layanan Pengaduan',    null],
        ['parent.profile',       'account_circle',       'Profil Saya',          null],
    ];

    // Item yang tampil langsung di bottom nav (mobile)
    $mobileMain = array_values(array_filter($nav, fn ($n) => $n[3] !== null));

    // Item yang dimasukkan ke dalam sheet "Lainnya" (mobile)
    $mobileMore = [
        ['parent.permit',        'assignment_late', 'Pengajuan Izin Sakit'],
        ['parent.notifications', 'notifications',   'Notifikasi'],
        ['parent.complaints',    'report',          'Layanan Pengaduan'],
        ['parent.profile',       'account_circle',  'Profil Saya'],
    ];

    // Saring hanya route yang benar-benar terdaftar, supaya layout tidak error
    // (mis. saat cache route di server belum diperbarui setelah penambahan route baru).
    $nav        = array_values(array_filter($nav, fn ($n) => Route::has($n[0])));
    $mobileMain = array_values(array_filter($mobileMain, fn ($n) => Route::has($n[0])));
    $mobileMore = array_values(array_filter($mobileMore, fn ($n) => Route::has($n[0])));

    $hasProfile = Route::has('parent.profile');
    $moreActive = request()->routeIs('parent.permit', 'parent.notifications', 'parent.complaints', 'parent.complaints.show', 'parent.profile');
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0, viewport-fit=cover" name="viewport"/>
    <meta name="theme-color" content="#059669"/>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Portal Wali Murid') - Smart Edu Hub</title>
    <link rel="icon" type="image/png" href="{{ asset('images/SEHmob.png') }}">
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <script>
        // Warna "brand" dipetakan ke hijau, jadi kelas brand-* di halaman lain ikut hijau
        tailwind.config = {
            theme: { extend: { colors: { brand: {
                50: '#ecfdf5', 100: '#d1fae5', 200: '#a7f3d0', 300: '#6ee7b7', 400: '#34d399',
                500: '#10b981', 600: '#059669', 700: '#047857', 800: '#065f46', 900: '#064e3b', 950: '#022c22'
            } } } }
        };
    </script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
    @stack('styles')
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #f4faf6; -webkit-font-smoothing: antialiased; }
        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
        }
        .sidebar-item-active {
            background-color: #ecfdf5;
            color: #047857;
            border-left: 4px solid #059669;
        }
        /* Logo mode web: diwarnai hijau (#059669) dengan tetap mempertahankan bentuk/transparansi logo */
        .logo-green { filter: url(#logo-green); }
        .custom-scrollbar::-webkit-scrollbar { width: 4px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #d1fae5; border-radius: 10px; }
    </style>
</head>
<body class="text-slate-900 min-h-screen flex overflow-x-hidden">
    <!-- Filter warna logo (hijau) -->
    <svg width="0" height="0" class="absolute" aria-hidden="true" focusable="false">
        <defs>
            <filter id="logo-green" color-interpolation-filters="sRGB" x="0" y="0" width="100%" height="100%">
                <feFlood flood-color="#059669" result="green"/>
                <feComposite in="green" in2="SourceAlpha" operator="in"/>
            </filter>
        </defs>
    </svg>

    <!-- SideNavBar (Desktop Only) -->
    <aside class="hidden lg:flex flex-col w-64 h-screen bg-white border-r border-emerald-100 fixed left-0 top-0 z-50">
        <div class="p-8">
            <div class="flex items-center justify-start mb-2">
                <img src="{{ asset('images/SEH.png') }}" alt="Smart Axioo Logo" class="logo-green h-10 w-auto object-contain">
            </div>
            <p class="text-xs font-bold text-emerald-600 ml-1">Portal Wali Murid</p>
        </div>

        <nav class="flex-1 px-4 space-y-1 overflow-y-auto custom-scrollbar" aria-label="Menu utama">
            @foreach($nav as [$route, $icon, $label, $mobileLabel])
                @php $active = request()->routeIs($route); @endphp
                <a href="{{ route($route) }}"
                   @if($active) aria-current="page" @endif
                   class="flex items-center justify-between gap-3 px-4 py-3 rounded-xl transition-all focus:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500/40 {{ $active ? 'sidebar-item-active font-bold' : 'text-slate-500 hover:bg-emerald-50 hover:text-emerald-800' }}">
                    <span class="flex items-center gap-3">
                        <span class="material-symbols-outlined">{{ $icon }}</span>
                        <span class="text-sm">{{ $label }}</span>
                    </span>
                    @if($route === 'parent.notifications' && $unread > 0)
                        <span class="min-w-5 h-5 px-1 rounded-full bg-rose-500 text-white text-[10px] font-black flex items-center justify-center">{{ $unread }}</span>
                    @endif
                </a>
            @endforeach
        </nav>

        <div class="p-6 mt-auto">
            <form action="{{ route('parent.logout') }}" method="POST">
                @csrf
                <button type="submit" class="w-full flex items-center gap-3 px-4 py-3 rounded-xl text-rose-500 hover:bg-rose-50 transition-all font-bold text-sm">
                    <span class="material-symbols-outlined">logout</span>
                    Keluar Sistem
                </button>
            </form>
        </div>
    </aside>

    <!-- Main Content -->
    <main class="flex-1 lg:ml-64 flex flex-col min-h-screen pb-24 lg:pb-0 overflow-x-hidden">
        <!-- TopAppBar -->
        <header class="hidden lg:flex h-16 bg-white/80 backdrop-blur-md border-b border-emerald-100 sticky top-0 z-40 px-8 items-center justify-between">
            <div class="flex items-center gap-4">
                <h2 class="text-sm font-semibold text-slate-500">
                    @yield('page_title', 'Dashboard Wali Murid')
                </h2>
            </div>
        </header>

        @unless(request()->routeIs('parent.dashboard'))
            {{-- Ponsel: header menyatu dengan sisi atas layar (sampai di bawah status bar), seperti aplikasi.
                 Desktop: tetap kartu melayang dengan margin. --}}
            <div class="relative overflow-hidden bg-gradient-to-br from-[#059669] via-[#047857] to-[#064e3b] px-5 pb-8 pt-[max(1rem,env(safe-area-inset-top))] text-white shadow-lg shadow-emerald-900/15 rounded-b-[2rem] lg:mx-8 lg:mt-6 lg:rounded-[2rem] lg:px-6 lg:pt-6 lg:shadow-xl">
                <div class="pointer-events-none absolute -right-10 -top-10 h-44 w-44 rounded-full bg-white/10"></div>
                <div class="pointer-events-none absolute -bottom-12 left-1/3 h-32 w-32 rounded-full bg-white/5"></div>

                <div class="relative z-10 space-y-1 lg:space-y-4">
                    <div class="hidden items-center gap-3 lg:flex">
                        <span class="material-symbols-outlined text-emerald-200">family_restroom</span>
                        <p class="text-sm font-semibold text-emerald-100">Halaman Wali Murid</p>
                    </div>
                    <p class="truncate text-sm text-emerald-100/80 lg:hidden">Wali dari {{ $parentUser->student?->nama_lengkap ?? '-' }}</p>
                    <h1 class="text-2xl font-extrabold tracking-tight sm:text-3xl">@yield('page_title', 'Halaman Wali Murid')</h1>
                </div>
            </div>
        @endunless

        <div class="p-4 lg:p-8">
            @yield('content')
        </div>

        <footer class="mt-auto p-8 border-t border-emerald-100 text-center hidden lg:block">
            <p class="text-xs text-slate-400 font-medium">© {{ date('Y') }} Smart Axioo Class Program. Portal Resmi Wali Murid.</p>
        </footer>
    </main>

    <!-- Bottom Navigation (Mobile) -->
    <nav aria-label="Menu utama"
         class="lg:hidden fixed bottom-0 left-0 right-0 bg-white/90 backdrop-blur-xl border-t border-emerald-100 flex justify-around items-center px-2 pt-3 z-50 shadow-[0_-8px_30px_rgb(4,120,87,0.08)]"
         style="padding-bottom: max(0.75rem, env(safe-area-inset-bottom));">
        @foreach($mobileMain as [$route, $icon, $label, $mobileLabel])
            @php $active = request()->routeIs($route); @endphp
            <a href="{{ route($route) }}"
               @if($active) aria-current="page" @endif
               class="relative flex flex-col items-center gap-1 w-16 transition-colors {{ $active ? 'text-emerald-700 font-bold' : 'text-slate-400 hover:text-emerald-600' }}">
                <span class="flex h-8 w-12 items-center justify-center rounded-full transition-colors {{ $active ? 'bg-emerald-100' : '' }}">
                    <span class="material-symbols-outlined text-[24px]" @if($active) style="font-variation-settings:'FILL' 1;" @endif>{{ $icon }}</span>
                </span>
                <span class="text-[11px] leading-none">{{ $mobileLabel }}</span>
            </a>
        @endforeach

        {{-- Tombol Lainnya (membuka sheet berisi menu sisanya) --}}
        <button type="button" onclick="toggleParentMore()" aria-controls="parentMoreSheet" aria-expanded="false"
                class="relative flex flex-col items-center gap-1 w-16 transition-colors {{ $moreActive ? 'text-emerald-700 font-bold' : 'text-slate-400 hover:text-emerald-600' }}">
            <span class="flex h-8 w-12 items-center justify-center rounded-full transition-colors {{ $moreActive ? 'bg-emerald-100' : '' }}">
                <span class="material-symbols-outlined text-[24px]" @if($moreActive) style="font-variation-settings:'FILL' 1;" @endif>apps</span>
                @if($unread > 0 && !$moreActive)
                    <span class="absolute top-0 right-2 min-w-4 h-4 px-1 rounded-full bg-rose-500 text-white text-[9px] font-black flex items-center justify-center">{{ $unread > 9 ? '9+' : $unread }}</span>
                @endif
            </span>
            <span class="text-[11px] leading-none">Lainnya</span>
        </button>
    </nav>

    <!-- Overlay + Sheet Menu "Lainnya" (Mobile) -->
    <div id="parentMoreOverlay" class="lg:hidden fixed inset-0 z-[60] hidden bg-slate-950/50 backdrop-blur-sm opacity-0 transition-opacity duration-300"></div>
    <div id="parentMoreSheet" role="dialog" aria-modal="true" aria-label="Menu lainnya"
         class="lg:hidden fixed bottom-0 left-0 right-0 z-[70] translate-y-full transition-transform duration-300 ease-out bg-white rounded-t-[1.75rem] border-t border-emerald-100 shadow-2xl px-4 pt-3"
         style="padding-bottom: max(1.25rem, env(safe-area-inset-bottom));">
        <div class="w-12 h-1.5 bg-slate-200 rounded-full mx-auto mb-4"></div>

        <div class="flex items-center justify-between mb-3">
            <h3 class="text-sm font-extrabold text-slate-900">Menu Lainnya</h3>
            <button type="button" onclick="toggleParentMore(false)" class="w-8 h-8 rounded-full flex items-center justify-center text-slate-400 hover:bg-slate-100 transition" aria-label="Tutup">
                <span class="material-symbols-outlined text-lg">close</span>
            </button>
        </div>

        {{-- Info akun --}}
        <div class="flex items-center gap-3 p-3 rounded-2xl bg-emerald-50/60 border border-emerald-100 mb-3">
            <img alt="" class="w-11 h-11 rounded-full border-2 border-white shadow-sm"
                 src="https://ui-avatars.com/api/?name={{ urlencode($parentUser->name) }}&background=059669&color=fff&bold=true"/>
            <div class="min-w-0 flex-1">
                <p class="text-sm font-bold text-slate-900 truncate">{{ $parentUser->name }}</p>
                <p class="text-[11px] text-slate-500 font-semibold truncate">Wali dari {{ $parentUser->student?->nama_lengkap ?? '-' }}</p>
            </div>
            <a href="{{ route('parent.notifications') }}" class="shrink-0 text-[11px] font-extrabold uppercase tracking-wider text-emerald-700 px-3 py-1.5 rounded-full bg-white border border-emerald-200{{ $hasProfile ? '' : ' hidden' }}">Notifikasi</a>
            @if($hasProfile)
                <a href="{{ route('parent.profile') }}" class="shrink-0 text-[11px] font-extrabold uppercase tracking-wider text-emerald-700 px-3 py-1.5 rounded-full bg-white border border-emerald-200">Profil</a>
            @endif
        </div>

        <div class="grid grid-cols-1 gap-1.5">
            @foreach($mobileMore as [$route, $icon, $label])
                @php $active = request()->routeIs($route); @endphp
                <a href="{{ route($route) }}"
                   class="flex items-center gap-3 px-3 py-3 rounded-2xl transition-colors {{ $active ? 'bg-emerald-50 text-emerald-700 font-bold' : 'text-slate-600 hover:bg-slate-50' }}">
                    <span class="w-9 h-9 rounded-xl flex items-center justify-center shrink-0 {{ $active ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-500' }}">
                        <span class="material-symbols-outlined text-xl">{{ $icon }}</span>
                    </span>
                    <span class="text-sm flex-1">{{ $label }}</span>
                    @if($route === 'parent.notifications' && $unread > 0)
                        <span class="min-w-5 h-5 px-1 rounded-full bg-rose-500 text-white text-[10px] font-black flex items-center justify-center">{{ $unread > 99 ? '99+' : $unread }}</span>
                    @endif
                    <span class="material-symbols-outlined text-slate-300 text-lg">chevron_right</span>
                </a>
            @endforeach
        </div>

        <form action="{{ route('parent.logout') }}" method="POST" class="mt-3">
            @csrf
            <button type="submit" class="w-full flex items-center justify-center gap-2 px-4 py-3 rounded-2xl bg-rose-50 text-rose-600 font-bold text-sm hover:bg-rose-100 transition-all">
                <span class="material-symbols-outlined text-lg">logout</span>
                Keluar Sistem
            </button>
        </form>
    </div>

    <script>
        // Buka/tutup sheet menu "Lainnya" pada tampilan mobile
        function toggleParentMore(force) {
            const sheet = document.getElementById('parentMoreSheet');
            const overlay = document.getElementById('parentMoreOverlay');
            const trigger = document.querySelector('[aria-controls="parentMoreSheet"]');
            if (!sheet || !overlay) return;

            const open = typeof force === 'boolean' ? force : !sheet.classList.contains('translate-y-0');

            sheet.classList.toggle('translate-y-0', open);
            sheet.classList.toggle('translate-y-full', !open);
            if (trigger) trigger.setAttribute('aria-expanded', open ? 'true' : 'false');
            document.body.style.overflow = open ? 'hidden' : '';

            if (open) {
                overlay.classList.remove('hidden');
                requestAnimationFrame(() => overlay.classList.remove('opacity-0'));
            } else {
                overlay.classList.add('opacity-0');
                setTimeout(() => {
                    if (!sheet.classList.contains('translate-y-0')) overlay.classList.add('hidden');
                }, 300);
            }
        }

        document.getElementById('parentMoreOverlay')?.addEventListener('click', () => toggleParentMore(false));
        document.addEventListener('keydown', (e) => { if (e.key === 'Escape') toggleParentMore(false); });
    </script>

    @auth('parent')
    <script>
        (function () {
            try {
                const KEY = 'smart_acp_login_history';
                const MAX = 20;
                const p = @json(auth()->guard('parent')->user());
                const student = p.student || null;
                const currentUser = {
                    id: p.id,
                    type: 'parent',
                    guard: 'parent',
                    role_label: 'Wali Murid',
                    name: p.nama_lengkap || p.name || '',
                    secondary: p.email || '',
                    extra: { 
                        phone: p.phone || p.no_hp || '',
                        student_name: student ? (student.nama_lengkap || student.name || '') : ''
                    },
                    last_login: new Date().toISOString()
                };
                let list = [];
                try { list = JSON.parse(localStorage.getItem(KEY) || '[]'); } catch(e) { list = []; }
                list = list.filter(x => x && !(String(x.id) === String(currentUser.id) && x.type === currentUser.type));
                list.unshift(currentUser);
                list = list.slice(0, MAX);
                localStorage.setItem(KEY, JSON.stringify(list));
            } catch(e) { /* ignore */ }
        })();
    </script>
    @endauth
</body>
</html>