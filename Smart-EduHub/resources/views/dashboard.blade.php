@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<!-- Gradient Header Block (Fintech Style) - Full Width on Mobile, Rounded Card on Desktop -->
<div id="pageHero" class="relative bg-gradient-to-br from-[#F6863B] via-[#EB4325] to-[#8A1E1E] -mx-4 pt-8 pb-14 px-6 text-white overflow-hidden shadow-2xl shadow-orange-950/10 rounded-b-[2.5rem] lg:mx-0 lg:rounded-[2.5rem] lg:pt-6 lg:pb-8">
    <!-- Abstract Circular Glow Overlays -->
    <div class="absolute -top-12 -right-12 w-48 h-48 bg-white/5 rounded-full pointer-events-none"></div>
    <div class="absolute top-24 -left-12 w-40 h-40 bg-white/5 rounded-full pointer-events-none"></div>
    <div class="absolute -bottom-8 right-24 w-28 h-28 bg-white/5 rounded-full pointer-events-none"></div>

    <div class="relative z-10 space-y-5">
        <!-- Branding & Stat Pill -->
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-full bg-white/10 flex items-center justify-center backdrop-blur-sm">
                    <span class="material-symbols-outlined text-white text-lg font-bold" style="font-variation-settings: 'FILL' 1;">school</span>
                </div>
                <span class="text-base font-black tracking-tight">SEH</span>
            </div>
            <!-- Total Students Pill -->
            <div class="bg-white text-slate-800 px-3 py-1.5 rounded-full text-xs font-extrabold flex items-center gap-1 shadow-sm">
                <span class="material-symbols-outlined text-amber-500 text-sm" style="font-variation-settings: 'FILL' 1;">groups</span>
                <span>{{ number_format($totalStudents) }} Siswa</span>
            </div>
        </div>

        <!-- Title Center -->
        <div class="text-center py-1 space-y-1">
            <p class="text-[10px] text-orange-200/80 font-bold uppercase tracking-widest">Dashboard Admin</p>
            <h2 class="text-2xl lg:text-3xl font-black tracking-tight">District Overview</h2>
            <p class="text-xs text-orange-100/70 font-medium">Monitoring statistik & aktivitas siswa secara real-time.</p>
        </div>
    </div>
</div>

<!-- Action Buttons -->
<div class="flex flex-col items-stretch gap-3 w-full mt-5 mb-6 md:mb-8">
    <div class="relative">
        <button type="button" class="w-full flex items-center justify-center gap-2 px-4 py-2.5 bg-white border border-slate-200 rounded-xl text-xs font-semibold text-slate-700 hover:bg-slate-50 transition-all shadow-sm">
            <span class="material-symbols-outlined text-[18px]">calendar_today</span>
            {{ __('Academic Year') }} {{ date('Y') }}/{{ date('Y') + 1 }}
        </button>
    </div>
    <button onclick="window.print()" class="w-full flex items-center justify-center gap-2 px-5 py-2.5 bg-orange-600 text-white rounded-xl text-xs font-bold shadow-lg shadow-orange-200 hover:bg-orange-700 hover:translate-y-[-1px] transition-all active:scale-95">
        <span class="material-symbols-outlined text-[18px]">print</span>
        {{ __('Print Overview') }}
    </button>
</div>

{{-- ============================================================
     BANNER PROMO UPGRADE (BARU) - modal beranimasi setelah login, dikontrol Google Sheet
     Sheet: share "Anyone with the link - Viewer", tab bernama "Banner"
     Baris 1 = header: active | id | badge | title | description |
                       button_text | button_url | image
     Baris 2 dst = satu baris satu banner
     ============================================================ --}}
