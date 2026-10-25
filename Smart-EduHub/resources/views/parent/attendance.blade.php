@extends('layouts.parent')

@section('title', 'Presensi Anak')
@section('page_title', 'Presensi Harian Anak')

@php
    // Kelas ditulis lengkap agar terbaca oleh Tailwind
    $statusMap = [
        'present' => ['label' => 'Ontime',     'icon' => 'check_circle', 'chip' => 'bg-emerald-100 text-emerald-700', 'tile' => 'bg-emerald-50 text-emerald-600', 'bar' => 'bg-emerald-500', 'card' => 'bg-emerald-50 border-emerald-100', 'num' => 'text-emerald-800', 'sub' => 'text-emerald-700'],
        'late'    => ['label' => 'Telat',      'icon' => 'schedule',     'chip' => 'bg-amber-100 text-amber-700',     'tile' => 'bg-amber-50 text-amber-600',     'bar' => 'bg-amber-400',   'card' => 'bg-amber-50 border-amber-100',     'num' => 'text-amber-800',   'sub' => 'text-amber-700'],
        'excused' => ['label' => 'Izin / Sakit','icon' => 'medical_information', 'chip' => 'bg-orange-100 text-orange-700', 'tile' => 'bg-orange-50 text-orange-600', 'bar' => 'bg-orange-400', 'card' => 'bg-orange-50 border-orange-100', 'num' => 'text-orange-800', 'sub' => 'text-orange-700'],
        'absent'  => ['label' => 'Alpa',       'icon' => 'cancel',       'chip' => 'bg-rose-100 text-rose-700',       'tile' => 'bg-rose-50 text-rose-600',       'bar' => 'bg-rose-500',    'card' => 'bg-rose-50 border-rose-100',       'num' => 'text-rose-800',    'sub' => 'text-rose-700'],
    ];
    $statusFor = fn ($s) => $statusMap[in_array($s, ['present', 'late', 'excused']) ? $s : 'absent'];

    // Statistik
    $totalDays = collect($statusMap)->keys()->sum(fn ($k) => (int) ($stats[$k] ?? 0));
    $attended  = (int) ($stats['present'] ?? 0) + (int) ($stats['late'] ?? 0);
    $rate      = $totalDays > 0 ? round($attended / $totalDays * 100) : 0;

    // Preset rentang tanggal
    $today   = now();
    $presets = [
        ['Hari ini',  $today->copy(),                $today->copy()],
        ['7 hari',    $today->copy()->subDays(6),    $today->copy()],
        ['30 hari',   $today->copy()->subDays(29),   $today->copy()],
        ['Bulan ini', $today->copy()->startOfMonth(), $today->copy()],
    ];

    $startLabel = \Carbon\Carbon::parse($start_date)->translatedFormat('d M Y');
    $endLabel   = \Carbon\Carbon::parse($end_date)->translatedFormat('d M Y');

    $dateInput = 'h-11 w-full rounded-xl border border-slate-200 bg-white px-3 text-base text-slate-800 shadow-sm transition focus:border-emerald-500 focus:outline-none focus:ring-4 focus:ring-emerald-500/15 sm:text-sm';
@endphp

