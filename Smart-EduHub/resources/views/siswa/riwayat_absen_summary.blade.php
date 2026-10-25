@extends('layouts.siswa')

@section('title', 'Ringkasan Presensi')
@section('page_title', 'Riwayat Kehadiran')

@section('content')
@php
    // ---------------------------------------------------------------
    // Hitung data per bulan satu kali, dipakai oleh kartu (mobile)
    // dan tabel (desktop).
    // ---------------------------------------------------------------
    $reportStart = \Carbon\Carbon::parse($start_date)->startOfDay();
    $reportEnd   = \Carbon\Carbon::parse($end_date)->startOfDay();
    $today       = \Carbon\Carbon::today();

    $rows = [];
    $total_o = $total_t = $total_i = $total_s = $total_a = $total_days = 0;

    foreach ($months as $month) {
        $monthStart  = \Carbon\Carbon::parse($month['key'] . '-01')->startOfDay();
        $monthEnd    = $monthStart->copy()->endOfMonth()->startOfDay();
        $periodStart = $monthStart->copy()->max($reportStart);
        $periodEnd   = $monthEnd->copy()->min($reportEnd)->min($today); // hanya dihitung sampai hari ini

        $holidayCount = 0;
        $totalDays    = 0;
        if ($periodStart <= $periodEnd) {
            $cursor = $periodStart->copy();
            while ($cursor <= $periodEnd) {
                if (\App\Models\Holiday::checkHoliday($cursor)) {
                    $holidayCount++;
                }
                $cursor->addDay();
            }
            $totalDays = (int) abs($periodStart->diffInDays($periodEnd)) + 1 - $holidayCount;
        }

        $monthAttendances = $student->attendances->filter(function ($a) use ($month) {
            return strpos($a->date, $month['key']) === 0;
        });

        $o = $monthAttendances->where('status', 'present')->count();
        $t = $monthAttendances->where('status', 'late')->count();
        $i = $monthAttendances->filter(fn ($a) => $a->status === 'excused' && strpos(strtolower($a->location), 'sakit') === false)->count();
        $s = $monthAttendances->filter(fn ($a) => $a->status === 'excused' && strpos(strtolower($a->location), 'sakit') !== false)->count();
        $recordedAbsent = $monthAttendances->where('status', 'absent')->count();

        // Alpa = hari tanpa catatan (sampai hari ini, tanpa libur) + alpa yang tercatat
        $daysWithAttendance = $o + $t + $i + $s + $recordedAbsent;
        $a = max(0, ($totalDays - $daysWithAttendance) + $recordedAbsent);

        $hadir   = $o + $t;
        $percent = $totalDays > 0 ? min(100, round(($hadir / $totalDays) * 100)) : null;

        $rows[] = [
            'name' => $month['name'], 'h' => $hadir, 'o' => $o, 't' => $t,
            'i' => $i, 's' => $s, 'a' => $a, 'days' => $totalDays, 'percent' => $percent,
        ];

        $total_o += $o; $total_t += $t; $total_i += $i; $total_s += $s; $total_a += $a;
        $total_days += max(0, $totalDays);
    }

    $total_h       = $total_o + $total_t;
    $total_percent = $total_days > 0 ? min(100, round(($total_h / $total_days) * 100)) : null;

    $barColor = function ($p) {
        if ($p === null) return 'bg-slate-200';
        if ($p >= 90) return 'bg-emerald-500';
        if ($p >= 75) return 'bg-amber-500';
        return 'bg-rose-500';
    };

    // Status keseluruhan (dipakai badge header, ring, dan kotak tingkat)
    if ($total_percent === null) {
        $lv = ['tier' => -1, 'short' => 'Belum ada data', 'label' => 'Belum ada data kehadiran', 'icon' => 'info',
               'dot' => 'bg-slate-300', 'text' => 'text-slate-500', 'ring' => 'text-slate-300', 'bar' => 'bg-slate-300'];
    } elseif ($total_percent >= 90) {
        $lv = ['tier' => 0, 'short' => 'Baik', 'label' => 'Kehadiran sangat baik', 'icon' => 'check_circle',
               'dot' => 'bg-emerald-500', 'text' => 'text-emerald-700', 'ring' => 'text-emerald-500', 'bar' => 'bg-emerald-500'];
    } elseif ($total_percent >= 75) {
        $lv = ['tier' => 1, 'short' => 'Cukup', 'label' => 'Perlu ditingkatkan', 'icon' => 'error',
               'dot' => 'bg-amber-500', 'text' => 'text-amber-700', 'ring' => 'text-amber-500', 'bar' => 'bg-amber-500'];
    } else {
        $lv = ['tier' => 2, 'short' => 'Rendah', 'label' => 'Perlu perhatian', 'icon' => 'warning',
               'dot' => 'bg-rose-500', 'text' => 'text-rose-700', 'ring' => 'text-rose-500', 'bar' => 'bg-rose-500'];
    }

    $tiers = [
        ['name' => 'Baik',   'range' => '90–100%',      'dot' => 'bg-emerald-500', 'border' => 'border-emerald-400'],
        ['name' => 'Cukup',  'range' => '75–89%',       'dot' => 'bg-amber-500',   'border' => 'border-amber-400'],
        ['name' => 'Rendah', 'range' => 'di bawah 75%', 'dot' => 'bg-rose-500',    'border' => 'border-rose-400'],
    ];

    // Pilihan rentang cepat
    $presets = [
        'Bulan ini'        => [$today->copy()->startOfMonth(), $today->copy()->endOfMonth()],
        'Bulan lalu'       => [$today->copy()->subMonthNoOverflow()->startOfMonth(), $today->copy()->subMonthNoOverflow()->endOfMonth()],
        '30 hari terakhir' => [$today->copy()->subDays(29), $today->copy()],
    ];