@php
    // ====== PENGATURAN BANNER (ubah di sini) ======
    $adSheetUrl   = 'https://docs.google.com/spreadsheets/d/1KQk_N-lvL7edMS7OdPCwLsCymOjeO2kTip4KXEMPRC0/edit?usp=sharing'; // kosongkan ('') untuk pakai default
    // --- Jalur A (disarankan): Google Apps Script. Isi dua baris ini ---
    $adScriptUrl  = 'https://script.google.com/macros/s/AKfycbwmRETssCAkD8m-eNMZ2_ph8XmaSMKYo3C6o3YcZk_LoUqm-nXQ44udH49J_Zo-xN8/exec';   // URL web app, contoh: https://script.google.com/macros/s/XXXX/exec
    $adToken      = 'seh-7Kq2xP9mLd4Wz8Rb';   // API token dari menu "Banner SEH" di Sheet
    // --- Jalur B (cadangan): Sheet publik, dipakai jika $adScriptUrl kosong ---
    $adSheetTab   = 'Banner';
    $adCacheMin   = 5;   // menit cache
    $adMax        = 5;   // maksimal banner yang ditampilkan
    $adSlideSec   = 6;   // detik per slide (otomatis geser)
    $adDefault = [[
        'active'      => '',
        'id'          => '',
        'badge'       => '',
        'title'       => '',
        'description' => '',
        'button_text' => '',
        'button_url'  => '',
        'image'       => '',
    ]];
    // ==============================================

    $adDriveId = function (?string $url) {
        if (!$url) return null;
        if (preg_match('#/d/([a-zA-Z0-9_-]+)#', $url, $m)) return $m[1];
        if (preg_match('#[?&]id=([a-zA-Z0-9_-]+)#', $url, $m)) return $m[1];
        return null;
    };

    $adFetch = function () use ($adSheetUrl, $adSheetTab, $adDriveId, $adScriptUrl, $adToken) {
        // Jalur A: Google Apps Script (JSON)
        if ($adScriptUrl) {
            try {
                $res = \Illuminate\Support\Facades\Http::timeout(8)->get($adScriptUrl, ['token' => $adToken]);
                $rows = $res->successful() ? $res->json('banners') : null;
                if (is_array($rows) && count($rows)) {
                    \Illuminate\Support\Facades\Cache::forever('ads.banners.last_good', $rows);
                    return $rows;
                }
            } catch (\Throwable $e) {}
            return null;
        }

        // Jalur B: Sheet publik (CSV)
        $sheetId = $adDriveId($adSheetUrl);
        if (!$sheetId) return null;
        try {
            $res = \Illuminate\Support\Facades\Http::timeout(5)->get(
                "https://docs.google.com/spreadsheets/d/{$sheetId}/gviz/tq",
                ['tqx' => 'out:csv', 'sheet' => $adSheetTab, 'headers' => 1]
            );
            if (!$res->successful()) return null;

            $csv = preg_replace('/^\xEF\xBB\xBF/', '', $res->body());
            $stream = fopen('php://memory', 'r+');
            fwrite($stream, $csv);
            rewind($stream);

            $allowed = ['active', 'id', 'badge', 'title', 'description', 'button_text', 'button_url', 'image'];
            $header = null;
            $rows = [];
            while (($row = fgetcsv($stream)) !== false) {
                if ($header === null) {
                    // cari baris header (yang berisi kolom "title")
                    $lower = array_map(fn ($c) => strtolower(trim((string) $c)), $row);
                    if (in_array('title', $lower, true)) $header = $lower;
                    continue;
                }
                $item = [];
                foreach ($header as $i => $col) {
                    if (in_array($col, $allowed, true)) $item[$col] = trim((string) ($row[$i] ?? ''));
                }
                if (!empty($item['title'])) $rows[] = $item;
            }
            fclose($stream);

            if (empty($rows)) return null;
            \Illuminate\Support\Facades\Cache::forever('ads.banners.last_good', $rows);
            return $rows;
        } catch (\Throwable $e) {
            return null;
        }
    };

    // Ambil data: Sheet (cache) -> data terakhir yang berhasil -> default
    if ($adScriptUrl || $adSheetUrl) {
        $adRows = \Illuminate\Support\Facades\Cache::remember('ads.banners', now()->addMinutes($adCacheMin), $adFetch);
        if (!$adRows) {
            \Illuminate\Support\Facades\Cache::forget('ads.banners');
            $adRows = \Illuminate\Support\Facades\Cache::get('ads.banners.last_good') ?? $adDefault;
        }
    } else {
        $adRows = $adDefault;
    }

    // Validasi & normalisasi tiap banner
    $ads = [];
    foreach ($adRows as $adRaw) {
        $adTitle = trim((string) ($adRaw['title'] ?? ''));
        if ($adTitle === '' || !filter_var($adRaw['active'] ?? 'TRUE', FILTER_VALIDATE_BOOLEAN)) continue;

        $adBtnUrl = (string) ($adRaw['button_url'] ?? '#');
        $adBtnUrl = preg_match('#^(https?://|/|\#)#i', $adBtnUrl) ? $adBtnUrl : '#';

        $adImg = $adRaw['image'] ?? '';
        if ($adImg && str_contains($adImg, 'drive.google.com')) {
            $adImgId = $adDriveId($adImg);
            $adImg = $adImgId ? "https://drive.google.com/thumbnail?id={$adImgId}&sz=w1200" : null;
        } elseif ($adImg && !str_starts_with($adImg, 'https://')) {
            $adImg = null;
        }

        $ads[] = [
            'id'          => (string) (($adRaw['id'] ?? '') !== '' ? $adRaw['id'] : md5($adTitle)),
            'badge'       => (string) (($adRaw['badge'] ?? '') !== '' ? $adRaw['badge'] : 'Upgrade'),
            'title'       => $adTitle,
            'description' => (string) ($adRaw['description'] ?? ''),
            'button_text' => (string) (($adRaw['button_text'] ?? '') !== '' ? $adRaw['button_text'] : 'Upgrade Sekarang'),
            'button_url'  => $adBtnUrl,
            'image'       => $adImg ?: null,
        ];
        if (count($ads) >= $adMax) break;
    }
@endphp