@section('content')
<div class="mx-auto max-w-6xl space-y-6">

    {{-- ================= Ringkasan ================= --}}
    <section class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-emerald-600 via-emerald-700 to-teal-900 p-5 text-white shadow-lg shadow-emerald-900/15 sm:p-8">
        <div class="pointer-events-none absolute -right-10 -top-10 h-44 w-44 rounded-full bg-white/10"></div>
        <div class="pointer-events-none absolute -bottom-12 left-1/3 h-32 w-32 rounded-full bg-white/5"></div>

        <div class="relative grid gap-6 sm:grid-cols-[auto_1fr] sm:items-center sm:gap-8">
            {{-- Lingkaran tingkat kehadiran --}}
            <div class="flex items-center gap-5 sm:block">
                <div class="relative h-24 w-24 shrink-0 sm:h-32 sm:w-32">
                    <svg viewBox="0 0 100 100" class="h-full w-full -rotate-90" role="img" aria-label="Tingkat kehadiran {{ $rate }} persen">
                        <circle cx="50" cy="50" r="42" fill="none" stroke="white" stroke-opacity="0.18" stroke-width="10"/>
                        <circle cx="50" cy="50" r="42" fill="none" stroke="white" stroke-width="10" stroke-linecap="round"
                                stroke-dasharray="{{ round(2 * M_PI * 42, 2) }}"
                                stroke-dashoffset="{{ round(2 * M_PI * 42 * (1 - $rate / 100), 2) }}"/>
                    </svg>
                    <span class="absolute inset-0 flex items-center justify-center text-2xl font-extrabold tabular-nums sm:text-3xl">{{ $rate }}%</span>
                </div>
                <div class="sm:hidden">
                    <p class="text-sm text-emerald-100/80">Tingkat kehadiran</p>
                    <p class="text-sm text-emerald-100/70">{{ $startLabel }} – {{ $endLabel }}</p>
                </div>
            </div>

            <div class="min-w-0">
                <p class="hidden text-sm text-emerald-100/80 sm:block">Tingkat kehadiran</p>
                <p class="hidden text-lg font-bold sm:block">{{ $startLabel }} – {{ $endLabel }}</p>
                <p class="text-sm text-emerald-100/80 sm:mt-1">
                    Hadir <span class="font-bold text-white">{{ $attended }}</span> dari <span class="font-bold text-white">{{ $totalDays }}</span> hari efektif
                </p>

                {{-- Bar proporsi status --}}
                <div class="mt-4 flex h-3 w-full overflow-hidden rounded-full bg-white/15" role="img"
                     aria-label="Proporsi status presensi">
                    @if($totalDays > 0)
                        @foreach($statusMap as $key => $m)
                            @php $n = (int) ($stats[$key] ?? 0); @endphp
                            @if($n > 0)
                                <span class="{{ $m['bar'] }} h-full" style="width: {{ $n / $totalDays * 100 }}%" title="{{ $m['label'] }}: {{ $n }}"></span>
                            @endif
                        @endforeach
                    @endif
                </div>
            </div>
        </div>
    </section>

    {{-- ================= Statistik ================= --}}
    <section aria-label="Statistik presensi" class="grid grid-cols-2 gap-3 sm:gap-4 lg:grid-cols-4">
        @foreach($statusMap as $key => $m)
            <div class="flex items-center gap-3 rounded-2xl border {{ $m['card'] }} p-4">
                <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-white/80 {{ $m['sub'] }}">
                    <span class="material-symbols-outlined text-[22px]" style="font-variation-settings:'FILL' 1;">{{ $m['icon'] }}</span>
                </span>
                <div class="min-w-0">
                    <p class="text-2xl font-extrabold leading-none tabular-nums {{ $m['num'] }}">{{ $stats[$key] ?? 0 }}</p>
                    <p class="mt-1 truncate text-xs font-medium {{ $m['sub'] }}">{{ $m['label'] }}</p>
                </div>
            </div>
        @endforeach
    </section>

    {{-- ================= Filter ================= --}}
    <section aria-label="Filter tanggal" class="rounded-3xl border border-emerald-100 bg-white p-4 shadow-sm sm:p-5">
        {{-- Preset cepat --}}
        <div class="-mx-1 mb-4 flex gap-2 overflow-x-auto px-1 pb-1 [scrollbar-width:none] [&::-webkit-scrollbar]:hidden">
            @foreach($presets as [$label, $from, $to])
                @php $on = $start_date === $from->format('Y-m-d') && $end_date === $to->format('Y-m-d'); @endphp
                <a href="{{ route('parent.attendance', ['start_date' => $from->format('Y-m-d'), 'end_date' => $to->format('Y-m-d')]) }}"
                   @if($on) aria-current="true" @endif
                   class="shrink-0 rounded-full px-4 py-1.5 text-sm font-semibold transition focus:outline-none focus-visible:ring-4 focus-visible:ring-emerald-500/25
                          {{ $on ? 'bg-emerald-600 text-white shadow-sm' : 'bg-slate-100 text-slate-600 hover:bg-emerald-50 hover:text-emerald-700' }}">
                    {{ $label }}
                </a>
            @endforeach
        </div>

        <form action="{{ route('parent.attendance') }}" method="GET" class="grid gap-3 sm:grid-cols-2 lg:grid-cols-[1fr_1fr_auto_auto] lg:items-end">
            <div>
                <label for="start_date" class="mb-1.5 block text-sm font-semibold text-slate-700">Dari tanggal</label>
                <input type="date" id="start_date" name="start_date" value="{{ $start_date }}" class="{{ $dateInput }}">
            </div>
            <div>
                <label for="end_date" class="mb-1.5 block text-sm font-semibold text-slate-700">Sampai tanggal</label>
                <input type="date" id="end_date" name="end_date" value="{{ $end_date }}" class="{{ $dateInput }}">
            </div>

            <button type="submit"
                    class="inline-flex h-11 items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-emerald-600 to-emerald-500 px-6 text-sm font-semibold text-white shadow-md shadow-emerald-600/25 transition hover:-translate-y-0.5 hover:shadow-lg focus:outline-none focus-visible:ring-4 focus-visible:ring-emerald-500/30 active:translate-y-0 active:scale-[0.98]">
                <span class="material-symbols-outlined text-[18px]">filter_alt</span>
                Terapkan
            </button>

            <a href="{{ route('parent.attendance', ['start_date' => $start_date, 'end_date' => $end_date, 'print' => 1]) }}" target="_blank" rel="noopener"
               class="inline-flex h-11 items-center justify-center gap-2 rounded-xl bg-white px-5 text-sm font-semibold text-slate-700 ring-1 ring-inset ring-slate-200 transition hover:bg-slate-50 focus:outline-none focus-visible:ring-4 focus-visible:ring-slate-300/50 active:scale-[0.98] sm:col-span-2 lg:col-span-1">
                <span class="material-symbols-outlined text-[18px]">print</span>
                Cetak laporan
            </a>
        </form>
    </section>

    {{-- ================= Daftar presensi ================= --}}
    <section aria-labelledby="h-daftar">
        <h3 id="h-daftar" class="mb-4 px-1 text-base font-bold text-slate-900">Riwayat presensi</h3>

        <div class="overflow-hidden rounded-3xl border border-emerald-100 bg-white shadow-sm">

            {{-- Tabel (tablet ke atas) --}}
            <div class="hidden overflow-x-auto md:block">
                <table class="w-full border-collapse text-left text-sm">
                    <thead>
                        <tr class="border-b border-emerald-100 bg-emerald-50/60 text-slate-600">
                            <th scope="col" class="px-6 py-3.5 font-semibold">Tanggal</th>
                            <th scope="col" class="px-6 py-3.5 font-semibold">Jam masuk</th>
                            <th scope="col" class="px-6 py-3.5 font-semibold">Status</th>
                            <th scope="col" class="px-6 py-3.5 font-semibold">Lokasi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($attendances as $att)
                            @php $m = $statusFor($att->status); @endphp
                            <tr class="transition-colors hover:bg-emerald-50/40">
                                <td class="whitespace-nowrap px-6 py-4 font-semibold text-slate-800">
                                    {{ \Carbon\Carbon::parse($att->date)->translatedFormat('l, d F Y') }}
                                </td>
                                <td class="whitespace-nowrap px-6 py-4 font-mono tabular-nums text-slate-600">
                                    {{ $att->check_in ?: '-' }}
                                </td>
                                <td class="whitespace-nowrap px-6 py-4">
                                    <span class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-xs font-semibold {{ $m['chip'] }}">
                                        <span class="material-symbols-outlined text-[15px]" style="font-variation-settings:'FILL' 1;">{{ $m['icon'] }}</span>
                                        {{ $m['label'] }}
                                    </span>
                                </td>
                                <td class="max-w-sm px-6 py-4 text-slate-500">
                                    <span class="line-clamp-2">{{ $att->location ?: '-' }}</span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-14 text-center">
                                    <span class="mx-auto mb-3 flex h-12 w-12 items-center justify-center rounded-full bg-emerald-50 text-emerald-400">
                                        <span class="material-symbols-outlined text-3xl">event_busy</span>
                                    </span>
                                    <p class="text-sm font-semibold text-slate-600">Tidak ada data presensi</p>
                                    <p class="mt-1 text-xs text-slate-400">Coba ubah rentang tanggal di atas.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Daftar (ponsel) --}}
            <ul class="divide-y divide-slate-100 md:hidden">
                @forelse($attendances as $att)
                    @php
                        $m = $statusFor($att->status);
                        $d = \Carbon\Carbon::parse($att->date);
                    @endphp
                    <li class="flex items-start gap-3 px-4 py-4">
                        <div class="flex h-14 w-14 shrink-0 flex-col items-center justify-center rounded-2xl {{ $m['tile'] }}">
                            <span class="text-lg font-extrabold leading-none tabular-nums">{{ $d->format('d') }}</span>
                            <span class="mt-0.5 text-xs font-medium">{{ $d->translatedFormat('M') }}</span>
                        </div>
                        <div class="min-w-0 flex-1">
                            <div class="flex items-center justify-between gap-2">
                                <p class="truncate text-sm font-semibold text-slate-800">{{ $d->translatedFormat('l, d F Y') }}</p>
                            </div>
                            <div class="mt-1.5 flex flex-wrap items-center gap-x-3 gap-y-1.5">
                                <span class="inline-flex items-center gap-1 rounded-full px-2.5 py-0.5 text-xs font-semibold {{ $m['chip'] }}">
                                    <span class="material-symbols-outlined text-[14px]" style="font-variation-settings:'FILL' 1;">{{ $m['icon'] }}</span>
                                    {{ $m['label'] }}
                                </span>
                                <span class="inline-flex items-center gap-1 text-xs text-slate-500">
                                    <span class="material-symbols-outlined text-[15px]">login</span>
                                    <span class="font-mono tabular-nums">{{ $att->check_in ?: '-' }}</span>
                                </span>
                            </div>
                            @if($att->location)
                                <p class="mt-2 inline-flex items-start gap-1 text-sm leading-relaxed text-slate-500">
                                    <span class="material-symbols-outlined text-[15px] leading-5">location_on</span>
                                    <span>{{ $att->location }}</span>
                                </p>
                            @endif
                        </div>
                    </li>
                @empty
                    <li class="px-6 py-12 text-center">
                        <span class="mx-auto mb-3 flex h-12 w-12 items-center justify-center rounded-full bg-emerald-50 text-emerald-400">
                            <span class="material-symbols-outlined text-3xl">event_busy</span>
                        </span>
                        <p class="text-sm font-semibold text-slate-600">Tidak ada data presensi</p>
                        <p class="mt-1 text-xs text-slate-400">Coba ubah rentang tanggal di atas.</p>
                    </li>
                @endforelse
            </ul>
        </div>
    </section>
</div>
@endsection