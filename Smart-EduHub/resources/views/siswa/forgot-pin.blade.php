<!DOCTYPE html>
<html class="light" lang="id">
<head>
    <meta charset="utf-8"/>
    <meta content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover" name="viewport"/>
    <meta name="theme-color" content="#C2410C"/>
    <title>Lupa PIN - Smart Axioo</title>
    <script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: {
                            50: '#FFF7ED', 100: '#FFEDD5', 200: '#FED7AA', 300: '#FDBA74',
                            400: '#FB923C', 500: '#F97316', 600: '#EA580C', 700: '#C2410C',
                            800: '#9A3412', 900: '#7C2D12', 950: '#431407'
                        }
                    }
                }
            }
        }
    </script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&display=swap" rel="stylesheet"/>
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            -webkit-tap-highlight-color: transparent;
        }
        .bg-orange-grad {
            background: linear-gradient(160deg, #FDBA74 0%, #F97316 35%, #C2410C 75%, #7C2D12 100%);
        }
        .bg-orange-grad-desktop {
            background: linear-gradient(145deg, rgba(67,20,7,.94) 0%, rgba(154,52,18,.86) 45%, rgba(249,115,22,.78) 100%);
        }
        .btn-grad {
            background: linear-gradient(135deg, #FB923C 0%, #EA580C 55%, #9A3412 100%);
        }
        .hero-photo {
            background-image: url('https://images.unsplash.com/photo-1523240795612-9a054b0db644?q=80&w=1470&auto=format&fit=crop');
            background-size: cover;
            background-position: center;
        }
        .sheet-in { animation: sheet-in .55s cubic-bezier(.16,1,.3,1) both; }
        @keyframes sheet-in {
            from { opacity: 0; transform: translateY(48px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        .field {
            font-size: 16px; /* cegah zoom otomatis di iOS */
        }
        ::-webkit-scrollbar { width: 0; background: transparent; }
        @media (prefers-reduced-motion: reduce) { .sheet-in { animation: none; } }
    </style>
</head>
<body class="text-slate-900 min-h-screen lg:flex bg-brand-950">

    <!-- ============ DESKTOP: panel brand ============ -->
    <section class="hidden lg:flex flex-1 relative hero-photo">
        <div class="absolute inset-0 bg-orange-grad-desktop"></div>
        <div class="absolute -top-24 -left-24 w-96 h-96 bg-brand-300/20 rounded-full blur-3xl pointer-events-none"></div>
        <div class="absolute -bottom-24 right-12 w-80 h-80 bg-white/10 rounded-full blur-3xl pointer-events-none"></div>

        <div class="relative z-10 p-16 flex flex-col justify-between h-full text-white">
            <img src="{{ asset('images/SEH.png') }}" alt="Smart Axioo Logo" class="h-12 w-auto object-contain self-start">

            <div class="max-w-md pb-12 space-y-5">
                <span class="px-4 py-2 bg-white/15 backdrop-blur rounded-full text-sm font-bold inline-flex items-center gap-2 border border-white/20">
                    <span class="material-symbols-outlined text-base" style="font-variation-settings:'FILL' 1;">lock_reset</span>
                    Lupa PIN
                </span>
                <h2 class="text-5xl font-extrabold leading-tight tracking-tight">Atur ulang PIN Anda dengan cepat dan aman</h2>
                <p class="text-base text-brand-100/90 leading-relaxed font-medium">Verifikasi data diri Anda, atau minta bantuan admin sekolah untuk mengembalikan PIN ke default.</p>
            </div>
        </div>
    </section>

    <!-- ============ MAIN (mobile app + desktop form) ============ -->
    <main class="w-full lg:w-[520px] xl:w-[600px] min-h-screen flex flex-col relative bg-orange-grad lg:bg-none lg:bg-white overflow-hidden">

        <!-- dekorasi mobile -->
        <div class="lg:hidden absolute -top-20 -right-16 w-64 h-64 bg-white/20 rounded-full blur-3xl pointer-events-none"></div>
        <div class="lg:hidden absolute top-40 -left-20 w-56 h-56 bg-brand-900/30 rounded-full blur-3xl pointer-events-none"></div>

        <!-- App bar (mobile) -->
        <header class="lg:hidden relative z-10 px-5 pt-[max(1.25rem,env(safe-area-inset-top))] pb-6 text-white">
            <div class="flex items-center justify-between">
                <a href="{{ route('siswa.login') }}" aria-label="Kembali ke login"
                   class="w-11 h-11 rounded-2xl bg-white/20 backdrop-blur border border-white/25 flex items-center justify-center active:scale-90 transition">
                    <span class="material-symbols-outlined">arrow_back</span>
                </a>
                <img src="{{ asset('images/SEH.png') }}" alt="Smart Axioo Logo" class="h-8 w-auto object-contain">
                <span class="w-11"></span>
            </div>

            <div class="mt-8 flex items-center gap-4">
                <div class="w-16 h-16 shrink-0 rounded-3xl bg-white/20 backdrop-blur border border-white/30 shadow-lg flex items-center justify-center">
                    <span class="material-symbols-outlined text-4xl" style="font-variation-settings:'FILL' 1;">lock_reset</span>
                </div>
                <div>
                    <h1 class="text-2xl font-extrabold tracking-tight leading-tight">Lupa PIN?</h1>
                    <p class="text-sm text-brand-50/90 font-medium mt-0.5">Verifikasi data untuk reset PIN</p>
                </div>
            </div>
        </header>

        <!-- Bottom sheet (mobile) / form biasa (desktop) -->
        <section class="sheet-in relative z-10 flex-1 flex flex-col bg-white rounded-t-[2.25rem] lg:rounded-none shadow-[0_-12px_40px_rgba(67,20,7,.35)] lg:shadow-none px-5 sm:px-8 lg:px-12 pt-3 lg:pt-0 lg:justify-center">

            <!-- handle -->
            <div class="lg:hidden mx-auto w-11 h-1.5 rounded-full bg-slate-200 mb-5"></div>

            <div class="w-full max-w-[420px] mx-auto flex-1 flex flex-col">

                <!-- Judul desktop -->
                <div class="hidden lg:block mb-8">
                    <span class="px-3 py-1.5 bg-brand-50 text-brand-700 rounded-full text-xs font-bold">Portal Siswa</span>
                    <h1 class="text-4xl font-extrabold text-slate-900 tracking-tight mt-4 mb-2">Lupa PIN?</h1>
                    <p class="text-slate-500 text-sm font-medium">Verifikasi data diri Anda untuk mereset PIN secara mandiri.</p>
                </div>

                <p class="lg:hidden text-sm text-slate-500 font-medium mb-5">Isi data di bawah ini untuk mereset PIN secara mandiri.</p>

                @if($errors->any())
                <div class="mb-5 p-4 bg-rose-50 border border-rose-100 rounded-2xl text-rose-800 text-sm font-semibold flex items-start gap-3">
                    <span class="material-symbols-outlined text-rose-500 text-xl">error</span>
                    <span>{{ $errors->first() }}</span>
                </div>
                @endif
                @if(session('success'))
                <div class="mb-5 p-4 bg-emerald-50 border border-emerald-100 rounded-2xl text-emerald-800 text-sm font-semibold flex items-start gap-3">
                    <span class="material-symbols-outlined text-emerald-500 text-xl">check_circle</span>
                    <span>{{ session('success') }}</span>
                </div>
                @endif

                <form action="{{ route('siswa.forgot-pin.process') }}" method="POST" class="flex-1 flex flex-col gap-5">
                    @csrf

                    <!-- Identifier -->
                    <div class="space-y-2">
                        <label class="text-sm font-bold text-slate-700 ml-1" for="identifier">Username / NISN</label>
                        <div class="relative group">
                            <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-brand-400 group-focus-within:text-brand-600 transition-colors">badge</span>
                            <input class="field w-full pl-12 pr-4 py-4 bg-brand-50/60 border border-brand-100 rounded-2xl font-semibold focus:outline-none focus:ring-4 focus:ring-brand-500/15 focus:border-brand-500 focus:bg-white transition-all placeholder:text-slate-400 placeholder:font-medium @error('identifier') border-rose-400 focus:ring-rose-500/10 focus:border-rose-500 @enderror"
                                   id="identifier" name="identifier" value="{{ old('identifier') }}" placeholder="Masukkan NISN / username" type="text" autocomplete="username" required/>
                        </div>
                        @error('identifier')
                        <p class="text-rose-500 text-xs font-semibold ml-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Verification Field -->
                    <div class="space-y-2">
                        <label class="text-sm font-bold text-slate-700 ml-1" for="verification_field">Data verifikasi</label>
                        <div class="relative group">
                            <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-brand-400 group-focus-within:text-brand-600 transition-colors pointer-events-none">verified_user</span>
                            <select class="field w-full pl-12 pr-11 py-4 bg-brand-50/60 border border-brand-100 rounded-2xl font-semibold focus:outline-none focus:ring-4 focus:ring-brand-500/15 focus:border-brand-500 focus:bg-white transition-all appearance-none cursor-pointer"
                                    id="verification_field" name="verification_field" required>
                                <option value="email" {{ old('verification_field') == 'email' ? 'selected' : '' }}>Email</option>
                                <option value="phone" {{ old('verification_field') == 'phone' ? 'selected' : '' }}>No. HP / No. HP Orang Tua</option>
                                <option value="mother_name" {{ old('verification_field') == 'mother_name' ? 'selected' : '' }}>Nama Ibu Kandung</option>
                                <option value="dateofbirth" {{ old('verification_field') == 'dateofbirth' ? 'selected' : '' }}>Tanggal Lahir (YYYY-MM-DD)</option>
                            </select>
                            <span class="material-symbols-outlined absolute right-4 top-1/2 -translate-y-1/2 text-brand-400 pointer-events-none">expand_more</span>
                        </div>
                    </div>

                    <!-- Verification Value -->
                    <div class="space-y-2">
                        <label class="text-sm font-bold text-slate-700 ml-1" for="verification_value">Isi data verifikasi</label>
                        <div class="relative group">
                            <span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-brand-400 group-focus-within:text-brand-600 transition-colors">edit_note</span>
                            <input class="field w-full pl-12 pr-4 py-4 bg-brand-50/60 border border-brand-100 rounded-2xl font-semibold focus:outline-none focus:ring-4 focus:ring-brand-500/15 focus:border-brand-500 focus:bg-white transition-all placeholder:text-slate-400 placeholder:font-medium @error('verification_value') border-rose-400 focus:ring-rose-500/10 focus:border-rose-500 @enderror"
                                   id="verification_value" name="verification_value" value="{{ old('verification_value') }}" placeholder="Sesuai pilihan di atas" type="text" required/>
                        </div>
                        @error('verification_value')
                        <p class="text-rose-500 text-xs font-semibold ml-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Panduan admin -->
                    <div class="rounded-2xl p-4 border border-brand-200 bg-gradient-to-br from-brand-50 to-brand-100/70">
                        <h3 class="text-sm font-extrabold text-brand-800 mb-3 inline-flex items-center gap-2">
                            <span class="w-8 h-8 rounded-xl btn-grad text-white flex items-center justify-center">
                                <span class="material-symbols-outlined text-lg" style="font-variation-settings:'FILL' 1;">support_agent</span>
                            </span>
                            Atau minta bantuan admin
                        </h3>
                        <ol class="list-decimal list-inside space-y-1.5 text-[13px] text-brand-900/80 leading-relaxed font-medium">
                            <li>Hubungi wali kelas, BK, atau staf sekolah</li>
                            <li>Berikan NIS atau username Anda untuk verifikasi</li>
                            <li>Admin akan mereset PIN ke default: <code class="bg-brand-200 px-1.5 py-0.5 rounded-md font-bold text-brand-800">123456</code></li>
                        </ol>
                    </div>

                    <!-- Aksi (menempel di bawah pada mobile) -->
                    <div class="mt-auto sticky bottom-0 -mx-5 sm:-mx-8 lg:mx-0 px-5 sm:px-8 lg:px-0 pt-3 pb-[max(1rem,env(safe-area-inset-bottom))] lg:pb-0 bg-gradient-to-t from-white via-white to-white/0 space-y-3">
                        <button type="submit"
                                class="btn-grad w-full text-white font-extrabold py-4 rounded-2xl shadow-[0_10px_24px_rgba(234,88,12,.4)] active:scale-[0.98] hover:brightness-110 transition-all text-[15px] inline-flex items-center justify-center gap-2">
                            <span class="material-symbols-outlined text-xl">lock_reset</span>
                            Verifikasi dan reset PIN
                        </button>
                        <a href="{{ route('siswa.login') }}"
                           class="hidden lg:block w-full bg-white border border-brand-200 text-brand-700 font-bold py-4 rounded-2xl text-center hover:bg-brand-50 transition-all">
                            Kembali ke login
                        </a>
                        <p class="lg:hidden text-center text-[11px] text-slate-400 font-medium">&copy; 2026 Smart Axioo. All rights reserved.</p>
                    </div>
                </form>

                <p class="hidden lg:block text-center text-[11px] text-slate-400 font-medium mt-8">&copy; 2026 Smart Axioo. All rights reserved.</p>
            </div>
        </section>
    </main>
</body>
</html>