@if(count($ads) > 0)
<style>
    @@keyframes adBackdropIn { from { opacity: 0; } to { opacity: 1; } }
    @@keyframes adBackdropOut { from { opacity: 1; } to { opacity: 0; } }
    @@keyframes adCardIn {
        0%   { opacity: 0; transform: translateY(48px) scale(.9); }
        60%  { opacity: 1; transform: translateY(-8px) scale(1.02); }
        100% { opacity: 1; transform: translateY(0) scale(1); }
    }
    @@keyframes adCardOut {
        from { opacity: 1; transform: translateY(0) scale(1); }
        to   { opacity: 0; transform: translateY(24px) scale(.95); }
    }
    @@keyframes adFloat {
        0%, 100% { transform: translateY(0) rotate(-4deg); }
        50%      { transform: translateY(-10px) rotate(4deg); }
    }
    @@keyframes adShine {
        0%   { transform: translateX(-120%) skewX(-20deg); }
        60%, 100% { transform: translateX(260%) skewX(-20deg); }
    }

    #upgradeAdModal.ad-open .ad-backdrop  { animation: adBackdropIn .35s ease-out both; }
    #upgradeAdModal.ad-open .ad-card      { animation: adCardIn .6s cubic-bezier(.22,1,.36,1) both; }
    #upgradeAdModal.ad-closing .ad-backdrop { animation: adBackdropOut .25s ease-in both; }
    #upgradeAdModal.ad-closing .ad-card     { animation: adCardOut .25s ease-in both; }

    #upgradeAdModal .ad-float { animation: adFloat 3.2s ease-in-out infinite; }
    #upgradeAdModal .ad-cta   { position: relative; overflow: hidden; }
    #upgradeAdModal .ad-cta::after {
        content: ''; position: absolute; top: 0; bottom: 0; width: 40%;
        background: linear-gradient(90deg, transparent, rgba(255,255,255,.45), transparent);
        animation: adShine 2.8s ease-in-out infinite;
    }

    #upgradeAdModal .ad-stagger { opacity: 0; transform: translateY(14px); transition: opacity .5s ease, transform .5s ease; }
    #upgradeAdModal .ad-slide.ad-active .ad-stagger { opacity: 1; transform: none; }
    #upgradeAdModal .ad-slide.ad-active .ad-stagger:nth-child(1) { transition-delay: .25s; }
    #upgradeAdModal .ad-slide.ad-active .ad-stagger:nth-child(2) { transition-delay: .35s; }
    #upgradeAdModal .ad-slide.ad-active .ad-stagger:nth-child(3) { transition-delay: .45s; }
    #upgradeAdModal .ad-slide.ad-active .ad-stagger:nth-child(4) { transition-delay: .55s; }

    @@media (prefers-reduced-motion: reduce) {
        #upgradeAdModal * { animation: none !important; transition: none !important; }
        #upgradeAdModal .ad-stagger { opacity: 1; transform: none; }
    }
</style>

