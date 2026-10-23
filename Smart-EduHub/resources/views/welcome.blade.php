<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="theme-color" content="#1a0706">
    <meta name="description" content="Smart EduHub — dompet digital & kartu pintar untuk siswa SMKS Muhammadiyah 1 Pringsewu.">
    <title>Smart EduHub</title>
    <link rel="icon" type="image/png" href="{{ asset('images/SEHmob.png') }}">

    <link rel="preconnect" href="https://cdn.tailwindcss.com">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="preconnect" href="https://cdnjs.cloudflare.com">
    <link rel="preconnect" href="https://i.ytimg.com">

    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['"Plus Jakarta Sans"', 'system-ui', 'sans-serif'] },
                    colors: {
                        maroon: { 50:'#fdf3f2',100:'#fbe5e3',200:'#f7cdc9',300:'#f0a8a1',400:'#e67a70',500:'#d6513f',600:'#b93a2b',700:'#992e22',800:'#7e2a21',900:'#61180f',950:'#1a0706' },
                        brand: { 400:'#fb923c', 500:'#f97316', 600:'#ea580c', 700:'#c2410c' }
                    },
                    screens: { xs: '400px' }
                }
            }
        }
    </script>

    <link rel="preload" as="style" href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" media="print" onload="this.media='all'">
    <noscript><link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap"></noscript>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" media="print" onload="this.media='all'">
    <noscript><link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"></noscript>

    <style>
        html, body { overflow-x: hidden; max-width: 100%; }
        img, video, iframe { max-width: 100%; }
        body { font-family: 'Plus Jakarta Sans', system-ui, sans-serif; background:#fff; color:#0f172a; -webkit-text-size-adjust:100%; }
        section[id] { scroll-margin-top: 5.5rem; }
        @media (max-width: 1023px) {
            section[id] { scroll-margin-top: 1rem; }
        }
        .cv-auto { content-visibility: auto; contain-intrinsic-size: auto 900px; }

        /* Tipografi fluid */
        .h-display { font-size: clamp(2.5rem, 1.1rem + 6vw, 5.5rem); line-height: 1; letter-spacing: -0.04em; }
        .h-section { font-size: clamp(1.75rem, 1.1rem + 2.8vw, 3.25rem); line-height: 1.1; letter-spacing: -0.025em; }

        ::-webkit-scrollbar { width: 8px; height: 8px; }
        ::-webkit-scrollbar-track { background:#f1f5f9; }
        ::-webkit-scrollbar-thumb { background: linear-gradient(180deg,#f97316,#992e22); border-radius:10px; }

        .text-grad { background: linear-gradient(110deg,#fdba74 0%,#f97316 45%,#ef4444 100%); -webkit-background-clip:text; background-clip:text; -webkit-text-fill-color:transparent; }
        .text-grad-dark { background: linear-gradient(110deg,#ea580c,#dc2626); -webkit-background-clip:text; background-clip:text; -webkit-text-fill-color:transparent; }

        /* Hero mesh */
        .mesh {
            background:
                radial-gradient(60% 50% at 15% 10%, rgba(249,115,22,.30) 0, transparent 60%),
                radial-gradient(50% 45% at 90% 20%, rgba(220,38,38,.28) 0, transparent 60%),
                radial-gradient(55% 50% at 60% 100%, rgba(234,179,8,.16) 0, transparent 60%),
                #1a0706;
        }
        .grid-lines {
            background-image: linear-gradient(rgba(255,255,255,.04) 1px, transparent 1px), linear-gradient(90deg, rgba(255,255,255,.04) 1px, transparent 1px);
            background-size: 44px 44px;
            mask-image: radial-gradient(ellipse at center, #000 30%, transparent 75%);
            -webkit-mask-image: radial-gradient(ellipse at center, #000 30%, transparent 75%);
        }
        .glass { background: rgba(255,255,255,.06); border: 1px solid rgba(255,255,255,.12); backdrop-filter: blur(16px); -webkit-backdrop-filter: blur(16px); }

        @keyframes float { 0%,100% { transform: translateY(0);} 50% { transform: translateY(-14px);} }
        @keyframes floatSm { 0%,100% { transform: translateY(0);} 50% { transform: translateY(-8px);} }
        .float { animation: float 6s ease-in-out infinite; }
        .float-sm { animation: floatSm 4.5s ease-in-out infinite; }
        .float-sm-d { animation: floatSm 4.5s ease-in-out 1.2s infinite; }
        @keyframes marquee { to { transform: translateX(-50%); } }
        .animate-marquee { animation: marquee 32s linear infinite; }
        .marquee-mask { mask-image: linear-gradient(to right, transparent, #000 12%, #000 88%, transparent); -webkit-mask-image: linear-gradient(to right, transparent, #000 12%, #000 88%, transparent); }

        /* Reveal */
        .reveal { opacity:0; transform: translateY(24px); transition: opacity .7s cubic-bezier(.22,1,.36,1), transform .7s cubic-bezier(.22,1,.36,1); }
        .reveal.active { opacity:1; transform:none; }

        .card-hover { transition: transform .35s cubic-bezier(.4,0,.2,1), box-shadow .35s, border-color .35s; }
        @media (hover:hover) { .card-hover:hover { transform: translateY(-8px); box-shadow: 0 28px 50px -18px rgba(234,88,12,.28); border-color: rgba(251,146,60,.5); } }

        .btn-shine { position: relative; overflow: hidden; isolation: isolate; }
        .btn-shine::after { content:''; position:absolute; inset:-50%; background: linear-gradient(100deg, transparent 40%, rgba(255,255,255,.45) 50%, transparent 60%); transform: translateX(-100%) rotate(0deg); transition: transform .7s; z-index:-1; }
        .btn-shine:hover::after { transform: translateX(100%); }

        /* Kartu kredit */
        .credit-card { background: linear-gradient(135deg,#c2410c 0%,#ea580c 35%,#dc2626 75%,#991b1b 100%); border: 1px solid rgba(255,255,255,.2); }
        .card-3d { transform-style: preserve-3d; transition: transform .3s cubic-bezier(.23,1,.32,1); box-shadow: 0 30px 60px -15px rgba(234,88,12,.5); }
        .persp { perspective: 1400px; }
        .chip-gold { background: linear-gradient(135deg,#fde68a,#f59e0b 50%,#d97706); box-shadow: inset 0 0 0 1px rgba(255,255,255,.35); position: relative; }
        .chip-gold::before { content:''; position:absolute; inset:5px; border-radius:4px; border:1px solid rgba(120,53,15,.35); }
        @keyframes shimmer { 0% { left:-160%; } 55%,100% { left:160%; } }
        .shimmer { position:absolute; top:0; left:-160%; width:55%; height:100%; background: linear-gradient(100deg, transparent 10%, rgba(255,255,255,.35) 50%, transparent 90%); transform: skewX(-18deg); animation: shimmer 4.8s ease-in-out infinite; }

        .tilt { transform-style: preserve-3d; transition: transform .2s ease-out; will-change: transform; }
        .scene { perspective: 1200px; }

        /* Aurora & grain */
        @keyframes aurora { 0%,100% { transform: translate3d(0,0,0) scale(1);} 33% { transform: translate3d(6%,-8%,0) scale(1.12);} 66% { transform: translate3d(-6%,6%,0) scale(.95);} }
        .aurora { animation: aurora 18s ease-in-out infinite; will-change: transform; }
        .grain::after { content:''; position:absolute; inset:0; pointer-events:none; opacity:.07; mix-blend-mode: overlay;
            background-image: url("data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' width='160' height='160'><filter id='n'><feTurbulence type='fractalNoise' baseFrequency='.9' numOctaves='2' stitchTiles='stitch'/></filter><rect width='100%' height='100%' filter='url(%23n)'/></svg>"); }

        /* Border gradasi */
        .gborder { position:relative; background: #fff; border-radius: 1.5rem; }
        .gborder::before { content:''; position:absolute; inset:0; padding:1px; border-radius:inherit; pointer-events:none;
            background: linear-gradient(135deg, rgba(249,115,22,.55), rgba(226,232,240,.5) 40%, rgba(220,38,38,.45));
            -webkit-mask: linear-gradient(#000 0 0) content-box, linear-gradient(#000 0 0); -webkit-mask-composite: xor; mask-composite: exclude; }

        /* Garis bawah tombol nav aktif */
        .glow-btn { box-shadow: 0 0 0 1px rgba(255,255,255,.15) inset, 0 10px 30px -8px rgba(249,115,22,.6); }

        /* Drawer */
        #drawer { transition: transform .35s cubic-bezier(.22,1,.36,1), opacity .25s; }

        @media (prefers-reduced-motion: reduce) {
            *, *::before, *::after { animation-duration:.01ms !important; animation-iteration-count:1 !important; transition-duration:.01ms !important; }
            .reveal { opacity:1; transform:none; }
        }
    </style>
</head>
<body class="antialiased selection:bg-orange-500 selection:text-white">

    <!-- ====== NAVBAR (floating pill) — hanya tampil di desktop ====== -->
    <div id="progress" class="fixed top-0 left-0 h-[3px] w-0 z-[70] bg-gradient-to-r from-amber-400 via-orange-500 to-red-500"></div>
    <header class="hidden lg:block fixed inset-x-0 top-0 z-50 px-6 pt-4" id="navbar">
        <div class="max-w-7xl mx-auto flex items-center justify-between gap-3 rounded-full px-5 py-2.5 bg-maroon-950/70 backdrop-blur-xl border border-white/10 shadow-xl shadow-black/20 transition-all duration-300" id="nav-pill">
            <a href="#home" class="flex items-center gap-2 min-w-0" aria-label="Smart EduHub">
                <img src="{{ asset('images/SEH.png') }}" alt="Smart EduHub" class="h-9 xl:h-10 w-auto max-w-[45vw] object-contain" style="filter:brightness(0) invert(1)" fetchpriority="high">
            </a>

            <nav class="hidden lg:flex items-center gap-1 text-sm font-semibold" id="nav-links" aria-label="Navigasi utama">
                <a href="#home" class="nav-a px-3.5 py-2 rounded-full text-white/70 hover:text-white hover:bg-white/10 transition">Beranda</a>
                <a href="#nama-tim" class="nav-a px-3.5 py-2 rounded-full text-white/70 hover:text-white hover:bg-white/10 transition">Tim</a>
                <a href="#creative-bank" class="nav-a px-3.5 py-2 rounded-full text-white/70 hover:text-white hover:bg-white/10 transition">Smart Bank</a>
                <a href="#download" class="nav-a px-3.5 py-2 rounded-full text-white/70 hover:text-white hover:bg-white/10 transition">Aplikasi</a>
                <a href="#prestasi" class="nav-a px-3.5 py-2 rounded-full text-white/70 hover:text-white hover:bg-white/10 transition">Prestasi</a>
                <a href="#youtube" class="nav-a px-3.5 py-2 rounded-full text-white/70 hover:text-white hover:bg-white/10 transition">Video</a>
            </nav>

            <div class="flex items-center gap-2">
                <a href="{{ route('registrations.check') }}" class="hidden md:inline-flex items-center gap-2 px-4 py-2 rounded-full text-sm font-bold text-white/80 hover:text-white hover:bg-white/10 transition">
                    <i class="fa-solid fa-magnifying-glass text-xs"></i> Verifikasi
                </a>
                <a href="{{ route('registrations.form') }}" class="btn-shine inline-flex items-center px-5 py-2.5 rounded-full bg-gradient-to-r from-orange-500 to-red-600 text-white text-sm font-bold shadow-lg shadow-orange-600/30 hover:shadow-orange-600/50 transition">Join Us</a>
                <button type="button" id="menu-btn" class="lg:hidden w-10 h-10 rounded-full bg-white/10 text-white flex items-center justify-center" aria-label="Buka menu" aria-expanded="false" aria-controls="drawer">
                    <i class="fa-solid fa-bars" id="menu-icon"></i>
                </button>
            </div>
        </div>

        <!-- Drawer lama (tidak dipakai di mobile; dipertahankan agar script tetap aman) -->
        <div id="drawer" class="lg:hidden max-w-7xl mx-auto mt-2 rounded-3xl bg-maroon-950/95 backdrop-blur-xl border border-white/10 shadow-2xl overflow-hidden opacity-0 -translate-y-3 pointer-events-none">
            <div class="p-3 grid grid-cols-2 gap-2 text-sm font-bold text-white/90">
                <a href="#home" class="drawer-a flex items-center gap-2.5 px-4 py-3 rounded-2xl bg-white/5 hover:bg-white/10"><i class="fa-solid fa-house text-orange-400 w-4"></i>Beranda</a>
                <a href="#nama-tim" class="drawer-a flex items-center gap-2.5 px-4 py-3 rounded-2xl bg-white/5 hover:bg-white/10"><i class="fa-solid fa-users text-orange-400 w-4"></i>Tim</a>
                <a href="#creative-bank" class="drawer-a flex items-center gap-2.5 px-4 py-3 rounded-2xl bg-white/5 hover:bg-white/10"><i class="fa-solid fa-credit-card text-orange-400 w-4"></i>Smart Bank</a>
                <a href="#download" class="drawer-a flex items-center gap-2.5 px-4 py-3 rounded-2xl bg-white/5 hover:bg-white/10"><i class="fa-brands fa-android text-orange-400 w-4"></i>Aplikasi</a>
                <a href="#prestasi" class="drawer-a flex items-center gap-2.5 px-4 py-3 rounded-2xl bg-white/5 hover:bg-white/10"><i class="fa-solid fa-trophy text-orange-400 w-4"></i>Prestasi</a>
                <a href="#youtube" class="drawer-a flex items-center gap-2.5 px-4 py-3 rounded-2xl bg-white/5 hover:bg-white/10"><i class="fa-brands fa-youtube text-orange-400 w-4"></i>Video</a>
                <a href="{{ route('registrations.check') }}" class="col-span-2 flex items-center justify-center gap-2.5 px-4 py-3 rounded-2xl border border-white/15 hover:bg-white/10"><i class="fa-solid fa-magnifying-glass text-orange-400"></i>Verifikasi Pendaftaran</a>
            </div>
        </div>
    </header>

    <main>
    <!-- ====== HERO ====== -->
    <section id="home" class="grain relative overflow-hidden mesh pt-16 sm:pt-20 lg:pt-40 pb-20 sm:pb-28 lg:pb-32 min-h-[100svh] flex items-center">
        <div class="absolute inset-0 grid-lines pointer-events-none"></div>
        <div class="aurora absolute -top-24 -left-24 w-72 h-72 sm:w-[32rem] sm:h-[32rem] rounded-full bg-orange-600/30 blur-[120px] pointer-events-none"></div>
        <div class="aurora absolute bottom-0 right-0 w-64 h-64 sm:w-[28rem] sm:h-[28rem] rounded-full bg-red-600/30 blur-[120px] pointer-events-none" style="animation-delay:-6s;"></div>
        <div class="aurora absolute top-1/3 left-1/3 w-56 h-56 sm:w-80 sm:h-80 rounded-full bg-amber-400/15 blur-[120px] pointer-events-none" style="animation-delay:-12s;"></div>
        <div class="absolute inset-x-0 bottom-0 h-32 bg-gradient-to-b from-transparent to-white/0 pointer-events-none"></div>

        <div class="relative z-10 w-full max-w-7xl mx-auto px-4 sm:px-6">
            <div class="grid lg:grid-cols-12 gap-12 lg:gap-8 items-center">

                <div class="lg:col-span-7 text-center lg:text-left">
                    <div class="reveal inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full glass text-orange-300 text-xs sm:text-sm font-bold mb-6">
                        <span class="relative flex h-2 w-2"><span class="absolute inline-flex h-full w-full rounded-full bg-orange-400 opacity-75 animate-ping"></span><span class="relative inline-flex h-2 w-2 rounded-full bg-orange-500"></span></span>
                        Digital Creative Hub
                    </div>

                    <h1 class="h-display font-extrabold text-white mb-5 sm:mb-6 reveal" style="transition-delay:80ms;">
                        Visual Mastery <span class="text-white/40">&amp;</span><br><span class="text-grad">Viral Strategy.</span>
                    </h1>

                    <p class="max-w-xl mx-auto lg:mx-0 text-white/65 text-base sm:text-lg leading-relaxed mb-8 sm:mb-10 reveal" style="transition-delay:160ms;">
                        Menggabungkan estetika desain visual kelas dunia dengan strategi konten viral untuk membangun pengaruh digital yang tak tertandingi.
                    </p>

                    <div class="flex flex-col sm:flex-row gap-3 sm:gap-4 justify-center lg:justify-start max-w-sm sm:max-w-none mx-auto lg:mx-0 reveal" style="transition-delay:240ms;">
                        <a href="#prestasi" class="btn-shine inline-flex items-center justify-center gap-2 px-7 py-3.5 rounded-2xl bg-gradient-to-r from-orange-500 to-red-600 text-white font-bold shadow-xl shadow-orange-600/30 hover:-translate-y-0.5 transition">Lihat Karya <i class="fa-solid fa-arrow-right text-sm"></i></a>
                        <a href="#download" class="inline-flex items-center justify-center gap-2 px-7 py-3.5 rounded-2xl glass text-white font-bold hover:bg-white/15 transition"><i class="fa-solid fa-download text-sm"></i> Unduh Aplikasi</a>
                    </div>

                </div>

                <!-- Foto: tampil di semua ukuran layar -->
                <div class="lg:col-span-5 reveal" style="transition-delay:200ms;">
                    <div id="hero-photo-scene" class="scene w-full max-w-[300px] xs:max-w-[340px] sm:max-w-[400px] lg:max-w-[440px] mx-auto">
                        <div id="hero-photo-tilt" class="tilt">
                            <div class="float relative">
                                <div class="absolute -inset-10 bg-gradient-to-tr from-orange-500/30 via-red-500/20 to-amber-400/25 blur-3xl rounded-full pointer-events-none"></div>
                                <div class="relative rounded-[2rem] sm:rounded-[2.5rem] overflow-hidden border border-white/15 bg-maroon-950 shadow-2xl shadow-orange-900/40">
                                    <img src="{{ asset('images/krisnaAI2.png') }}" alt="Siswa Axioo Creative" class="w-full aspect-[4/5] object-cover object-top" fetchpriority="high">
                                    <div class="absolute inset-0 bg-gradient-to-t from-maroon-950/50 via-transparent to-white/5 pointer-events-none"></div>
                                </div>

                                <div class="absolute -left-3 sm:-left-8 top-16 sm:top-24 bg-white rounded-2xl shadow-2xl px-3 py-2.5 sm:px-4 sm:py-3 flex items-center gap-2.5" style="transform: translateZ(70px);">
                                    <div class="w-8 h-8 sm:w-9 sm:h-9 bg-orange-100 rounded-xl flex items-center justify-center text-orange-500"><i class="fa-solid fa-award"></i></div>
                                    <div>
                                        <p class="text-[8px] sm:text-[9px] font-bold text-slate-400 uppercase tracking-widest leading-none">Lomba</p>
                                        <p class="text-xs sm:text-sm font-extrabold text-slate-900 leading-none mt-1">Directorat SMK</p>
                                    </div>
                                </div>
                                <div class="absolute -right-3 sm:-right-8 bottom-20 sm:bottom-28 bg-white rounded-2xl shadow-2xl px-3 py-2.5 sm:px-4 sm:py-3 flex items-center gap-2.5" style="transform: translateZ(90px);">
                                    <div class="w-8 h-8 sm:w-9 sm:h-9 bg-red-100 rounded-xl flex items-center justify-center text-red-600"><i class="fa-solid fa-user-group"></i></div>
                                    <div>
                                        <p class="text-[8px] sm:text-[9px] font-bold text-slate-400 uppercase tracking-widest leading-none">Tim</p>
                                        <p class="text-xs sm:text-sm font-extrabold text-slate-900 leading-none mt-1">Smart EduHub</p>
                                    </div>
                                </div>
                                <div class="absolute -bottom-7 left-1/2 -translate-x-1/2 w-3/4 h-5 bg-orange-500/30 blur-xl rounded-[50%]"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ====== PARTNER MARQUEE ====== -->
    <section class="py-6 sm:py-10 bg-white border-b border-slate-100" aria-label="Mitra">
        <div class="marquee-mask overflow-hidden">
            <div class="animate-marquee flex w-max items-center">
                @for ($i = 0; $i < 2; $i++)
                <div class="flex items-center gap-10 sm:gap-20 pr-10 sm:pr-20 shrink-0" @if($i) aria-hidden="true" @endif>
                    <span class="text-sm sm:text-xl font-extrabold tracking-[0.25em] text-slate-300 hover:text-orange-500 transition-colors select-none">INTEL</span>
                    <span class="text-sm sm:text-xl font-extrabold tracking-[0.25em] text-slate-300 hover:text-orange-500 transition-colors select-none">MAKEBLOCK</span>
                    <span class="text-sm sm:text-xl font-extrabold tracking-[0.25em] text-slate-300 hover:text-orange-500 transition-colors select-none">GOOGLE</span>
                    <span class="text-sm sm:text-xl font-extrabold tracking-[0.25em] text-slate-300 hover:text-orange-500 transition-colors select-none">VISIPRO</span>
                    <span class="text-sm sm:text-xl font-extrabold tracking-[0.25em] text-slate-300 hover:text-orange-500 transition-colors select-none">AXIOO CLASS PROGRAM</span>
                </div>
                @endfor
            </div>
        </div>
    </section>

    <!-- ====== NAMA TIM ====== -->
    <section id="nama-tim" class="relative overflow-hidden py-16 sm:py-24 lg:py-28 bg-slate-50 cv-auto" style="contain-intrinsic-size:auto 1400px;">
        <div class="absolute top-[5%] right-[-10%] w-64 h-64 sm:w-96 sm:h-96 bg-orange-100/70 rounded-full blur-[120px] -z-0 pointer-events-none"></div>
        <div class="relative max-w-7xl mx-auto px-4 sm:px-6">
            <div class="text-center max-w-2xl mx-auto mb-10 sm:mb-14 reveal">
                <span class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-orange-50 border border-orange-100 text-orange-600 text-xs font-bold uppercase tracking-widest mb-4"><i class="fa-solid fa-users"></i> Smart EduHub</span>
                <h2 class="h-section font-extrabold text-slate-900">Nama Anggota Tim</h2>
            </div>

            <div class="grid grid-cols-1 xs:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
                @php
                    $team = [
                        ['img'=>'Krisna.png','name'=>'Krisna Aditama','badge'=>'Ketua Tim','icon'=>'fa-crown','color'=>'bg-red-600','a'=>'Kelas','av'=>'XI','b'=>'Jurusan','bv'=>'Axioo Class Program'],
                        ['img'=>'mujahid.jpeg','name'=>'Mujahid Muhammad A.G','badge'=>'Anggota 1','icon'=>'fa-user-graduate','color'=>'bg-orange-600','a'=>'Kelas','av'=>'XI','b'=>'Jurusan','bv'=>'Axioo Class Program'],
                        ['img'=>'najih.jpeg','name'=>'Najih Banyu Wicaksono','badge'=>'Anggota 2','icon'=>'fa-user-graduate','color'=>'bg-amber-600','a'=>'Kelas','av'=>'XI','b'=>'Jurusan','bv'=>'Axioo Class Program'],
                        ['img'=>'jeri.jpg','name'=>'Jeri Wilaga','badge'=>'Pembimbing','icon'=>'fa-chalkboard-user','color'=>'bg-slate-800','a'=>'NBM','av'=>'—','b'=>'Mapel','bv'=>'Kejuruan ACP'],
                    ];
                @endphp
                @foreach ($team as $i => $m)
                <article class="reveal group bg-white rounded-3xl border border-slate-100 shadow-sm card-hover overflow-hidden flex flex-col" style="transition-delay:{{ $i * 80 }}ms;">
                    <div class="relative aspect-[4/3] xs:aspect-square lg:aspect-[4/4.2] bg-gradient-to-br from-orange-50 to-red-100 overflow-hidden">
                        <img src="{{ asset('images/' . $m['img']) }}" alt="Foto {{ $m['name'] }}" class="w-full h-full object-cover object-top group-hover:scale-105 transition duration-700" loading="lazy" decoding="async">
                        <div class="absolute inset-x-0 bottom-0 h-1/3 bg-gradient-to-t from-black/40 to-transparent"></div>
                        <span class="absolute top-3 left-3 inline-flex items-center gap-1.5 px-3 py-1 rounded-full {{ $m['color'] }} text-white text-[10px] sm:text-[11px] font-bold uppercase tracking-wider shadow-lg">
                            <i class="fa-solid {{ $m['icon'] }} text-[10px]"></i> {{ $m['badge'] }}
                        </span>
                    </div>
                    <div class="p-4 sm:p-5 flex flex-col gap-3 flex-1">
                        <h3 class="text-base sm:text-lg font-extrabold text-slate-900 leading-tight">{{ $m['name'] }}</h3>
                        <dl class="space-y-1.5 text-sm mt-auto">
                            <div class="flex items-center gap-2.5"><dt class="w-16 shrink-0 text-[10px] font-bold text-slate-400 uppercase tracking-widest">{{ $m['a'] }}</dt><dd class="font-bold text-slate-700">{{ $m['av'] }}</dd></div>
                            <div class="flex items-center gap-2.5"><dt class="w-16 shrink-0 text-[10px] font-bold text-slate-400 uppercase tracking-widest">{{ $m['b'] }}</dt><dd class="font-bold text-slate-700">{{ $m['bv'] }}</dd></div>
                        </dl>
                    </div>
                </article>
                @endforeach
            </div>
        </div>
    </section>

    <!-- ====== SMART BANK ====== -->
    <section id="creative-bank" class="relative overflow-hidden py-16 sm:py-24 lg:py-28 bg-white">
        <div class="absolute top-[15%] left-[-10%] w-72 h-72 sm:w-[30rem] sm:h-[30rem] bg-red-100/50 rounded-full blur-[130px] pointer-events-none"></div>
        <div class="relative max-w-7xl mx-auto px-4 sm:px-6">
            <div class="grid lg:grid-cols-12 gap-12 lg:gap-14 items-center">

                <div class="lg:col-span-7 text-center lg:text-left reveal">
                    <div class="flex items-center justify-center lg:justify-start gap-3 sm:gap-4 mb-6">
                        <img src="{{ asset('images/SEHmob.png') }}" alt="Logo SEH" class="w-20 h-20 sm:w-24 sm:h-24 lg:w-28 lg:h-28 object-contain shrink-0" loading="lazy" decoding="async">
                        <div class="text-left">
                            <span class="block text-xl sm:text-2xl font-extrabold tracking-tight text-slate-900 uppercase">SMART<span class="text-orange-600">EDUHUB</span></span>
                            <span class="text-[10px] sm:text-xs font-bold text-slate-400 tracking-wider uppercase">Smart Student Wallet System</span>
                        </div>
                    </div>
                    <h2 class="h-section font-extrabold text-slate-900 mb-5">Simpan Uang Digital &amp; <span class="text-grad-dark">Jajan Tanpa Tunai.</span></h2>
                    <p class="text-slate-500 text-base sm:text-lg leading-relaxed mb-8 max-w-2xl mx-auto lg:mx-0">Aplikasi pengelolaan keuangan modern yang dirancang khusus untuk siswa. Simpan uang saku secara digital, pantau pengeluaran harian, dan lakukan transaksi instan menggunakan <strong class="text-slate-800">Kartu Pintar Axioo</strong> di seluruh merchant kantin sekolah.</p>

                    <div class="grid grid-cols-2 gap-3 sm:gap-4 text-left">
                        <div class="gborder card-hover col-span-2 p-5 sm:p-6 flex items-start gap-4 bg-gradient-to-br from-orange-50 via-white to-white">
                            <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-orange-500 to-red-600 text-white flex items-center justify-center shrink-0 shadow-lg shadow-orange-500/30"><i class="fa-solid fa-bolt"></i></div>
                            <div><h3 class="font-extrabold text-slate-900 mb-1">Tap &amp; Go Jajan</h3><p class="text-sm text-slate-500 leading-relaxed">Cukup gunakan kartu fisik atau scan QR untuk transaksi kilat di seluruh kantin sekolah.</p></div>
                        </div>
                        <div class="gborder card-hover p-4 sm:p-5">
                            <div class="w-10 h-10 rounded-xl bg-emerald-100 text-emerald-600 flex items-center justify-center mb-3"><i class="fa-solid fa-shield-halved"></i></div>
                            <h3 class="font-extrabold text-slate-900 mb-1 text-sm sm:text-base">Tabungan Aman</h3>
                            <p class="text-xs sm:text-sm text-slate-500 leading-relaxed">Dilindungi enkripsi PIN &amp; sistem pintar.</p>
                        </div>
                        <div class="gborder card-hover p-4 sm:p-5">
                            <div class="w-10 h-10 rounded-xl bg-sky-100 text-sky-600 flex items-center justify-center mb-3"><i class="fa-solid fa-chart-line"></i></div>
                            <h3 class="font-extrabold text-slate-900 mb-1 text-sm sm:text-base">Pantau Pengeluaran</h3>
                            <p class="text-xs sm:text-sm text-slate-500 leading-relaxed">Lihat riwayat jajan harian secara real-time.</p>
                        </div>
                    </div>
                </div>

                <div class="lg:col-span-5 flex flex-col items-center w-full reveal" style="transition-delay:120ms;">
                    <div class="relative w-full max-w-[430px]">
                        <div class="absolute -inset-6 bg-gradient-to-tr from-orange-500/25 via-red-500/20 to-amber-400/20 blur-3xl rounded-[3rem] pointer-events-none"></div>
                        <div class="persp relative" id="card-3d-wrap">
                            <div class="credit-card card-3d aspect-[1.586/1] rounded-[1.25rem] sm:rounded-[1.75rem] p-4 xs:p-5 sm:p-7 text-white relative" id="card-3d-inner">
                                <div class="absolute inset-0 rounded-[inherit] overflow-hidden pointer-events-none">
                                    <div class="absolute -top-20 -right-20 w-64 h-64 bg-amber-300/30 rounded-full blur-3xl"></div>
                                    <div class="absolute -bottom-24 -left-16 w-64 h-64 bg-fuchsia-500/20 rounded-full blur-3xl"></div>
                                    <div class="absolute inset-0 opacity-10" style="background-image:radial-gradient(rgba(255,255,255,.7) 1px,transparent 1px);background-size:18px 18px;"></div>
                                    <div class="shimmer"></div>
                                    <div class="absolute inset-0" id="card-glare"></div>
                                </div>
                                <div class="relative h-full flex flex-col" style="transform-style:preserve-3d;">
                                    <div class="flex justify-between items-start mb-2 sm:mb-6" style="transform:translateZ(35px);">
                                        <div>
                                            <p class="text-[8px] sm:text-[10px] uppercase tracking-[0.2em] text-white/60 font-bold mb-1">Student Credit Card</p>
                                            <span class="text-base sm:text-lg font-extrabold tracking-tight">SMART<span class="text-yellow-300">BANK</span></span>
                                        </div>
                                        <i class="fa-solid fa-wifi rotate-90 text-white/70 text-base sm:text-lg"></i>
                                    </div>
                                    <div class="flex items-center gap-3 mb-2 sm:mb-5" style="transform:translateZ(55px);">
                                        <div class="w-10 h-7 sm:w-12 sm:h-9 chip-gold rounded-lg"></div>
                                        <div class="flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-black/20 border border-white/10">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                                            <span class="text-[8px] sm:text-[9px] font-bold tracking-widest text-emerald-300 uppercase">Active</span>
                                        </div>
                                    </div>
                                    <div class="mb-2 sm:mb-4" style="transform:translateZ(75px);">
                                        <p class="text-base xs:text-lg sm:text-2xl font-mono font-bold tracking-[0.12em] sm:tracking-[0.18em] drop-shadow-lg">**** **** **** 2026</p>
                                    </div>
                                    <div class="flex justify-between items-end mt-auto gap-2" style="transform:translateZ(45px);">
                                        <div class="min-w-0">
                                            <p class="text-[8px] sm:text-[9px] uppercase text-white/50 tracking-[0.15em]">Card Holder</p>
                                            <p class="text-xs sm:text-sm font-bold tracking-wide truncate">Creative Student</p>
                                        </div>
                                        <div class="text-center">
                                            <p class="text-[8px] sm:text-[9px] uppercase text-white/50 tracking-[0.15em]">Expires</p>
                                            <p class="text-xs sm:text-sm font-bold font-mono">12/30</p>
                                        </div>
                                        <div class="text-right">
                                            <span class="text-[10px] sm:text-xs font-black tracking-[0.25em] text-white/80">AXIOO</span>
                                            <p class="text-[8px] uppercase tracking-widest text-white/40">debit</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="w-full max-w-[430px] mt-6 bg-white border border-slate-100 rounded-2xl p-3 sm:p-4 flex flex-wrap justify-between items-center gap-3 shadow-xl shadow-slate-200/60">
                        <div class="flex items-center gap-3">
                            <div class="w-11 h-11 bg-gradient-to-br from-emerald-500 to-teal-500 rounded-xl flex items-center justify-center text-white shadow-lg shadow-emerald-500/30"><i class="fa-solid fa-wallet"></i></div>
                            <div>
                                <span class="text-[10px] text-slate-400 font-bold block uppercase leading-none mb-1 tracking-widest">Saldo Aktif</span>
                                <span class="font-extrabold text-slate-800 text-base sm:text-lg">Rp <span class="counter" data-target="750000">0</span></span>
                            </div>
                        </div>
                        <div class="flex gap-2">
                            <button type="button" class="px-4 py-2 bg-gradient-to-r from-orange-500 to-red-600 text-white rounded-xl text-xs font-bold hover:opacity-90 transition shadow-md shadow-orange-500/25">Top Up</button>
                            <button type="button" class="px-4 py-2 bg-slate-50 border border-slate-200 text-slate-600 rounded-xl text-xs font-bold hover:bg-slate-100 transition">Riwayat</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ====== UNDUH APLIKASI ====== -->
    <section id="download" class="relative overflow-hidden py-16 sm:py-24 lg:py-28 bg-gradient-to-b from-maroon-950 via-[#2a0b09] to-maroon-950">
        <div class="absolute inset-0 grid-lines pointer-events-none opacity-70"></div>
        <div class="absolute -top-20 right-[10%] w-72 h-72 sm:w-[28rem] sm:h-[28rem] bg-red-600/20 rounded-full blur-[130px] pointer-events-none"></div>
        <div class="absolute -bottom-20 left-[5%] w-64 h-64 sm:w-96 sm:h-96 bg-orange-600/20 rounded-full blur-[130px] pointer-events-none"></div>

        <div class="relative max-w-7xl mx-auto px-4 sm:px-6">
            <div class="grid lg:grid-cols-2 gap-14 lg:gap-16 items-center">

                <div class="text-center lg:text-left">
                    <span class="reveal inline-flex items-center gap-2 px-4 py-1.5 rounded-full glass text-orange-300 text-xs font-bold uppercase tracking-widest mb-5"><i class="fa-brands fa-android"></i> Android App</span>
                    <h2 class="reveal h-section font-extrabold text-white mb-5" style="transition-delay:80ms;">Smart Bank <span class="text-grad">di Genggamanmu.</span></h2>
                    <p class="reveal text-white/60 text-base sm:text-lg leading-relaxed max-w-xl mx-auto lg:mx-0 mb-8" style="transition-delay:140ms;">Kelola saldo, top up, dan pantau transaksi jajan langsung dari HP. Cepat, aman, dan gratis — khusus untuk siswa SMKS Muhammadiyah 1 Pringsewu.</p>

                    <div class="reveal grid grid-cols-2 sm:flex sm:flex-wrap justify-center lg:justify-start gap-2.5 mb-8" style="transition-delay:200ms;">
                        <div class="glass rounded-full px-4 py-2 text-sm text-white/70 flex items-center justify-center gap-2"><i class="fa-solid fa-star text-amber-400"></i><b class="text-white">4.9</b> Rating</div>
                        <div class="glass rounded-full px-4 py-2 text-sm text-white/70 flex items-center justify-center gap-2"><i class="fa-solid fa-download text-orange-400"></i><b class="text-white">1.200+</b> Unduhan</div>
                        <div class="glass rounded-full px-4 py-2 text-sm text-white/70 flex items-center justify-center gap-2"><i class="fa-brands fa-android text-emerald-400"></i>Android 8.0+</div>
                        <div class="glass rounded-full px-4 py-2 text-sm text-white/70 flex items-center justify-center gap-2"><i class="fa-solid fa-hard-drive text-indigo-300"></i>±24 MB</div>
                    </div>

                    <!-- ✏️ Letakkan file APK di public/apk/ -->
                    <div class="reveal grid gap-3 sm:grid-cols-3 lg:grid-cols-1 xl:grid-cols-3" style="transition-delay:260ms;">
                        @foreach ([['APK SISWA','fa-user-graduate'],['APK WALI MURID','fa-people-roof'],['APK TELLER','fa-cash-register']] as $apk)
                        <a href="{{ asset('apk/axiobank.apk') }}" download class="group flex sm:flex-col lg:flex-row xl:flex-col items-center sm:items-start lg:items-center xl:items-start gap-3 p-4 rounded-2xl glass hover:bg-white/10 hover:-translate-y-1 transition text-left">
                            <div class="w-11 h-11 rounded-xl bg-gradient-to-br from-orange-500 to-red-600 text-white flex items-center justify-center shrink-0 group-hover:rotate-6 transition"><i class="fa-solid {{ $apk[1] }}"></i></div>
                            <div class="min-w-0">
                                <p class="text-[10px] font-bold text-white/40 uppercase tracking-widest leading-none mb-1">Unduh Langsung</p>
                                <p class="text-sm font-extrabold text-white leading-tight">{{ $apk[0] }} • V.1.0</p>
                            </div>
                            <i class="fa-solid fa-file-arrow-down ml-auto sm:ml-0 lg:ml-auto xl:ml-0 xl:mt-1 text-orange-400"></i>
                        </a>
                        @endforeach
                    </div>
                </div>

                <div class="flex justify-center reveal" style="transition-delay:150ms;">
                    <div class="relative w-[230px] xs:w-[260px] sm:w-[290px]">
                        <div class="absolute -inset-8 bg-gradient-to-tr from-orange-500/30 via-red-500/20 to-amber-400/20 blur-3xl rounded-[3rem]"></div>

                        <div class="absolute -left-3 xs:-left-6 sm:-left-12 top-24 z-20 float-sm bg-white rounded-2xl shadow-2xl px-3 py-2.5 flex items-center gap-2">
                            <div class="w-8 h-8 bg-emerald-100 rounded-xl flex items-center justify-center text-emerald-600"><i class="fa-solid fa-arrow-trend-up"></i></div>
                            <div><p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest leading-none">Saldo</p><p class="text-xs sm:text-sm font-extrabold text-slate-900 leading-none mt-1">Rp 750.000</p></div>
                        </div>
                        <div class="absolute -right-3 xs:-right-5 sm:-right-10 bottom-28 z-20 float-sm-d bg-white rounded-2xl shadow-2xl px-3 py-2.5 flex items-center gap-2">
                            <div class="w-8 h-8 bg-orange-100 rounded-xl flex items-center justify-center text-orange-600"><i class="fa-solid fa-circle-check"></i></div>
                            <div><p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest leading-none">Kantin</p><p class="text-xs sm:text-sm font-extrabold text-slate-900 leading-none mt-1">-Rp 15.000</p></div>
                        </div>

                        <div class="float relative rounded-[2.8rem] border border-white/15 bg-slate-900 p-2.5 shadow-2xl">
                            <div class="rounded-[2.3rem] overflow-hidden bg-gradient-to-b from-[#1a0b0b] to-[#2b1212] aspect-[9/19] relative">
                                <div class="absolute top-2.5 left-1/2 -translate-x-1/2 w-24 h-5 bg-black rounded-full z-10"></div>
                                <div class="p-4 pt-12 text-white h-full flex flex-col">
                                    <div class="flex justify-between items-center mb-4">
                                        <div><p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest">Selamat Datang</p><p class="text-sm font-extrabold">Creative Student</p></div>
                                        <div class="w-9 h-9 rounded-full bg-gradient-to-br from-orange-500 to-red-600 flex items-center justify-center text-xs font-black border border-white/10">A</div>
                                    </div>
                                    <div class="rounded-2xl p-4 bg-gradient-to-br from-orange-600 via-red-600 to-maroon-800 border border-white/10 shadow-xl mb-4 relative overflow-hidden">
                                        <div class="absolute -right-6 -top-6 w-20 h-20 bg-amber-300/30 rounded-full blur-2xl"></div>
                                        <p class="text-[9px] text-white/70 font-bold uppercase tracking-widest mb-1">Saldo Aktif</p>
                                        <p class="text-xl font-black">Rp 750.000</p>
                                        <p class="text-[10px] text-emerald-200 font-bold mt-1">+12.5% bulan ini</p>
                                    </div>
                                    <div class="grid grid-cols-4 gap-2 mb-4">
                                        @foreach ([['fa-plus','Top Up'],['fa-qrcode','Scan'],['fa-credit-card','Kartu'],['fa-clock-rotate-left','Riwayat']] as $q)
                                        <div class="flex flex-col items-center gap-1.5"><div class="w-10 h-10 rounded-2xl bg-white/5 border border-white/10 flex items-center justify-center text-orange-300"><i class="fa-solid {{ $q[0] }} text-sm"></i></div><span class="text-[8px] font-bold text-slate-400 uppercase">{{ $q[1] }}</span></div>
                                        @endforeach
                                    </div>
                                    <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest mb-2">Transaksi Terbaru</p>
                                    <div class="space-y-2">
                                        <div class="flex items-center gap-2.5 bg-white/5 border border-white/10 rounded-xl p-2.5"><div class="w-8 h-8 rounded-lg bg-red-500/20 text-red-400 flex items-center justify-center text-xs"><i class="fa-solid fa-utensils"></i></div><div class="flex-1"><p class="text-[11px] font-bold leading-none">Kantin Sekolah</p><p class="text-[9px] text-slate-500 mt-0.5">Hari ini</p></div><span class="text-[11px] font-black text-red-400">-15.000</span></div>
                                        <div class="flex items-center gap-2.5 bg-white/5 border border-white/10 rounded-xl p-2.5"><div class="w-8 h-8 rounded-lg bg-emerald-500/20 text-emerald-400 flex items-center justify-center text-xs"><i class="fa-solid fa-wallet"></i></div><div class="flex-1"><p class="text-[11px] font-bold leading-none">Top Up Dana</p><p class="text-[9px] text-slate-500 mt-0.5">Kemarin</p></div><span class="text-[11px] font-black text-emerald-400">+100.000</span></div>
                                        <div class="flex items-center gap-2.5 bg-white/5 border border-white/10 rounded-xl p-2.5"><div class="w-8 h-8 rounded-lg bg-amber-500/20 text-amber-400 flex items-center justify-center text-xs"><i class="fa-solid fa-print"></i></div><div class="flex-1"><p class="text-[11px] font-bold leading-none">Print Dokumen</p><p class="text-[9px] text-slate-500 mt-0.5">2 hari lalu</p></div><span class="text-[11px] font-black text-red-400">-5.000</span></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ====== PRESTASI ====== -->
    <section id="prestasi" class="py-16 sm:py-24 lg:py-28 bg-slate-50 cv-auto">
        <div class="max-w-7xl mx-auto px-4 sm:px-6">
            <div class="grid lg:grid-cols-2 gap-10 lg:gap-16 items-center">
                <div class="reveal text-center lg:text-left">
                    <span class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-orange-50 border border-orange-100 text-orange-600 text-xs font-bold uppercase tracking-widest mb-4"><i class="fa-solid fa-trophy"></i> Prestasi</span>
                    <h2 class="h-section font-extrabold text-slate-900 mb-8">Standar Industri, Kualitas Teruji.</h2>
                    <div class="grid grid-cols-2 gap-3 sm:gap-6 mb-8">
                        <div class="p-4 sm:p-6 bg-gradient-to-br from-orange-500 to-red-600 rounded-3xl text-white shadow-xl shadow-orange-500/20 text-left">
                            <span class="block text-3xl sm:text-5xl font-extrabold mb-1 counter" data-target="{{ \App\Models\Student::count() }}">0</span>
                            <span class="text-[10px] sm:text-xs font-bold text-white/80 uppercase tracking-widest">Siswa Binaan</span>
                        </div>
                        <div class="p-4 sm:p-6 bg-white border border-slate-100 rounded-3xl shadow-sm text-left">
                            <span class="block text-3xl sm:text-5xl font-extrabold text-slate-900 mb-1"><span class="counter" data-target="60">0</span>K+</span>
                            <span class="text-[10px] sm:text-xs font-bold text-slate-400 uppercase tracking-widest">Alumni</span>
                        </div>
                    </div>
                    <p class="text-slate-500 leading-relaxed max-w-xl mx-auto lg:mx-0">Axioo Creative telah mendidik ribuan talenta muda untuk siap menghadapi tantangan industri visual digital secara global.</p>
                </div>

                <div class="reveal grid grid-cols-2 gap-3 sm:gap-5" style="transition-delay:120ms;">
                    <figure class="group relative row-span-2 rounded-3xl overflow-hidden shadow-xl min-h-[16rem]">
                        <img src="{{ asset('images/juara2.jpeg') }}" class="absolute inset-0 w-full h-full object-cover group-hover:scale-105 transition duration-700" loading="lazy" decoding="async" alt="Juara 2">
                        <figcaption class="absolute inset-x-0 bottom-0 p-4 bg-gradient-to-t from-black/70 to-transparent text-white text-sm font-bold">Juara 2</figcaption>
                    </figure>
                    <figure class="group relative rounded-3xl overflow-hidden shadow-xl aspect-[4/3]">
                        <img src="https://placehold.co/800x600/ea580c/ffffff?text=Portofolio+Unggulan" class="w-full h-full object-cover group-hover:scale-105 transition duration-700" loading="lazy" decoding="async" alt="Portofolio Unggulan">
                        <figcaption class="absolute inset-x-0 bottom-0 p-3 bg-gradient-to-t from-black/70 to-transparent text-white text-xs sm:text-sm font-bold">Portofolio</figcaption>
                    </figure>
                    <figure class="group relative rounded-3xl overflow-hidden shadow-xl aspect-[4/3]">
                        <img src="{{ asset('images/juara1.jpeg') }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-700" loading="lazy" decoding="async" alt="Juara 1">
                        <figcaption class="absolute inset-x-0 bottom-0 p-3 bg-gradient-to-t from-black/70 to-transparent text-white text-xs sm:text-sm font-bold">Juara 1</figcaption>
                    </figure>
                </div>
            </div>
        </div>
    </section>

    <!-- ====== YOUTUBE ====== -->
    <section id="youtube" class="relative overflow-hidden py-16 sm:py-24 lg:py-28 bg-white cv-auto">
        <div class="absolute -top-20 -left-20 w-72 h-72 sm:w-[30rem] sm:h-[30rem] bg-orange-100/60 rounded-full blur-[120px] pointer-events-none"></div>
        <div class="relative max-w-7xl mx-auto px-4 sm:px-6">
            <div class="text-center max-w-2xl mx-auto mb-10 sm:mb-14 reveal">
                <span class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-orange-50 border border-orange-100 text-orange-600 text-xs font-bold uppercase tracking-widest mb-4"><i class="fa-brands fa-youtube"></i> YouTube Portfolio</span>
                <h2 class="h-section font-extrabold text-slate-900 mb-4">Cinematic Showcase</h2>
                <p class="text-slate-500 text-base sm:text-lg">Eksplorasi karya video kami dalam resolusi tinggi, mulai dari dokumenter pendek hingga profil kreatif.</p>
            </div>

            <div class="reveal mb-6 sm:mb-10">
                <div class="yt-facade group relative aspect-video rounded-2xl sm:rounded-[2rem] overflow-hidden ring-1 ring-slate-200 shadow-2xl cursor-pointer select-none" data-yt-id="HRgiivfr5aY">
                    <img src="https://i.ytimg.com/vi/HRgiivfr5aY/maxresdefault.jpg" onerror="this.onerror=null;this.src='https://i.ytimg.com/vi/HRgiivfr5aY/hqdefault.jpg';" alt="Axioo Main Showcase" class="w-full h-full object-cover group-hover:scale-[1.02] transition duration-700" loading="lazy" decoding="async">
                    <div class="yt-overlay absolute inset-0 bg-gradient-to-t from-black/50 via-transparent to-transparent"></div>
                    <div class="yt-overlay absolute inset-0 flex items-center justify-center">
                        <div class="w-14 h-14 sm:w-20 sm:h-20 bg-red-600 rounded-full flex items-center justify-center text-white shadow-2xl shadow-red-600/40 group-hover:scale-110 transition"><i class="fa-solid fa-play text-lg sm:text-2xl ml-1"></i></div>
                    </div>
                    <div class="yt-overlay absolute top-3 left-3 sm:top-5 sm:left-5 bg-white/90 backdrop-blur text-red-600 px-3 py-1.5 rounded-full text-[10px] sm:text-xs font-bold uppercase tracking-wider shadow-lg">Featured Video</div>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 sm:gap-8">
                @foreach ([
                    ['bj6W7WvmGCI','Podcast Cari Tahu Axioo Creative Production','Production • 2026','Podcast Cari Tahu Axioo Creative Production'],
                    ['BOFekec1Fw0','Juara 3 Film Pendek || Sebelum Terlambat','Event • 2024','Juara 3 Film Pendek Sebelum Terlambat'],
                    ['VeZSRTU-Q7g','Murotal Quran','Animation • 2026','Murotal Quran'],
                ] as $i => $v)
                <div class="reveal {{ $i === 2 ? 'sm:col-span-2 lg:col-span-1 sm:max-w-md sm:mx-auto lg:max-w-none lg:mx-0 w-full' : '' }}" style="transition-delay:{{ ($i+1) * 90 }}ms;">
                    <div class="yt-facade group relative aspect-video rounded-2xl overflow-hidden ring-1 ring-slate-200 shadow-lg mb-4 cursor-pointer select-none" data-yt-id="{{ $v[0] }}">
                        <img src="https://i.ytimg.com/vi/{{ $v[0] }}/hqdefault.jpg" alt="{{ $v[3] }}" class="w-full h-full object-cover md:grayscale-[.4] group-hover:grayscale-0 group-hover:scale-105 transition duration-500" loading="lazy" decoding="async">
                        <div class="yt-overlay absolute inset-0 flex items-center justify-center"><div class="w-12 h-12 bg-red-600/90 rounded-full flex items-center justify-center text-white shadow-lg group-hover:scale-110 transition"><i class="fa-solid fa-play text-sm ml-0.5"></i></div></div>
                    </div>
                    <h3 class="font-bold text-slate-900 px-1 leading-snug">{{ $v[1] }}</h3>
                    <p class="text-slate-500 text-sm px-1 mt-0.5">{{ $v[2] }}</p>
                </div>
                @endforeach
            </div>

            <div class="mt-10 sm:mt-14 text-center reveal">
                <a href="https://www.youtube.com/@axioosewu" target="_blank" rel="noopener" class="group inline-flex items-center justify-center gap-3 px-6 sm:px-8 py-3.5 bg-slate-900 text-white font-bold rounded-2xl hover:bg-red-600 transition shadow-lg text-sm sm:text-base"><i class="fa-brands fa-youtube text-xl"></i>Kunjungi Channel YouTube<i class="fa-solid fa-arrow-right group-hover:translate-x-1 transition-transform"></i></a>
            </div>
        </div>
    </section>

    <!-- ====== SOSIAL MEDIA ====== -->
    <section id="sosmed" class="relative overflow-hidden py-16 sm:py-24 lg:py-28 bg-slate-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6">
            <div class="text-center max-w-2xl mx-auto mb-10 sm:mb-14 reveal">
                <span class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-orange-50 border border-orange-100 text-orange-600 text-xs font-bold uppercase tracking-widest mb-4">Connect With Us</span>
                <h2 class="h-section font-extrabold text-slate-900 mb-4">Ikuti Perjalanan Kami</h2>
                <p class="text-slate-500 text-base sm:text-lg">Semua update kegiatan, karya, dan keseruan siswa Axioo Creative — langsung dari sosial media resmi kami.</p>
            </div>

            <!-- ✏️ Sesuaikan angka pengikut & nomor WA dengan data asli -->
            <div class="grid grid-cols-1 xs:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
                @foreach ([
                    ['https://www.instagram.com/axioocreative','fa-instagram','Instagram','@axioocreative','Dokumentasi harian kegiatan, karya desain, dan momen seru siswa.','2.5K+','Pengikut','Follow','bg-gradient-to-br from-fuchsia-500 via-rose-500 to-amber-400','group-hover:bg-gradient-to-r group-hover:from-fuchsia-600 group-hover:to-rose-500'],
                    ['https://www.tiktok.com/@axioo.creative','fa-tiktok','TikTok','@axioo.creative','Video proses klinik komputer, behind the scenes, dan konten viral.','15K+','Pengikut','Follow','bg-slate-900','group-hover:bg-gradient-to-r group-hover:from-cyan-500 group-hover:to-pink-500'],
                    ['https://www.youtube.com/@axioosewu','fa-youtube','YouTube','@axioosewu','Cinematic showcase, film pendek, podcast, dan profil kreatif.','1K+','Subscriber','Subscribe','bg-red-600','group-hover:bg-red-600'],
                    ['https://wa.me/6281234567890?text=Halo%20Axioo%20Creative%2C%20saya%20ingin%20bertanya','fa-whatsapp','WhatsApp','+62 812-3456-7890','Hubungi kami untuk kolaborasi, pendaftaran, atau pertanyaan via chat.','Online','Respon Cepat','Chat','bg-gradient-to-br from-emerald-500 to-green-600','group-hover:bg-gradient-to-r group-hover:from-emerald-500 group-hover:to-green-600'],
                ] as $i => $s)
                <a href="{{ $s[0] }}" target="_blank" rel="noopener" class="reveal group relative bg-white border border-slate-100 rounded-3xl p-5 sm:p-6 card-hover overflow-hidden flex flex-col" style="transition-delay:{{ $i * 80 }}ms;">
                    <div class="w-12 h-12 sm:w-14 sm:h-14 rounded-2xl {{ $s[8] }} flex items-center justify-center text-white text-xl sm:text-2xl shadow-lg mb-4 group-hover:scale-110 group-hover:rotate-6 transition-transform"><i class="fa-brands {{ $s[1] }}"></i></div>
                    <h3 class="font-extrabold text-lg text-slate-900">{{ $s[2] }}</h3>
                    <p class="text-sm text-slate-400 font-bold mb-3 break-all">{{ $s[3] }}</p>
                    <p class="text-sm text-slate-500 leading-relaxed mb-5">{{ $s[4] }}</p>
                    <div class="flex justify-between items-end gap-2 mt-auto">
                        <div>
                            <p class="text-xl font-extrabold text-slate-900">{{ $s[5] }}</p>
                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">{{ $s[6] }}</p>
                        </div>
                        <span class="inline-flex items-center gap-1.5 px-4 py-2 rounded-full bg-slate-900 text-white text-xs font-bold {{ $s[9] }} transition-all">{{ $s[7] }} <i class="fa-solid fa-arrow-right text-[10px] group-hover:translate-x-0.5 transition-transform"></i></span>
                    </div>
                </a>
                @endforeach
            </div>
        </div>
    </section>
    </main>

    <!-- ====== FOOTER ====== -->
    <footer class="bg-maroon-950 text-white pt-14 sm:pt-20 pb-32 lg:pb-10 relative overflow-hidden">
        <div class="absolute inset-0 grid-lines opacity-40 pointer-events-none"></div>
        <div class="relative max-w-7xl mx-auto px-4 sm:px-6">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-12 gap-10 lg:gap-12 mb-12">
                <div class="lg:col-span-5">
                    <img src="{{ asset('images/SEH.png') }}" alt="Smart EduHub" class="h-10 sm:h-12 w-auto object-contain mb-5" style="filter:brightness(0) invert(1)" loading="lazy" decoding="async">
                    <p class="text-white/55 max-w-sm mb-6 leading-relaxed">Eksplorasi kreativitas tanpa batas melalui pendidikan berbasis industri dan teknologi terkini.</p>
                    <div class="flex flex-wrap gap-3">
                        <a href="https://www.instagram.com/axioocreative" target="_blank" rel="noopener" aria-label="Instagram" class="w-11 h-11 glass rounded-xl flex items-center justify-center hover:bg-orange-500 transition"><i class="fa-brands fa-instagram"></i></a>
                        <a href="https://www.tiktok.com/@axioo.creative" target="_blank" rel="noopener" aria-label="TikTok" class="w-11 h-11 glass rounded-xl flex items-center justify-center hover:bg-orange-500 transition"><i class="fa-brands fa-tiktok"></i></a>
                        <a href="https://www.youtube.com/@axioosewu" target="_blank" rel="noopener" aria-label="YouTube" class="w-11 h-11 glass rounded-xl flex items-center justify-center hover:bg-red-600 transition"><i class="fa-brands fa-youtube"></i></a>
                        <a href="https://wa.me/6281234567890" target="_blank" rel="noopener" aria-label="WhatsApp" class="w-11 h-11 glass rounded-xl flex items-center justify-center hover:bg-emerald-500 transition"><i class="fa-brands fa-whatsapp"></i></a>
                    </div>
                </div>
                <div class="lg:col-span-4">
                    <h3 class="font-bold mb-5">Navigasi</h3>
                    <ul class="grid grid-cols-2 gap-x-6 gap-y-3 text-white/55 text-sm">
                        <li><a href="#home" class="hover:text-orange-400 transition">Beranda</a></li>
                        <li><a href="#nama-tim" class="hover:text-orange-400 transition">Tim</a></li>
                        <li><a href="#creative-bank" class="hover:text-orange-400 transition">Smart Bank</a></li>
                        <li><a href="#download" class="hover:text-orange-400 transition">Unduh Aplikasi</a></li>
                        <li><a href="#prestasi" class="hover:text-orange-400 transition">Prestasi</a></li>
                        <li><a href="#youtube" class="hover:text-orange-400 transition">Video</a></li>
                        <li><a href="#sosmed" class="hover:text-orange-400 transition">Sosial Media</a></li>
                    </ul>
                </div>
                <div class="lg:col-span-3">
                    <h3 class="font-bold mb-5">Hubungi Kami</h3>
                    <ul class="space-y-3 text-white/55 text-sm break-words">
                        <li class="flex gap-3"><i class="fa-solid fa-envelope text-orange-400 mt-0.5"></i>acpaxioo@gmail.com</li>
                        <li class="flex gap-3"><i class="fa-solid fa-location-dot text-orange-400 mt-0.5"></i>SMKS Muhammadiyah 1 Pringsewu</li>
                    </ul>
                </div>
            </div>
            <div class="pt-6 sm:pt-8 border-t border-white/10 flex flex-col md:flex-row justify-between text-xs text-white/40 gap-3 text-center md:text-left">
                <p>&copy; 2026 Axioo Creative. All rights reserved.</p>
                <div class="flex gap-6 justify-center md:justify-start"><a href="#" class="hover:text-white">Privacy Policy</a><a href="#" class="hover:text-white">Terms of Service</a></div>
            </div>
        </div>
    </footer>

    <!-- ====== FLOATING LOGIN FAB ====== -->
    <div class="fixed bottom-28 right-3 sm:right-4 lg:bottom-8 lg:right-8 z-[100] flex flex-col items-end max-w-[calc(100vw-1.5rem)] transition-opacity duration-200" id="login-fab-root">
        <div id="login-fab-menu" class="flex flex-col items-end gap-2.5 mb-3 transition-all duration-300 ease-out origin-bottom-right opacity-0 scale-90 translate-y-4 pointer-events-none">
            @foreach ([
                [route('login.guru'),'Login Guru','fa-chalkboard-user','from-emerald-500 to-teal-600','shadow-emerald-500/30','hover:border-emerald-200'],
                [route('login.admin'),'Login Admin','fa-user-shield','from-indigo-600 to-violet-700','shadow-indigo-500/30','hover:border-indigo-200'],
                [route('siswa.login'),'Login Siswa','fa-user-graduate','from-orange-500 to-red-600','shadow-orange-500/30','hover:border-orange-200'],
                [route('parent.login'),'Login Orang Tua','fa-people-roof','from-fuchsia-600 to-pink-600','shadow-pink-500/30','hover:border-pink-200'],
            ] as $l)
            <a href="{{ $l[0] }}" class="flex items-center gap-3 pl-3 pr-5 py-2.5 bg-white/95 backdrop-blur border border-slate-100 {{ $l[5] }} rounded-2xl shadow-2xl hover:-translate-y-0.5 active:scale-95 transition group">
                <div class="w-10 h-10 bg-gradient-to-br {{ $l[3] }} rounded-xl flex items-center justify-center text-white shadow-lg {{ $l[4] }} group-hover:rotate-12 transition-transform"><i class="fa-solid {{ $l[2] }}"></i></div>
                <div class="text-left">
                    <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest leading-none mb-1">Akses Sistem</p>
                    <p class="text-[13px] font-extrabold text-slate-900 leading-none whitespace-nowrap">{{ $l[1] }}</p>
                </div>
            </a>
            @endforeach
        </div>

        <button type="button" id="login-fab-btn" aria-expanded="false" aria-haspopup="true" aria-label="Masuk akun"
            class="flex items-center gap-3 pl-3 pr-4 sm:pr-5 py-2.5 bg-gradient-to-r from-orange-500 to-red-600 text-white rounded-full shadow-2xl shadow-orange-600/40 hover:-translate-y-0.5 active:scale-95 transition select-none">
            <div class="w-9 h-9 sm:w-10 sm:h-10 bg-white/20 rounded-full flex items-center justify-center"><i id="login-fab-icon" class="fa-solid fa-right-to-bracket text-base"></i></div>
            <span class="text-[13px] sm:text-sm font-extrabold">Masuk Akun</span>
        </button>
    </div>

    <!-- ====== BOTTOM NAV (mobile & tablet) — floating pill ====== -->
    <nav class="lg:hidden fixed inset-x-0 bottom-0 z-[60] px-3 pointer-events-none"
         style="padding-bottom:calc(env(safe-area-inset-bottom) + .75rem)" aria-label="Navigasi bawah">
        <div class="pointer-events-auto mx-auto max-w-md flex items-center justify-between rounded-[1.75rem] bg-maroon-950/90 backdrop-blur-2xl border border-white/10 shadow-2xl shadow-black/40 px-2.5 py-2">

            <a href="#home" class="bn flex flex-col items-center justify-center gap-0.5 w-14 h-12 rounded-2xl text-white/50 transition-all active:scale-90">
                <i class="fa-solid fa-house text-[15px]"></i>
                <span class="text-[9px] font-bold tracking-wide">Home</span>
            </a>

            <a href="#creative-bank" class="bn flex flex-col items-center justify-center gap-0.5 w-14 h-12 rounded-2xl text-white/50 transition-all active:scale-90">
                <i class="fa-solid fa-credit-card text-[15px]"></i>
                <span class="text-[9px] font-bold tracking-wide">Bank</span>
            </a>

            <!-- Tombol tengah: Join -->
            <a href="{{ route('registrations.form') }}" aria-label="Join Us"
               class="btn-shine -mt-8 w-14 h-14 rounded-full bg-gradient-to-br from-orange-500 to-red-600 text-white flex flex-col items-center justify-center ring-4 ring-maroon-950 shadow-lg shadow-orange-600/50 active:scale-90 transition">
                <i class="fa-solid fa-user-plus text-base"></i>
                <span class="text-[8px] font-extrabold tracking-wide mt-0.5">Join</span>
            </a>

            <a href="#download" class="bn flex flex-col items-center justify-center gap-0.5 w-14 h-12 rounded-2xl text-white/50 transition-all active:scale-90">
                <i class="fa-brands fa-android text-[15px]"></i>
                <span class="text-[9px] font-bold tracking-wide">App</span>
            </a>

            <button type="button" id="more-btn" aria-expanded="false" aria-controls="more-sheet"
                    class="flex flex-col items-center justify-center gap-0.5 w-14 h-12 rounded-2xl text-white/50 transition-all active:scale-90">
                <i id="more-icon" class="fa-solid fa-grip text-[15px]"></i>
                <span class="text-[9px] font-bold tracking-wide">Menu</span>
            </button>
        </div>
    </nav>

    <!-- Sheet menu "Lainnya" -->
    <div id="more-sheet" class="lg:hidden fixed inset-x-3 z-[59] mx-auto max-w-md rounded-3xl bg-maroon-950/95 backdrop-blur-xl border border-white/10 shadow-2xl p-3 grid grid-cols-4 gap-2 opacity-0 translate-y-4 pointer-events-none transition-all duration-300"
         style="bottom:calc(env(safe-area-inset-bottom) + 5.5rem)">
        <a href="#nama-tim" class="more-a flex flex-col items-center gap-1.5 py-3 rounded-2xl bg-white/5 hover:bg-white/10 text-white/90 text-[10px] font-bold"><i class="fa-solid fa-users text-orange-400 text-base"></i>Tim</a>
        <a href="#prestasi" class="more-a flex flex-col items-center gap-1.5 py-3 rounded-2xl bg-white/5 hover:bg-white/10 text-white/90 text-[10px] font-bold"><i class="fa-solid fa-trophy text-orange-400 text-base"></i>Prestasi</a>
        <a href="#youtube" class="more-a flex flex-col items-center gap-1.5 py-3 rounded-2xl bg-white/5 hover:bg-white/10 text-white/90 text-[10px] font-bold"><i class="fa-brands fa-youtube text-orange-400 text-base"></i>Video</a>
        <a href="#sosmed" class="more-a flex flex-col items-center gap-1.5 py-3 rounded-2xl bg-white/5 hover:bg-white/10 text-white/90 text-[10px] font-bold"><i class="fa-solid fa-share-nodes text-orange-400 text-base"></i>Sosmed</a>
        <a href="{{ route('registrations.check') }}" class="col-span-4 flex items-center justify-center gap-2 py-3 rounded-2xl border border-white/15 hover:bg-white/10 text-white/90 text-xs font-bold"><i class="fa-solid fa-magnifying-glass text-orange-400"></i>Verifikasi Pendaftaran</a>
    </div>

    <!-- ====== SCRIPTS ====== -->
    <script>
        (function () {
            const canHover = window.matchMedia('(hover: hover)').matches;
            const reduce = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

            /* --- Menu mobile (drawer lama, header tersembunyi di mobile) --- */
            const menuBtn = document.getElementById('menu-btn');
            const drawer = document.getElementById('drawer');
            const menuIcon = document.getElementById('menu-icon');
            function setDrawer(open) {
                drawer.classList.toggle('opacity-0', !open);
                drawer.classList.toggle('-translate-y-3', !open);
                drawer.classList.toggle('pointer-events-none', !open);
                menuBtn.setAttribute('aria-expanded', open ? 'true' : 'false');
                menuIcon.className = open ? 'fa-solid fa-xmark' : 'fa-solid fa-bars';
            }
            menuBtn.addEventListener('click', (e) => { e.stopPropagation(); setDrawer(menuBtn.getAttribute('aria-expanded') !== 'true'); });
            drawer.querySelectorAll('a').forEach(a => a.addEventListener('click', () => setDrawer(false)));
            document.addEventListener('click', (e) => { if (!drawer.contains(e.target) && !menuBtn.contains(e.target)) setDrawer(false); });
            window.addEventListener('resize', () => { if (window.innerWidth >= 1024) setDrawer(false); });

            /* --- Navbar: bayangan lebih tegas saat scroll --- */
            const pill = document.getElementById('nav-pill');
            const bar = document.getElementById('progress');
            const onScroll = () => {
                pill.classList.toggle('bg-maroon-950/90', window.scrollY > 24);
                const h = document.documentElement.scrollHeight - window.innerHeight;
                bar.style.width = (h > 0 ? (window.scrollY / h) * 100 : 0) + '%';
            };
            onScroll(); window.addEventListener('scroll', onScroll, { passive: true });

            /* --- Scroll Reveal --- */
            const revealIO = new IntersectionObserver((entries) => {
                entries.forEach(en => { if (en.isIntersecting) { en.target.classList.add('active'); revealIO.unobserve(en.target); } });
            }, { threshold: 0.08, rootMargin: '0px 0px -40px 0px' });
            document.querySelectorAll('.reveal').forEach(el => revealIO.observe(el));

            /* --- Penanda menu aktif (navbar desktop + bottom nav) --- */
            const sections = ['home','nama-tim','creative-bank','download','prestasi','youtube','sosmed'].map(id => document.getElementById(id)).filter(Boolean);
            const navA = document.querySelectorAll('.nav-a');
            const bn = document.querySelectorAll('.bn');
            const spy = new IntersectionObserver((entries) => {
                entries.forEach(en => {
                    if (!en.isIntersecting) return;
                    const id = '#' + en.target.id;
                    navA.forEach(a => {
                        const on = a.getAttribute('href') === id;
                        a.classList.toggle('bg-white/15', on); a.classList.toggle('text-white', on);
                    });
                    bn.forEach(a => {
                        const on = a.getAttribute('href') === id;
                        a.classList.toggle('text-orange-400', on);
                        a.classList.toggle('bg-white/10', on);
                        a.classList.toggle('text-white/50', !on);
                    });
                });
            }, { rootMargin: '-45% 0px -50% 0px' });
            sections.forEach(s => spy.observe(s));

            /* --- Counter --- */
            const counterIO = new IntersectionObserver((entries) => {
                entries.forEach(en => {
                    if (!en.isIntersecting) return;
                    const el = en.target;
                    const target = parseInt(el.getAttribute('data-target')) || 0;
                    counterIO.unobserve(el);
                    if (!target) return;
                    if (reduce) { el.textContent = target.toLocaleString('id-ID'); return; }
                    let cur = 0; const inc = Math.max(1, Math.ceil(target / 60));
                    const t = setInterval(() => {
                        cur += inc;
                        if (cur >= target) { cur = target; clearInterval(t); }
                        el.textContent = cur.toLocaleString('id-ID');
                    }, 25);
                });
            }, { threshold: 0.5 });
            document.querySelectorAll('.counter').forEach(el => counterIO.observe(el));

            /* --- YouTube klik-untuk-muat --- */
            document.querySelectorAll('.yt-facade').forEach(el => {
                el.setAttribute('role', 'button'); el.setAttribute('tabindex', '0');
                const load = () => {
                    if (el.querySelector('iframe')) return;
                    const id = el.getAttribute('data-yt-id'); if (!id) return;
                    const img = el.querySelector('img');
                    const f = document.createElement('iframe');
                    f.className = 'absolute inset-0 w-full h-full';
                    f.src = 'https://www.youtube.com/embed/' + id + '?autoplay=1&rel=0';
                    f.title = img ? (img.alt || 'Video YouTube') : 'Video YouTube';
                    f.setAttribute('frameborder', '0');
                    f.setAttribute('allow', 'accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture');
                    f.setAttribute('allowfullscreen', '');
                    el.appendChild(f);
                    el.querySelectorAll('.yt-overlay').forEach(o => o.remove());
                };
                el.addEventListener('click', load);
                el.addEventListener('keydown', (ev) => { if (ev.key === 'Enter' || ev.key === ' ') { ev.preventDefault(); load(); } });
            });

            /* --- Tilt 3D (hanya perangkat dengan mouse) --- */
            if (canHover && !reduce) {
                const scene = document.getElementById('hero-photo-scene');
                const tilt = document.getElementById('hero-photo-tilt');
                if (scene && tilt) {
                    scene.addEventListener('mousemove', (e) => {
                        const r = scene.getBoundingClientRect();
                        const cx = r.width / 2, cy = r.height / 2;
                        tilt.style.transform = `rotateY(${((e.clientX - r.left - cx) / cx) * 12}deg) rotateX(${-((e.clientY - r.top - cy) / cy) * 10}deg)`;
                    });
                    scene.addEventListener('mouseleave', () => { tilt.style.transform = 'rotateY(0) rotateX(0)'; });
                }
                const wrap = document.getElementById('card-3d-wrap');
                const inner = document.getElementById('card-3d-inner');
                const glare = document.getElementById('card-glare');
                if (wrap && inner) {
                    wrap.addEventListener('mousemove', (e) => {
                        const r = wrap.getBoundingClientRect();
                        const x = e.clientX - r.left, y = e.clientY - r.top, cx = r.width / 2, cy = r.height / 2;
                        inner.style.transform = `rotateY(${((x - cx) / cx) * 16}deg) rotateX(${-((y - cy) / cy) * 16}deg) scale(1.03)`;
                        if (glare) glare.style.background = `radial-gradient(circle at ${x}px ${y}px, rgba(255,255,255,.3), transparent 55%)`;
                    });
                    wrap.addEventListener('mouseleave', () => {
                        inner.style.transform = 'rotateY(0) rotateX(0) scale(1)';
                        if (glare) glare.style.background = 'none';
                    });
                }
            }

            /* --- FAB Login --- */
            const root = document.getElementById('login-fab-root');
            const btn = document.getElementById('login-fab-btn');
            const menu = document.getElementById('login-fab-menu');
            const icon = document.getElementById('login-fab-icon');
            let open = false;
            function toggle(state) {
                open = typeof state === 'boolean' ? state : !open;
                menu.classList.toggle('opacity-0', !open);
                menu.classList.toggle('scale-90', !open);
                menu.classList.toggle('translate-y-4', !open);
                menu.classList.toggle('pointer-events-none', !open);
                icon.className = 'fa-solid text-base ' + (open ? 'fa-xmark' : 'fa-right-to-bracket');
                btn.setAttribute('aria-expanded', open ? 'true' : 'false');
            }
            btn.addEventListener('click', (e) => { e.stopPropagation(); toggle(); });
            document.addEventListener('click', (e) => { if (open && !root.contains(e.target)) toggle(false); });

            /* --- Sheet menu bawah (mobile) --- */
            const moreBtn = document.getElementById('more-btn');
            const moreSheet = document.getElementById('more-sheet');
            const moreIcon = document.getElementById('more-icon');
            function setMore(openState) {
                moreSheet.classList.toggle('opacity-0', !openState);
                moreSheet.classList.toggle('translate-y-4', !openState);
                moreSheet.classList.toggle('pointer-events-none', !openState);
                moreBtn.setAttribute('aria-expanded', openState ? 'true' : 'false');
                moreBtn.classList.toggle('text-orange-400', openState);
                moreBtn.classList.toggle('bg-white/10', openState);
                moreBtn.classList.toggle('text-white/50', !openState);
                moreIcon.className = 'text-[15px] fa-solid ' + (openState ? 'fa-xmark' : 'fa-grip');
                // sembunyikan FAB login saat sheet terbuka agar tidak bertumpuk
                root.classList.toggle('opacity-0', openState);
                root.classList.toggle('pointer-events-none', openState);
                if (openState) toggle(false);
            }
            moreBtn.addEventListener('click', (e) => { e.stopPropagation(); setMore(moreBtn.getAttribute('aria-expanded') !== 'true'); });
            moreSheet.querySelectorAll('a').forEach(a => a.addEventListener('click', () => setMore(false)));
            document.addEventListener('click', (e) => { if (!moreSheet.contains(e.target) && !moreBtn.contains(e.target)) setMore(false); });
            window.addEventListener('resize', () => { if (window.innerWidth >= 1024) setMore(false); });

            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape') { if (open) toggle(false); setDrawer(false); setMore(false); }
            });
        })();
    </script>
</body>
</html>