<!DOCTYPE html>
<html class="light" lang="id">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1, viewport-fit=cover" name="viewport"/>
    <meta name="theme-color" content="#0f172a"/>
    <title>Login Siswa - Smart EduHub</title>

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
    <script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>

    <style>
        :root { --brand: #ea580c; --brand-deep: #c2410c; }

        html, body { min-height: 100%; }
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            -webkit-tap-highlight-color: transparent;
            -webkit-font-smoothing: antialiased;
        }
        :focus-visible { outline: 2px solid var(--brand); outline-offset: 2px; }

        /* ---------- Background ---------- */
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

        /* Grid halus di panel kiri (desktop) */
        .grid-fade {
            background-image:
                linear-gradient(rgba(255,255,255,.06) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255,255,255,.06) 1px, transparent 1px);
            background-size: 44px 44px;
            -webkit-mask-image: radial-gradient(ellipse at 30% 40%, #000 20%, transparent 75%);
                    mask-image: radial-gradient(ellipse at 30% 40%, #000 20%, transparent 75%);
        }
        /* Grid hero mobile: memudar ke bawah */
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

        .animate-step { animation: stepIn .28s cubic-bezier(.16,1,.3,1) both; }
        @keyframes stepIn {
            from { opacity: 0; transform: translateX(10px); }
            to   { opacity: 1; transform: translateX(0); }
        }

        .step-hidden { display: none !important; }

        /* Saat input PIN (mobile): hero dipadatkan agar keypad lega */
        main:has(#step2:not(.step-hidden)) .hero-sub { display: none; }
        main:has(#step2:not(.step-hidden)) .hero-h1  { margin-top: 1.25rem; }
        main:has(#step2:not(.step-hidden)) .hero     { padding-bottom: 2.75rem; }

        /* ---------- PIN ---------- */
        .pin-box {
            flex: 1 1 0; max-width: 3.25rem; aspect-ratio: 4 / 5;
            border: 2px solid #e2e8f0; background: #f8fafc; border-radius: 1rem;
            display: flex; align-items: center; justify-content: center;
            transition: border-color .2s, background-color .2s, box-shadow .2s, transform .2s;
        }
        .pin-box.is-active { border-color: var(--brand); background: #fff7ed; box-shadow: 0 0 0 4px rgba(234,88,12,.12); }
        .pin-box.is-filled { border-color: var(--brand); background: #fff; }
        .pin-dot { width: .875rem; height: .875rem; border-radius: 9999px; background: var(--brand); animation: dotPop .18s ease-out both; }
        @keyframes dotPop { from { transform: scale(.4); opacity: 0; } to { transform: scale(1); opacity: 1; } }
        .pin-shake { animation: shake .35s ease; }
        @keyframes shake { 20%,60% { transform: translateX(-6px); } 40%,80% { transform: translateX(6px); } }

        .key-btn, .key-ghost {
            width: 100%;
            height: clamp(3rem, 8.5dvh, 4rem);
            border-radius: 1.125rem;
            display: flex; align-items: center; justify-content: center;
            user-select: none; -webkit-user-select: none; touch-action: manipulation;
            transition: transform .12s, background-color .15s, box-shadow .15s;
        }
        .key-btn {
            background: #fff; border: 1px solid #e2e8f0; color: #0f172a;
            font-size: 1.5rem; font-weight: 600;
            box-shadow: 0 1px 2px rgba(15,23,42,.06);
        }
        .key-btn:hover  { background: #fff7ed; border-color: #fed7aa; }
        .key-btn:active { transform: scale(.93); background: #ffedd5; }
        .key-ghost { color: #64748b; }
        .key-ghost:hover  { color: #0f172a; background: #f1f5f9; }
        .key-ghost:active { transform: scale(.9); }

        /* ---------- Kamera ---------- */
        #cameraScanner video {
            width: 100% !important; height: auto !important;
            max-height: 55dvh; object-fit: cover; border-radius: 1rem;
        }
        #cameraScanner { min-height: 12rem; }

        ::-webkit-scrollbar { width: 0; background: transparent; }

        /* Layar pendek (landscape HP) */
        @media (max-height: 640px) {
            .intro-block, .hero-sub { display: none; }
        }

        @media (prefers-reduced-motion: reduce) {
            *, *::before, *::after { animation: none !important; transition: none !important; }
        }
    </style>
</head>
<body class="bg-slate-950 text-slate-900 overflow-x-hidden">

    <!-- Sprite ikon WhatsApp (tombol bantuan mobile & tombol melayang desktop) -->
    <svg width="0" height="0" style="position:absolute" aria-hidden="true">
        <symbol id="icon-wa" viewBox="0 0 24 24">
            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.488-.491-.672-.5-.198-.01-.422-.015-.645-.015-.223 0-.585.085-.892.413-.307.329-1.168 1.137-1.168 2.773 0 1.636 1.195 3.213 1.363 3.449.167.237 2.36 3.605 5.719 5.033.799.335 1.437.531 1.927.678.813.243 1.551.208 2.134.125.651-.09 1.998-.814 2.276-1.599.278-.785.278-1.455.198-1.599-.08-.144-.297-.237-.621-.386l-.001.001z"/>
            <path d="M12.002 2.163c-5.435 0-9.84 4.405-9.84 9.842 0 1.73.464 3.346 1.271 4.779l-1.338 4.894 5.024-1.309c1.39.755 2.988 1.189 4.883 1.189h.001c5.435 0 9.84-4.406 9.84-9.843 0-2.636-1.027-5.107-2.891-6.97a9.804 9.804 0 0 0-6.949-2.881zm6.875 14.407c-.198.56-.967 1.064-1.624 1.139-.415.047-.968.068-1.637-.087-.376-.088-.859-.278-1.478-.544-2.624-1.138-4.336-3.773-4.448-3.945-.112-.172-.919-1.22-.919-2.335 0-1.115.482-1.663.656-1.895.174-.232.379-.29.504-.29.126 0 .251.004.362.009.124.007.325-.047.507.372.184.42.626 1.453.68 1.558.055.106.092.227.017.363-.075.137-.112.224-.223.341-.112.118-.232.259-.333.347-.113.098-.227.213-.097.42.131.208 1.166 1.916 2.513 3.093.343.3.623.478.892.574.293.104.56.089.771.054.197-.034.624-.253.951-.497.327-.244.654-.206.904-.122.25.085 1.566.658 1.836.778.27.12.451.18.515.278.065.096.011.193-.055.273-.168.15-.129.16-.347.073-.47-.042-.123-.118-.435-.764-.579-.402-.178-.783-.208-.895-.216z"/>
        </symbol>
    </svg>

    <!-- Background mesh (mobile & tablet) -->
    <div class="fixed inset-0 mesh-auth-bg -z-10 overflow-hidden lg:hidden" aria-hidden="true">
        <div class="blob-auth absolute top-[-10%] left-[-10%] w-[22rem] sm:w-[32rem] aspect-square rounded-full bg-orange-400/30 blur-[100px]"></div>
        <div class="blob-auth d1 absolute bottom-[-15%] right-[-10%] w-[20rem] sm:w-[28rem] aspect-square rounded-full bg-amber-400/20 blur-[100px]"></div>
        <div class="blob-auth d2 absolute top-[40%] right-[25%] w-60 sm:w-72 aspect-square rounded-full bg-blue-400/15 blur-[90px]"></div>
    </div>

    <main class="min-h-dvh flex flex-col lg:grid lg:grid-cols-2 xl:grid-cols-[1.1fr_1fr]">

        <!-- ===================== PANEL KIRI (desktop) ===================== -->
        <aside class="relative hidden lg:flex flex-col justify-between overflow-hidden mesh-auth-bg text-white p-10 xl:p-16">
            <div class="absolute inset-0 grid-fade" aria-hidden="true"></div>
            <div class="blob-auth absolute -top-24 -left-20 w-[30rem] aspect-square rounded-full bg-orange-400/30 blur-[110px]" aria-hidden="true"></div>
            <div class="blob-auth d1 absolute -bottom-32 -right-20 w-[26rem] aspect-square rounded-full bg-amber-400/20 blur-[110px]" aria-hidden="true"></div>

            <div class="relative flex items-center gap-3">
                <div class="p-2.5 rounded-2xl bg-white/10 backdrop-blur-md border border-white/15">
                    <img src="{{ asset('images/SEH.png') }}" alt="" class="h-7 w-auto object-contain brightness-0 invert" onerror="this.style.display='none'">
                </div>
                <span class="font-display text-xl font-bold tracking-tight">Smart EduHub</span>
            </div>

            <div class="relative max-w-lg">
                <h1 class="font-display text-4xl xl:text-5xl font-extrabold leading-[1.08] tracking-tight">
                    Semua urusan sekolah, dalam satu portal.
                </h1>
                <p class="mt-5 text-base text-orange-50/75 leading-relaxed max-w-md">
                    Masuk sebagai siswa atau wali murid dengan PIN, atau cukup scan ID card.
                </p>

                <ul class="mt-10 space-y-4">
                    <li class="flex items-center gap-4">
                        <span class="w-11 h-11 rounded-xl bg-white/10 border border-white/15 flex items-center justify-center shrink-0">
                            <span class="material-symbols-outlined text-orange-200" style="font-variation-settings:'FILL' 1;">qr_code_scanner</span>
                        </span>
                        <span class="text-sm font-semibold text-white/90">Scan ID card untuk masuk otomatis</span>
                    </li>
                    <li class="flex items-center gap-4">
                        <span class="w-11 h-11 rounded-xl bg-white/10 border border-white/15 flex items-center justify-center shrink-0">
                            <span class="material-symbols-outlined text-orange-200" style="font-variation-settings:'FILL' 1;">history</span>
                        </span>
                        <span class="text-sm font-semibold text-white/90">Akun tersimpan, masuk tanpa mengetik ulang</span>
                    </li>
                    <li class="flex items-center gap-4">
                        <span class="w-11 h-11 rounded-xl bg-white/10 border border-white/15 flex items-center justify-center shrink-0">
                            <span class="material-symbols-outlined text-orange-200" style="font-variation-settings:'FILL' 1;">lock</span>
                        </span>
                        <span class="text-sm font-semibold text-white/90">PIN 6 digit yang hanya Anda yang tahu</span>
                    </li>
                </ul>
            </div>

            <p class="relative text-xs text-white/45 font-medium">&copy; 2026 Smart EduHub. All rights reserved.</p>
        </aside>

        <!-- ===================== PANEL FORM ===================== -->
        <section class="relative flex flex-1 flex-col lg:items-center lg:justify-center lg:bg-white lg:px-8 lg:py-12">

            <!-- Hero (mobile & tablet) -->
            <header class="hero relative lg:hidden overflow-hidden px-6 pb-14 pt-[max(2.25rem,env(safe-area-inset-top))] text-white sm:pb-16">
                <div class="pointer-events-none absolute inset-0 grid-fade-mobile" aria-hidden="true"></div>
                <div class="pointer-events-none absolute -right-12 -top-12 h-44 w-44 rounded-full bg-white/10" aria-hidden="true"></div>
                <div class="pointer-events-none absolute -bottom-10 left-1/4 h-28 w-28 rounded-full bg-white/5" aria-hidden="true"></div>

                <div class="relative mx-auto max-w-md">
                    <div class="flex items-center justify-between gap-3">
                        <span class="rounded-xl border border-white/15 bg-white/10 p-2 backdrop-blur-md">
                            <img src="{{ asset('images/SEH.png') }}" alt="Smart EduHub" class="h-6 w-auto object-contain brightness-0 invert" onerror="this.style.display='none'">
                        </span>
                        <span class="inline-flex items-center gap-1.5 rounded-full border border-white/15 bg-white/10 px-3 py-1.5 text-xs font-semibold text-orange-50 backdrop-blur-md">
                            <span class="material-symbols-outlined text-sm text-amber-300" style="font-variation-settings:'FILL' 1;">school</span>
                            Siswa
                        </span>
                    </div>

                    <h1 class="hero-h1 mt-8 font-display text-[2rem] font-extrabold leading-[1.1] tracking-tight sm:text-4xl">
                        Selamat datang kembali
                    </h1>
                    <p class="hero-sub mt-2.5 max-w-xs text-sm leading-relaxed text-orange-50/80">
                        Masuk dengan PIN 6 digit, atau cukup scan ID card.
                    </p>
                </div>
            </header>

            <!-- Pembungkus sheet -->
            <div class="sheet-rise relative z-10 mx-auto -mt-7 flex w-full max-w-md flex-1 flex-col sm:mb-10 sm:flex-none lg:mt-0 lg:mb-0 lg:max-w-sm xl:max-w-md">

                <!-- CARD / SHEET -->
                <div id="formCard" class="sheet-card flex-1 rounded-t-[2rem] px-5 pb-[max(1.5rem,env(safe-area-inset-bottom))] pt-7 sm:flex-none sm:rounded-[2rem] sm:px-8 sm:pb-8 sm:pt-8 lg:rounded-none lg:p-0">

                    <!-- Intro (disembunyikan saat langkah PIN) -->
                    <div id="introBlock" class="intro-block mb-6">
                        <!-- mobile & tablet -->
                        <div class="lg:hidden">
                            <h2 class="font-display text-xl font-extrabold tracking-tight text-slate-900">Masuk ke akun Anda</h2>
                            <p class="mt-1 text-sm text-slate-500">Masukkan NISN atau username untuk melanjutkan.</p>
                        </div>
                        <!-- desktop -->
                        <div class="hidden lg:block">
                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-orange-50 text-orange-700 text-xs font-bold">
                                <span class="material-symbols-outlined text-sm" style="font-variation-settings:'FILL' 1;">school</span>
                                Portal Siswa
                            </span>
                            <h2 class="font-display text-3xl font-extrabold text-slate-900 tracking-tight mt-3 leading-tight">Selamat datang kembali</h2>
                            <p class="text-sm text-slate-500 mt-1.5">Masukkan NISN atau username untuk melanjutkan.</p>
                        </div>
                    </div>

                    <!-- Error -->
                    @php $hasSiswaError = $errors->any(); $oldUserVal = old('username'); @endphp
                    @if($hasSiswaError)
                    <div role="alert" class="mb-5 p-3.5 bg-rose-50 border border-rose-100 rounded-2xl text-rose-800 text-sm font-semibold flex items-start gap-3">
                        <span class="material-symbols-outlined text-rose-500 text-xl shrink-0">error</span>
                        <span class="leading-snug">{{ $errors->first() }}</span>
                    </div>
                    @endif

                    <!-- ============ AREA LOGIN ============ -->
                    <div id="siswaStandardArea">

                        <!-- FORM UTAMA -->
                        <form action="{{ route('siswa.login') }}" method="POST" id="loginForm" novalidate>
                            @csrf

                            <!-- STEP 1 -->
                            <div id="step1" class="space-y-4">
                                <div class="group">
                                    <label class="block text-sm font-bold text-slate-700 mb-2" for="username">NISN atau username</label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                            <span class="material-symbols-outlined text-slate-400 group-focus-within:text-orange-600 text-xl transition-colors">person</span>
                                        </div>
                                        <input class="block h-14 sm:h-auto w-full pl-12 pr-4 sm:py-3.5 bg-slate-50 border border-slate-200 rounded-2xl text-base sm:text-sm font-semibold text-slate-900 placeholder:text-slate-400 placeholder:font-medium focus:outline-none focus:ring-4 focus:ring-orange-500/15 focus:border-orange-500 focus:bg-white transition-all duration-200"
                                               id="username" name="username" placeholder="Contoh: 0012345678" type="text" required
                                               autocomplete="username" inputmode="text" autocapitalize="none" spellcheck="false"/>
                                    </div>
                                    <p id="usernameHint" class="hidden mt-2 text-xs font-semibold text-rose-600">Isi NISN atau username dulu.</p>
                                </div>

                                <button type="button" onclick="goToStep2()"
                                    class="w-full h-14 sm:h-auto flex justify-center items-center gap-2 sm:py-3.5 px-4 rounded-2xl text-white bg-gradient-to-r from-orange-600 to-amber-500 hover:brightness-110 font-bold text-sm focus:outline-none focus-visible:ring-4 focus-visible:ring-orange-500/30 active:scale-[0.98] transition-all duration-200 shadow-lg shadow-orange-500/30">
                                    Lanjutkan
                                    <span class="material-symbols-outlined text-xl">arrow_forward</span>
                                </button>
                            </div>

                            <!-- STEP 2: PIN -->
                            <div id="step2" class="step-hidden space-y-5">
                                <div class="flex items-center justify-between gap-3">
                                    <button type="button" onclick="goToStep1()" class="inline-flex items-center gap-1.5 text-slate-500 hover:text-orange-600 transition-colors group -ml-1 px-1 py-1 rounded-lg">
                                        <span class="material-symbols-outlined text-xl group-hover:-translate-x-0.5 transition-transform">arrow_back</span>
                                        <span class="text-sm font-bold">Ganti akun</span>
                                    </button>
                                    <span class="inline-flex items-center gap-1.5 max-w-[55%] px-3 py-1.5 rounded-full bg-slate-100 text-slate-700 text-xs font-bold">
                                        <span class="material-symbols-outlined text-sm shrink-0">person</span>
                                        <span id="pinUserLabel" class="truncate"></span>
                                    </span>
                                </div>

                                <div class="text-center">
                                    <h3 class="font-display text-2xl font-extrabold text-slate-900 tracking-tight">Masukkan PIN</h3>
                                    <p class="text-sm text-slate-500 mt-1">6 digit. Jangan dibagikan ke siapa pun.</p>
                                </div>

                                <div class="flex justify-center gap-2 sm:gap-2.5 py-1" id="pinContainer" role="group" aria-label="PIN 6 digit">
                                    <div class="pin-box" id="pinBox1"></div>
                                    <div class="pin-box" id="pinBox2"></div>
                                    <div class="pin-box" id="pinBox3"></div>
                                    <div class="pin-box" id="pinBox4"></div>
                                    <div class="pin-box" id="pinBox5"></div>
                                    <div class="pin-box" id="pinBox6"></div>
                                </div>

                                <p id="pinStatus" class="hidden text-center text-sm font-bold text-orange-700 items-center justify-center gap-2" role="status">
                                    <span class="material-symbols-outlined text-lg animate-spin">progress_activity</span>
                                    Memverifikasi PIN...
                                </p>

                                <input type="hidden" id="pin" name="pin" required maxlength="6" inputmode="numeric" pattern="[0-9]*"/>

                                <div id="keyboardContainer" class="w-full max-w-[17.5rem] mx-auto">
                                    <div class="grid grid-cols-3 gap-2.5 sm:gap-3">
                                        <button type="button" onclick="addPinDigit('1')" class="key-btn">1</button>
                                        <button type="button" onclick="addPinDigit('2')" class="key-btn">2</button>
                                        <button type="button" onclick="addPinDigit('3')" class="key-btn">3</button>
                                        <button type="button" onclick="addPinDigit('4')" class="key-btn">4</button>
                                        <button type="button" onclick="addPinDigit('5')" class="key-btn">5</button>
                                        <button type="button" onclick="addPinDigit('6')" class="key-btn">6</button>
                                        <button type="button" onclick="addPinDigit('7')" class="key-btn">7</button>
                                        <button type="button" onclick="addPinDigit('8')" class="key-btn">8</button>
                                        <button type="button" onclick="addPinDigit('9')" class="key-btn">9</button>
                                        <div></div>
                                        <button type="button" onclick="addPinDigit('0')" class="key-btn">0</button>
                                        <button type="button" onclick="removePinDigit()" class="key-ghost" aria-label="Hapus digit terakhir">
                                            <span class="material-symbols-outlined text-2xl">backspace</span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>

                        <!-- OPSI LAIN (hanya di step 1) -->
                        <div id="altOptions" class="mt-6">
                            <div class="flex items-center gap-3 mb-4" aria-hidden="true">
                                <div class="h-px flex-1 bg-slate-200"></div>
                                <span class="text-xs font-semibold text-slate-400">atau masuk lebih cepat</span>
                                <div class="h-px flex-1 bg-slate-200"></div>
                            </div>

                            <div id="altGrid" class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <!-- Scan ID card -->
                                <form action="{{ route('siswa.login') }}" method="POST" id="scanLoginForm" class="contents">
                                    @csrf
                                    <input id="scan_nis" name="scan_nis" type="hidden">
                                    <button type="button" id="openCameraButton"
                                        class="group flex sm:flex-col items-center sm:items-start gap-3.5 sm:gap-4 p-3.5 sm:p-4 rounded-2xl border border-orange-100 bg-gradient-to-br from-orange-50 to-amber-50 hover:border-orange-300 hover:shadow-md hover:shadow-orange-500/10 transition-all text-left active:scale-[0.98]">
                                        <span class="w-11 h-11 sm:w-10 sm:h-10 rounded-xl bg-orange-600 text-white flex items-center justify-center shrink-0 shadow-md shadow-orange-500/30">
                                            <span class="material-symbols-outlined">qr_code_scanner</span>
                                        </span>
                                        <span class="min-w-0 flex-1">
                                            <span class="block text-sm font-extrabold text-slate-900 leading-tight">Scan ID card</span>
                                            <span class="block text-xs text-slate-500 mt-0.5 leading-snug">Pakai kamera, masuk otomatis</span>
                                        </span>
                                        <span class="material-symbols-outlined text-orange-300 group-hover:text-orange-600 transition-colors sm:hidden">chevron_right</span>
                                    </button>
                                </form>

                                <!-- Quick login -->
                                <button type="button" id="siswaQuickTrigger"
                                    class="group flex sm:flex-col items-center sm:items-start gap-3.5 sm:gap-4 p-3.5 sm:p-4 rounded-2xl border border-slate-200 bg-slate-50 hover:bg-white hover:border-slate-300 hover:shadow-md hover:shadow-slate-900/5 transition-all text-left active:scale-[0.98]">
                                    <span class="w-11 h-11 sm:w-10 sm:h-10 rounded-xl bg-slate-900 text-white flex items-center justify-center shrink-0">
                                        <span class="material-symbols-outlined">history</span>
                                    </span>
                                    <span class="min-w-0 flex-1">
                                        <span class="block text-sm font-extrabold text-slate-900 leading-tight">Akun tersimpan</span>
                                        <span class="block text-xs text-slate-500 mt-0.5 leading-snug">Pilih akun, tanpa PIN</span>
                                    </span>
                                    <span class="material-symbols-outlined text-slate-400 group-hover:text-orange-600 transition-colors sm:hidden">chevron_right</span>
                                </button>
                            </div>

                            <p id="scanStatus" class="hidden mt-3 text-center text-xs font-bold text-orange-700" role="status">ID card terbaca, memproses login...</p>
                        </div>

                        <form id="siswaQuickSubmitForm" method="POST" style="display:none;">
                            @csrf
                        </form>
                    </div>

                    <!-- Bantuan -->
                    <footer class="mt-7 pt-5 border-t border-slate-100 text-center space-y-1.5">
                        <p class="text-sm text-slate-500 font-medium">
                            Lupa PIN? <a class="text-orange-600 hover:underline font-bold" href="{{ route('siswa.forgot-pin') }}">Reset PIN</a>
                        </p>
                        <p class="text-sm text-slate-500 font-medium">
                            Belum punya akun? <a class="text-orange-600 hover:underline font-bold" href="{{ route('registrations.form') }}">Daftar sekarang</a>
                        </p>
                    </footer>

                    <!-- Bantuan WhatsApp (mobile & tablet) -->
                    <div class="mt-5 flex justify-center lg:hidden">
                        <a href="https://wa.me/6282289635649" target="_blank" rel="noopener"
                           class="inline-flex items-center gap-2 rounded-full bg-green-50 px-4 py-2.5 text-xs font-bold text-green-700 ring-1 ring-inset ring-green-200 transition active:scale-95">
                            <svg class="h-4 w-4" fill="currentColor" aria-hidden="true"><use href="#icon-wa"/></svg>
                            Butuh bantuan? Chat admin
                        </a>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <!-- ============ QUICK LOGIN SHEET (disembunyikan; aktifkan via SHOW_SAVED_ACCOUNTS) ============ -->
    <div id="qlWrapper" class="hidden">
                    <div id="qlBackdrop" class="fixed inset-0 bg-slate-950/70 backdrop-blur-sm z-[105] hidden opacity-0 transition-opacity duration-300"></div>
                    <div id="qlSheet" role="dialog" aria-modal="true" aria-labelledby="qlTitle"
                         class="fixed bottom-0 left-0 right-0 mx-auto w-full max-w-xl bg-white rounded-t-[2rem] sm:rounded-b-[2rem] sm:bottom-6 shadow-2xl z-[110] translate-y-full sm:translate-y-[120%] transition-transform duration-300 ease-out max-h-[88dvh] overflow-y-auto">
                        <div class="p-5 sm:p-8 pb-[max(1.25rem,env(safe-area-inset-bottom))]">
                            <div class="w-12 h-1.5 bg-slate-200 rounded-full mx-auto mb-5 sm:hidden"></div>

                            <div class="flex items-start justify-between mb-5 gap-4">
                                <div class="flex items-center gap-3 min-w-0">
                                    <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-orange-600 to-amber-500 flex items-center justify-center shrink-0">
                                        <span class="material-symbols-outlined text-white text-xl">school</span>
                                    </div>
                                    <div class="min-w-0">
                                        <h3 id="qlTitle" class="font-display text-lg font-extrabold text-slate-900 tracking-tight">Akun tersimpan</h3>
                                        <p class="text-xs text-slate-500 font-medium mt-0.5">Pilih akun siswa dari riwayat login</p>
                                    </div>
                                </div>
                                <button id="qlSheetClose" type="button" aria-label="Tutup" class="p-2 text-slate-400 hover:text-slate-700 rounded-xl bg-slate-100 hover:bg-slate-200 transition-colors shrink-0">
                                    <span class="material-symbols-outlined text-xl">close</span>
                                </button>
                            </div>

                            <div class="relative mb-4">
                                <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 text-xl pointer-events-none">search</span>
                                <input id="qlSearch"
                                       class="w-full bg-slate-50 border border-slate-200 rounded-2xl py-3.5 pl-12 pr-4 text-base sm:text-sm font-medium focus:ring-4 focus:ring-orange-500/15 focus:border-orange-500 focus:bg-white outline-none transition-all placeholder:text-slate-400"
                                       placeholder="Cari nama atau NISN" type="search" autocomplete="off"/>
                            </div>

                            <div id="qlList"><div id="qlListInner" class="space-y-2"></div></div>

                            <div class="mt-6 pt-5 border-t border-slate-100 flex flex-col-reverse sm:flex-row gap-3">
                                <button type="button" id="qlClose2"
                                    class="flex-1 flex items-center justify-center gap-2 px-5 py-3.5 rounded-2xl border-2 border-slate-200 text-slate-700 hover:border-slate-300 hover:bg-slate-50 transition-colors font-bold text-sm">
                                    <span class="material-symbols-outlined text-lg">edit</span>
                                    Gunakan form login
                                </button>
                                <button type="button" id="qlClearAll"
                                    class="flex items-center justify-center gap-1.5 px-5 py-3.5 rounded-2xl bg-rose-50 text-rose-600 hover:bg-rose-100 text-sm font-bold transition-colors">
                                    <span class="material-symbols-outlined text-lg">delete_sweep</span>
                                    Hapus semua
                                </button>
                            </div>
                        </div>
                    </div>

    </div>

    <!-- ============ MODAL KAMERA ============ -->
    <div id="cameraModal" class="hidden fixed inset-0 z-[100] bg-slate-950/80 backdrop-blur-sm items-end sm:items-center justify-center p-0 sm:p-4" role="dialog" aria-modal="true" aria-labelledby="cameraTitle">
        <div class="w-full max-w-md bg-white rounded-t-[2rem] sm:rounded-3xl p-5 pb-[max(1.25rem,env(safe-area-inset-bottom))] shadow-2xl max-h-[92dvh] overflow-y-auto">
            <div class="flex items-start justify-between gap-3 mb-4">
                <div>
                    <h3 id="cameraTitle" class="font-display text-lg font-extrabold text-slate-900">Scan ID card</h3>
                    <p class="text-xs text-slate-500 mt-1">Arahkan barcode atau QR ke kotak kamera.</p>
                </div>
                <button type="button" id="closeCameraButton" aria-label="Tutup kamera" class="w-10 h-10 rounded-full bg-slate-100 text-slate-500 hover:bg-slate-200 flex items-center justify-center shrink-0 transition-colors">
                    <span class="material-symbols-outlined">close</span>
                </button>
            </div>
            <div id="cameraScanner" class="overflow-hidden rounded-2xl bg-slate-900"></div>
            <p id="cameraStatus" class="mt-3 text-center text-xs font-semibold text-slate-500" role="status">Menyiapkan kamera...</p>
        </div>
    </div>

    <!-- WhatsApp (desktop; di mobile sudah ada tombol bantuan di dalam sheet) -->
    <a href="https://wa.me/6282289635649" target="_blank" rel="noopener" aria-label="Hubungi lewat WhatsApp"
       class="fixed bottom-6 right-6 z-50 hidden h-14 w-14 items-center justify-center rounded-full bg-green-500 shadow-xl shadow-green-500/30 transition-all hover:scale-110 hover:bg-green-600 active:scale-95 lg:flex">
        <svg class="h-7 w-7 text-white" fill="currentColor" aria-hidden="true"><use href="#icon-wa"/></svg>
    </a>

    <!-- ===================== JAVASCRIPT ===================== -->
    <script>
        let currentPin = '';
        let isSubmittingPin = false;

        const $ = (id) => document.getElementById(id);

        const scanInput = $('scan_nis');
        const scanForm = $('scanLoginForm');
        const scanStatus = $('scanStatus');
        const openCameraButton = $('openCameraButton');
        const closeCameraButton = $('closeCameraButton');
        const cameraModal = $('cameraModal');
        const cameraStatus = $('cameraStatus');
        let html5QrCode = null;
        let isSubmittingScan = false;

        /* ---------- Kamera ---------- */
        async function stopCamera() {
            if (html5QrCode) {
                try {
                    await html5QrCode.stop();
                    await html5QrCode.clear();
                } catch (error) {
                    console.warn('Kamera scanner belum aktif.', error);
                }
                html5QrCode = null;
            }
            cameraModal.classList.add('hidden');
            cameraModal.classList.remove('flex');
            document.body.style.overflow = '';
        }

        async function startCamera() {
            if (!window.Html5Qrcode) {
                cameraModal.classList.remove('hidden');
                cameraModal.classList.add('flex');
                cameraStatus.textContent = 'Library kamera belum tersedia. Periksa koneksi internet.';
                return;
            }
            cameraModal.classList.remove('hidden');
            cameraModal.classList.add('flex');
            document.body.style.overflow = 'hidden';
            cameraStatus.textContent = 'Meminta izin kamera...';
            html5QrCode = new Html5Qrcode('cameraScanner');
            try {
                await html5QrCode.start(
                    { facingMode: 'environment' },
                    {
                        fps: 10,
                        qrbox: (w, h) => {
                            const s = Math.floor(Math.min(w, h) * 0.75);
                            return { width: s, height: Math.floor(s * 0.7) };
                        }
                    },
                    function (decodedText) {
                        if (isSubmittingScan) return;
                        isSubmittingScan = true;
                        scanInput.value = decodedText.trim();
                        scanStatus.classList.remove('hidden');
                        stopCamera().finally(() => scanForm.submit());
                    },
                    function () {}
                );
                cameraStatus.textContent = 'Kamera aktif. Arahkan ID card ke kotak scan.';
            } catch (error) {
                cameraStatus.textContent = 'Kamera tidak dapat dibuka. Pastikan izin kamera diberikan.';
                console.error('Gagal membuka kamera:', error);
            }
        }

        openCameraButton?.addEventListener('click', startCamera);
        closeCameraButton?.addEventListener('click', stopCamera);
        cameraModal?.addEventListener('click', (e) => { if (e.target === cameraModal) stopCamera(); });

        if (scanInput && scanForm) {
            scanInput.addEventListener('keydown', function (event) {
                if (event.key === 'Enter' && scanInput.value.trim() !== '') {
                    event.preventDefault();
                    scanStatus.classList.remove('hidden');
                    scanForm.submit();
                }
            });
        }

        /* ---------- Step login ---------- */
        const usernameInput = $('username');
        const usernameHint = $('usernameHint');

        function showStep2Layout(on) {
            $('step1').classList.toggle('step-hidden', on);
            $('step2').classList.toggle('step-hidden', !on);
            $('altOptions').classList.toggle('step-hidden', on);
            $('introBlock').classList.toggle('step-hidden', on);
            if (on) $('step2').classList.add('animate-step');
            else $('step1').classList.add('animate-step');
        }

        function goToStep2() {
            const value = usernameInput.value.trim();
            if (value === '') {
                usernameInput.focus();
                usernameInput.classList.add('border-rose-500', 'ring-4', 'ring-rose-500/10');
                usernameHint.classList.remove('hidden');
                return;
            }
            usernameInput.classList.remove('border-rose-500', 'ring-4', 'ring-rose-500/10');
            usernameHint.classList.add('hidden');
            $('pinUserLabel').textContent = value;
            showStep2Layout(true);
            updatePinDots();
        }

        function goToStep1() {
            showStep2Layout(false);
            currentPin = '';
            isSubmittingPin = false;
            $('pin').value = '';
            $('pinStatus').classList.add('hidden');
            $('pinStatus').classList.remove('flex');
            updatePinDots();
            usernameInput.focus();
        }

        usernameInput.addEventListener('input', () => {
            usernameInput.classList.remove('border-rose-500', 'ring-4', 'ring-rose-500/10');
            usernameHint.classList.add('hidden');
        });
        usernameInput.addEventListener('keydown', (e) => {
            if (e.key === 'Enter') { e.preventDefault(); goToStep2(); }
        });

        /* ---------- PIN ---------- */
        function submitPin() {
            if (isSubmittingPin) return;
            isSubmittingPin = true;
            $('pinStatus').classList.remove('hidden');
            $('pinStatus').classList.add('flex');
            $('loginForm').submit();
        }

        function addPinDigit(digit) {
            if (isSubmittingPin) return;
            if (currentPin.length < 6) {
                currentPin += digit;
                $('pin').value = currentPin;
                updatePinDots();
                if (currentPin.length === 6) setTimeout(submitPin, 250);
            }
        }

        function removePinDigit() {
            if (isSubmittingPin) return;
            if (currentPin.length > 0) {
                currentPin = currentPin.slice(0, -1);
                $('pin').value = currentPin;
                updatePinDots();
            }
        }

        function updatePinDots() {
            for (let i = 1; i <= 6; i++) {
                const box = $(`pinBox${i}`);
                box.className = 'pin-box';
                if (i <= currentPin.length) {
                    box.classList.add('is-filled');
                    box.innerHTML = '<span class="pin-dot"></span>';
                } else {
                    box.innerHTML = '';
                    if (i === currentPin.length + 1) box.classList.add('is-active');
                }
            }
        }

        const sheetIsOpen = () => !$('qlSheet').classList.contains('translate-y-full') && !$('qlBackdrop').classList.contains('hidden');

        document.addEventListener('keydown', function (event) {
            if (event.ctrlKey || event.metaKey || event.altKey) return;
            const isStep2Visible = !$('step2').classList.contains('step-hidden');
            if (!isStep2Visible || sheetIsOpen()) return;

            if (event.key >= '0' && event.key <= '9') {
                addPinDigit(event.key);
            } else if (event.key === 'Backspace') {
                removePinDigit();
            } else if (event.key === 'Enter') {
                if (currentPin.length === 6) submitPin();
            }
        });

        /* ---------- Quick login siswa ---------- */
        const GLOBAL_HISTORY_KEY = 'smart_acp_login_history';
        const SISWA_QUICK_URL   = @json(route('quick-login.student', ['student' => '__STUDENT_ID__']));
        const SISWA_CSRF_TOKEN  = @json(csrf_token());
        const siswaHasError = @json($hasSiswaError ?? false);
        const siswaOldUser = @json($oldUserVal ?? null);

        const qlBackdrop  = $('qlBackdrop');
        const qlSheet     = $('qlSheet');
        const qlTrigger   = $('siswaQuickTrigger');
        const qlCloseBtn  = $('qlSheetClose');
        const qlCloseBtn2 = $('qlClose2');
        const qlSearch    = $('qlSearch');
        const qlListInner = $('qlListInner');

        /* Ubah ke true untuk menampilkan kembali fitur "Akun tersimpan" */
        const SHOW_SAVED_ACCOUNTS = true;

        $('qlWrapper')?.classList.toggle('hidden', !SHOW_SAVED_ACCOUNTS);

        if (!SHOW_SAVED_ACCOUNTS) {
            qlTrigger?.classList.add('step-hidden');
            $('altGrid')?.classList.remove('sm:grid-cols-2');
            // Tombol scan jadi satu baris penuh saat sendirian
            openCameraButton?.classList.remove('sm:flex-col', 'sm:items-start', 'sm:gap-4');
            const divider = document.querySelector('#altOptions span.text-slate-400');
            if (divider) divider.textContent = 'atau masuk dengan';
        }

        function escapeHtml(str) {
            return String(str ?? '').replace(/[&<>"']/g, (c) => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]));
        }

        function getRecentSiswa() {
            try {
                const raw = localStorage.getItem(GLOBAL_HISTORY_KEY);
                const all = raw ? JSON.parse(raw) : [];
                return all.filter(u => u && u.type === 'student');
            } catch { return []; }
        }

        function renderList(filter = '') {
            let users = getRecentSiswa();
            const f = filter.trim().toLowerCase();
            if (f) {
                users = users.filter(u => {
                    const nisn = u.secondary || '';
                    const kelas = u.extra && u.extra.kelas ? u.extra.kelas : '';
                    const username = u.extra && u.extra.username ? u.extra.username : '';
                    return (u.name || '').toLowerCase().includes(f) ||
                           String(nisn).toLowerCase().includes(f) ||
                           String(username).toLowerCase().includes(f) ||
                           String(kelas).toLowerCase().includes(f);
                });
            }

            if (!users.length) {
                qlListInner.innerHTML = `
                    <div class="py-10 flex flex-col items-center text-center">
                        <div class="w-16 h-16 rounded-full bg-slate-100 flex items-center justify-center mb-4">
                            <span class="material-symbols-outlined text-3xl text-slate-300">person_off</span>
                        </div>
                        <p class="text-sm font-extrabold text-slate-600 mb-1">${filter ? 'Tidak ada akun yang cocok' : 'Belum ada riwayat login'}</p>
                        <p class="text-xs text-slate-400 max-w-xs leading-relaxed">${filter ? 'Coba kata kunci lain.' : 'Login sekali dengan form, lalu akun akan tersimpan di sini.'}</p>
                    </div>`;
                return;
            }

            qlListInner.innerHTML = users.map(u => {
                const name = escapeHtml(u.name || 'Siswa');
                const initials = escapeHtml(((u.name || '?').match(/\b\w/g) || []).slice(0, 2).join('').toUpperCase() || 'S');
                const nisn = escapeHtml(u.secondary || '');
                const kelas = escapeHtml(u.extra && u.extra.kelas ? u.extra.kelas : '');
                const uname = escapeHtml(u.extra && u.extra.username ? u.extra.username : (u.secondary || '—'));
                return `
                <div class="flex items-center gap-2">
                    <form method="POST" action="${SISWA_QUICK_URL.replace('__STUDENT_ID__', encodeURIComponent(u.id))}" class="flex-1 min-w-0">
                        <input type="hidden" name="_token" value="${SISWA_CSRF_TOKEN}">
                        <button type="submit"
                            class="w-full flex items-center gap-3 sm:gap-4 p-3 sm:p-4 rounded-2xl border border-slate-100 bg-white hover:bg-orange-50/50 hover:border-orange-200 transition-all group text-left active:scale-[0.99]">
                            <div class="w-11 h-11 sm:w-12 sm:h-12 rounded-2xl bg-gradient-to-br from-orange-600 to-amber-500 flex items-center justify-center text-white font-extrabold text-sm shrink-0 shadow-sm">
                                ${initials}
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-bold text-slate-900 truncate">${name}</p>
                                <div class="flex items-center gap-1.5 flex-wrap mt-1">
                                    ${nisn ? `<span class="text-[11px] font-bold px-2 py-0.5 rounded-full border bg-orange-50 text-orange-700 border-orange-200">NISN ${nisn}</span>` : ''}
                                    ${kelas ? `<span class="text-[11px] font-bold px-2 py-0.5 rounded-full border bg-slate-100 text-slate-700 border-slate-200">${kelas}</span>` : ''}
                                </div>
                                <p class="text-xs text-slate-400 truncate mt-1">${uname}</p>
                            </div>
                            <div class="w-9 h-9 rounded-xl bg-slate-50 text-slate-400 flex items-center justify-center group-hover:bg-orange-600 group-hover:text-white transition-all shrink-0">
                                <span class="material-symbols-outlined text-lg">arrow_forward</span>
                            </div>
                        </button>
                    </form>
                    <button type="button" onclick="removeAccount(${JSON.stringify(String(u.id)).replace(/"/g, '&quot;')})"
                        class="shrink-0 w-10 h-10 rounded-2xl bg-slate-100 hover:bg-rose-100 text-slate-400 hover:text-rose-500 flex items-center justify-center transition-colors"
                        aria-label="Hapus dari riwayat" title="Hapus dari riwayat">
                        <span class="material-symbols-outlined text-lg">delete</span>
                    </button>
                </div>`;
            }).join('');
        }

        function removeAccount(id) {
            try {
                const all = JSON.parse(localStorage.getItem(GLOBAL_HISTORY_KEY) || '[]');
                const filtered = all.filter(u => !u || u.type !== 'student' || String(u.id) !== String(id));
                localStorage.setItem(GLOBAL_HISTORY_KEY, JSON.stringify(filtered));
                renderList(qlSearch?.value || '');
            } catch (e) {}
        }
        window.removeAccount = removeAccount;

        function clearAllSiswa() {
            try {
                const all = JSON.parse(localStorage.getItem(GLOBAL_HISTORY_KEY) || '[]');
                const keep = all.filter(u => u && u.type !== 'student');
                localStorage.setItem(GLOBAL_HISTORY_KEY, JSON.stringify(keep));
                renderList('');
            } catch (e) {}
        }

        function openSheet() {
            if (!SHOW_SAVED_ACCOUNTS) return;
            qlBackdrop.classList.remove('hidden');
            requestAnimationFrame(() => qlBackdrop.classList.remove('opacity-0'));
            qlSheet.classList.remove('translate-y-full', 'sm:translate-y-[120%]');
            renderList('');
            setTimeout(() => qlSearch?.focus({ preventScroll: true }), 350);
            document.body.style.overflow = 'hidden';
        }
        function closeSheet() {
            qlBackdrop.classList.add('opacity-0');
            qlSheet.classList.add('translate-y-full', 'sm:translate-y-[120%]');
            setTimeout(() => qlBackdrop.classList.add('hidden'), 300);
            document.body.style.overflow = '';
        }

        qlTrigger?.addEventListener('click', openSheet);
        qlCloseBtn?.addEventListener('click', closeSheet);
        qlCloseBtn2?.addEventListener('click', closeSheet);
        qlBackdrop?.addEventListener('click', closeSheet);
        qlSearch?.addEventListener('input', e => renderList(e.target.value));
        $('qlClearAll')?.addEventListener('click', clearAllSiswa);
        document.addEventListener('keydown', e => {
            if (e.key === 'Escape') {
                closeSheet();
                if (!cameraModal.classList.contains('hidden')) stopCamera();
            }
        });

        if (siswaOldUser) usernameInput.value = siswaOldUser;
        if (siswaHasError) usernameInput.focus();
        updatePinDots();
    </script>
</body>
</html>