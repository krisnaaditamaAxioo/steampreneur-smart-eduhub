<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Portal Siswa') - Smart Edu Hub</title>
    <link rel="icon" type="image/png" href="{{ asset('images/SEHmob.png') }}">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: {
                            50:  '#fff5ed',
                            100: '#ffe8d4',
                            200: '#ffcda8',
                            300: '#ffb07c',
                            400: '#ff944f',
                            500: '#F6863B',
                            600: '#EB4325',
                            700: '#c43018',
                            800: '#a52717',
                            900: '#8A1E1E',
                            950: '#4a0f0f',
                        }
                    }
                }
            }
        }
    </script>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
    @stack('styles')
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #f8fafc; }
        .material-symbols-outlined {
            font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
        }
        .sidebar-item-active {
            background-color: #fff5ed;
            color: #EB4325;
            border-left: 4px solid #EB4325;
        }
        .custom-scrollbar::-webkit-scrollbar { width: 4px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 10px; }
        
        /* Animasi Slide Up untuk Mobile Menu Sheet */
        .mobile-sheet-transition {
            transition: transform 0.4s cubic-bezier(0.16, 1, 0.3, 1), opacity 0.3s ease;
        }
        
        /* Animasi Fade In Down untuk Toast Notifikasi */
        @keyframes fade-in-down {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        .animate-fade-in-down {
            animation: fade-in-down 0.3s ease-out;
        }
        
        /* Loading Progress Bar for Mobile Only */
        #mobileLoadingBar {
            position: fixed;
            top: 0;
            left: 0;
            height: 3px;
            width: 0%;
            background: linear-gradient(90deg, #F6863B, #EB4325, #8A1E1E);
            z-index: 9999;
            transition: width 0.3s ease;
        }
    </style>
</head>
<body class="text-slate-900 min-h-screen flex overflow-x-hidden">
    <!-- Mobile Loading Progress Bar -->
    <div id="mobileLoadingBar" class="lg:hidden"></div>
    <!-- SideNavBar (Desktop Only) -->
    <aside class="hidden lg:flex flex-col w-64 h-screen bg-white border-r border-slate-100 fixed left-0 top-0 z-50">
        <div class="p-8">
            <div class="flex items-center justify-start mb-2">
                <img src="{{ asset('images/SEH.png') }}" alt="Smart Axioo Logo" class="h-10 w-auto object-contain">
            </div>
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-[2px] ml-1">Portal Siswa</p>
        </div>

        <nav class="flex-1 px-4 space-y-1 overflow-y-auto custom-scrollbar">
            <a href="{{ route('siswa.dashboard') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all {{ request()->routeIs('siswa.dashboard') ? 'sidebar-item-active font-bold' : 'text-slate-500 hover:bg-slate-50 hover:text-slate-900' }}">
                <span class="material-symbols-outlined">dashboard</span>
                <span class="text-sm">Dashboard</span>
            </a>
            <a href="{{ route('siswa.notifications') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all {{ request()->routeIs('siswa.notifications') ? 'sidebar-item-active font-bold' : 'text-slate-500 hover:bg-slate-50 hover:text-slate-900' }}">
                <span class="material-symbols-outlined relative">
                    notifications
                    @php
                        $unreadCount = auth('student')->user()->unreadNotifications()->count();
                    @endphp
                    @if($unreadCount > 0)
                        <span class="absolute -top-1 -right-1 text-[8px] bg-rose-500 text-white rounded-full w-4 h-4 flex items-center justify-center font-black">
                            {{ $unreadCount > 9 ? '9+' : $unreadCount }}
                        </span>
                    @endif
                </span>
                <span class="text-sm">Notifikasi</span>
            </a>
            <a href="{{ route('siswa.finance') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all {{ request()->routeIs('siswa.finance') ? 'sidebar-item-active font-bold' : 'text-slate-500 hover:bg-slate-50 hover:text-slate-900' }}">
                <span class="material-symbols-outlined">account_balance_wallet</span>
                <span class="text-sm">Keuangan</span>
            </a>
            <a href="{{ route('siswa.courses') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all {{ request()->routeIs('siswa.courses') ? 'sidebar-item-active font-bold' : 'text-slate-500 hover:bg-slate-50 hover:text-slate-900' }}">
                <span class="material-symbols-outlined">auto_stories</span>
                <span class="text-sm">Kursus Saya</span>
            </a>
            <a href="{{ route('siswa.grades') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all {{ request()->routeIs('siswa.grades*') ? 'sidebar-item-active font-bold' : 'text-slate-500 hover:bg-slate-50 hover:text-slate-900' }}">
                <span class="material-symbols-outlined">grade</span>
                <span class="text-sm">Rekap Nilai</span>
            </a>
            <a href="{{ route('siswa.points') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all {{ request()->routeIs('siswa.points') ? 'sidebar-item-active font-bold' : 'text-slate-500 hover:bg-slate-50 hover:text-slate-900' }}">
                <span class="material-symbols-outlined">military_tech</span>
                <span class="text-sm">Riwayat Poin</span>
            </a>
            <a href="{{ route('siswa.attendance') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all {{ request()->routeIs('siswa.attendance') ? 'sidebar-item-active font-bold' : 'text-slate-500 hover:bg-slate-50 hover:text-slate-900' }}">
                <span class="material-symbols-outlined">event_available</span>
                <span class="text-sm">Presensi</span>
            </a>
            <a href="{{ route('siswa.permit') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all {{ request()->routeIs('siswa.permit') ? 'sidebar-item-active font-bold' : 'text-slate-500 hover:bg-slate-50 hover:text-slate-900' }}">
                <span class="material-symbols-outlined">assignment_late</span>
                <span class="text-sm">Izin / Sakit</span>
            </a>
            <!-- <a href="{{ route('siswa.letters') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all {{ request()->routeIs('siswa.letters') ? 'sidebar-item-active font-bold' : 'text-slate-500 hover:bg-slate-50 hover:text-slate-900' }}">
                <span class="material-symbols-outlined">description</span>
                <span class="text-sm">Surat Resmi</span>
            </a> -->
            <a href="{{ route('siswa.complaints.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl transition-all {{ request()->routeIs('siswa.complaints*') ? 'sidebar-item-active font-bold' : 'text-slate-500 hover:bg-slate-50 hover:text-slate-900' }}">
                <span class="material-symbols-outlined">report</span>
                <span class="text-sm">Aduan</span>
            </a>
        </nav>

        <div class="p-6 mt-auto">
            <form action="{{ route('siswa.logout') }}" method="POST">
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
        <header class="hidden lg:flex h-16 bg-white/80 backdrop-blur-md border-b border-slate-100 sticky top-0 z-40 px-8 items-center justify-between">
            <div class="flex items-center gap-4">
                <h2 class="text-sm font-bold text-slate-400 uppercase tracking-widest">
                    @yield('page_title', 'Dashboard')
                </h2>
            </div>
            <div class="flex items-center gap-4 lg:gap-6">
                <!-- Academic Year Filter -->
                <div class="mr-2 hidden md:block">
                    <form action="{{ route('settings.set-academic-year') }}" method="POST" id="yearSwitcherForm">
                        @csrf
                        <select name="academic_year" onchange="document.getElementById('yearSwitcherForm').submit()" class="bg-slate-50 border-none rounded-full py-1.5 px-4 text-[10px] font-black uppercase tracking-widest text-slate-500 focus:ring-2 focus:ring-brand-500/20 transition-all cursor-pointer">
                            @foreach($globalAcademicYears ?? [] as $year)
                                <option value="{{ $year->name }}" {{ $currentViewYear == $year->name ? 'selected' : '' }}>
                                    AY {{ $year->name }}
                                </option>
                            @endforeach
                        </select>
                    </form>
                </div>

                <!-- Notification Bell -->
                <a href="{{ route('siswa.notifications') }}" class="relative p-2 text-slate-500 hover:text-brand-600 transition-colors" title="Notifikasi">
                    <span class="material-symbols-outlined text-2xl">notifications</span>
                    @php
                        $unreadCount = auth('student')->user()->unreadNotifications()->count();
                    @endphp
                    @if($unreadCount > 0)
                        <span class="absolute -top-1 -right-1 bg-rose-500 text-white text-[10px] font-black rounded-full w-5 h-5 flex items-center justify-center">
                            {{ $unreadCount > 99 ? '99+' : $unreadCount }}
                        </span>
                    @endif
                </a>

                <a href="{{ route('siswa.profile') }}" class="flex items-center gap-3 group" title="Lihat Profil">
                    <div class="text-right hidden sm:block group-hover:text-brand-600 transition-colors">
                        <p class="text-sm font-bold text-slate-900 leading-none">{{ Auth::guard('student')->user()->nama_lengkap }}</p>
                        <p class="text-[10px] text-slate-400 font-black uppercase tracking-widest mt-1">{{ Auth::guard('student')->user()->username }}</p>
                    </div>
                    <img alt="Profile" class="w-10 h-10 rounded-full border-2 border-white shadow-sm ring-1 ring-slate-100" src="https://ui-avatars.com/api/?name={{ urlencode(Auth::guard('student')->user()->nama_lengkap) }}&background=6366f1&color=fff"/>
                </a>
            </div>
        </header>

        <div class="p-4 lg:p-8">
            @yield('content')
        </div>

        <footer class="mt-auto p-8 border-t border-slate-100 text-center hidden lg:block">
            <p class="text-xs text-slate-400 font-medium">© {{ date('Y') }} Smart Edu Hub. Seluruh Hak Cipta Dilindungi.</p>
        </footer>
    </main>

    <!-- Bottom Navigation (Mobile) -->
    <nav class="lg:hidden fixed bottom-0 left-0 right-0 bg-white/80 backdrop-blur-xl border-t border-slate-100 flex justify-around items-center px-2 py-3 z-50 shadow-[0_-8px_30px_rgb(0,0,0,0.06)]">
        <a href="{{ route('siswa.dashboard') }}" class="flex flex-col items-center gap-1 w-16 {{ request()->routeIs('siswa.dashboard') ? 'text-brand-600' : 'text-slate-400' }}">
            <span class="material-symbols-outlined text-[24px]">dashboard</span>
            <span class="text-[10px] font-bold uppercase tracking-tight">Home</span>
        </a>
        <a href="{{ route('siswa.finance') }}" class="flex flex-col items-center gap-1 w-16 {{ request()->routeIs('siswa.finance') ? 'text-brand-600' : 'text-slate-400' }}">
            <span class="material-symbols-outlined text-[24px]">account_balance_wallet</span>
            <span class="text-[10px] font-bold uppercase tracking-tight">Keuangan</span>
        </a>
        <a href="{{ route('siswa.courses') }}" class="flex flex-col items-center gap-1 w-16 {{ request()->routeIs('siswa.courses') ? 'text-brand-600' : 'text-slate-400' }}">
            <span class="material-symbols-outlined text-[24px]">auto_stories</span>
            <span class="text-[10px] font-bold uppercase tracking-tight">Kursus</span>
        </a>
        <a href="{{ route('siswa.attendance') }}" class="flex flex-col items-center gap-1 w-16 {{ request()->routeIs('siswa.attendance') ? 'text-brand-600' : 'text-slate-400' }}">
            <span class="material-symbols-outlined text-[24px]">event_available</span>
            <span class="text-[10px] font-bold uppercase tracking-tight">Absen</span>
        </a>
        <!-- <a href="{{ route('siswa.profile') }}" class="flex flex-col items-center gap-1 w-16 {{ request()->routeIs('siswa.profile') ? 'text-brand-600' : 'text-slate-400' }}">
            <span class="material-symbols-outlined text-[24px]">person</span>
            <span class="text-[10px] font-bold uppercase tracking-tight">Profil</span>
        </a> -->
        <button type="button" onclick="toggleMobileMenu()" class="flex flex-col items-center gap-1 w-16 text-slate-400 hover:text-brand-600">
            <span class="material-symbols-outlined text-[24px]">apps</span>
            <span class="text-[10px] font-bold uppercase tracking-tight">Semua</span>
        </button>
    </nav>

    <!-- Slide-Up Bottom Sheet Overlay -->
    <div id="mobileMenuOverlay" onclick="toggleMobileMenu()" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-[100] hidden opacity-0 transition-opacity duration-300"></div>

    <!-- Mobile Slide-Up Bottom Sheet Navigation -->
    <div id="mobileMenuSheet" class="lg:hidden fixed bottom-0 left-0 right-0 max-h-[85vh] bg-white rounded-t-[32px] z-[101] translate-y-full mobile-sheet-transition shadow-[0_-10px_40px_rgba(0,0,0,0.15)] flex flex-col overflow-hidden">
        <!-- Sheet Handle / Indicator -->
        <div class="w-full flex items-center justify-between px-6 py-3 bg-white border-b border-slate-50 shrink-0 cursor-pointer" onclick="toggleMobileMenu()">
            <div class="w-12 h-1.5 bg-slate-300 rounded-full mx-auto"></div>
            <button type="button" onclick="event.stopPropagation(); toggleMobileMenu()" class="w-8 h-8 rounded-full bg-slate-100 text-slate-500 hover:text-slate-700 flex items-center justify-center -mr-2">
                <span class="material-symbols-outlined text-lg">close</span>
            </button>
        </div>

        <!-- Scrollable Content Area -->
        <div class="overflow-y-auto custom-scrollbar p-6 space-y-6 pb-28">
            <!-- Header & Student Profile Details Inside Sheet -->
            <div class="flex items-center justify-between border-b border-slate-100 pb-5">
                <div class="flex items-center gap-3">
                    <img alt="Profile" class="w-12 h-12 rounded-full border-2 border-brand-100 shadow-sm" src="https://ui-avatars.com/api/?name={{ urlencode(Auth::guard('student')->user()->nama_lengkap ?? Auth::guard('student')->user()->name ?? 'Siswa') }}&background=6366f1&color=fff"/>
                    <div>
                        <h4 class="font-bold text-slate-900 text-base leading-tight">{{ Auth::guard('student')->user()->nama_lengkap ?? Auth::guard('student')->user()->name ?? 'Siswa' }}</h4>
                        <p class="text-xs text-slate-400 font-bold tracking-wider uppercase mt-1">{{ Auth::guard('student')->user()->username ?? '' }}</p>
                    </div>
                </div>
                <!-- Mini Academic Year Selector for Mobile Inside Menu -->
                <div class="md:hidden">
                    <form action="{{ route('settings.set-academic-year') }}" method="POST" id="mobileYearSwitcherForm">
                        @csrf
                        <select name="academic_year" onchange="document.getElementById('mobileYearSwitcherForm').submit()" class="bg-slate-50 border-none rounded-full py-1.5 px-3.5 text-[9px] font-black uppercase tracking-wider text-slate-500 focus:ring-2 focus:ring-brand-500/20 transition-all cursor-pointer">
                            @foreach($globalAcademicYears ?? [] as $year)
                                <option value="{{ $year->name }}" {{ $currentViewYear == $year->name ? 'selected' : '' }}>
                                    AY {{ $year->name }}
                                </option>
                            @endforeach
                        </select>
                    </form>
                </div>
            </div>

            <!-- Menu Grid Links (Beautiful, Touch-friendly cards) -->
            <div>
                <p class="text-[10px] font-black tracking-[2px] text-slate-400 uppercase mb-3">Akses Navigasi</p>
                <div class="grid grid-cols-2 gap-3">
                    <a href="{{ route('siswa.dashboard') }}" class="flex flex-col p-3.5 rounded-2xl border border-slate-100 hover:border-brand-100 transition-all {{ request()->routeIs('siswa.dashboard') ? 'bg-brand-50/50 border-brand-100 text-brand-600' : 'bg-slate-50/50 text-slate-700' }}">
                        <span class="material-symbols-outlined text-[28px] mb-2 {{ request()->routeIs('siswa.dashboard') ? 'text-brand-600' : 'text-slate-500' }}">dashboard</span>
                        <span class="text-xs font-bold">Dashboard</span>
                    </a>
                    <a href="{{ route('siswa.notifications') }}" class="flex flex-col p-3.5 rounded-2xl border border-slate-100 hover:border-brand-100 transition-all {{ request()->routeIs('siswa.notifications') ? 'bg-brand-50/50 border-brand-100 text-brand-600' : 'bg-slate-50/50 text-slate-700' }}">
                        <span class="material-symbols-outlined text-[28px] mb-2 relative {{ request()->routeIs('siswa.notifications') ? 'text-brand-600' : 'text-slate-500' }}">
                            notifications
                            @php
                                $unreadCount = auth('student')->user()->unreadNotifications()->count();
                            @endphp
                            @if($unreadCount > 0)
                                <span class="absolute -top-1 -right-1 text-[8px] bg-rose-500 text-white rounded-full w-4 h-4 flex items-center justify-center font-black">
                                    {{ $unreadCount > 9 ? '9+' : $unreadCount }}
                                </span>
                            @endif
                        </span>
                        <span class="text-xs font-bold">Notifikasi</span>
                    </a>
                    <a href="{{ route('siswa.finance') }}" class="flex flex-col p-3.5 rounded-2xl border border-slate-100 hover:border-brand-100 transition-all {{ request()->routeIs('siswa.finance') ? 'bg-brand-50/50 border-brand-100 text-brand-600' : 'bg-slate-50/50 text-slate-700' }}">
                        <span class="material-symbols-outlined text-[28px] mb-2 {{ request()->routeIs('siswa.finance') ? 'text-brand-600' : 'text-slate-500' }}">account_balance_wallet</span>
                        <span class="text-xs font-bold">Keuangan</span>
                    </a>
                    <a href="{{ route('siswa.courses') }}" class="flex flex-col p-3.5 rounded-2xl border border-slate-100 hover:border-brand-100 transition-all {{ request()->routeIs('siswa.courses') ? 'bg-brand-50/50 border-brand-100 text-brand-600' : 'bg-slate-50/50 text-slate-700' }}">
                        <span class="material-symbols-outlined text-[28px] mb-2 {{ request()->routeIs('siswa.courses') ? 'text-brand-600' : 'text-slate-500' }}">auto_stories</span>
                        <span class="text-xs font-bold">Kursus Saya</span>
                    </a>
                    <a href="{{ route('siswa.grades') }}" class="flex flex-col p-3.5 rounded-2xl border border-slate-100 hover:border-brand-100 transition-all {{ request()->routeIs('siswa.grades*') ? 'bg-brand-50/50 border-brand-100 text-brand-600' : 'bg-slate-50/50 text-slate-700' }}">
                        <span class="material-symbols-outlined text-[28px] mb-2 {{ request()->routeIs('siswa.grades*') ? 'text-brand-600' : 'text-slate-500' }}">grade</span>
                        <span class="text-xs font-bold">Rekap Nilai</span>
                    </a>
                    <a href="{{ route('siswa.points') }}" class="flex flex-col p-3.5 rounded-2xl border border-slate-100 hover:border-brand-100 transition-all {{ request()->routeIs('siswa.points') ? 'bg-brand-50/50 border-brand-100 text-brand-600' : 'bg-slate-50/50 text-slate-700' }}">
                        <span class="material-symbols-outlined text-[28px] mb-2 {{ request()->routeIs('siswa.points') ? 'text-brand-600' : 'text-slate-500' }}">military_tech</span>
                        <span class="text-xs font-bold">Riwayat Poin</span>
                    </a>
                    <a href="{{ route('siswa.attendance') }}" class="flex flex-col p-3.5 rounded-2xl border border-slate-100 hover:border-brand-100 transition-all {{ request()->routeIs('siswa.attendance') ? 'bg-brand-50/50 border-brand-100 text-brand-600' : 'bg-slate-50/50 text-slate-700' }}">
                        <span class="material-symbols-outlined text-[28px] mb-2 {{ request()->routeIs('siswa.attendance') ? 'text-brand-600' : 'text-slate-500' }}">event_available</span>
                        <span class="text-xs font-bold">Presensi</span>
                    </a>
                    <a href="{{ route('siswa.permit') }}" class="flex flex-col p-3.5 rounded-2xl border border-slate-100 hover:border-brand-100 transition-all {{ request()->routeIs('siswa.permit') ? 'bg-brand-50/50 border-brand-100 text-brand-600' : 'bg-slate-50/50 text-slate-700' }}">
                        <span class="material-symbols-outlined text-[28px] mb-2 {{ request()->routeIs('siswa.permit') ? 'text-brand-600' : 'text-slate-500' }}">assignment_late</span>
                        <span class="text-xs font-bold">Izin / Sakit</span>
                    </a>
                    <!-- <a href="{{ route('siswa.letters') }}" class="flex flex-col p-3.5 rounded-2xl border border-slate-100 hover:border-brand-100 transition-all {{ request()->routeIs('siswa.letters') ? 'bg-brand-50/50 border-brand-100 text-brand-600' : 'bg-slate-50/50 text-slate-700' }}">
                        <span class="material-symbols-outlined text-[28px] mb-2 {{ request()->routeIs('siswa.letters') ? 'text-brand-600' : 'text-slate-500' }}">description</span>
                        <span class="text-xs font-bold">Surat Resmi</span>
                    </a> -->
                    <a href="{{ route('siswa.complaints.index') }}" class="flex flex-col p-3.5 rounded-2xl border border-slate-100 hover:border-brand-100 transition-all {{ request()->routeIs('siswa.complaints*') ? 'bg-brand-50/50 border-brand-100 text-brand-600' : 'bg-slate-50/50 text-slate-700' }}">
                        <span class="material-symbols-outlined text-[28px] mb-2 {{ request()->routeIs('siswa.complaints*') ? 'text-brand-600' : 'text-slate-500' }}">report</span>
                        <span class="text-xs font-bold">Aduan</span>
                    </a>
                </div>
            </div>

            <!-- Profile & Account Options -->
            <div class="space-y-2 pt-2">
                <p class="text-[10px] font-black tracking-[2px] text-slate-400 uppercase mb-3">Akun & Sesi</p>
                <a href="{{ route('siswa.profile') }}" class="flex items-center justify-between p-4 bg-slate-50 hover:bg-slate-100 rounded-2xl transition-all">
                    <div class="flex items-center gap-3">
                        <span class="material-symbols-outlined text-slate-600">manage_accounts</span>
                        <span class="text-xs font-bold text-slate-700">Profil Saya</span>
                    </div>
                    <span class="material-symbols-outlined text-slate-400 text-sm">chevron_right</span>
                </a>
                
                <form action="{{ route('siswa.logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="w-full flex items-center gap-3 p-4 bg-rose-50 hover:bg-rose-100 text-rose-600 rounded-2xl transition-all font-bold text-xs mt-3">
                        <span class="material-symbols-outlined">logout</span>
                        Keluar Sistem
                    </button>
                </form>
            </div>
            
            <div class="text-center pt-4">
                <p class="text-[10px] text-slate-400 font-semibold">© {{ date('Y') }} Smart Edu Hub</p>
            </div>
        </div>
    </div>

    @if(request()->routeIs('siswa.finance*'))
    <!-- Slide-Up Finance Bottom Sheet Overlay -->
    <div id="studentFinanceOverlay" onclick="toggleStudentFinanceMenu()" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-[100] hidden opacity-0 transition-opacity duration-300"></div>

    <!-- Student Finance Mobile Menu Sheet -->
    <div id="studentFinanceSheet" class="lg:hidden fixed bottom-0 left-0 right-0 max-h-[85vh] bg-white rounded-t-[32px] z-[101] translate-y-full mobile-sheet-transition shadow-[0_-10px_40px_rgba(0,0,0,0.15)] flex flex-col overflow-hidden">
        <!-- Sheet Handle -->
        <div class="w-full flex justify-center py-4 bg-white border-b border-slate-50 shrink-0 cursor-pointer" onclick="toggleStudentFinanceMenu()">
            <div class="w-12 h-1.5 bg-slate-300 rounded-full"></div>
        </div>

        <div class="overflow-y-auto custom-scrollbar p-6 space-y-6 pb-28">
            <div class="flex items-center justify-between border-b border-slate-100 pb-5">
                <div>
                    <h3 class="text-lg font-black text-slate-900 tracking-tight">Menu Keuangan</h3>
                    <p class="text-xs text-slate-500 font-medium">Navigasi dompet digital siswa</p>
                </div>
                <button onclick="toggleStudentFinanceMenu()" class="p-2 text-slate-400 hover:text-slate-600 rounded-xl bg-slate-50 transition-all">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>

            <div>
                <p class="text-[10px] font-black tracking-[2px] text-slate-400 uppercase mb-3">Akses Keuangan</p>
                <div class="grid grid-cols-2 gap-3">
                    <a href="{{ route('siswa.finance') }}" class="flex flex-col p-3.5 rounded-2xl border {{ request()->routeIs('siswa.finance') && !request()->routeIs('siswa.finance.*') ? 'bg-brand-50/50 border-brand-100 text-brand-600' : 'bg-slate-50/50 border-slate-100 text-slate-700' }} hover:border-brand-100 transition-all">
                        <span class="material-symbols-outlined text-[28px] mb-2 {{ request()->routeIs('siswa.finance') && !request()->routeIs('siswa.finance.*') ? 'text-brand-600' : 'text-slate-500' }}">dashboard</span>
                        <span class="text-xs font-bold">Beranda Dompet</span>
                    </a>
                    <a href="{{ route('siswa.finance.transfer') }}" class="flex flex-col p-3.5 rounded-2xl border {{ request()->routeIs('siswa.finance.transfer*') ? 'bg-brand-50/50 border-brand-100 text-brand-600' : 'bg-slate-50/50 border-slate-100 text-slate-700' }} hover:border-brand-100 transition-all">
                        <span class="material-symbols-outlined text-[28px] mb-2 {{ request()->routeIs('siswa.finance.transfer*') ? 'text-brand-600' : 'text-slate-500' }}">currency_exchange</span>
                        <span class="text-xs font-bold">Transfer Saldo</span>
                    </a>
                    <a href="{{ route('siswa.finance.history') }}" class="flex flex-col p-3.5 rounded-2xl border {{ request()->routeIs('siswa.finance.history*') ? 'bg-brand-50/50 border-brand-100 text-brand-600' : 'bg-slate-50/50 border-slate-100 text-slate-700' }} hover:border-brand-100 transition-all">
                        <span class="material-symbols-outlined text-[28px] mb-2 {{ request()->routeIs('siswa.finance.history*') ? 'text-brand-600' : 'text-slate-500' }}">history</span>
                        <span class="text-xs font-bold">Riwayat & Struk</span>
                    </a>
                    <a href="{{ route('siswa.finance.request-card') }}" class="flex flex-col p-3.5 rounded-2xl border {{ request()->routeIs('siswa.finance.request-card*') ? 'bg-brand-50/50 border-brand-100 text-brand-600' : 'bg-slate-50/50 border-slate-100 text-slate-700' }} hover:border-brand-100 transition-all">
                        <span class="material-symbols-outlined text-[28px] mb-2 {{ request()->routeIs('siswa.finance.request-card*') ? 'text-brand-600' : 'text-slate-500' }}">add_card</span>
                        <span class="text-xs font-bold">Ajukan Kartu</span>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Floating Button for Student Finance Menu (Mobile Only) -->
    <button onclick="toggleStudentFinanceMenu()" 
            class="lg:hidden fixed bottom-24 right-6 w-14 h-14 bg-gradient-to-tr from-[#F6863B] to-[#8A1E1E] hover:from-[#EB4325] hover:to-[#8A1E1E] text-white rounded-full flex items-center justify-center shadow-2xl z-[55] hover:scale-105 active:scale-95 transition-all border-4 border-white/20">
        <span class="material-symbols-outlined text-[26px]">account_balance_wallet</span>
    </button>
    @endif

    <script>
        // Mobile Loading Progress Bar Functionality
        let loadingBarInterval;
        const mobileLoadingBar = document.getElementById('mobileLoadingBar');
        
        function startLoading() {
            if (!mobileLoadingBar) return;
            mobileLoadingBar.style.width = '0%';
            mobileLoadingBar.style.display = 'block';
            
            let width = 0;
            loadingBarInterval = setInterval(() => {
                if (width < 90) {
                    width += Math.random() * 15;
                    mobileLoadingBar.style.width = width + '%';
                }
            }, 200);
        }
        
        function finishLoading() {
            if (!mobileLoadingBar) return;
            clearInterval(loadingBarInterval);
            mobileLoadingBar.style.width = '100%';
            setTimeout(() => {
                mobileLoadingBar.style.display = 'none';
                mobileLoadingBar.style.width = '0%';
            }, 300);
        }
        
        // Attach click handlers to all internal links for loading bar on mobile
        document.addEventListener('DOMContentLoaded', function() {
            // Show loading bar on page load (for navigation from other pages)
            if (window.performance) {
                const navigation = performance.getEntriesByType('navigation')[0];
                if (navigation && navigation.type === 'navigate') {
                    startLoading();
                }
            }
            
            // Show loading bar when clicking links on mobile
            document.querySelectorAll('a[href]').forEach(link => {
                link.addEventListener('click', function(e) {
                    const href = this.getAttribute('href');
                    // Don't show for hash links or external links
                    if (href && !href.startsWith('#') && !href.startsWith('http')) {
                        startLoading();
                    }
                });
            });
            
            // Finish loading when page is fully loaded
            window.addEventListener('load', finishLoading);
        });
        
        // Also finish loading when DOM content is loaded (in case window load takes too long)
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(finishLoading, 500);
        });
        
        // Mengendalikan menu bottom sheet slide-up pada perangkat mobile
        function toggleMobileMenu() {
            const overlay = document.getElementById('mobileMenuOverlay');
            const sheet = document.getElementById('mobileMenuSheet');
            
            if (sheet.classList.contains('translate-y-full')) {
                // Tampilkan overlay
                overlay.classList.remove('hidden');
                setTimeout(() => {
                    overlay.classList.add('opacity-100');
                    // Slide up panel menu
                    sheet.classList.remove('translate-y-full');
                    sheet.classList.add('translate-y-0');
                }, 20);
                
                // Mencegah background scroll saat menu aktif
                document.body.style.overflow = 'hidden';
            } else {
                // Sembunyikan panel menu (slide down)
                sheet.classList.remove('translate-y-0');
                sheet.classList.add('translate-y-full');
                
                // Sembunyikan overlay
                overlay.classList.remove('opacity-100');
                setTimeout(() => {
                    overlay.classList.add('hidden');
                }, 300);
                
                // Mengaktifkan kembali background scroll
                document.body.style.overflow = '';
            }
        }

        // Mengendalikan menu keuangan bottom sheet pada perangkat mobile
        function toggleStudentFinanceMenu() {
            const overlay = document.getElementById('studentFinanceOverlay');
            const sheet = document.getElementById('studentFinanceSheet');
            
            if (!overlay || !sheet) return;

            if (sheet.classList.contains('translate-y-full')) {
                overlay.classList.remove('hidden');
                setTimeout(() => {
                    overlay.classList.add('opacity-100');
                    sheet.classList.remove('translate-y-full');
                    sheet.classList.add('translate-y-0');
                }, 20);
                document.body.style.overflow = 'hidden';
            } else {
                sheet.classList.remove('translate-y-0');
                sheet.classList.add('translate-y-full');
                overlay.classList.remove('opacity-100');
                setTimeout(() => {
                    overlay.classList.add('hidden');
                }, 300);
                document.body.style.overflow = '';
            }
        }

        // Fungsi untuk menampilkan toast notifikasi (hanya satu toast yang tampil)
        function showNotificationToast(title, message, notificationId = null, type = 'admin_custom') {
            // Hapus toast sebelumnya agar tidak menumpuk/berentetan
            document.querySelectorAll('.notif-toast').forEach(el => el.remove());

            const toast = document.createElement('div');
            toast.className = 'notif-toast fixed top-3 right-3 left-3 sm:left-auto sm:right-4 sm:top-4 z-[100] bg-white rounded-xl sm:rounded-2xl shadow-xl p-3 sm:p-4 border border-slate-100 sm:w-80 max-w-full animate-fade-in-down cursor-pointer';

            // Icon berdasarkan tipe
            let iconClass = 'text-brand-600 bg-brand-100';
            let iconName = 'notifications';
            if (type === 'finance') {
                iconClass = 'text-emerald-600 bg-emerald-100';
                iconName = 'account_balance_wallet';
            } else if (type === 'point') {
                iconClass = 'text-amber-600 bg-amber-100';
                iconName = 'military_tech';
            }
            
            toast.innerHTML = `
                <div class="flex items-start gap-2.5 sm:gap-3">
                    <div class="w-8 h-8 sm:w-10 sm:h-10 rounded-lg sm:rounded-xl ${iconClass} flex items-center justify-center shrink-0">
                        <span class="material-symbols-outlined text-lg sm:text-2xl">${iconName}</span>
                    </div>
                    <div class="flex-1 min-w-0">
                        <h4 class="font-bold text-slate-800 text-[13px] sm:text-sm line-clamp-1">${title}</h4>
                        <p class="text-[11px] sm:text-xs text-slate-600 mt-0.5 sm:mt-1 line-clamp-2 sm:line-clamp-3">${message}</p>
                    </div>
                    <button onclick="event.stopPropagation(); this.closest('.notif-toast').remove()" class="shrink-0 p-1 text-slate-400 hover:text-slate-600 rounded-lg hover:bg-slate-100">
                        <span class="material-symbols-outlined text-sm">close</span>
                    </button>
                </div>
            `;

            toast.addEventListener('click', async () => {
                let destination = '{{ route('siswa.notifications') }}';
                if (notificationId) {
                    try {
                        const markReadUrl = '{{ route('siswa.notifications.read', ['id' => 'NOTIFICATION_ID']) }}'.replace('NOTIFICATION_ID', notificationId);
                        await fetch(markReadUrl, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/x-www-form-urlencoded',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                            },
                            body: new URLSearchParams({ '_token': document.querySelector('meta[name="csrf-token"]').content })
                        });
                    } catch (e) {
                        console.error('Error marking notification read:', e);
                    }
                    destination = '{{ route('siswa.notifications.show', ['id' => 'NOTIFICATION_ID']) }}'.replace('NOTIFICATION_ID', notificationId);
                }
                window.location.href = destination;
            });
            
            document.body.appendChild(toast);
            
            // Auto hapus toast setelah 5 detik
            setTimeout(() => {
                toast.style.opacity = '0';
                toast.style.transform = 'translateY(-10px)';
                toast.style.transition = 'all 0.3s ease';
                setTimeout(() => toast.remove(), 300);
            }, 5000);
        }

        // Polling for unread notifications count and new notifications
        document.addEventListener('DOMContentLoaded', function() {
            const notificationBell = document.querySelector('.relative.p-2.text-slate-500');
            if (notificationBell) {
                const notificationBadge = notificationBell.querySelector('span.absolute');
                let lastShownNotificationId = 0;
                
                async function updateNotificationCount() {
                    try {
                        const response = await fetch('{{ route('siswa.api.notifications.unread-count') }}');
                        const data = await response.json();
                        
                        if (notificationBadge) {
                            if (data.count > 0) {
                                notificationBadge.textContent = data.count > 99 ? '99+' : data.count;
                                notificationBadge.style.display = 'flex';
                            } else {
                                notificationBadge.style.display = 'none';
                            }
                        }
                    } catch (e) {
                        console.error('Error fetching notifications:', e);
                    }
                }
                
                async function checkNewNotifications() {
                    try {
                        const response = await fetch('{{ route('siswa.api.notifications.latest') }}?last_shown_id=' + lastShownNotificationId);
                        const data = await response.json();
                        
                        if (data.notifications && data.notifications.length > 0) {
                            // Urutkan dari yang terbaru
                            const list = [...data.notifications].sort((a, b) => b.id - a.id);

                            // Tandai id terakhir yang sudah ditampilkan agar tidak diulang
                            const maxId = list[0].id;
                            if (maxId > lastShownNotificationId) {
                                lastShownNotificationId = maxId;
                            }

                            if (list.length === 1) {
                                // Satu notifikasi: tampilkan detailnya
                                const n = list[0];
                                showNotificationToast(n.title, n.message, n.id, n.type);
                            } else {
                                // Lebih dari satu: cukup satu toast ringkasan
                                const latest = list[0];
                                showNotificationToast(
                                    latest.title,
                                    `Ada ${list.length} notifikasi baru untuk Anda. Ketuk untuk melihat.`,
                                    null,
                                    latest.type
                                );
                            }
                        }
                    } catch (e) {
                        console.error('Error checking new notifications:', e);
                    }
                }
                
                // Update every 5 seconds (lebih cepat untuk real-time feel)
                updateNotificationCount();
                checkNewNotifications();
                setInterval(updateNotificationCount, 5000);
                setInterval(checkNewNotifications, 5000);
            }
        });
    </script>
    @stack('scripts')
    @auth('student')
    <script>
        (function () {
            try {
                const KEY = 'smart_acp_login_history';
                const MAX = 20;
                const STUDENT_GRADIENT = 'from-[#0a2540] to-brand-700';
                const u = @json(auth()->guard('student')->user());
                const currentUser = {
                    id: u.id,
                    type: 'student',
                    guard: 'student',
                    role_label: 'Siswa',
                    name: u.nama_lengkap || u.name || '',
                    secondary: u.nisn || u.username || '',
                    extra: { 
                        kelas: u.kelas ? (typeof u.kelas === 'object' ? u.kelas.nama_kelas : u.kelas) : '',
                        username: u.username || ''
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