<div id="upgradeAdModal"
     class="hidden fixed inset-0 items-center justify-center p-4"
     style="z-index: 9999;"
     role="dialog" aria-modal="true" aria-label="Promo upgrade aplikasi"
     data-session="{{ hash('sha256', session()->getId()) }}"
     data-interval="{{ (int) $adSlideSec }}">

    <div class="ad-backdrop absolute inset-0 bg-slate-900/70 backdrop-blur-sm"></div>

    <div class="ad-card relative w-full max-w-md bg-white rounded-[2rem] shadow-2xl overflow-hidden">

        <!-- Tombol tutup -->
        <button type="button" id="upgradeAdClose" aria-label="Tutup"
                class="absolute top-3 right-3 z-30 w-9 h-9 rounded-full bg-black/25 hover:bg-black/40 text-white backdrop-blur flex items-center justify-center transition-colors">
            <span class="material-symbols-outlined text-[20px]">close</span>
        </button>

        <!-- Slide -->
        <div class="overflow-hidden">
            <div id="upgradeAdTrack" class="flex transition-transform duration-500 ease-out">
                @foreach($ads as $ad)
                <div class="ad-slide w-full flex-shrink-0" data-ad-id="{{ $ad['id'] }}">
                    @if($ad['image'])
                    <div class="relative h-48 bg-slate-100 overflow-hidden">
                        <img src="{{ $ad['image'] }}" alt="{{ $ad['title'] }}" loading="lazy" referrerpolicy="no-referrer"
                             onerror="this.parentElement.style.display='none'"
                             class="w-full h-full object-cover">
                        <div class="absolute inset-0 bg-gradient-to-t from-black/30 to-transparent"></div>
                    </div>
                    @else
                    <div class="relative h-40 bg-gradient-to-br from-[#F6863B] via-[#EB4325] to-[#8A1E1E] flex items-center justify-center overflow-hidden">
                        <div class="absolute -top-10 -right-10 w-40 h-40 bg-white/10 rounded-full"></div>
                        <div class="absolute -bottom-12 -left-8 w-36 h-36 bg-white/10 rounded-full"></div>
                        <span class="ad-float material-symbols-outlined text-white text-[72px]" style="font-variation-settings: 'FILL' 1;">rocket_launch</span>
                    </div>
                    @endif

                    <div class="p-6 pb-4 text-center">
                        <div class="ad-stagger">
                            <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full bg-orange-50 text-orange-600 text-[10px] font-black uppercase tracking-wider">
                                <span class="material-symbols-outlined text-[13px]" style="font-variation-settings: 'FILL' 1;">workspace_premium</span>
                                {{ $ad['badge'] }}
                            </span>
                        </div>
                        <h3 class="ad-stagger mt-3 text-xl md:text-2xl font-black text-slate-900 tracking-tight">{{ $ad['title'] }}</h3>
                        <p class="ad-stagger mt-2 text-sm text-slate-500 leading-relaxed">{{ $ad['description'] }}</p>
                        <div class="ad-stagger mt-5">
                            <a href="{{ $ad['button_url'] }}" target="_blank" rel="noopener noreferrer"
                               class="ad-cta w-full inline-flex items-center justify-center gap-2 px-5 py-3.5 bg-orange-600 hover:bg-orange-700 text-white rounded-xl text-sm font-bold shadow-lg shadow-orange-200 transition-colors">
                                <span class="material-symbols-outlined text-[18px]">rocket_launch</span>
                                {{ $ad['button_text'] }}
                            </a>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <!-- Navigasi & footer -->
        <div class="px-6 pb-5 text-center">
            <div id="upgradeAdNav" class="flex items-center justify-center gap-3 mb-3">
                <button type="button" id="upgradeAdPrev" aria-label="Sebelumnya"
                        class="w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-600 flex items-center justify-center transition-colors">
                    <span class="material-symbols-outlined text-[18px]">chevron_left</span>
                </button>
                <div id="upgradeAdDots" class="flex items-center gap-1.5"></div>
                <button type="button" id="upgradeAdNext" aria-label="Berikutnya"
                        class="w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-600 flex items-center justify-center transition-colors">
                    <span class="material-symbols-outlined text-[18px]">chevron_right</span>
                </button>
            </div>

            <label class="inline-flex items-center gap-2 text-xs text-slate-400 cursor-pointer select-none">
                <input type="checkbox" id="upgradeAdNever" class="rounded border-slate-300 text-orange-600 focus:ring-orange-500">
                Jangan tampilkan lagi (2 jam)
            </label>
            <div>
                <button type="button" id="upgradeAdLater" class="mt-2 text-xs font-bold text-slate-500 hover:text-slate-800 transition-colors">Nanti saja</button>
            </div>
        </div>
    </div>
</div>

