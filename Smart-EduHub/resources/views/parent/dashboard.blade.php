@extends('layouts.parent')

@section('title', 'Dashboard Wali Murid')
@section('page_title', 'Ringkasan Wali Murid')

@php
    $studentName = $student?->nama_lengkap;
    $initials = $studentName
        ? \Illuminate\Support\Str::of($studentName)->explode(' ')->filter()->take(2)->map(fn ($w) => mb_strtoupper(mb_substr($w, 0, 1)))->implode('')
        : '?';

    $menus = [
        ['route' => 'parent.attendance', 'icon' => 'event_available',        'label' => 'Absensi',   'hint' => 'Kehadiran harian', 'tone' => 'emerald'],
        ['route' => 'parent.finance',    'icon' => 'account_balance_wallet', 'label' => 'Keuangan',  'hint' => 'Tagihan & bayar',  'tone' => 'orange'],
        ['route' => 'parent.points',     'icon' => 'military_tech',          'label' => 'Poin',      'hint' => 'Kedisiplinan',     'tone' => 'amber'],
        ['route' => 'parent.permit',     'icon' => 'assignment_late',        'label' => 'Izin Sakit','hint' => 'Ajukan izin',      'tone' => 'rose'],
    ];

    $tones = [
        'emerald' => 'bg-emerald-50 text-emerald-600 group-hover:bg-emerald-600 group-hover:text-white',
        'orange'  => 'bg-orange-50 text-orange-600 group-hover:bg-orange-500 group-hover:text-white',
        'amber'   => 'bg-amber-50 text-amber-600 group-hover:bg-amber-500 group-hover:text-white',
        'rose'    => 'bg-rose-50 text-rose-600 group-hover:bg-rose-500 group-hover:text-white',
    ];

    $statusMap = [
        'present' => ['label' => 'Tepat waktu', 'pill' => 'bg-emerald-50 text-emerald-700 ring-emerald-200', 'dot' => 'bg-emerald-500'],
        'late'    => ['label' => 'Terlambat',   'pill' => 'bg-amber-50 text-amber-700 ring-amber-200',       'dot' => 'bg-amber-500'],
        'excused' => ['label' => 'Izin/Sakit',  'pill' => 'bg-sky-50 text-sky-700 ring-sky-200',             'dot' => 'bg-sky-500'],
        'absent'  => ['label' => 'Alpa',        'pill' => 'bg-rose-50 text-rose-700 ring-rose-200',          'dot' => 'bg-rose-500'],
    ];
@endphp

