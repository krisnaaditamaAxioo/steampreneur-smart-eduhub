@extends('layouts.parent')

@section('title', 'Poin Kedisiplinan')
@section('page_title', 'Catatan Poin Kedisiplinan Siswa')

@section('content')
<div class="space-y-4 sm:space-y-6">

    {{-- Ringkasan poin --}}
    <section class="grid grid-cols-1 gap-3 sm:grid-cols-3" aria-label="Ringkasan poin kedisiplinan">

        <div class="flex items-center gap-3 rounded-2xl border border-rose-100 bg-rose-50 p-4">
            <span class="grid h-11 w-11 shrink-0 place-items-center rounded-xl bg-white/80 text-rose-600" aria-hidden="true">
                <span class="material-symbols-outlined text-[22px]" style="font-variation-settings:'FILL' 1;">report</span>
            </span>
            <div class="min-w-0">
                <p class="text-xs font-semibold text-rose-700">Jumlah Pelanggaran</p>
                <p class="mt-0.5 text-xl font-extrabold leading-none tabular-nums text-rose-700">
                    {{ $violationPoints }} <span class="text-xs font-semibold text-rose-500/80">poin</span>
                </p>
                <p class="mt-1 text-[11px] text-rose-600/80">{{ $violationCount }} catatan</p>
            </div>
        </div>

        <div class="flex items-center gap-3 rounded-2xl border border-emerald-100 bg-emerald-50 p-4">
            <span class="grid h-11 w-11 shrink-0 place-items-center rounded-xl bg-white/80 text-emerald-600" aria-hidden="true">
                <span class="material-symbols-outlined text-[22px]" style="font-variation-settings:'FILL' 1;">emoji_events</span>
            </span>
            <div class="min-w-0">
                <p class="text-xs font-semibold text-emerald-700">Jumlah Penghargaan</p>
                <p class="mt-0.5 text-xl font-extrabold leading-none tabular-nums text-emerald-700">
                    +{{ $achievementPoints }} <span class="text-xs font-semibold text-emerald-500/80">poin</span>
                </p>
                <p class="mt-1 text-[11px] text-emerald-600/80">{{ $achievementCount }} catatan</p>
            </div>
        </div>

        <div class="flex items-center gap-3 rounded-2xl border border-slate-200 bg-slate-50 p-4">
            <span class="grid h-11 w-11 shrink-0 place-items-center rounded-xl bg-white/80 text-slate-600" aria-hidden="true">
                <span class="material-symbols-outlined text-[22px]" style="font-variation-settings:'FILL' 1;">calculate</span>
            </span>
            <div class="min-w-0">
                <p class="text-xs font-semibold text-slate-600">Total Semua Poin</p>
                <p class="mt-0.5 text-xl font-extrabold leading-none tabular-nums text-slate-800">
                    {{ $totalPoints }} <span class="text-xs font-semibold text-slate-400">poin</span>
                </p>
                <p class="mt-1 text-[11px] text-slate-500">{{ $violationCount + $achievementCount }} catatan</p>
            </div>
        </div>
    </section>

    <!-- Riwayat Poin -->
    <div class="bg-white rounded-3xl border border-slate-100 shadow-sm overflow-hidden">

        {{-- Tabel (tablet ke atas) --}}
        <div class="hidden overflow-x-auto md:block">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-slate-50/50 border-b border-slate-100">
                        <th class="px-6 py-4 font-bold text-slate-500 uppercase">Tanggal</th>
                        <th class="px-6 py-4 font-bold text-slate-500 uppercase">Kategori</th>
                        <th class="px-6 py-4 font-bold text-slate-500 uppercase">Keterangan / Alasan</th>
                        <th class="px-6 py-4 font-bold text-slate-500 uppercase text-right">Poin</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($points as $p)
                    <tr class="hover:bg-slate-50/50 transition-colors">
                        <td class="px-6 py-4 text-slate-600 font-bold">
                            {{ \Carbon\Carbon::parse($p->date)->translatedFormat('d M Y') }}
                        </td>
                        <td class="px-6 py-4">
                            @if($p->type === 'violation')
                                <span class="px-2.5 py-1 rounded-full font-bold bg-rose-100 text-rose-700 text-[10px]">Pelanggaran</span>
                            @else
                                <span class="px-2.5 py-1 rounded-full font-bold bg-emerald-100 text-emerald-700 text-[10px]">Penghargaan</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 font-semibold text-slate-800">
                            {{ $p->reason }}
                        </td>
                        <td class="px-6 py-4 text-right font-black text-sm {{ $p->type === 'violation' ? 'text-rose-600' : 'text-emerald-600' }}">
                            {{ $p->type === 'violation' ? '-' : '+' }}{{ $p->score }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-6 py-10 text-center text-slate-400 italic">
                            Tidak ada data catatan poin kedisiplinan.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Daftar kartu (ponsel) --}}
        <ul class="divide-y divide-slate-100 md:hidden">
            @forelse($points as $p)
                @php $isViolation = $p->type === 'violation'; @endphp
                <li class="flex items-start gap-3 px-4 py-4">
                    <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-2xl {{ $isViolation ? 'bg-rose-50 text-rose-600' : 'bg-emerald-50 text-emerald-600' }}">
                        <span class="material-symbols-outlined text-xl" style="font-variation-settings:'FILL' 1;">{{ $isViolation ? 'gavel' : 'workspace_premium' }}</span>
                    </div>
                    <div class="min-w-0 flex-1">
                        <div class="flex items-start justify-between gap-2">
                            <p class="min-w-0 text-sm font-bold text-slate-800">{{ $p->reason }}</p>
                            <span class="shrink-0 text-sm font-black tabular-nums {{ $isViolation ? 'text-rose-600' : 'text-emerald-600' }}">
                                {{ $isViolation ? '-' : '+' }}{{ $p->score }}
                            </span>
                        </div>
                        <div class="mt-1.5 flex flex-wrap items-center gap-2">
                            <span class="rounded-full px-2.5 py-0.5 text-[10px] font-bold {{ $isViolation ? 'bg-rose-100 text-rose-700' : 'bg-emerald-100 text-emerald-700' }}">
                                {{ $isViolation ? 'Pelanggaran' : 'Penghargaan' }}
                            </span>
                            <span class="inline-flex items-center gap-1 text-xs text-slate-500">
                                <span class="material-symbols-outlined text-[15px]">calendar_today</span>
                                {{ \Carbon\Carbon::parse($p->date)->translatedFormat('d M Y') }}
                            </span>
                        </div>
                    </div>
                </li>
            @empty
                <li class="px-6 py-12 text-center">
                    <span class="mx-auto mb-3 flex h-12 w-12 items-center justify-center rounded-full bg-slate-50 text-slate-300">
                        <span class="material-symbols-outlined text-3xl">stars</span>
                    </span>
                    <p class="text-sm font-semibold text-slate-600">Tidak ada data catatan poin</p>
                    <p class="mt-1 text-xs text-slate-400">Belum ada pelanggaran maupun penghargaan tercatat.</p>
                </li>
            @endforelse
        </ul>

        <div class="border-t border-slate-100 p-4">
            {{ $points->links() }}
        </div>
    </div>
</div>
@endsection