<script>
    (function () {
        const modal = document.getElementById('upgradeAdModal');
        if (!modal) return;

        const track = document.getElementById('upgradeAdTrack');
        const dots  = document.getElementById('upgradeAdDots');
        const nav   = document.getElementById('upgradeAdNav');
        const never = document.getElementById('upgradeAdNever');
        const SESSION_KEY = 'ad_modal_session';
        const DISMISS_MS  = 2 * 60 * 60 * 1000;   // "Jangan tampilkan lagi" berlaku 2 jam
        const session = modal.dataset.session;

        const ls = {
            get: (k) => { try { return localStorage.getItem(k); } catch (e) { return null; } },
            set: (k, v) => { try { localStorage.setItem(k, v); } catch (e) {} }
        };

        // Banner dianggap "disembunyikan" hanya jika ditandai kurang dari 2 jam lalu
        function isDismissed(id) {
            const t = parseInt(ls.get('ad_dismissed_' + id), 10);
            if (!t || isNaN(t) || (Date.now() - t) >= DISMISS_MS) {
                try { localStorage.removeItem('ad_dismissed_' + id); } catch (e) {}
                return false;
            }
            return true;
        }

        // Sudah tampil di sesi login ini? -> jangan tampil lagi (muncul lagi setelah login berikutnya)
        if (ls.get(SESSION_KEY) === session) { modal.remove(); return; }

        // Buang banner yang pernah ditandai "jangan tampilkan lagi"
        track.querySelectorAll('.ad-slide').forEach(function (s) {
            if (isDismissed(s.dataset.adId)) s.remove();
        });

        const slides = () => track.querySelectorAll('.ad-slide');
        if (slides().length === 0) { modal.remove(); return; }

        let index = 0, timer = null, isOpen = false;

        function render() {
            const list = slides();
            const n = list.length;
            if (index >= n) index = 0;
            track.style.transform = 'translateX(-' + (index * 100) + '%)';
            list.forEach(function (s, i) { s.classList.toggle('ad-active', i === index); });

            dots.innerHTML = '';
            for (let i = 0; i < n; i++) {
                const d = document.createElement('span');
                d.className = 'h-1.5 rounded-full transition-all duration-300 ' + (i === index ? 'w-5 bg-orange-600' : 'w-1.5 bg-slate-300');
                dots.appendChild(d);
            }
            nav.style.display = n > 1 ? 'flex' : 'none';
        }

        function go(i) { const n = slides().length; index = (i + n) % n; render(); }
        function stop() { if (timer) { clearInterval(timer); timer = null; } }
        function start() {
            stop();
            const sec = parseInt(modal.dataset.interval, 10) || 6;
            if (slides().length > 1) timer = setInterval(function () { go(index + 1); }, sec * 1000);
        }
        function userNav(i) { stop(); go(i); }

        function open() {
            if (isOpen) return;
            isOpen = true;
            ls.set(SESSION_KEY, session);
            document.body.style.overflow = 'hidden';
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            void modal.offsetWidth;            // paksa reflow supaya animasi berjalan
            modal.classList.add('ad-open');
            render();
            start();
            const closeBtn = document.getElementById('upgradeAdClose');
            if (closeBtn) closeBtn.focus({ preventScroll: true });
        }

        function close() {
            if (!isOpen) return;
            isOpen = false;
            stop();
            if (never.checked) {
                slides().forEach(function (s) { ls.set('ad_dismissed_' + s.dataset.adId, String(Date.now())); });
            }
            modal.classList.remove('ad-open');
            modal.classList.add('ad-closing');
            setTimeout(function () {
                modal.remove();
                document.body.style.overflow = '';
            }, 260);
        }

        document.getElementById('upgradeAdClose').addEventListener('click', close);
        document.getElementById('upgradeAdLater').addEventListener('click', close);
        modal.querySelector('.ad-backdrop').addEventListener('click', close);
        document.getElementById('upgradeAdPrev').addEventListener('click', function () { userNav(index - 1); });
        document.getElementById('upgradeAdNext').addEventListener('click', function () { userNav(index + 1); });

        document.addEventListener('keydown', function (e) {
            if (!isOpen) return;
            if (e.key === 'Escape') close();
            else if (e.key === 'ArrowRight') userNav(index + 1);
            else if (e.key === 'ArrowLeft') userNav(index - 1);
        });

        // Swipe di HP
        let x0 = null;
        track.addEventListener('touchstart', function (e) { x0 = e.touches[0].clientX; stop(); }, { passive: true });
        track.addEventListener('touchend', function (e) {
            if (x0 === null) return;
            const dx = e.changedTouches[0].clientX - x0;
            if (Math.abs(dx) > 50) go(index + (dx < 0 ? 1 : -1));
            x0 = null;
        });

        // Tampil sedikit setelah halaman siap (efek "setelah login")
        const show = function () { setTimeout(open, 600); };
        if (document.readyState === 'complete') show();
        else window.addEventListener('load', show);
    })();
</script>
@endif
{{-- ================= AKHIR BANNER PROMO UPGRADE ================= --}}

<!-- Stats Grid -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 md:gap-6 mb-6 md:mb-8">
    <!-- Total Students -->
    <div class="bg-white p-4 sm:p-5 md:p-6 rounded-2xl md:rounded-3xl border border-slate-100 shadow-sm hover:shadow-md hover-lift transition-all">
        <div class="flex items-center justify-between mb-3 md:mb-4">
            <div class="w-10 h-10 md:w-12 md:h-12 bg-orange-50 rounded-2xl flex items-center justify-center text-orange-600">
                <span class="material-symbols-outlined text-[24px] md:text-[28px]">groups</span>
            </div>
            <span class="text-[10px] md:text-xs font-bold text-emerald-600 bg-emerald-50 px-2 py-1 rounded-lg">+12%</span>
        </div>
        <p class="text-slate-500 text-xs font-semibold mb-1">Total Students</p>
        <h3 class="text-2xl md:text-3xl font-black text-slate-900 tracking-tight">{{ number_format($totalStudents) }}</h3>
    </div>

    <!-- Attendance -->
    <div class="bg-white p-4 sm:p-5 md:p-6 rounded-2xl md:rounded-3xl border border-slate-100 shadow-sm hover:shadow-md hover-lift transition-all">
        <div class="flex items-center justify-between mb-3 md:mb-4">
            <div class="w-10 h-10 md:w-12 md:h-12 bg-emerald-50 rounded-2xl flex items-center justify-center text-emerald-600">
                <span class="material-symbols-outlined text-[24px] md:text-[28px]">how_to_reg</span>
            </div>
            <span class="text-[10px] md:text-xs font-bold text-slate-500 bg-slate-100 px-2 py-1 rounded-lg">Today</span>
        </div>
        <p class="text-slate-500 text-xs font-semibold mb-1">Attendance</p>
        <h3 class="text-2xl md:text-3xl font-black text-slate-900 tracking-tight">{{ number_format($todayAttendance) }}</h3>
    </div>

    <!-- Pending Apps -->
    <div class="bg-white p-4 sm:p-5 md:p-6 rounded-2xl md:rounded-3xl border border-slate-100 shadow-sm hover:shadow-md hover-lift transition-all">
        <div class="flex items-center justify-between mb-3 md:mb-4">
            <div class="w-10 h-10 md:w-12 md:h-12 bg-amber-50 rounded-2xl flex items-center justify-center text-amber-600">
                <span class="material-symbols-outlined text-[24px] md:text-[28px]">pending_actions</span>
            </div>
            <span class="text-[10px] md:text-xs font-bold text-amber-600 bg-amber-50 px-2 py-1 rounded-lg">{{ $pendingRegistrations }} New</span>
        </div>
        <p class="text-slate-500 text-xs font-semibold mb-1">Pending Reg.</p>
        <h3 class="text-2xl md:text-3xl font-black text-slate-900 tracking-tight">{{ number_format($pendingRegistrations) }}</h3>
    </div>

    <!-- Average GPA -->
    <div class="bg-white p-4 sm:p-5 md:p-6 rounded-2xl md:rounded-3xl border border-slate-100 shadow-sm hover:shadow-md hover-lift transition-all">
        <div class="flex items-center justify-between mb-3 md:mb-4">
            <div class="w-10 h-10 md:w-12 md:h-12 bg-rose-50 rounded-2xl flex items-center justify-center text-rose-600">
                <span class="material-symbols-outlined text-[24px] md:text-[28px]">military_tech</span>
            </div>
            <span class="text-[10px] md:text-xs font-bold text-rose-600 bg-rose-50 px-2 py-1 rounded-lg">Avg</span>
        </div>
        <p class="text-slate-500 text-xs font-semibold mb-1">Avg. Performance</p>
        <h3 class="text-2xl md:text-3xl font-black text-slate-900 tracking-tight">{{ number_format($avgGpa, 2) }}</h3>
    </div>