@section('content')
<div class="max-w-md mx-auto lg:max-w-5xl space-y-6 pb-6">

    {{-- Header --}}
    <section class="relative -mx-4 -mt-4 overflow-hidden rounded-b-[2.5rem] bg-gradient-to-br from-emerald-600 via-emerald-800 to-emerald-950 px-6 pt-8 pb-16 text-white shadow-xl lg:mx-0 lg:mt-0 lg:rounded-[2.5rem] lg:px-10 lg:pt-8 lg:pb-12">
        {{-- lingkaran dekorasi --}}
        <div class="pointer-events-none absolute -right-10 -top-10 h-44 w-44 rounded-full bg-white/10"></div>
        <div class="pointer-events-none absolute -bottom-12 left-1/4 h-32 w-32 rounded-full bg-white/5"></div>

        <div class="relative z-10 space-y-6">
            <div class="flex items-center justify-between gap-3">
                <div class="flex items-center gap-2 rounded-full bg-white/10 px-3 py-1.5 text-xs font-semibold text-emerald-50 ring-1 ring-white/15 backdrop-blur">
                    <span class="material-symbols-outlined text-base text-emerald-300">family_restroom</span>
                    Portal Orang Tua
                </div>
                <div class="flex items-center gap-1.5 rounded-full bg-white/15 px-3 py-1.5 text-xs font-bold ring-1 ring-white/20 backdrop-blur">
                    <span class="material-symbols-outlined text-base text-amber-300" style="font-variation-settings: 'FILL' 1;">stars</span>
                    {{ $netPoints }} poin
                </div>
            </div>

            <div class="flex items-center gap-4 lg:gap-6">
                <div class="flex h-16 w-16 shrink-0 items-center justify-center rounded-2xl bg-white/15 text-2xl font-extrabold ring-2 ring-white/30 backdrop-blur lg:h-20 lg:w-20 lg:text-3xl">
                    {{ $initials }}
                </div>
                <div class="min-w-0 space-y-1">
                    <p class="text-sm text-emerald-100/90">Selamat datang, {{ $parent->name }}</p>
                    <h2 class="truncate text-xl font-extrabold leading-tight tracking-tight lg:text-3xl">
                        {{ $studentName ?? 'Belum terhubung dengan siswa' }}
                    </h2>
                    <div class="flex flex-wrap items-center gap-x-3 gap-y-1 pt-0.5 text-xs text-emerald-100/80">
                        <span class="inline-flex items-center gap-1">
                            <span class="material-symbols-outlined text-sm">badge</span>
                            NISN {{ $student?->nisn ?? '-' }}
                        </span>
                        <span class="inline-flex items-center gap-1">
                            <span class="material-symbols-outlined text-sm">school</span>
                            Angkatan {{ $student?->angkatanModel?->code ?? '-' }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Menu cepat --}}
    <section class="relative z-20 -mt-12 px-1 lg:mt-6 lg:px-0">
        <div class="grid grid-cols-4 gap-2 rounded-[2rem] border border-slate-100 bg-white p-3 shadow-lg shadow-slate-200/60 lg:gap-4 lg:p-4">
            @foreach($menus as $m)
                <a href="{{ route($m['route']) }}"
                   class="group flex flex-col items-center gap-2 rounded-2xl px-1 py-3 transition hover:bg-slate-50 focus:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500 lg:flex-row lg:justify-start lg:gap-3 lg:px-4">
                    <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl transition-colors duration-200 {{ $tones[$m['tone']] }}">
                        <span class="material-symbols-outlined text-[22px]">{{ $m['icon'] }}</span>
                    </div>
                    <div class="text-center lg:text-left">
                        <span class="block text-[11px] font-bold leading-tight text-slate-800 lg:text-sm">{{ $m['label'] }}</span>
                        <span class="hidden text-xs text-slate-400 lg:block">{{ $m['hint'] }}</span>
                    </div>
                </a>
            @endforeach
        </div>
    </section>

    {{-- Peringatan pelanggaran --}}
    @if($violationAlert)
        <section role="alert" class="flex items-start gap-3 rounded-2xl border border-rose-200 bg-gradient-to-r from-rose-50 to-white p-4 shadow-sm">
            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-rose-500 text-white shadow-md shadow-rose-200">
                <span class="material-symbols-outlined text-xl">warning</span>
            </div>
            <div class="min-w-0 space-y-0.5">
                <h3 class="text-sm font-bold text-rose-900">{{ $violationAlert['title'] }}</h3>
                <p class="text-xs leading-relaxed text-rose-800/90">{{ $violationAlert['message'] }}</p>
            </div>
        </section>
    @endif

    {{-- Presensi & Poin --}}
    <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">

        {{-- Presensi terakhir --}}
        <section class="overflow-hidden rounded-3xl border border-slate-100 bg-white shadow-sm">
            <header class="flex items-center justify-between gap-3 border-b border-slate-100 px-5 py-4">
                <h3 class="flex items-center gap-2.5 text-sm font-bold text-slate-900">
                    <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-emerald-50 text-emerald-600">
                        <span class="material-symbols-outlined text-lg">event_available</span>
                    </span>
                    Presensi terakhir
                </h3>
                <a href="{{ route('parent.attendance') }}" class="inline-flex items-center gap-0.5 rounded-full px-2.5 py-1 text-xs font-semibold text-emerald-700 transition hover:bg-emerald-50">
                    Lihat semua
                    <span class="material-symbols-outlined text-base">chevron_right</span>
                </a>
            </header>

            <ul class="divide-y divide-slate-100">
                @forelse($recentAttendances as $att)
                    @php $s = $statusMap[$att->status] ?? $statusMap['absent']; @endphp
                    <li class="flex items-center gap-3 px-5 py-3.5 transition hover:bg-slate-50/70">
                        <div class="flex w-12 shrink-0 flex-col items-center rounded-xl bg-slate-50 py-1.5 ring-1 ring-slate-100">
                            <span class="text-base font-extrabold leading-none text-slate-800">{{ \Carbon\Carbon::parse($att->date)->translatedFormat('d') }}</span>
                            <span class="mt-0.5 text-[10px] font-medium text-slate-400">{{ \Carbon\Carbon::parse($att->date)->translatedFormat('M') }}</span>
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="truncate text-sm font-semibold text-slate-800">{{ \Carbon\Carbon::parse($att->date)->translatedFormat('l, d F Y') }}</p>
                            <p class="mt-0.5 flex items-center gap-1 text-xs text-slate-400">
                                <span class="material-symbols-outlined text-sm">schedule</span>
                                Masuk {{ $att->check_in ?: '-' }}
                            </p>
                        </div>
                        <span class="inline-flex shrink-0 items-center gap-1.5 rounded-full px-2.5 py-1 text-[11px] font-semibold ring-1 ring-inset {{ $s['pill'] }}">
                            <span class="h-1.5 w-1.5 rounded-full {{ $s['dot'] }}"></span>
                            {{ $s['label'] }}
                        </span>
                    </li>
                @empty
                    <li class="flex flex-col items-center gap-2 px-5 py-10 text-center">
                        <span class="flex h-12 w-12 items-center justify-center rounded-2xl bg-slate-50 text-slate-300">
                            <span class="material-symbols-outlined text-2xl">event_busy</span>
                        </span>
                        <p class="text-sm font-semibold text-slate-600">Belum ada riwayat presensi</p>
                        <p class="text-xs text-slate-400">Presensi siswa akan muncul di sini setelah tercatat.</p>
                    </li>
                @endforelse
            </ul>
        </section>

        {{-- Catatan poin --}}
        <section class="overflow-hidden rounded-3xl border border-slate-100 bg-white shadow-sm">
            <header class="flex items-center justify-between gap-3 border-b border-slate-100 px-5 py-4">
                <h3 class="flex items-center gap-2.5 text-sm font-bold text-slate-900">
                    <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-amber-50 text-amber-600">
                        <span class="material-symbols-outlined text-lg">military_tech</span>
                    </span>
                    Catatan poin kedisiplinan
                </h3>
                <a href="{{ route('parent.points') }}" class="inline-flex items-center gap-0.5 rounded-full px-2.5 py-1 text-xs font-semibold text-emerald-700 transition hover:bg-emerald-50">
                    Lihat semua
                    <span class="material-symbols-outlined text-base">chevron_right</span>
                </a>
            </header>

            <ul class="divide-y divide-slate-100">
                @forelse($recentPoints as $p)
                    @php $isViolation = $p->type === 'violation'; @endphp
                    <li class="flex items-center gap-3 px-5 py-3.5 transition hover:bg-slate-50/70">
                        <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl {{ $isViolation ? 'bg-rose-50 text-rose-600' : 'bg-emerald-50 text-emerald-600' }}">
                            <span class="material-symbols-outlined text-xl">{{ $isViolation ? 'trending_down' : 'trending_up' }}</span>
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="truncate text-sm font-semibold text-slate-800">{{ $p->reason }}</p>
                            <p class="mt-0.5 text-xs text-slate-400">{{ \Carbon\Carbon::parse($p->date)->translatedFormat('d M Y') }}</p>
                        </div>
                        <span class="shrink-0 rounded-full px-2.5 py-1 text-xs font-bold ring-1 ring-inset {{ $isViolation ? 'bg-rose-50 text-rose-700 ring-rose-200' : 'bg-emerald-50 text-emerald-700 ring-emerald-200' }}">
                            {{ $isViolation ? '-' : '+' }}{{ $p->score }} poin
                        </span>
                    </li>
                @empty
                    <li class="flex flex-col items-center gap-2 px-5 py-10 text-center">
                        <span class="flex h-12 w-12 items-center justify-center rounded-2xl bg-emerald-50 text-emerald-500">
                            <span class="material-symbols-outlined text-2xl">verified</span>
                        </span>
                        <p class="text-sm font-semibold text-slate-600">Tidak ada catatan pelanggaran</p>
                        <p class="text-xs text-slate-400">Pertahankan kedisiplinan yang baik.</p>
                    </li>
                @endforelse
            </ul>
        </section>
    </div>

</div>
@endsection