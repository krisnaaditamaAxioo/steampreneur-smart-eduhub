@extends('layouts.siswa')

@section('title', 'Pengajuan Izin')
@section('page_title', 'Perizinan Siswa')

@push('styles')
<style>
    /* ---------- Form fields ---------- */
    .field {
        width: 100%;
        min-height: 48px;
        padding: .75rem .9rem;
        border: 1px solid #e2e8f0;
        border-radius: 14px;
        background: #f8fafc;
        color: #0f172a;
        font-size: 1rem; /* 16px on phones prevents iOS zoom on focus */
        line-height: 1.4;
        outline: none;
        transition: border-color .15s, box-shadow .15s, background .15s;
    }
    .field:focus { border-color: #F6863B; background: #fff; box-shadow: 0 0 0 4px rgba(246, 134, 59, .15); }
    .field.is-invalid { border-color: #f43f5e; }
    @media (min-width: 640px) { .field { font-size: .875rem; } }

    .focus-ring:focus-visible { outline: 2px solid #EB4325; outline-offset: 2px; }

    /* ---------- Permit type radio cards ---------- */
    .type-card {
        display: flex; flex-direction: column; gap: .5rem; height: 100%;
        padding: .75rem; border: 1px solid #e2e8f0; border-radius: 16px; background: #f8fafc;
        transition: border-color .15s, background .15s, box-shadow .15s;
    }
    .type-option:hover .type-card { border-color: #cbd5e1; }
    .type-option input:checked + .type-card { border-color: #F6863B; background: #FFF3EA; box-shadow: 0 0 0 3px rgba(246, 134, 59, .15); }
    .type-option input:checked + .type-card .type-icon { color: #F6863B; }
    .type-option input:focus-visible + .type-card { outline: 2px solid #EB4325; outline-offset: 2px; }

    /* ---------- Upload dropzone ---------- */
    #proof_photo:focus-visible + .drop { outline: 2px solid #EB4325; outline-offset: 2px; }
    .drop.is-filled { border-style: solid; border-color: #10b981; background: #ecfdf5; }
    .drop.is-filled .drop-icon { color: #059669; }

    /* ---------- Filter chips ---------- */
    .chip {
        display: inline-flex; align-items: center; gap: .4rem; min-height: 36px; padding: 0 .85rem;
        border: 1px solid #e2e8f0; border-radius: 999px; background: #fff; color: #475569;
        font-size: .8125rem; font-weight: 600; white-space: nowrap;
        transition: background .15s, color .15s, border-color .15s;
    }
    .chip:hover { background: #f8fafc; }
    .chip.is-active { background: #0f172a; border-color: #0f172a; color: #fff; }
    .chip-count { font-size: .75rem; font-variant-numeric: tabular-nums; opacity: .7; }

    .clamp-2 { display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }

    /* ---------- Form panel: sticky card on desktop, bottom sheet on mobile ---------- */
    .permit-overlay {
        position: fixed; inset: 0; z-index: 100;
        background: rgba(15, 23, 42, .5); backdrop-filter: blur(4px);
        opacity: 0; visibility: hidden;
        transition: opacity .25s ease, visibility 0s linear .25s;
    }
    .permit-overlay.is-open { opacity: 1; visibility: visible; transition-delay: 0s; }

    @media (max-width: 1023.98px) {
        .permit-panel {
            position: fixed; left: 0; right: 0; bottom: 0; z-index: 110;
            width: 100%; max-width: 32rem; margin-inline: auto;
            max-height: 92vh; max-height: 92dvh;
            padding-bottom: env(safe-area-inset-bottom);
            transform: translateY(105%); visibility: hidden;
            transition: transform .32s cubic-bezier(.32, .72, 0, 1), visibility 0s linear .32s;
        }
        .permit-panel.is-open { transform: translateY(0); visibility: visible; transition-delay: 0s; }
    }
    @media (min-width: 1024px) {
        .permit-overlay { display: none; }
        .permit-panel { max-height: calc(100vh - 3rem); max-height: calc(100dvh - 3rem); }
    }
    .permit-panel:focus { outline: none; }

    @media (prefers-reduced-motion: reduce) {
        .permit-panel, .permit-overlay { transition: none !important; }
    }

    /* ---------- Print: only the permit whose print button was pressed ---------- */
    @media print {
        body * { visibility: hidden !important; }
        .permit-row.is-printing, .permit-row.is-printing * { visibility: visible !important; }
        .permit-row.is-printing { position: absolute; left: 0; top: 0; width: 100%; padding: 24px; }
        .no-print { display: none !important; }
    }
</style>
@endpush

@section('content')
@php
    // Works whether $permits is a Collection or a paginator.
    $permitList = $permits instanceof \Illuminate\Pagination\AbstractPaginator ? collect($permits->items()) : collect($permits);

    $counts = [
        'all'      => $permitList->count(),
        'pending'  => $permitList->filter(fn ($p) => ! in_array($p->status, ['approved', 'rejected']))->count(),
        'approved' => $permitList->where('status', 'approved')->count(),
        'rejected' => $permitList->where('status', 'rejected')->count(),
    ];

    $typeMeta = [
        'sakit'  => ['label' => 'Sakit',       'icon' => 'medical_services', 'hint' => 'Keperluan medis'],
        'izin'   => ['label' => 'Izin',        'icon' => 'assignment',       'hint' => 'Keperluan penting'],
        'dispen' => ['label' => 'Dispensasi',  'icon' => 'emoji_events',     'hint' => 'Lomba / kegiatan'],
    ];

    $statusMeta = [
        'approved' => ['label' => 'Disetujui', 'icon' => 'check_circle', 'pill' => 'bg-emerald-50 text-emerald-700 border-emerald-200', 'month' => 'text-emerald-700'],
        'rejected' => ['label' => 'Ditolak',   'icon' => 'cancel',       'pill' => 'bg-rose-50 text-rose-700 border-rose-200',          'month' => 'text-rose-700'],
        'pending'  => ['label' => 'Menunggu',  'icon' => 'schedule',     'pill' => 'bg-amber-50 text-amber-800 border-amber-200',       'month' => 'text-amber-700'],
    ];

    $oldType = old('type', 'sakit');
@endphp

<div class="space-y-4 px-4 sm:space-y-6 lg:px-0">

    {{-- Curved gradient header (full width on mobile) --}}
    <header class="no-print relative -mx-2 -mt-4 overflow-hidden rounded-b-[2rem] bg-gradient-to-br from-[#F6863B] via-[#EB4325] to-[#8A1E1E] px-5 pb-8 pt-5 text-white shadow-xl shadow-[#EB4325]/15 lg:mx-0 lg:mt-0 lg:rounded-[2rem] lg:pb-6">
        {{-- Abstract circular glow overlays --}}
        <div class="pointer-events-none absolute -right-12 -top-12 h-32 w-32 rounded-full bg-white/5" aria-hidden="true"></div>
        <div class="pointer-events-none absolute -bottom-8 left-12 h-24 w-24 rounded-full bg-white/5" aria-hidden="true"></div>

        <div class="relative z-10 flex items-center justify-between gap-3">
            <div class="flex min-w-0 items-center gap-3">
                <div class="flex h-9 w-9 shrink-0 items-center justify-center rounded-xl border border-white/10 bg-white/15 backdrop-blur-md" aria-hidden="true">
                    <span class="material-symbols-outlined text-lg text-white" style="font-variation-settings: 'FILL' 1;">edit_calendar</span>
                </div>
                <div class="min-w-0">
                    <span class="block text-[10px] font-bold uppercase leading-none tracking-wider text-white/80">Perizinan Siswa</span>
                    <h1 class="mt-1 block truncate text-base font-black tracking-tight sm:text-lg">Pengajuan Izin</h1>
                </div>
            </div>

            {{-- Pending shortcut: filters the history to "Menunggu" --}}
            @if($counts['pending'] > 0)
                <button type="button" onclick="showPendingPermits()" title="Lihat pengajuan yang menunggu"
                        aria-label="Lihat {{ $counts['pending'] }} pengajuan yang menunggu persetujuan"
                        class="focus-ring relative flex h-9 w-9 shrink-0 items-center justify-center rounded-xl border border-white/10 bg-white/15 backdrop-blur-md transition-all hover:bg-white/25 active:scale-95">
                    <span class="material-symbols-outlined text-lg text-white" aria-hidden="true">schedule</span>
                    <span class="absolute -right-1.5 -top-1.5 grid h-[18px] min-w-[18px] place-items-center rounded-full border border-[#EB4325] bg-white px-1 text-[10px] font-bold tabular-nums text-[#EB4325]">{{ $counts['pending'] }}</span>
                </button>
            @endif
        </div>

        <p class="relative z-10 mt-4 max-w-md text-xs leading-relaxed text-white/85 sm:text-[13px]">
            Ajukan izin sakit, keperluan penting, atau dispensasi, lalu pantau statusnya di sini.
        </p>
    </header>

    {{-- Success notification --}}
    @if(session('success'))
        <div role="status" class="no-print flex items-start gap-3 rounded-2xl border border-emerald-200 bg-emerald-50 p-3.5 text-emerald-900">
            <span class="material-symbols-outlined mt-0.5 text-emerald-600" style="font-variation-settings: 'FILL' 1;">check_circle</span>
            <p class="flex-1 text-sm font-medium leading-relaxed">{{ session('success') }}</p>
            <button type="button" aria-label="Tutup pemberitahuan" onclick="this.closest('[role=status]').remove()"
                    class="focus-ring -m-1 grid h-9 w-9 shrink-0 place-items-center rounded-full text-emerald-700 hover:bg-emerald-100">
                <span class="material-symbols-outlined text-lg">close</span>
            </button>
        </div>
    @endif

    {{-- Error notification --}}
    @if(session('error'))
        <div role="alert" class="no-print flex items-start gap-3 rounded-2xl border border-rose-200 bg-rose-50 p-3.5 text-rose-900">
            <span class="material-symbols-outlined mt-0.5 text-rose-600" style="font-variation-settings: 'FILL' 1;">error</span>
            <p class="flex-1 text-sm font-medium leading-relaxed">{{ session('error') }}</p>
            <button type="button" aria-label="Tutup pemberitahuan" onclick="this.closest('[role=alert]').remove()"
                    class="focus-ring -m-1 grid h-9 w-9 shrink-0 place-items-center rounded-full text-rose-700 hover:bg-rose-100">
                <span class="material-symbols-outlined text-lg">close</span>
            </button>
        </div>
    @endif

    <div class="grid grid-cols-1 items-start gap-6 lg:grid-cols-12 lg:gap-8">

        {{-- ================================================= --}}
        {{-- FORM: sticky card on desktop, bottom sheet on mobile --}}
        {{-- ================================================= --}}
        <div class="contents lg:col-span-5 lg:block lg:self-stretch">
            <div id="permitOverlay" class="permit-overlay no-print" onclick="closePermitPanel()" aria-hidden="true"></div>

            <section id="permitPanel" tabindex="-1" aria-labelledby="permitPanelTitle"
                     class="permit-panel no-print flex flex-col rounded-t-[28px] bg-white shadow-2xl lg:sticky lg:top-6 lg:overflow-hidden lg:rounded-3xl lg:border lg:border-slate-100 lg:shadow-sm">

                {{-- Header --}}
                <div class="shrink-0 border-b border-slate-100 px-5 pb-4 pt-3 lg:pt-5">
                    <div class="mx-auto mb-3 h-1 w-10 rounded-full bg-slate-200 lg:hidden"></div>
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <h2 id="permitPanelTitle" class="text-lg font-extrabold tracking-tight text-slate-900">Ajukan izin</h2>
                            <p class="mt-0.5 text-[13px] leading-snug text-slate-500">Setelah terkirim, Anda bisa mengabari lewat WhatsApp dari daftar riwayat.</p>
                        </div>
                        <button type="button" onclick="closePermitPanel()" aria-label="Tutup formulir"
                                class="focus-ring -mr-1.5 grid h-10 w-10 shrink-0 place-items-center rounded-full text-slate-500 hover:bg-slate-100 lg:hidden">
                            <span class="material-symbols-outlined">close</span>
                        </button>
                    </div>
                </div>

                <form id="permitForm" action="{{ route('siswa.permit.store') }}" method="POST" enctype="multipart/form-data" class="flex min-h-0 flex-1 flex-col">
                    @csrf

                    <div class="flex-1 space-y-5 overflow-y-auto px-5 py-5">

                        @if($errors->any())
                            <div role="alert" class="rounded-2xl border border-rose-200 bg-rose-50 p-3.5 text-rose-800">
                                <p class="text-sm font-semibold">Pengajuan belum terkirim</p>
                                <ul class="mt-1 list-disc space-y-0.5 pl-5 text-[13px]">
                                    @foreach($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        {{-- Permit type --}}
                        <fieldset class="min-w-0">
                            <legend class="mb-1.5 text-xs font-semibold text-slate-600">Jenis izin</legend>
                            <div class="grid grid-cols-3 gap-2">
                                @foreach($typeMeta as $value => $tm)
                                    <label class="type-option block cursor-pointer">
                                        <input type="radio" name="type" value="{{ $value }}" class="sr-only" required {{ $oldType === $value ? 'checked' : '' }}>
                                        <span class="type-card">
                                            <span class="material-symbols-outlined type-icon text-[22px] text-slate-400">{{ $tm['icon'] }}</span>
                                            <span>
                                                <span class="block text-sm font-bold text-slate-900">{{ $tm['label'] }}</span>
                                                <span class="mt-0.5 block text-[11px] leading-snug text-slate-500">{{ $tm['hint'] }}</span>
                                            </span>
                                        </span>
                                    </label>
                                @endforeach
                            </div>
                        </fieldset>

                        {{-- Dates --}}
                        <div>
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label for="start_date" class="mb-1.5 block text-xs font-semibold text-slate-600">Dari tanggal</label>
                                    <input type="date" id="start_date" name="start_date" required value="{{ old('start_date') }}"
                                           class="field cursor-pointer @error('start_date') is-invalid @enderror">
                                </div>
                                <div>
                                    <label for="end_date" class="mb-1.5 block text-xs font-semibold text-slate-600">Sampai tanggal</label>
                                    <input type="date" id="end_date" name="end_date" value="{{ old('end_date') }}"
                                           class="field cursor-pointer @error('end_date') is-invalid @enderror">
                                </div>
                            </div>
                            <p class="mt-1.5 text-xs text-slate-500">Kosongkan tanggal akhir jika izin hanya satu hari.</p>
                        </div>

                        {{-- Reason --}}
                        <div>
                            <label for="reason" class="mb-1.5 block text-xs font-semibold text-slate-600">Alasan / keterangan</label>
                            <textarea id="reason" name="reason" rows="4" required
                                      class="field resize-none @error('reason') is-invalid @enderror"
                                      placeholder="Jelaskan secara singkat alasan ketidakhadiran">{{ old('reason') }}</textarea>
                        </div>

                        {{-- Proof upload --}}
                        <div>
                            <span class="mb-1.5 block text-xs font-semibold text-slate-600">Bukti pendukung</span>
                            <input type="file" id="proof_photo" name="proof_photo" class="sr-only" accept="image/*,application/pdf" onchange="showProofName(this)">
                            <label for="proof_photo" id="proofDrop"
                                   class="drop flex cursor-pointer items-center gap-3 rounded-2xl border-2 border-dashed border-slate-200 bg-slate-50 p-3.5 transition hover:border-[#F6863B]/60 hover:bg-[#FFF3EA]/60">
                                <span class="grid h-11 w-11 shrink-0 place-items-center rounded-xl bg-white text-slate-400 shadow-sm">
                                    <span class="material-symbols-outlined drop-icon">upload_file</span>
                                </span>
                                <span class="min-w-0">
                                    <span id="proofName" class="block truncate text-sm font-semibold text-slate-800">Pilih foto atau PDF</span>
                                    <span id="proofHint" class="block text-xs text-slate-500">Surat dokter, undangan lomba, atau bukti lainnya</span>
                                </span>
                            </label>
                        </div>
                    </div>

                    {{-- Footer: stays visible while the sheet scrolls --}}
                    <div class="shrink-0 border-t border-slate-100 p-4">
                        <button type="submit" id="permitSubmit"
                                class="focus-ring flex h-12 w-full items-center justify-center gap-2 rounded-2xl bg-[#F6863B] text-sm font-bold text-slate-900 shadow-sm transition hover:bg-[#F3782A] active:scale-[.98] disabled:opacity-60">
                            <span class="material-symbols-outlined text-xl">send</span>
                            <span class="label">Kirim pengajuan</span>
                        </button>
                    </div>
                </form>
            </section>
        </div>

        {{-- ================================================= --}}
        {{-- HISTORY                                           --}}
        {{-- ================================================= --}}
        <div class="lg:col-span-7">
            <div class="overflow-hidden rounded-3xl border border-slate-100 bg-white shadow-sm">

                <div class="px-4 pt-4 sm:px-6 sm:pt-5">
                    <h2 class="text-lg font-extrabold tracking-tight text-slate-900 sm:text-xl">Riwayat izin</h2>
                    <p class="mt-0.5 text-[13px] text-slate-500">Pantau status setiap pengajuan.</p>

                    @if($counts['all'] > 0)
                        <div class="no-print -mx-4 mt-4 overflow-x-auto px-4 [scrollbar-width:none] sm:-mx-6 sm:px-6" role="group" aria-label="Saring berdasarkan status">
                            <div class="flex w-max gap-2 pb-1">
                                @foreach(['all' => 'Semua', 'pending' => 'Menunggu', 'approved' => 'Disetujui', 'rejected' => 'Ditolak'] as $key => $label)
                                    <button type="button" data-filter="{{ $key }}" aria-pressed="{{ $key === 'all' ? 'true' : 'false' }}"
                                            class="chip focus-ring {{ $key === 'all' ? 'is-active' : '' }}">
                                        {{ $label }}
                                        <span class="chip-count">{{ $counts[$key] }}</span>
                                    </button>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>

                <div id="permitList" class="mt-3 divide-y divide-slate-100">
                    @forelse($permits as $permit)
                        @php
                            $statusKey = in_array($permit->status, ['approved', 'rejected']) ? $permit->status : 'pending';
                            $st   = $statusMeta[$statusKey];
                            $meta = $typeMeta[$permit->type] ?? ['label' => ucfirst($permit->type), 'icon' => 'description', 'hint' => ''];

                            $start = \Carbon\Carbon::parse($permit->start_date);
                            $end   = $permit->end_date ? \Carbon\Carbon::parse($permit->end_date) : null;
                            $multiDay = $end && ! $end->isSameDay($start);
                            $days  = $multiDay ? abs((int) $start->copy()->startOfDay()->diffInDays($end->copy()->startOfDay())) + 1 : 1;

                            $rangeText = $start->translatedFormat('j M Y');
                            if ($multiDay) {
                                $rangeText .= ' – ' . $end->translatedFormat('j M Y') . ' (' . $days . ' hari)';
                            }

                            $showWa    = $permit->status === 'pending' && $contacts->count() > 0;
                            $showPrint = $permit->status === 'approved';
                            $showDelete = $statusKey === 'pending';

                            if ($showWa) {
                                $waMessage = "Assalamualaikum Wr. Wb.\n\nSaya " . $student->name . " (NIS: " . $student->nis_siswa . ") ingin memberitahu bahwa saya telah mengajukan izin " . strtolower($permit->type) . ".\n\nDetail:\n- Tanggal: " . $start->translatedFormat('j F Y') . ($multiDay ? " s/d " . $end->translatedFormat('j F Y') : "") . "\n- Alasan: " . $permit->reason . "\n\nMohon untuk dicek dan disetujui ya. Terima kasih.";
                            }
                        @endphp

                        <article class="permit-row px-4 py-4 sm:px-6" data-status="{{ $statusKey }}">
                            <div class="flex gap-3">
                                {{-- Date tile --}}
                                <div class="flex h-14 w-14 shrink-0 flex-col items-center justify-center rounded-2xl bg-slate-50 ring-1 ring-slate-100" aria-hidden="true">
                                    <span class="text-[11px] font-bold leading-none {{ $st['month'] }}">{{ $start->translatedFormat('M') }}</span>
                                    <span class="mt-1 text-xl font-extrabold leading-none tabular-nums text-slate-900">{{ $start->format('d') }}</span>
                                </div>

                                <div class="min-w-0 flex-1">
                                    <div class="flex flex-wrap items-center justify-between gap-x-3 gap-y-1.5">
                                        <h3 class="flex items-center gap-1.5 text-sm font-bold text-slate-900 sm:text-[15px]">
                                            <span class="material-symbols-outlined text-lg text-slate-400" aria-hidden="true">{{ $meta['icon'] }}</span>
                                            {{ $meta['label'] }}
                                        </h3>
                                        <span class="inline-flex items-center gap-1 rounded-full border px-2.5 py-1 text-xs font-semibold {{ $st['pill'] }}">
                                            <span class="material-symbols-outlined text-sm" style="font-variation-settings: 'FILL' 1;" aria-hidden="true">{{ $st['icon'] }}</span>
                                            {{ $st['label'] }}
                                        </span>
                                    </div>
                                    <p class="mt-1 text-xs tabular-nums text-slate-500">{{ $rangeText }}</p>
                                    <p class="clamp-2 mt-1.5 text-[13px] leading-relaxed text-slate-700">{{ $permit->reason }}</p>
                                </div>
                            </div>

                            @if($showWa || $showPrint || $showDelete)
                                <div class="no-print mt-3 flex flex-wrap items-center gap-2 sm:pl-[4.25rem]">
                                    @if($showWa)
                                        <span class="basis-full text-xs text-slate-500 sm:basis-auto">Kabari lewat WhatsApp</span>
                                        @foreach($contacts as $contact)
                                            @php
                                                $wa = preg_replace('/[^0-9]/', '', $contact->no_wa);
                                                if (\Illuminate\Support\Str::startsWith($wa, '0')) {
                                                    $wa = '62' . substr($wa, 1);
                                                }
                                                $waUrl = 'https://wa.me/' . $wa . '?text=' . urlencode($waMessage);
                                            @endphp
                                            <a href="{{ $waUrl }}" target="_blank" rel="noopener"
                                               class="focus-ring inline-flex min-h-[40px] items-center gap-1.5 rounded-full border border-emerald-200 bg-emerald-50 px-3.5 text-xs font-semibold text-emerald-800 transition hover:bg-emerald-100 active:scale-95"
                                               title="Hubungi {{ $contact->name }}">
                                                <span class="material-symbols-outlined text-base" aria-hidden="true">chat</span>
                                                {{ \Illuminate\Support\Str::limit($contact->name, 20) }}
                                            </a>
                                        @endforeach
                                    @endif

                                    @if($showPrint)
                                        <button type="button" onclick="printPermit(this)"
                                                class="focus-ring inline-flex min-h-[40px] items-center gap-1.5 rounded-full border border-slate-200 bg-white px-3.5 text-xs font-semibold text-slate-700 transition hover:bg-slate-50 active:scale-95">
                                            <span class="material-symbols-outlined text-base" aria-hidden="true">print</span>
                                            Cetak surat
                                        </button>
                                    @endif

                                    @if($showDelete)
                                        <form action="{{ route('siswa.permit.destroy', $permit) }}" method="POST" class="contents"
                                              onsubmit="return confirm('Hapus pengajuan izin ini? Tindakan tidak dapat dibatalkan.')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                    class="focus-ring inline-flex min-h-[40px] items-center gap-1.5 rounded-full border border-rose-200 bg-rose-50 px-3.5 text-xs font-semibold text-rose-700 transition hover:bg-rose-100 active:scale-95">
                                                <span class="material-symbols-outlined text-base" aria-hidden="true">delete</span>
                                                Hapus
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            @endif
                        </article>
                    @empty
                        <div class="px-4 py-12 text-center sm:py-14">
                            <div class="mx-auto mb-3 grid h-12 w-12 place-items-center rounded-full bg-slate-50 text-slate-300">
                                <span class="material-symbols-outlined text-2xl">event_busy</span>
                            </div>
                            <h3 class="text-sm font-bold text-slate-800">Belum ada pengajuan izin</h3>
                            <p class="mx-auto mt-1 max-w-xs text-[13px] leading-relaxed text-slate-500">
                                Kehadiran Anda tercatat penuh tanpa izin khusus.
                                <span class="lg:hidden">Ketuk tombol Ajukan izin untuk membuat pengajuan baru.</span>
                                <span class="hidden lg:inline">Gunakan formulir di samping untuk membuat pengajuan baru.</span>
                            </p>
                        </div>
                    @endforelse

                    {{-- Shown when a filter has no matches --}}
                    <div id="filterEmpty" hidden class="px-4 py-10 text-center text-[13px] text-slate-500 sm:px-6">
                        Tidak ada pengajuan dengan status ini.
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Space so the last item clears the bottom nav and the button on mobile --}}
    <div class="h-24 lg:hidden"></div>
</div>

{{-- Mobile: extended floating button --}}
<div class="no-print fixed bottom-20 right-4 z-40 sm:bottom-24 sm:right-6 lg:hidden" style="margin-bottom: env(safe-area-inset-bottom);">
    <button type="button" id="permitFab" onclick="openPermitPanel()"
            class="focus-ring inline-flex h-12 items-center gap-2 rounded-full bg-[#F6863B] pl-4 pr-5 text-sm font-bold text-slate-900 shadow-lg shadow-[#F6863B]/40 transition hover:bg-[#F3782A] active:scale-95">
        <span class="material-symbols-outlined text-xl">add</span>
        Ajukan izin
    </button>
</div>
@endsection

@push('scripts')
<script>
(function () {
    const panel   = document.getElementById('permitPanel');
    const overlay = document.getElementById('permitOverlay');
    const form    = document.getElementById('permitForm');
    const submit  = document.getElementById('permitSubmit');
    const mobileMq = window.matchMedia('(max-width: 1023.98px)');
    let lastFocus = null;

    /* ----- Bottom sheet (mobile) ----- */
    window.openPermitPanel = function () {
        if (!panel) return;
        lastFocus = document.activeElement;
        panel.classList.add('is-open');
        overlay.classList.add('is-open');
        panel.setAttribute('role', 'dialog');
        panel.setAttribute('aria-modal', 'true');
        document.body.style.overflow = 'hidden';
        requestAnimationFrame(() => panel.focus({ preventScroll: true }));
    };

    window.closePermitPanel = function () {
        if (!panel) return;
        panel.classList.remove('is-open');
        overlay.classList.remove('is-open');
        panel.removeAttribute('role');
        panel.removeAttribute('aria-modal');
        document.body.style.overflow = '';
        if (lastFocus && lastFocus.focus) lastFocus.focus({ preventScroll: true });
    };

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && panel && panel.classList.contains('is-open')) closePermitPanel();
    });

    mobileMq.addEventListener('change', function (e) {
        if (!e.matches && panel && panel.classList.contains('is-open')) closePermitPanel();
    });

    @if($errors->any())
        if (mobileMq.matches) openPermitPanel();
    @endif

    /* ----- Date range: end date cannot precede start date ----- */
    const startDate = document.getElementById('start_date');
    const endDate   = document.getElementById('end_date');
    if (startDate && endDate) {
        const sync = () => {
            endDate.min = startDate.value || '';
            if (endDate.value && startDate.value && endDate.value < startDate.value) endDate.value = '';
        };
        startDate.addEventListener('change', sync);
        sync();
    }

    /* ----- Proof file name + size ----- */
    window.showProofName = function (input) {
        const drop = document.getElementById('proofDrop');
        const name = document.getElementById('proofName');
        const hint = document.getElementById('proofHint');
        const file = input.files && input.files[0];

        if (!file) {
            drop.classList.remove('is-filled');
            name.textContent = 'Pilih foto atau PDF';
            hint.textContent = 'Surat dokter, undangan lomba, atau bukti lainnya';
            return;
        }
        const size = file.size >= 1048576
            ? (file.size / 1048576).toFixed(1) + ' MB'
            : Math.max(1, Math.round(file.size / 1024)) + ' KB';
        drop.classList.add('is-filled');
        name.textContent = file.name;
        hint.textContent = size + ' • ketuk untuk mengganti';
    };

    /* ----- Prevent double submit ----- */
    if (form && submit) {
        form.addEventListener('submit', function () {
            submit.disabled = true;
            submit.querySelector('.label').textContent = 'Mengirim…';
        });
        window.addEventListener('pageshow', function () {
            submit.disabled = false;
            submit.querySelector('.label').textContent = 'Kirim pengajuan';
        });
    }

    /* ----- Status filter ----- */
    const chips = document.querySelectorAll('[data-filter]');
    const rows  = document.querySelectorAll('.permit-row');
    const filterEmpty = document.getElementById('filterEmpty');

    chips.forEach(function (chip) {
        chip.addEventListener('click', function () {
            const filter = chip.dataset.filter;
            let visible = 0;

            chips.forEach(function (c) {
                const on = c === chip;
                c.classList.toggle('is-active', on);
                c.setAttribute('aria-pressed', on ? 'true' : 'false');
            });
            rows.forEach(function (row) {
                const show = filter === 'all' || row.dataset.status === filter;
                row.hidden = !show;
                if (show) visible++;
            });
            if (filterEmpty) filterEmpty.hidden = visible !== 0;
        });
    });

    /* ----- Header shortcut: show pending permits ----- */
    window.showPendingPermits = function () {
        const chip = document.querySelector('[data-filter="pending"]');
        const list = document.getElementById('permitList');
        if (chip) chip.click();
        if (list) {
            const reduce = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
            list.scrollIntoView({ behavior: reduce ? 'auto' : 'smooth', block: 'start' });
        }
    };

    /* ----- Print a single permit ----- */
    window.printPermit = function (btn) {
        const row = btn.closest('.permit-row');
        if (!row) return;
        row.classList.add('is-printing');
        window.print();
    };
    window.addEventListener('afterprint', function () {
        document.querySelectorAll('.permit-row.is-printing').forEach(function (r) { r.classList.remove('is-printing'); });
    });
})();
</script>
@endpush