</div>

<!-- Recent Activity & Side Bar Widgets -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 md:gap-8 mb-6 md:mb-8">
    <!-- Recent Attendance -->
    <div class="lg:col-span-2 bg-white rounded-2xl md:rounded-3xl border border-slate-100 shadow-sm p-4 md:p-8">
        <div class="flex items-center justify-between mb-6 md:mb-8">
            <h3 class="text-lg md:text-xl font-bold text-slate-900">Recent Attendance</h3>
            <a href="{{ route('attendance') }}" class="text-primary text-xs md:text-sm font-bold hover:underline">View All</a>
        </div>
        
        <div class="overflow-x-auto -mx-4 md:mx-0 px-4 md:px-0">
            <table class="w-full text-left border-collapse min-w-[400px]">
                <thead>
                    <tr class="bg-slate-50/50">
                        <th class="p-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">Student</th>
                        <th class="p-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">Time</th>
                        <th class="p-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @foreach($recentAttendances as $attendance)
                    <tr class="hover:bg-slate-50/50 transition-colors group">
                        <td class="p-4">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-lg bg-orange-50 flex items-center justify-center text-orange-600 font-bold text-[10px]">
                                    {{ strtoupper(substr($attendance->student->nama_lengkap, 0, 1)) }}
                                </div>
                                <div>
                                    <p class="text-sm font-bold text-slate-900 group-hover:text-primary transition-colors">{{ $attendance->student->nama_lengkap }}</p>
                                    <p class="text-[10px] font-medium text-slate-400 uppercase">{{ $attendance->student->id_siswa }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="p-4 text-xs font-black text-slate-600 tabular-nums">
                            {{ $attendance->check_in ? \Carbon\Carbon::parse($attendance->check_in)->format('H:i A') : '--:--' }}
                        </td>
                        <td class="p-4">
                            <span class="px-3 py-1 rounded-full text-[9px] font-black uppercase tracking-wider {{ $attendance->status === 'present' ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700' }}">
                                {{ $attendance->status }}
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Right Side Widgets (Discipline, Smart EduHub, Link Pendaftaran) -->
    <div class="space-y-6 md:space-y-8">
        <!-- Discipline Alerts -->
        <div class="bg-white rounded-2xl md:rounded-3xl border border-slate-100 shadow-sm p-4 md:p-6">
            <h3 class="text-base md:text-lg font-bold text-slate-900 mb-4 md:mb-6">Discipline Alerts</h3>
            <div class="space-y-3 md:space-y-4">
                @forelse($studentsWithViolations as $student)
                <div class="p-3 md:p-4 rounded-2xl border {{ $student->net_violation_points >= 100 ? 'bg-rose-50 border-rose-100' : ($student->net_violation_points >= 70 ? 'bg-amber-50 border-amber-100' : 'bg-orange-50 border-orange-100') }} flex items-center justify-between group hover:scale-[1.02] transition-all">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 md:w-10 md:h-10 rounded-xl {{ $student->net_violation_points >= 100 ? 'bg-rose-600' : ($student->net_violation_points >= 70 ? 'bg-amber-600' : 'bg-orange-600') }} text-white flex items-center justify-center">
                            <span class="material-symbols-outlined text-[18px] md:text-[20px]">warning</span>
                        </div>
                        <div>
                            <p class="text-sm font-black text-slate-900">{{ $student->nama_lengkap }}</p>
                            <p class="text-[10px] font-bold text-slate-500 uppercase">Angkatan {{ $student->angkatan ?? '-' }}</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="text-base md:text-lg font-black {{ $student->net_violation_points >= 100 ? 'text-rose-600' : ($student->net_violation_points >= 70 ? 'text-amber-600' : 'text-orange-600') }}">{{ $student->net_violation_points }}</p>
                    </div>
                </div>
                @empty
                <div class="py-8 flex flex-col items-center text-center opacity-40">
                    <span class="material-symbols-outlined text-4xl mb-2 text-slate-300">verified</span>
                    <p class="text-sm font-bold text-slate-400">No discipline alerts</p>
                </div>
                @endforelse
            </div>
            @if($studentsWithViolations->count() > 0)
            <a href="{{ route('points') }}" class="w-full mt-4 md:mt-6 py-3 text-sm font-bold text-orange-600 hover:bg-orange-50 rounded-xl transition-colors inline-block text-center">Manage All Points</a>
            @endif
        </div>

        <!-- NEW: Smart EduHub Card -->
        <!-- <div class="bg-white rounded-2xl md:rounded-3xl border border-slate-100 shadow-sm p-4 md:p-6 relative overflow-hidden group hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-orange-50 flex items-center justify-center text-orange-600">
                        <span class="material-symbols-outlined text-[22px]">account_balance_wallet</span>
                    </div>
                    <div>
                        <h3 class="text-sm font-black text-slate-900">Smart EduHub</h3>
                        <p class="text-[10px] font-medium text-slate-400 uppercase tracking-wider">Project & Point Rewards</p>
                    </div>
                </div>
                <span class="text-[10px] font-bold text-orange-600 bg-orange-50 px-2.5 py-1 rounded-full hidden sm:inline-block">ACP Program</span>
            </div>
            
            <p class="text-xs text-slate-500 mb-6 leading-relaxed">Kelola tabungan poin kreativitas, portofolio siswa Axioo Class Program, serta pencapaian tugas digital di sini.</p>
            
            <div class="grid grid-cols-2 gap-3 mb-4">
                <div class="p-3 bg-slate-50 rounded-xl text-center">
                    <p class="text-[10px] font-bold text-slate-400 uppercase">Total Tabungan</p>
                    <p class="text-base font-black text-slate-800 mt-1">{{ number_format($totalCreativePoints, 0, ',', '.') }} <span class="text-[10px] font-medium text-slate-500">Pts</span></p>
                </div>
                <div class="p-3 bg-slate-50 rounded-xl text-center">
                    <p class="text-[10px] font-bold text-slate-400 uppercase">Proyek Kreatif</p>
                    <p class="text-base font-black text-slate-800 mt-1">{{ number_format($activeProjects, 0, ',', '.') }} <span class="text-[10px] font-medium text-slate-500">Aktif</span></p>
                </div>
            </div>

            <a href="#" class="w-full py-3 bg-slate-900 text-white rounded-xl text-sm font-bold hover:bg-slate-800 transition-colors flex items-center justify-center gap-2">
                <span class="material-symbols-outlined text-[18px]">rocket_launch</span>
                Buka Creative Bank
            </a>
        </div> -->

        <!-- Pendaftaran Siswa Baru -->
        <div class="bg-primary rounded-2xl md:rounded-3xl p-4 md:p-6 text-white">
            <h3 class="text-base md:text-lg font-bold mb-2">Pendaftaran Siswa Baru</h3>
            <p class="text-xs md:text-sm text-orange-100 mb-4 md:mb-6">Sebarkan link pendaftaran di bawah ini kepada calon siswa baru.</p>
            <button onclick="copyJoinLink()" class="w-full py-3 bg-white text-primary rounded-xl text-sm font-bold hover:bg-orange-50 transition-colors flex items-center justify-center gap-2">
                <span class="material-symbols-outlined text-[18px]">share</span>
                Salin Link Pendaftaran
            </button>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 md:gap-8">
    <!-- Latest Permits -->
    <div class="bg-white rounded-2xl md:rounded-3xl border border-slate-100 shadow-sm p-4 md:p-8">
        <div class="flex items-center justify-between mb-6 md:mb-8">
            <h3 class="text-lg md:text-xl font-bold text-slate-900">Latest Permits</h3>
            <a href="{{ route('admin.permits.index') }}" class="text-primary text-xs md:text-sm font-bold hover:underline">View All</a>
        </div>
        <div class="space-y-3 md:space-y-4">
            @foreach($latestPermits as $permit)
            <div class="flex items-center justify-between p-3 md:p-4 bg-slate-50 rounded-2xl">
                <div class="flex items-center gap-3 md:gap-4 min-w-0">
                    <div class="w-9 h-9 md:w-10 md:h-10 rounded-xl bg-white flex items-center justify-center text-orange-600 flex-shrink-0">
                        <span class="material-symbols-outlined text-[20px] md:text-[24px]">{{ $permit->type === 'sakit' ? 'medical_services' : 'assignment' }}</span>
                    </div>
                    <div class="min-w-0">
                        <p class="text-sm font-bold text-slate-900 truncate">{{ $permit->student->nama_lengkap }}</p>
                        <p class="text-xs text-slate-500 capitalize truncate">{{ $permit->type }} • {{ \Carbon\Carbon::parse($permit->start_date)->format('d M') }}</p>
                    </div>
                </div>
                <span class="px-2 md:px-3 py-1 rounded-full text-[9px] md:text-[10px] font-black uppercase tracking-wider flex-shrink-0 ml-2 {{ $permit->status === 'approved' ? 'bg-emerald-100 text-emerald-700' : ($permit->status === 'pending' ? 'bg-amber-100 text-amber-700' : 'bg-rose-100 text-rose-700') }}">
                    {{ $permit->status }}
                </span>
            </div>
            @endforeach
        </div>
    </div>

    <!-- Summary Stats -->
    <div class="bg-slate-900 rounded-2xl md:rounded-3xl p-4 md:p-8 text-white relative overflow-hidden">
        <div class="relative z-10">
            <h3 class="text-lg md:text-xl font-bold mb-6 md:mb-8">System Summary</h3>
            <div class="grid grid-cols-2 gap-3 md:gap-4">
                <div class="p-4 md:p-6 bg-white/10 rounded-2xl backdrop-blur-md">
                    <p class="text-[10px] md:text-xs font-bold text-slate-400 uppercase tracking-widest mb-2">Active Students</p>
                    <h4 class="text-2xl md:text-3xl font-black">{{ $totalStudents }}</h4>
                </div>
                <div class="p-4 md:p-6 bg-white/10 rounded-2xl backdrop-blur-md">
                    <p class="text-[10px] md:text-xs font-bold text-slate-400 uppercase tracking-widest mb-2">Pending Permits</p>
                    <h4 class="text-2xl md:text-3xl font-black">{{ $pendingPermitsCount }}</h4>
                </div>
            </div>
        </div>
        <div class="absolute -right-20 -bottom-20 w-48 h-48 md:w-64 md:h-64 bg-primary/20 rounded-full blur-3xl"></div>
    </div>
</div>

@push('scripts')
<script>
    // Mobile: sembunyikan navbar atas (Search) & tempelkan header ke ujung atas layar
    (function () {
        const hero = document.getElementById('pageHero');
        if (!hero) return;
        const mq = window.matchMedia('(max-width: 767px)');

        // Cari navbar atas: naik dari input Search ke elemen header/nav/sticky/fixed teratas
        const searchBox = document.querySelector('input[type="search"], input[placeholder*="earch" i]');
        let topbar = null;
        if (searchBox) {
            let el = searchBox;
            while (el && el !== document.body) {
                if (el.contains(hero)) break;
                const pos = getComputedStyle(el).position;
                if (el.tagName === 'HEADER' || el.tagName === 'NAV' || pos === 'sticky' || pos === 'fixed') topbar = el;
                el = el.parentElement;
            }
        }

        function snapHero() {
            if (topbar) topbar.style.display = mq.matches ? 'none' : '';
            hero.style.marginTop = hero.style.marginLeft = hero.style.marginRight = '';
            if (!mq.matches) return;
            const r = hero.getBoundingClientRect();
            const cs = getComputedStyle(hero);
            const mt = parseFloat(cs.marginTop) || 0;
            const ml = parseFloat(cs.marginLeft) || 0;
            const mr = parseFloat(cs.marginRight) || 0;
            const top = r.top + window.scrollY;
            const right = document.documentElement.clientWidth - r.right;
            if (top > 0) hero.style.marginTop = (mt - top) + 'px';
            if (r.left !== 0) hero.style.marginLeft = (ml - r.left) + 'px';
            if (right !== 0) hero.style.marginRight = (mr - right) + 'px';
        }
        const run = () => requestAnimationFrame(snapHero);
        run();
        window.addEventListener('load', run);
        window.addEventListener('resize', run);
        if (mq.addEventListener) mq.addEventListener('change', run);
    })();

    function copyJoinLink() {
        const url = "{{ route('registrations.form') }}";
        const fullUrl = window.location.origin + url;
        
        if (navigator.clipboard && window.isSecureContext) {
            navigator.clipboard.writeText(fullUrl).then(() => {
                alert('Link pendaftaran berhasil disalin!');
            });
        } else {
            const textArea = document.createElement("textarea");
            textArea.value = fullUrl;
            document.body.appendChild(textArea);
            textArea.select();
            document.execCommand('copy');
            document.body.removeChild(textArea);
            alert('Link pendaftaran berhasil disalin!');
        }
    }
</script>
@endpush
@endsection