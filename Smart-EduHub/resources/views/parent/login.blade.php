<!DOCTYPE html>
<html lang="id" class="light">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1, viewport-fit=cover" name="viewport"/>
    <meta name="theme-color" content="#0f172a"/>
    <title>Login Wali Murid - Smart EduHub</title>

    <link rel="icon" type="image/png" href="{{ asset('images/SEHmob.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'ui-sans-serif', 'system-ui', 'sans-serif'],
                        display: ['"Bricolage Grotesque"', '"Plus Jakarta Sans"', 'ui-sans-serif', 'sans-serif'],
                    },
                },
            },
        };
    </script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Bricolage+Grotesque:opsz,wght@12..96,600;12..96,700;12..96,800&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>

    <style>
        :root { --brand: #059669; }

        html, body { min-height: 100%; }
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            -webkit-tap-highlight-color: transparent;
            -webkit-font-smoothing: antialiased;
        }
        :focus-visible { outline: 2px solid var(--brand); outline-offset: 2px; }

        /* ---------- Background (sama dengan login siswa) ---------- */
        .mesh-auth-bg {
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 35%, #7c2d12 72%, #c2410c 100%);
            background-size: 300% 300%;
            animation: meshAuthShift 24s ease infinite;
        }
        @keyframes meshAuthShift {
            0%, 100% { background-position: 0% 50%; }
            50%      { background-position: 100% 50%; }
        }
        @keyframes floatAuth {
            0%, 100% { transform: translate(0,0) scale(1); }
            33%      { transform: translate(30px,-40px) scale(1.08); }
            66%      { transform: translate(-40px,20px) scale(0.95); }
        }
        .blob-auth { animation: floatAuth 18s ease-in-out infinite; }
        .blob-auth.d1 { animation-delay: -6s; }
        .blob-auth.d2 { animation-delay: -12s; }

        .grid-fade {
            background-image:
                linear-gradient(rgba(255,255,255,.06) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255,255,255,.06) 1px, transparent 1px);
            background-size: 44px 44px;
            -webkit-mask-image: radial-gradient(ellipse at 30% 40%, #000 20%, transparent 75%);
                    mask-image: radial-gradient(ellipse at 30% 40%, #000 20%, transparent 75%);
        }
        /* versi grid untuk hero mobile: memudar ke bawah */
        .grid-fade-mobile {
            background-image:
                linear-gradient(rgba(255,255,255,.07) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255,255,255,.07) 1px, transparent 1px);
            background-size: 36px 36px;
            -webkit-mask-image: linear-gradient(to bottom, #000 0%, transparent 85%);
                    mask-image: linear-gradient(to bottom, #000 0%, transparent 85%);
        }

        /* ---------- Sheet / kartu form ---------- */
        .sheet-card {
            background: #fff;
            box-shadow: 0 -24px 60px -24px rgba(0,0,0,0.5);
        }
        @media (min-width: 640px) {
            .sheet-card { box-shadow: 0 30px 80px -20px rgba(0,0,0,0.55); }
        }
        @media (min-width: 1024px) {
            .sheet-card { background: transparent; box-shadow: none; }
        }

        /* Satu gerakan masuk saat halaman dibuka: sheet naik dari bawah */
        .sheet-rise { animation: sheetRise .65s cubic-bezier(.16,1,.3,1) both; }
        @keyframes sheetRise {
            from { opacity: 0; transform: translateY(36px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        ::-webkit-scrollbar { width: 0; background: transparent; }

        @media (max-height: 640px) {
            .hero-sub { display: none; }
        }

        @media (prefers-reduced-motion: reduce) {
            *, *::before, *::after { animation: none !important; transition: none !important; }
        }
    </style>
</head>
<body class="bg-slate-950 text-slate-900 overflow-x-hidden">

    <!-- Sprite ikon WhatsApp (dipakai di tombol bantuan mobile & tombol melayang desktop) -->
    <svg width="0" height="0" style="position:absolute" aria-hidden="true">
        <symbol id="icon-wa" viewBox="0 0 24 24">
            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.488-.491-.672-.5-.198-.01-.422-.015-.645-.015-.223 0-.585.085-.892.413-.307.329-1.168 1.137-1.168 2.773 0 1.636 1.195 3.213 1.363 3.449.167.237 2.36 3.605 5.719 5.033.799.335 1.437.531 1.927.678.813.243 1.551.208 2.134.125.651-.09 1.998-.814 2.276-1.599.278-.785.278-1.455.198-1.599-.08-.144-.297-.237-.621-.386l-.001.001z"/>
            <path d="M12.002 2.163c-5.435 0-9.84 4.405-9.84 9.842 0 1.73.464 3.346 1.271 4.779l-1.338 4.894 5.024-1.309c1.39.755 2.988 1.189 4.883 1.189h.001c5.435 0 9.84-4.406 9.84-9.843 0-2.636-1.027-5.107-2.891-6.97a9.804 9.804 0 0 0-6.949-2.881zm6.875 14.407c-.198.56-.967 1.064-1.624 1.139-.415.047-.968.068-1.637-.087-.376-.088-.859-.278-1.478-.544-2.624-1.138-4.336-3.773-4.448-3.945-.112-.172-.919-1.22-.919-2.335 0-1.115.482-1.663.656-1.895.174-.232.379-.29.504-.29.126 0 .251.004.362.009.124.007.325-.047.507.372.184.42.626 1.453.68 1.558.055.106.092.227.017.363-.075.137-.112.224-.223.341-.112.118-.232.259-.333.347-.113.098-.227.213-.097.42.131.208 1.166 1.916 2.513 3.093.343.3.623.478.892.574.293.104.56.089.771.054.197-.034.624-.253.951-.497.327-.244.654-.206.904-.122.25.085 1.566.658 1.836.778.27.12.451.18.515.278.065.096.011.193-.055.273-.168.15-.129.16-.347.073-.47-.042-.123-.118-.435-.764-.579-.402-.178-.783-.208-.895-.216z"/>
        </symbol>
    </svg>

    <!-- Background mesh (mobile & tablet) -->
    <div class="fixed inset-0 mesh-auth-bg -z-10 overflow-hidden lg:hidden" aria-hidden="true">
        <div class="blob-auth absolute top-[-10%] left-[-10%] w-[22rem] sm:w-[32rem] aspect-square rounded-full bg-emerald-400/25 blur-[100px]"></div>
        <div class="blob-auth d1 absolute bottom-[-15%] right-[-10%] w-[20rem] sm:w-[28rem] aspect-square rounded-full bg-amber-400/20 blur-[100px]"></div>
        <div class="blob-auth d2 absolute top-[40%] right-[25%] w-60 sm:w-72 aspect-square rounded-full bg-blue-400/15 blur-[90px]"></div>
    </div>

    <main class="min-h-dvh flex flex-col lg:grid lg:grid-cols-2 xl:grid-cols-[1.1fr_1fr]">

        <!-- ===================== PANEL KIRI (desktop) ===================== -->
        <aside class="relative hidden lg:flex flex-col justify-between overflow-hidden mesh-auth-bg text-white p-10 xl:p-16">
            <div class="absolute inset-0 grid-fade" aria-hidden="true"></div>
            <div class="blob-auth absolute -top-24 -left-20 w-[30rem] aspect-square rounded-full bg-emerald-400/25 blur-[110px]" aria-hidden="true"></div>
            <div class="blob-auth d1 absolute -bottom-32 -right-20 w-[26rem] aspect-square rounded-full bg-amber-400/20 blur-[110px]" aria-hidden="true"></div>

            <div class="relative flex items-center gap-3">
                <div class="p-2.5 rounded-2xl bg-white/10 backdrop-blur-md border border-white/15">
                    <img src="{{ asset('images/SEH.png') }}" alt="" class="h-7 w-auto object-contain brightness-0 invert" onerror="this.style.display='none'">
                </div>
                <span class="font-display text-xl font-bold tracking-tight">Smart EduHub</span>
            </div>

            <div class="relative max-w-lg">
                <h1 class="font-display text-4xl xl:text-5xl font-extrabold leading-[1.08] tracking-tight">
                    Dampingi anak belajar, tanpa repot.
                </h1>
                <p class="mt-5 text-base text-orange-50/75 leading-relaxed max-w-md">
                    Masuk sebagai wali murid dengan email dan kata sandi, atau pilih akun yang tersimpan.
                </p>

                <ul class="mt-10 space-y-4">
                    <li class="flex items-center gap-4">
                        <span class="w-11 h-11 rounded-xl bg-white/10 border border-white/15 flex items-center justify-center shrink-0">
                            <span class="material-symbols-outlined text-emerald-200" style="font-variation-settings:'FILL' 1;">mail</span>
                        </span>
                        <span class="text-sm font-semibold text-white/90">Masuk dengan email dan kata sandi</span>
                    </li>
                    <li class="flex items-center gap-4">
                        <span class="w-11 h-11 rounded-xl bg-white/10 border border-white/15 flex items-center justify-center shrink-0">
                            <span class="material-symbols-outlined text-emerald-200" style="font-variation-settings:'FILL' 1;">manage_accounts</span>
                        </span>
                        <span class="text-sm font-semibold text-white/90">Akun tersimpan, masuk tanpa mengetik ulang</span>
                    </li>
                    <li class="flex items-center gap-4">
                        <span class="w-11 h-11 rounded-xl bg-white/10 border border-white/15 flex items-center justify-center shrink-0">
                            <span class="material-symbols-outlined text-emerald-200" style="font-variation-settings:'FILL' 1;">verified_user</span>
                        </span>
                        <span class="text-sm font-semibold text-white/90">Koneksi aman dan terenkripsi</span>
                    </li>
                </ul>
            </div>

            <p class="relative text-xs text-white/45 font-medium">&copy; 2026 Smart EduHub. All rights reserved.</p>
        </aside>

        <!-- ===================== PANEL FORM ===================== -->
        <section class="relative flex flex-1 flex-col lg:items-center lg:justify-center lg:bg-white lg:px-8 lg:py-12">

            <!-- Hero (mobile & tablet) -->
            <header class="relative lg:hidden overflow-hidden px-6 pb-14 pt-[max(2.25rem,env(safe-area-inset-top))] text-white sm:pb-16">
                <div class="pointer-events-none absolute inset-0 grid-fade-mobile" aria-hidden="true"></div>
                <div class="pointer-events-none absolute -right-12 -top-12 h-44 w-44 rounded-full bg-white/10" aria-hidden="true"></div>
                <div class="pointer-events-none absolute -bottom-10 left-1/4 h-28 w-28 rounded-full bg-white/5" aria-hidden="true"></div>

                <div class="relative mx-auto max-w-md">
                    <div class="flex items-center justify-between gap-3">
                        <div class="flex items-center gap-2.5">
                            <span class="rounded-xl border border-white/15 bg-white/10 p-2 backdrop-blur-md">
                                <img src="{{ asset('images/SEH.png') }}" alt="Smart EduHub" class="h-6 w-auto object-contain brightness-0 invert" onerror="this.style.display='none'">
                            </span>
                            <!--<span class="font-display text-lg font-bold tracking-tight">Smart EduHub</span>-->
                        </div>
                        <span class="inline-flex items-center gap-1.5 rounded-full border border-white/15 bg-white/10 px-3 py-1.5 text-xs font-semibold text-emerald-50 backdrop-blur-md">
                            <span class="material-symbols-outlined text-sm text-emerald-300" style="font-variation-settings:'FILL' 1;">diversity_3</span>
                            Wali murid
                        </span>
                    </div>

                    <h1 class="mt-8 font-display text-[2rem] font-extrabold leading-[1.1] tracking-tight sm:text-4xl">
                        Selamat datang kembali
                    </h1>
                    <p class="hero-sub mt-2.5 max-w-xs text-sm leading-relaxed text-orange-50/80">
                        Pantau presensi, keuangan, dan poin kedisiplinan anak dalam satu tempat.
                    </p>
                </div>
            </header>

            <!-- Pembungkus sheet -->
            <div class="sheet-rise relative z-10 mx-auto -mt-7 flex w-full max-w-md flex-1 flex-col sm:mb-10 sm:flex-none lg:mt-0 lg:mb-0 lg:max-w-sm xl:max-w-md">

                <!-- CARD / SHEET -->
                <div id="formCard" class="sheet-card flex-1 rounded-t-[2rem] px-5 pb-[max(1.5rem,env(safe-area-inset-bottom))] pt-7 sm:flex-none sm:rounded-[2rem] sm:px-8 sm:pb-8 sm:pt-8 lg:rounded-none lg:p-0">

                    <!-- Judul sheet (mobile) -->
                    <div class="mb-6 lg:hidden">
                        <h2 class="font-display text-xl font-extrabold tracking-tight text-slate-900">Masuk ke akun Anda</h2>
                        <p class="mt-1 text-sm text-slate-500">Masukkan email dan kata sandi untuk melanjutkan.</p>
                    </div>

                    <!-- Intro (desktop) -->
                    <div class="mb-6 hidden lg:block">
                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-50 text-emerald-700 text-xs font-bold">
                            <span class="material-symbols-outlined text-sm" style="font-variation-settings:'FILL' 1;">diversity_3</span>
                            Portal Wali
                        </span>
                        <h2 class="font-display text-3xl font-extrabold text-slate-900 tracking-tight mt-3 leading-tight">Selamat datang kembali</h2>
                        <p class="text-sm text-slate-500 mt-1.5">Masukkan email dan kata sandi untuk melanjutkan.</p>
                    </div>

                    @php $pOldEmail = old('email'); @endphp

                    @if(session('success'))
                    <div role="status" class="mb-5 flex items-start gap-3 p-3.5 bg-emerald-50 border border-emerald-200 rounded-2xl text-emerald-800">
                        <span class="material-symbols-outlined text-emerald-600 text-xl shrink-0">check_circle</span>
                        <p class="text-sm font-semibold leading-snug">{{ session('success') }}</p>
                    </div>
                    @endif

                    @if($errors->any() && !$errors->has('email') && !$errors->has('password'))
                    <div role="alert" class="mb-5 flex items-start gap-3 p-3.5 bg-rose-50 border border-rose-100 rounded-2xl text-rose-800">
                        <span class="material-symbols-outlined text-rose-500 text-xl shrink-0">error</span>
                        <p class="text-sm font-semibold leading-snug">{{ $errors->first() }}</p>
                    </div>
                    @endif

                    <!-- ============ FORM LOGIN ============ -->
                    <form id="loginForm" class="space-y-4" method="POST" action="{{ route('parent.login') }}">
                        @csrf

                        <!-- Email -->
                        <div class="group">
                            <label class="mb-2 block text-sm font-bold text-slate-700" for="email">Email</label>
                            <div class="relative">
                                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4">
                                    <span class="material-symbols-outlined text-xl text-slate-400 transition-colors group-focus-within:text-emerald-600">mail</span>
                                </div>
                                <input class="block h-14 w-full rounded-2xl border @error('email') border-rose-500 @else border-slate-200 @enderror bg-slate-50 pl-12 pr-4 text-base font-semibold text-slate-900 transition-all duration-200 placeholder:font-medium placeholder:text-slate-400 focus:border-emerald-500 focus:bg-white focus:outline-none focus:ring-4 focus:ring-emerald-500/15 sm:h-12 sm:text-sm"
                                       id="email" name="email" value="{{ old('email') }}" placeholder="nama@email.com"
                                       type="email" required autocomplete="email" inputmode="email" autocapitalize="none" spellcheck="false"/>
                            </div>
                            @error('email')
                                <p class="mt-2 flex items-center gap-1 text-xs font-semibold text-rose-600">
                                    <span class="material-symbols-outlined text-sm">error</span> {{ $message }}
                                </p>
                            @enderror
                        </div>

                        <!-- Password -->
                        <div class="group">
                            <label class="mb-2 block text-sm font-bold text-slate-700" for="password">Kata sandi</label>
                            <div class="relative">
                                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4">
                                    <span class="material-symbols-outlined text-xl text-slate-400 transition-colors group-focus-within:text-emerald-600">lock</span>
                                </div>
                                <input class="block h-14 w-full rounded-2xl border @error('password') border-rose-500 @else border-slate-200 @enderror bg-slate-50 pl-12 pr-14 text-base font-semibold text-slate-900 transition-all duration-200 placeholder:font-medium placeholder:text-slate-400 focus:border-emerald-500 focus:bg-white focus:outline-none focus:ring-4 focus:ring-emerald-500/15 sm:h-12 sm:text-sm"
                                       id="password" name="password" placeholder="Masukkan kata sandi" type="password" required autocomplete="current-password"/>
                                <div class="absolute inset-y-0 right-0 flex items-center pr-2">
                                    <button id="togglePassword" type="button" aria-label="Tampilkan kata sandi" aria-pressed="false"
                                            class="flex h-10 w-10 items-center justify-center rounded-xl text-slate-400 transition-colors hover:bg-slate-100 hover:text-slate-700">
                                        <span class="material-symbols-outlined text-xl">visibility</span>
                                    </button>
                                </div>
                            </div>
                            @error('password')
                                <p class="mt-2 flex items-center gap-1 text-xs font-semibold text-rose-600">
                                    <span class="material-symbols-outlined text-sm">error</span> {{ $message }}
                                </p>
                            @enderror
                        </div>

                        <!-- Ingat saya + lupa sandi -->
                        <div class="flex items-center justify-between gap-3">
                            <label class="flex w-fit cursor-pointer select-none items-center gap-2.5 py-1.5">
                                <input class="h-5 w-5 rounded-md border-slate-300 text-emerald-600 accent-emerald-600 focus:ring-emerald-500/30" id="remember" name="remember" type="checkbox"/>
                                <span class="text-sm font-medium text-slate-600">Ingat saya</span>
                            </label>
                            <a class="py-1.5 text-sm font-bold text-emerald-600 underline-offset-4 hover:underline"
                               href="{{ route('parent.forgot-password') }}">Lupa kata sandi?</a>
                        </div>

                        <!-- Submit -->
                        <button id="submitBtn" type="submit"
                            class="flex h-14 w-full items-center justify-center gap-2 rounded-2xl bg-gradient-to-r from-emerald-600 to-teal-600 px-4 text-sm font-bold text-white shadow-lg shadow-emerald-500/30 transition-all duration-200 hover:brightness-110 focus:outline-none focus-visible:ring-4 focus-visible:ring-emerald-500/30 active:scale-[0.98] disabled:cursor-not-allowed disabled:opacity-70 sm:h-12">
                            <span id="submitLabel">Masuk ke portal</span>
                            <span id="submitIcon" class="material-symbols-outlined text-xl">arrow_forward</span>
                        </button>
                    </form>

                    <!-- ============ OPSI CEPAT ============ -->
                    <form id="parentQuickSubmitForm" method="POST" style="display:none;">@csrf</form>

                    <div class="mt-6">
                        <div class="mb-4 flex items-center gap-3" aria-hidden="true">
                            <div class="h-px flex-1 bg-slate-200"></div>
                            <span class="text-xs font-semibold text-slate-400">atau masuk lebih cepat</span>
                            <div class="h-px flex-1 bg-slate-200"></div>
                        </div>

                        <button type="button" id="parentQuickTrigger"
                            class="group flex w-full items-center gap-3.5 rounded-2xl border border-slate-200 bg-slate-50 p-3.5 text-left transition-all hover:border-slate-300 hover:bg-white hover:shadow-md hover:shadow-slate-900/5 active:scale-[0.98] sm:p-4">
                            <span id="qlStack" class="flex shrink-0 items-center -space-x-3">
                                <span class="flex h-11 w-11 items-center justify-center rounded-xl bg-slate-900 text-white">
                                    <span class="material-symbols-outlined">manage_accounts</span>
                                </span>
                            </span>
                            <span class="min-w-0 flex-1">
                                <span class="block text-sm font-extrabold leading-tight text-slate-900">Akun tersimpan</span>
                                <span id="qlSub" class="mt-0.5 block text-xs leading-snug text-slate-500">Pilih akun, tanpa kata sandi</span>
                            </span>
                            <span class="material-symbols-outlined text-slate-400 transition-colors group-hover:text-emerald-600">chevron_right</span>
                        </button>
                    </div>

                    <!-- Bantuan -->
                    <footer class="mt-7 space-y-1.5 border-t border-slate-100 pt-5 text-center">
                        <p class="text-sm font-medium text-slate-500">
                            Belum punya akun? <a class="font-bold text-emerald-600 hover:underline" href="{{ route('parent.register') }}">Daftar sekarang</a>
                        </p>
                        <p class="text-sm font-medium text-slate-500">
                            Anda siswa? <a class="font-bold text-emerald-600 hover:underline" href="{{ route('siswa.login') }}">Masuk sebagai siswa</a>
                        </p>
                    </footer>

                    <!-- Bantuan & info (mobile & tablet) -->
                    <div class="mt-5 flex flex-col items-center gap-3 text-center lg:hidden">
                        <a href="https://wa.me/6282289635649" target="_blank" rel="noopener"
                           class="inline-flex items-center gap-2 rounded-full bg-green-50 px-4 py-2.5 text-xs font-bold text-green-700 ring-1 ring-inset ring-green-200 transition active:scale-95">
                            <svg class="h-4 w-4" fill="currentColor" aria-hidden="true"><use href="#icon-wa"/></svg>
                            Butuh bantuan? Chat admin
                        </a>
                        <!--<div class="space-y-0.5">-->
                        <!--    <p class="flex items-center justify-center gap-1.5 text-xs font-semibold text-slate-500">-->
                        <!--        <span class="material-symbols-outlined text-sm text-emerald-500" style="font-variation-settings:'FILL' 1;">verified_user</span>-->
                        <!--        Koneksi aman dan terenkripsi-->
                        <!--    </p>-->
                        <!--    <p class="text-xs font-medium text-slate-400">&copy; 2026 Smart EduHub. All rights reserved.</p>-->
                        <!--</div>-->
                    </div>
                </div>
            </div>
        </section>
    </main>

    <!-- ============ QUICK LOGIN SHEET (di luar card agar posisi fixed benar) ============ -->
    <div id="qlBackdrop" class="fixed inset-0 bg-slate-950/70 backdrop-blur-sm z-[105] hidden opacity-0 transition-opacity duration-300"></div>
    <div id="qlSheet" role="dialog" aria-modal="true" aria-labelledby="qlTitle" aria-hidden="true" inert
         class="fixed bottom-0 left-0 right-0 mx-auto w-full max-w-xl bg-white rounded-t-[2rem] sm:rounded-b-[2rem] sm:bottom-6 shadow-2xl z-[110] translate-y-full sm:translate-y-[120%] transition-transform duration-300 ease-out max-h-[88dvh] overflow-y-auto overscroll-contain">
        <div class="px-5 pb-[max(1.25rem,env(safe-area-inset-bottom))] pt-2 sm:p-8">
            <div id="qlHandle" class="-mx-5 flex touch-none justify-center py-2 sm:hidden" aria-hidden="true">
                <span class="h-1.5 w-12 rounded-full bg-slate-200"></span>
            </div>

            <div class="mb-5 mt-2 flex items-start justify-between gap-4 sm:mt-0">
                <div class="flex min-w-0 items-center gap-3">
                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-gradient-to-br from-emerald-600 to-teal-600">
                        <span class="material-symbols-outlined text-xl text-white">diversity_3</span>
                    </div>
                    <div class="min-w-0">
                        <h2 id="qlTitle" class="font-display text-lg font-extrabold tracking-tight text-slate-900">Akun tersimpan</h2>
                        <p class="mt-0.5 text-xs font-medium text-slate-500">Pilih akun wali dari riwayat login</p>
                    </div>
                </div>
                <button id="qlSheetClose" type="button" aria-label="Tutup" class="shrink-0 rounded-xl bg-slate-100 p-2 text-slate-400 transition-colors hover:bg-slate-200 hover:text-slate-700">
                    <span class="material-symbols-outlined text-xl">close</span>
                </button>
            </div>

            <div class="relative mb-4">
                <span class="material-symbols-outlined pointer-events-none absolute left-4 top-1/2 -translate-y-1/2 text-xl text-slate-400">search</span>
                <input id="qlSearch"
                       class="w-full rounded-2xl border border-slate-200 bg-slate-50 py-3.5 pl-12 pr-4 text-base font-medium outline-none transition-all placeholder:text-slate-400 focus:border-emerald-500 focus:bg-white focus:ring-4 focus:ring-emerald-500/15 sm:text-sm"
                       placeholder="Cari nama atau email" type="search" autocomplete="off"/>
            </div>

            <div id="qlList"><div id="qlListInner" class="space-y-2"></div></div>

            <div class="mt-6 flex flex-col-reverse gap-3 border-t border-slate-100 pt-5 sm:flex-row">
                <button type="button" id="qlClose2"
                    class="flex flex-1 items-center justify-center gap-2 rounded-2xl border-2 border-slate-200 px-5 py-3.5 text-sm font-bold text-slate-700 transition-colors hover:border-slate-300 hover:bg-slate-50">
                    <span class="material-symbols-outlined text-lg">edit</span>
                    Gunakan form login
                </button>
                <button type="button" id="qlClearAll"
                    class="flex items-center justify-center gap-1.5 rounded-2xl bg-rose-50 px-5 py-3.5 text-sm font-bold text-rose-600 transition-colors hover:bg-rose-100">
                    <span class="material-symbols-outlined text-lg">delete_sweep</span>
                    Hapus semua
                </button>
            </div>
        </div>
    </div>

    <!-- WhatsApp (desktop; di mobile sudah ada tombol bantuan di dalam sheet) -->
    <a href="https://wa.me/6282289635649" target="_blank" rel="noopener" aria-label="Hubungi lewat WhatsApp"
       class="fixed bottom-6 right-6 z-50 hidden h-14 w-14 items-center justify-center rounded-full bg-green-500 shadow-xl shadow-green-500/30 transition-all hover:scale-110 hover:bg-green-600 active:scale-95 lg:flex">
        <svg class="h-7 w-7 text-white" fill="currentColor" aria-hidden="true"><use href="#icon-wa"/></svg>
    </a>

<script>
(function () {
    const $ = (id) => document.getElementById(id);

    /* ---------- Form ---------- */
    const passwordInput = $('password');
    $('togglePassword')?.addEventListener('click', function () {
        const show = passwordInput.type === 'password';
        passwordInput.type = show ? 'text' : 'password';
        this.setAttribute('aria-pressed', String(show));
        this.setAttribute('aria-label', show ? 'Sembunyikan kata sandi' : 'Tampilkan kata sandi');
        this.querySelector('span').textContent = show ? 'visibility_off' : 'visibility';
    });

    function resetSubmit() {
        const btn = $('submitBtn');
        btn.disabled = false;
        $('submitLabel').textContent = 'Masuk ke portal';
        $('submitIcon').textContent = 'arrow_forward';
        $('submitIcon').classList.remove('animate-spin');
    }

    $('loginForm')?.addEventListener('submit', function () {
        const btn = $('submitBtn');
        if (btn.disabled) return;
        // Tunda sedikit agar form tetap terkirim sebelum tombol dikunci
        setTimeout(() => {
            btn.disabled = true;
            $('submitLabel').textContent = 'Memproses...';
            $('submitIcon').textContent = 'progress_activity';
            $('submitIcon').classList.add('animate-spin');
        }, 0);
    });

    // Tombol "kembali" di browser memulihkan halaman dari cache
    window.addEventListener('pageshow', (e) => { if (e.persisted) resetSubmit(); });

    /* ---------- Quick login ---------- */
    const LOGIN_KEY        = 'smart_acp_login_history';
    const CSRF_TOKEN       = @json(csrf_token());
    const PARENT_QUICK_URL = @json(route('quick-login.parent', ['parent' => '__PARENT_ID__']));

    const backdrop    = $('qlBackdrop');
    const sheet       = $('qlSheet');
    const trigger     = $('parentQuickTrigger');
    const searchInput = $('qlSearch');
    const listInner   = $('qlListInner');
    const stack       = $('qlStack');
    const sub         = $('qlSub');
    const defaultStack = stack ? stack.innerHTML : '';

    const esc = (s) => String(s ?? '').replace(/[&<>"']/g, (c) => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]));
    const initialsOf = (name) => esc(((name || '?').match(/\b\w/g) || []).slice(0, 2).join('').toUpperCase() || '?');

    function readAll() {
        try {
            const all = JSON.parse(localStorage.getItem(LOGIN_KEY) || '[]');
            return Array.isArray(all) ? all : [];
        } catch (e) { return []; }
    }

    function getSavedParents(filter) {
        const f = (filter || '').trim().toLowerCase();
        return readAll().filter(u => u && u.type === 'parent').filter(u => {
            if (!f) return true;
            return String(u.name || '').toLowerCase().includes(f)
                || String(u.secondary || '').toLowerCase().includes(f)
                || String((u.extra && u.extra.student_name) || '').toLowerCase().includes(f);
        });
    }

    // Pratinjau di tombol "Akun tersimpan": tumpukan avatar + jumlah akun
    function renderQuickPreview() {
        if (!stack || !sub) return;
        const users = getSavedParents('');
        if (!users.length) {
            stack.innerHTML = defaultStack;
            sub.textContent = 'Pilih akun, tanpa kata sandi';
            return;
        }
        stack.innerHTML = users.slice(0, 3).map(u => `
            <span class="flex h-11 w-11 items-center justify-center rounded-full bg-gradient-to-br from-emerald-600 to-teal-600 text-xs font-extrabold text-white ring-2 ring-slate-50">${initialsOf(u.name)}</span>
        `).join('');
        sub.textContent = users.length + ' akun siap dipakai, tanpa kata sandi';
    }

    function renderList(filter = '') {
        const users = getSavedParents(filter);

        if (!users.length) {
            listInner.innerHTML = `
                <div class="py-10 flex flex-col items-center text-center">
                    <div class="w-16 h-16 rounded-full bg-slate-100 flex items-center justify-center mb-4">
                        <span class="material-symbols-outlined text-3xl text-slate-300">person_off</span>
                    </div>
                    <p class="text-sm font-extrabold text-slate-600 mb-1">${filter ? 'Tidak ada akun yang cocok' : 'Belum ada riwayat login'}</p>
                    <p class="text-xs text-slate-400 max-w-xs leading-relaxed">${filter ? 'Coba kata kunci lain.' : 'Login sekali dengan form, lalu akun akan tersimpan di sini.'}</p>
                </div>`;
            return;
        }

        listInner.innerHTML = users.map(u => {
            const studentName = u.extra && u.extra.student_name ? esc(u.extra.student_name) : '';
            const quickUrl = esc(PARENT_QUICK_URL.replace('__PARENT_ID__', encodeURIComponent(u.id || '')));
            return `
            <div class="flex items-center gap-2">
                <form method="POST" action="${quickUrl}" class="flex-1 min-w-0">
                    <input type="hidden" name="_token" value="${esc(CSRF_TOKEN)}">
                    <button type="submit"
                        class="w-full flex items-center gap-3 sm:gap-4 p-3 sm:p-4 rounded-2xl border border-slate-100 bg-white hover:bg-emerald-50/50 hover:border-emerald-200 transition-all group text-left active:scale-[0.99]">
                        <div class="w-11 h-11 sm:w-12 sm:h-12 rounded-2xl bg-gradient-to-br from-emerald-600 to-teal-600 flex items-center justify-center text-white font-extrabold text-sm shrink-0 shadow-sm">${initialsOf(u.name)}</div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2 flex-wrap">
                                <p class="text-sm font-bold text-slate-900 truncate">${esc(u.name || 'Wali Murid')}</p>
                                <span class="text-[11px] font-bold px-2 py-0.5 rounded-full border bg-emerald-50 text-emerald-700 border-emerald-200">Wali</span>
                            </div>
                            <p class="text-xs text-slate-500 truncate mt-0.5">${esc(u.secondary || '—')}</p>
                            ${studentName ? `<p class="text-xs text-emerald-700 font-semibold truncate mt-0.5">Siswa: ${studentName}</p>` : ''}
                        </div>
                        <div class="w-9 h-9 rounded-xl bg-slate-50 text-slate-400 flex items-center justify-center group-hover:bg-emerald-600 group-hover:text-white transition-all shrink-0">
                            <span class="material-symbols-outlined text-lg">arrow_forward</span>
                        </div>
                    </button>
                </form>
                <button type="button" data-remove="${esc(u.id)}" aria-label="Hapus dari riwayat" title="Hapus dari riwayat"
                    class="shrink-0 w-10 h-10 rounded-2xl bg-slate-100 hover:bg-rose-100 text-slate-400 hover:text-rose-500 flex items-center justify-center transition-colors">
                    <span class="material-symbols-outlined text-lg">delete</span>
                </button>
            </div>`;
        }).join('');
    }

    function removeAccount(id) {
        try {
            const filtered = readAll().filter(u => !u || u.type !== 'parent' || String(u.id) !== String(id));
            localStorage.setItem(LOGIN_KEY, JSON.stringify(filtered));
            renderList(searchInput?.value || '');
            renderQuickPreview();
        } catch (e) {}
    }

    listInner.addEventListener('click', (e) => {
        const btn = e.target.closest('[data-remove]');
        if (btn) removeAccount(btn.getAttribute('data-remove'));
    });

    $('qlClearAll')?.addEventListener('click', () => {
        try {
            localStorage.setItem(LOGIN_KEY, JSON.stringify(readAll().filter(u => u && u.type !== 'parent')));
            renderList('');
            renderQuickPreview();
        } catch (e) {}
    });

    function openSheet() {
        backdrop.classList.remove('hidden');
        requestAnimationFrame(() => backdrop.classList.remove('opacity-0'));
        sheet.classList.remove('translate-y-full', 'sm:translate-y-[120%]');
        sheet.setAttribute('aria-hidden', 'false');
        sheet.removeAttribute('inert');
        renderList('');
        // Di layar sentuh, fokus otomatis memunculkan keyboard dan menutupi daftar akun
        if (window.matchMedia('(hover: hover)').matches) {
            setTimeout(() => searchInput?.focus({ preventScroll: true }), 350);
        }
        document.body.style.overflow = 'hidden';
    }
    function closeSheet() {
        backdrop.classList.add('opacity-0');
        sheet.classList.add('translate-y-full', 'sm:translate-y-[120%]');
        sheet.setAttribute('aria-hidden', 'true');
        sheet.setAttribute('inert', '');
        setTimeout(() => backdrop.classList.add('hidden'), 300);
        document.body.style.overflow = '';
    }

    trigger?.addEventListener('click', openSheet);
    $('qlSheetClose')?.addEventListener('click', closeSheet);
    $('qlClose2')?.addEventListener('click', closeSheet);
    backdrop?.addEventListener('click', closeSheet);
    searchInput?.addEventListener('input', e => renderList(e.target.value));
    document.addEventListener('keydown', e => { if (e.key === 'Escape') closeSheet(); });

    // Geser handle ke bawah untuk menutup sheet akun tersimpan
    const handle = $('qlHandle');
    let startY = null, dy = 0;
    handle?.addEventListener('touchstart', (e) => {
        startY = e.touches[0].clientY;
        sheet.style.transition = 'none';
    }, { passive: true });
    handle?.addEventListener('touchmove', (e) => {
        if (startY === null) return;
        dy = Math.max(0, e.touches[0].clientY - startY);
        sheet.style.transform = 'translateY(' + dy + 'px)';
    }, { passive: true });
    handle?.addEventListener('touchend', () => {
        if (startY === null) return;
        sheet.style.transition = '';
        sheet.style.transform = '';
        if (dy > 100) closeSheet();
        startY = null; dy = 0;
    });

    renderQuickPreview();

    const oldEmail = @json($pOldEmail ?? null);
    if (oldEmail) {
        const el = $('email');
        if (el) el.value = oldEmail;
    }

    // Fokus otomatis hanya di desktop; di HP keyboard akan menutupi tampilan
    if (window.matchMedia('(min-width: 1024px) and (hover: hover)').matches) {
        ($('email')?.value ? $('password') : $('email'))?.focus();
    }
})();
</script>
</body>
</html>