@endphp

{{-- Full frame: tanpa max-width --}}
<div class="w-full pb-8">

    {{-- ============ Header oranye (edge-to-edge di desktop) ============ --}}
    {{-- CATATAN: md:-mx-8 md:-mt-8 harus sama dengan padding konten di layouts.siswa --}}
    <header class="relative overflow-hidden text-white
                   -mx-4 -mt-4 px-5 pt-8 pb-24 rounded-b-[2.5rem]
                   md:-mx-8 md:-mt-8 md:px-12 md:pt-10 md:pb-28 md:rounded-t-none md:rounded-b-[3rem]
                   bg-gradient-to-br from-orange-400 via-red-500 to-red-800">
        <div class="pointer-events-none absolute -top-20 -right-12 w-72 h-72 md:w-96 md:h-96 rounded-full bg-white/10"></div>
        <div class="pointer-events-none absolute -bottom-20 left-10 w-56 h-56 rounded-full bg-white/5"></div>

        <div class="relative flex items-start justify-between gap-4">
            <div class="min-w-0">
                <h1 class="text-3xl md:text-4xl font-extrabold tracking-tight">Riwayat Kehadiran</h1>
                <p class="text-white/80 text-base md:text-lg mt-1">Laporan dan rekapitulasi presensi</p>
            </div>
            <span class="shrink-0 inline-flex items-center gap-2.5 bg-white text-slate-900 font-semibold text-base md:text-lg px-5 py-3 rounded-full shadow-lg shadow-red-900/20">
                <i class="w-3 h-3 rounded-full {{ $lv['dot'] }}"></i>
                {{ $lv['short'] }}
            </span>
        </div>
    </header>

    {{-- ============ Kartu utama (menimpa header) ============ --}}
    <section class="relative z-10 -mt-14 md:-mt-16 mx-1 md:mx-8 xl:mx-12 bg-white rounded-[2rem] shadow-xl shadow-slate-300/40 border border-slate-100 overflow-hidden">

        {{-- Filter --}}
        <div class="no-print p-5 md:p-7 border-b border-slate-100 space-y-4">
            <nav class="flex gap-2 overflow-x-auto -mx-5 px-5 md:mx-0 md:px-0 pb-1 [scrollbar-width:none] [&::-webkit-scrollbar]:hidden" aria-label="Rentang cepat">
                @foreach($presets as $label => [$from, $to])
                    @php $active = $reportStart->isSameDay($from) && $reportEnd->isSameDay($to); @endphp
                    <a href="{{ route('siswa.attendance', ['mode' => 'summary', 'start_date' => $from->toDateString(), 'end_date' => $to->toDateString()]) }}"
                       @if($active) aria-current="true" @endif
                       class="shrink-0 px-4 py-2 rounded-full text-sm md:text-base font-semibold border transition focus:outline-none focus-visible:ring-4 focus-visible:ring-orange-500/20
                              {{ $active ? 'bg-orange-500 text-white border-orange-500 shadow-md shadow-orange-500/30' : 'bg-slate-50 text-slate-600 border-slate-200 hover:bg-slate-100' }}">
                        {{ $label }}
                    </a>
                @endforeach
            </nav>

            <form action="{{ route('siswa.attendance') }}" method="GET" class="grid grid-cols-2 md:grid-cols-3 gap-3 md:gap-4">
                <div class="flex flex-col gap-1.5">
                    <label for="start_date" class="text-sm font-medium text-slate-500 ml-1">Dari tanggal</label>
                    <input id="start_date" type="date" name="start_date" value="{{ $start_date }}" onchange="this.form.submit()"
                           class="w-full min-h-[3rem] px-3 rounded-xl bg-slate-50 border border-slate-200 text-slate-800 text-sm md:text-base font-semibold focus:bg-white focus:ring-4 focus:ring-orange-500/10 focus:border-orange-500 outline-none transition cursor-pointer">
                </div>
                <div class="flex flex-col gap-1.5">
                    <label for="end_date" class="text-sm font-medium text-slate-500 ml-1">Sampai tanggal</label>
                    <input id="end_date" type="date" name="end_date" value="{{ $end_date }}" onchange="this.form.submit()"
                           class="w-full min-h-[3rem] px-3 rounded-xl bg-slate-50 border border-slate-200 text-slate-800 text-sm md:text-base font-semibold focus:bg-white focus:ring-4 focus:ring-orange-500/10 focus:border-orange-500 outline-none transition cursor-pointer">
                </div>
                <div class="flex flex-col gap-1.5 col-span-2 md:col-span-1">
                    <label for="mode" class="text-sm font-medium text-slate-500 ml-1">Mode tampilan</label>
                    <select id="mode" name="mode" onchange="this.form.submit()"
                            class="w-full min-h-[3rem] px-3 rounded-xl bg-slate-50 border border-slate-200 text-slate-800 text-sm md:text-base font-semibold focus:bg-white focus:ring-4 focus:ring-orange-500/10 focus:border-orange-500 outline-none transition cursor-pointer">
                        <option value="detail" {{ $mode === 'detail' ? 'selected' : '' }}>Harian</option>
                        <option value="summary" {{ $mode === 'summary' ? 'selected' : '' }}>Ringkasan</option>
                    </select>
                </div>
            </form>
        </div>

        {{-- Indeks kehadiran --}}
        <div class="p-5 md:p-7">
            <div class="flex items-center justify-between gap-3">
                <p class="text-base md:text-lg text-slate-500 font-medium">Indeks kehadiran</p>
                <span class="px-4 py-1.5 rounded-full bg-orange-50 text-orange-800 text-sm md:text-base font-semibold">{{ $total_days }} hari belajar</span>
            </div>

            <div class="mt-4 grid lg:grid-cols-5 lg:gap-10 lg:items-center">
                {{-- Ring --}}
                <div class="lg:col-span-2 flex justify-center">
                    <div class="relative w-52 h-52 md:w-60 md:h-60">
                        <svg viewBox="0 0 36 36" class="w-full h-full -rotate-90" aria-hidden="true">
                            <circle cx="18" cy="18" r="15.9" fill="none" stroke="currentColor" stroke-width="3.4" class="text-slate-100"/>
                            <circle cx="18" cy="18" r="15.9" fill="none" stroke="currentColor" stroke-width="3.4" stroke-linecap="round"
                                    class="{{ $lv['ring'] }}" stroke-dasharray="{{ $total_percent ?? 0 }}, 100"/>
                        </svg>
                        <div class="absolute inset-0 flex flex-col items-center justify-center">
                            <span class="text-6xl md:text-7xl font-extrabold text-slate-900 leading-none">{{ $total_h }}</span>
                            <span class="text-base md:text-lg text-slate-500 mt-2">dari {{ $total_days }} hari</span>
                        </div>
                    </div>
                </div>

                {{-- Status + tingkat --}}
                <div class="lg:col-span-3 mt-6 lg:mt-0 space-y-5">
                    <p class="flex items-center justify-center lg:justify-start gap-2.5 text-2xl md:text-3xl font-extrabold {{ $lv['text'] }}">
                        <span class="material-symbols-outlined text-[30px] md:text-[34px]" style="font-variation-settings:'FILL' 1">{{ $lv['icon'] }}</span>
                        {{ $lv['label'] }}
                    </p>

                    <div>
                        <div class="flex items-center justify-between gap-3 text-sm md:text-base">
                            <span class="text-slate-500 font-medium">Tingkat kehadiran</span>
                            <span class="font-bold {{ $lv['text'] }}">{{ $total_percent !== null ? $total_percent . '% hadir' : '-' }}</span>
                        </div>
                        <div class="mt-2 h-3 rounded-full bg-slate-100 overflow-hidden" role="progressbar"
                             aria-valuenow="{{ $total_percent ?? 0 }}" aria-valuemin="0" aria-valuemax="100">
                            <div class="h-full rounded-full {{ $lv['bar'] }}" style="width: {{ $total_percent ?? 0 }}%"></div>
                        </div>
                    </div>

                    <div class="grid grid-cols-3 gap-2 md:gap-3">
                        @foreach($tiers as $idx => $tr)
                            @php $on = $lv['tier'] === $idx; @endphp
                            <div class="rounded-2xl px-2 py-3 text-center {{ $on ? 'bg-white border-[3px] ' . $tr['border'] : 'bg-slate-50 border border-slate-100' }}">
                                <p class="flex items-center justify-center gap-1.5 font-semibold text-slate-800 text-sm md:text-base">
                                    <i class="w-2.5 h-2.5 rounded-full {{ $tr['dot'] }}"></i>{{ $tr['name'] }}
                                </p>
                                <p class="text-xs md:text-sm text-slate-500 mt-0.5">{{ $tr['range'] }}</p>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <p class="mt-6 pt-4 border-t border-slate-100 text-sm md:text-base text-slate-500">
                {{ $reportStart->translatedFormat('j M Y') }} – {{ $reportEnd->translatedFormat('j M Y') }}
            </p>
        </div>
    </section>

    {{-- ============ Rincian status ============ --}}
    <section aria-label="Rincian kehadiran" class="mt-4 mx-1 md:mx-8 xl:mx-12 grid grid-cols-2 lg:grid-cols-4 gap-3 md:gap-4">
        <div class="rounded-[1.75rem] bg-white border border-slate-100 shadow-md shadow-slate-200/60 p-4 md:p-5 flex items-center gap-3 md:gap-4">
            <span class="shrink-0 w-12 h-12 md:w-14 md:h-14 rounded-2xl bg-emerald-50 flex items-center justify-center">
                <span class="material-symbols-outlined text-emerald-600 text-[26px]" style="font-variation-settings:'FILL' 1">check_circle</span>
            </span>
            <div class="min-w-0">
                <p class="text-sm md:text-base text-slate-500 truncate">Tepat waktu</p>
                <p class="text-3xl md:text-4xl font-extrabold text-emerald-600 leading-tight">{{ $total_o }} <span class="text-sm md:text-base font-medium text-slate-400">hari</span></p>
            </div>
        </div>

        <div class="rounded-[1.75rem] bg-white border border-slate-100 shadow-md shadow-slate-200/60 p-4 md:p-5 flex items-center gap-3 md:gap-4">
            <span class="shrink-0 w-12 h-12 md:w-14 md:h-14 rounded-2xl bg-amber-50 flex items-center justify-center">
                <span class="material-symbols-outlined text-amber-600 text-[26px]" style="font-variation-settings:'FILL' 1">schedule</span>
            </span>
            <div class="min-w-0">
                <p class="text-sm md:text-base text-slate-500 truncate">Terlambat</p>
                <p class="text-3xl md:text-4xl font-extrabold text-amber-600 leading-tight">{{ $total_t }} <span class="text-sm md:text-base font-medium text-slate-400">hari</span></p>
            </div>
        </div>

        <div class="rounded-[1.75rem] bg-white border border-slate-100 shadow-md shadow-slate-200/60 p-4 md:p-5 flex items-center gap-3 md:gap-4">
            <span class="shrink-0 w-12 h-12 md:w-14 md:h-14 rounded-2xl bg-orange-50 flex items-center justify-center">
                <span class="material-symbols-outlined text-orange-600 text-[26px]" style="font-variation-settings:'FILL' 1">assignment_turned_in</span>
            </span>
            <div class="min-w-0">
                <p class="text-sm md:text-base text-slate-500 truncate">Izin / sakit</p>
                <p class="text-3xl md:text-4xl font-extrabold text-orange-600 leading-tight">{{ $total_i + $total_s }} <span class="text-sm md:text-base font-medium text-slate-400">hari</span></p>
            </div>
        </div>

        <div class="rounded-[1.75rem] bg-white border border-slate-100 shadow-md shadow-slate-200/60 p-4 md:p-5 flex items-center gap-3 md:gap-4">
            <span class="shrink-0 w-12 h-12 md:w-14 md:h-14 rounded-2xl bg-rose-50 flex items-center justify-center">
                <span class="material-symbols-outlined text-rose-600 text-[26px]" style="font-variation-settings:'FILL' 1">cancel</span>
            </span>
            <div class="min-w-0">
                <p class="text-sm md:text-base text-slate-500 truncate">Alpa</p>
                <p class="text-3xl md:text-4xl font-extrabold text-rose-600 leading-tight">{{ $total_a }} <span class="text-sm md:text-base font-medium text-slate-400">hari</span></p>
            </div>
        </div>
    </section>

    {{-- ============ Rekap bulanan ============ --}}
    <section class="mt-8 mx-1 md:mx-8 xl:mx-12">
        <div class="flex items-end justify-between gap-3 px-1">
            <div class="min-w-0">
                <h2 class="text-2xl md:text-3xl font-extrabold text-slate-900 tracking-tight">Rekapitulasi bulanan</h2>
                <p class="text-base md:text-lg text-slate-500 mt-0.5">Rincian kehadiran per bulan.</p>
            </div>
            <a href="{{ route('siswa.attendance', array_merge(request()->all(), ['print' => 1])) }}" target="_blank"
               class="no-print shrink-0 inline-flex items-center gap-2 px-4 md:px-5 py-2.5 rounded-full bg-white border border-slate-200 text-slate-700 text-sm md:text-base font-semibold hover:bg-slate-50 focus:outline-none focus-visible:ring-4 focus-visible:ring-orange-500/20 transition">
                <span class="material-symbols-outlined text-[20px]">print</span>
                Cetak PDF
            </a>
        </div>

        {{-- Mobile: kartu per bulan --}}
        <ul class="md:hidden mt-4 space-y-3">
            @forelse($rows as $r)
                <li class="rounded-[1.75rem] bg-white border border-slate-100 shadow-md shadow-slate-200/60 p-4 space-y-3">
                    <div class="flex items-center justify-between gap-3">
                        <p class="font-bold text-slate-900 text-lg">{{ $r['name'] }}</p>
                        <span class="px-3 py-1 rounded-full bg-orange-50 text-orange-800 text-xs font-semibold">
                            {{ $r['percent'] !== null ? $r['percent'] . '% hadir' : 'Belum ada hari' }}
                        </span>
                    </div>

                    <div class="h-2.5 rounded-full bg-slate-100 overflow-hidden" role="progressbar"
                         aria-valuenow="{{ $r['percent'] ?? 0 }}" aria-valuemin="0" aria-valuemax="100">
                        <div class="h-full rounded-full {{ $barColor($r['percent']) }}" style="width: {{ $r['percent'] ?? 0 }}%"></div>
                    </div>

                    <dl class="grid grid-cols-4 gap-2 text-center">
                        <div class="rounded-2xl bg-slate-50 border border-slate-100 py-2.5">
                            <dd class="text-xl font-extrabold text-emerald-600">{{ $r['o'] ?: '-' }}</dd>
                            <dt class="text-xs text-slate-500">Tepat</dt>
                        </div>
                        <div class="rounded-2xl bg-slate-50 border border-slate-100 py-2.5">
                            <dd class="text-xl font-extrabold text-amber-600">{{ $r['t'] ?: '-' }}</dd>
                            <dt class="text-xs text-slate-500">Telat</dt>
                        </div>
                        <div class="rounded-2xl bg-slate-50 border border-slate-100 py-2.5">
                            <dd class="text-xl font-extrabold text-orange-600">{{ ($r['i'] + $r['s']) ?: '-' }}</dd>
                            <dt class="text-xs text-slate-500">Izin/sakit</dt>
                        </div>
                        <div class="rounded-2xl bg-slate-50 border border-slate-100 py-2.5">
                            <dd class="text-xl font-extrabold text-rose-600">{{ $r['a'] ?: '-' }}</dd>
                            <dt class="text-xs text-slate-500">Alpa</dt>
                        </div>
                    </dl>
                </li>
            @empty
                <li class="rounded-[1.75rem] bg-white border border-slate-100 shadow-md shadow-slate-200/60 p-8 text-center text-slate-400">
                    Belum ada data pada rentang tanggal ini.
                </li>
            @endforelse
        </ul>

        {{-- Desktop: tabel --}}
        <div class="hidden md:block mt-4 rounded-[2rem] bg-white border border-slate-100 shadow-md shadow-slate-200/60 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50 text-sm font-semibold text-slate-500">
                            <th scope="col" class="px-6 py-4">Bulan</th>
                            <th scope="col" class="px-3 py-4 w-56">Kehadiran</th>
                            <th scope="col" class="px-3 py-4 text-center text-emerald-700">Hadir</th>
                            <th scope="col" class="px-3 py-4 text-center text-emerald-600">Tepat waktu</th>
                            <th scope="col" class="px-3 py-4 text-center text-amber-600">Terlambat</th>
                            <th scope="col" class="px-3 py-4 text-center text-orange-600">Izin</th>
                            <th scope="col" class="px-3 py-4 text-center text-sky-600">Sakit</th>
                            <th scope="col" class="px-6 py-4 text-center text-rose-600">Alpa</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($rows as $r)
                            <tr class="hover:bg-orange-50/40 transition-colors">
                                <th scope="row" class="px-6 py-4 font-bold text-slate-800 whitespace-nowrap">{{ $r['name'] }}</th>
                                <td class="px-3 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="flex-1 h-2.5 rounded-full bg-slate-100 overflow-hidden">
                                            <div class="h-full rounded-full {{ $barColor($r['percent']) }}" style="width: {{ $r['percent'] ?? 0 }}%"></div>
                                        </div>
                                        <span class="w-10 text-right text-xs font-bold text-slate-600">{{ $r['percent'] !== null ? $r['percent'] . '%' : '-' }}</span>
                                    </div>
                                </td>
                                <td class="px-3 py-4 text-center">
                                    <span class="inline-flex min-w-9 justify-center px-2.5 py-1 rounded-lg font-extrabold {{ $r['h'] ? 'bg-emerald-600 text-white' : 'text-slate-300' }}">{{ $r['h'] ?: '-' }}</span>
                                </td>
                                <td class="px-3 py-4 text-center font-semibold text-emerald-700">{{ $r['o'] ?: '-' }}</td>
                                <td class="px-3 py-4 text-center font-semibold text-amber-700">{{ $r['t'] ?: '-' }}</td>
                                <td class="px-3 py-4 text-center font-semibold text-orange-700">{{ $r['i'] ?: '-' }}</td>
                                <td class="px-3 py-4 text-center font-semibold text-sky-700">{{ $r['s'] ?: '-' }}</td>
                                <td class="px-6 py-4 text-center font-semibold text-rose-700">{{ $r['a'] ?: '-' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-6 py-10 text-center text-slate-400">Belum ada data pada rentang tanggal ini.</td>
                            </tr>
                        @endforelse
                    </tbody>
                    <tfoot>
                        <tr class="bg-slate-900 text-white">
                            <th scope="row" class="px-6 py-4 font-bold">Total akumulasi</th>
                            <td class="px-3 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="flex-1 h-2.5 rounded-full bg-white/15 overflow-hidden">
                                        <div class="h-full rounded-full bg-emerald-400" style="width: {{ $total_percent ?? 0 }}%"></div>
                                    </div>
                                    <span class="w-10 text-right text-xs font-bold">{{ $total_percent !== null ? $total_percent . '%' : '-' }}</span>
                                </div>
                            </td>
                            <td class="px-3 py-4 text-center font-extrabold">{{ $total_h ?: '-' }}</td>
                            <td class="px-3 py-4 text-center font-bold">{{ $total_o ?: '-' }}</td>
                            <td class="px-3 py-4 text-center font-bold">{{ $total_t ?: '-' }}</td>
                            <td class="px-3 py-4 text-center font-bold">{{ $total_i ?: '-' }}</td>
                            <td class="px-3 py-4 text-center font-bold">{{ $total_s ?: '-' }}</td>
                            <td class="px-6 py-4 text-center font-bold">{{ $total_a ?: '-' }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <p class="no-print mt-4 px-1 text-sm text-slate-500">
            Hadir = tepat waktu + terlambat. Hari libur dan hari yang belum berlangsung tidak dihitung.
        </p>
    </section>
</div>
@endsection