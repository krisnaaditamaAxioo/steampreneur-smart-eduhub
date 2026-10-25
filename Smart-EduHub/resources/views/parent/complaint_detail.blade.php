@extends('layouts.parent')

@section('title', 'Detail Pengaduan')
@section('page_title', 'Detail Pengaduan')

@section('content')
@php
    $statusMeta = [
        'pending'     => ['label' => 'Menunggu', 'icon' => 'schedule',     'pill' => 'bg-amber-50 text-amber-800 border-amber-200',       'reply' => 'border-sky-200 bg-sky-50 text-sky-900'],
        'in_progress' => ['label' => 'Diproses', 'icon' => 'autorenew',    'pill' => 'bg-sky-50 text-sky-800 border-sky-200',             'reply' => 'border-sky-200 bg-sky-50 text-sky-900'],
        'resolved'    => ['label' => 'Selesai',  'icon' => 'check_circle', 'pill' => 'bg-emerald-50 text-emerald-800 border-emerald-200', 'reply' => 'border-emerald-200 bg-emerald-50 text-emerald-900'],
        'rejected'    => ['label' => 'Ditolak',  'icon' => 'cancel',       'pill' => 'bg-rose-50 text-rose-800 border-rose-200',          'reply' => 'border-rose-200 bg-rose-50 text-rose-900'],
    ];

    $key = in_array($complaint->status, ['pending', 'in_progress', 'resolved']) ? $complaint->status : 'rejected';
    $st  = $statusMeta[$key];
    $fmt = fn ($date) => $date->translatedFormat('j M Y, H:i');

    // Tahapan aduan diturunkan dari status
    $steps = [];
    $steps[] = [
        'label' => 'Aduan terkirim',
        'note'  => $fmt($complaint->created_at),
        'state' => $key === 'pending' ? 'current' : 'done',
    ];
    if ($key === 'rejected') {
        $steps[] = [
            'label' => 'Ditolak',
            'note'  => $complaint->handled_at ? $fmt($complaint->handled_at) : ($complaint->response ? 'Lihat tanggapan untuk alasannya' : ''),
            'state' => 'rejected',
        ];
    } else {
        $steps[] = [
            'label' => 'Sedang diproses',
            'note'  => $key === 'pending' ? 'Belum dimulai' : ($key === 'in_progress' ? 'Aduan sedang ditangani' : 'Sudah ditinjau'),
            'state' => $key === 'pending' ? 'upcoming' : ($key === 'in_progress' ? 'current' : 'done'),
        ];
        $steps[] = [
            'label' => 'Selesai',
            'note'  => $key === 'resolved' ? ($complaint->handled_at ? $fmt($complaint->handled_at) : 'Aduan telah selesai') : 'Belum selesai',
            'state' => $key === 'resolved' ? 'done' : 'upcoming',
        ];
    }

    $circle = [
        'done'     => 'bg-emerald-500 text-white',
        'current'  => 'bg-amber-400 text-white',
        'upcoming' => 'border-2 border-slate-200 bg-white text-slate-300',
        'rejected' => 'bg-rose-500 text-white',
    ];
    $circleIcon = ['done' => 'check', 'current' => 'schedule', 'upcoming' => '', 'rejected' => 'close'];
@endphp

<div class="space-y-5 pb-28 lg:pb-6">

    {{-- Notifikasi --}}
    @if(session('success'))
        <div role="status" class="flex items-start gap-3 rounded-2xl border border-emerald-200 bg-emerald-50 p-3.5 text-emerald-900">
            <span class="material-symbols-outlined mt-0.5 text-emerald-600" style="font-variation-settings:'FILL' 1;">check_circle</span>
            <p class="flex-1 text-sm font-medium leading-relaxed">{{ session('success') }}</p>
        </div>
    @endif

    {{-- Aksi --}}
    <div class="flex items-center justify-between gap-3">
        <a href="{{ route('parent.complaints') }}"
           class="inline-flex min-h-[44px] items-center gap-2 rounded-2xl border border-slate-200 bg-white px-4 text-sm font-bold text-slate-700 transition hover:bg-slate-50 active:scale-[.98]">
            <span class="material-symbols-outlined text-xl" aria-hidden="true">arrow_back</span>
            Kembali
        </a>

        <form action="{{ route('parent.complaints.destroy', $complaint) }}" method="POST"
              onsubmit="return confirm('Hapus pengaduan ini? Tindakan tidak dapat dibatalkan.')">
            @csrf
            @method('DELETE')
            <button type="submit"
                    class="inline-flex min-h-[44px] items-center gap-2 rounded-2xl border border-rose-200 bg-rose-50 px-4 text-sm font-bold text-rose-700 transition hover:bg-rose-100 active:scale-[.98]">
                <span class="material-symbols-outlined text-xl" aria-hidden="true">delete</span>
                Hapus
            </button>
        </form>
    </div>

    <div class="grid grid-cols-1 items-start gap-5 lg:grid-cols-5 lg:gap-6">

        {{-- Kolom utama: isi aduan + tanggapan --}}
        <div class="space-y-5 lg:col-span-3">

            <article class="rounded-3xl border border-slate-100 bg-white p-5 shadow-sm sm:p-6">
                <div class="flex flex-wrap items-center justify-between gap-x-3 gap-y-2">
                    <span class="inline-flex items-center gap-1 rounded-full border px-3 py-1.5 text-xs font-semibold {{ $st['pill'] }}">
                        <span class="material-symbols-outlined text-sm" style="font-variation-settings:'FILL' 1;" aria-hidden="true">{{ $st['icon'] }}</span>
                        {{ $complaint->status_text ?: $st['label'] }}
                    </span>
                    <time datetime="{{ $complaint->created_at->toIso8601String() }}" class="text-xs tabular-nums text-slate-500">
                        {{ $fmt($complaint->created_at) }}
                    </time>
                </div>

                <h2 class="mt-4 break-words text-xl font-extrabold leading-snug tracking-tight text-slate-900 sm:text-2xl">{{ $complaint->title }}</h2>

                <p class="mt-3 whitespace-pre-line break-words text-[15px] leading-relaxed text-slate-700">{{ $complaint->description }}</p>

                @if($complaint->proof_photo)
                    <div class="mt-6 border-t border-slate-100 pt-5">
                        <h3 class="mb-2.5 text-xs font-semibold text-slate-600">Bukti foto</h3>
                        <a href="{{ asset('storage/' . $complaint->proof_photo) }}" target="_blank" rel="noopener"
                           class="group relative block w-full overflow-hidden rounded-2xl border border-slate-200 bg-slate-50">
                            <img src="{{ asset('storage/' . $complaint->proof_photo) }}" alt="Bukti pengaduan" loading="lazy"
                                 class="max-h-72 w-full object-cover sm:max-h-96">
                            <span class="absolute bottom-2.5 right-2.5 inline-flex items-center gap-1 rounded-full bg-slate-900/70 px-3 py-1.5 text-xs font-semibold text-white backdrop-blur-sm">
                                <span class="material-symbols-outlined text-base" aria-hidden="true">zoom_in</span>
                                Perbesar
                            </span>
                        </a>
                    </div>
                @endif
            </article>

            {{-- Tanggapan admin --}}
            @if($complaint->response)
                <section class="rounded-3xl border p-5 sm:p-6 {{ $st['reply'] }}">
                    <h3 class="flex items-center gap-2 text-sm font-bold">
                        <span class="material-symbols-outlined text-xl" style="font-variation-settings:'FILL' 1;" aria-hidden="true">forum</span>
                        Tanggapan admin
                    </h3>
                    <p class="mt-3 whitespace-pre-line break-words text-sm leading-relaxed">{{ $complaint->response }}</p>
                    @if($complaint->handled_at)
                        <p class="mt-3 text-xs tabular-nums opacity-80">Ditangani pada {{ $fmt($complaint->handled_at) }}</p>
                    @endif
                </section>
            @else
                <section class="flex items-start gap-3 rounded-3xl border border-dashed border-slate-200 bg-white p-5 sm:p-6">
                    <span class="material-symbols-outlined mt-0.5 text-slate-300" aria-hidden="true">forum</span>
                    <div>
                        <h3 class="text-sm font-bold text-slate-700">Belum ada tanggapan</h3>
                        <p class="mt-0.5 text-[13px] leading-relaxed text-slate-500">Tanggapan akan muncul di sini setelah pengaduan Anda ditinjau.</p>
                    </div>
                </section>
            @endif
        </div>

        {{-- Kolom samping: perkembangan --}}
        <aside class="lg:col-span-2 lg:sticky lg:top-6 lg:self-start">
            <section class="rounded-3xl border border-slate-100 bg-white p-5 shadow-sm sm:p-6">
                <h2 class="text-base font-extrabold tracking-tight text-slate-900">Perkembangan pengaduan</h2>

                <ol class="mt-5" aria-label="Tahapan pengaduan">
                    @foreach($steps as $i => $step)
                        @php
                            $next   = $steps[$i + 1]['state'] ?? null;
                            $lineOn = $step['state'] === 'done' && $next && $next !== 'upcoming';
                        @endphp
                        <li class="relative flex gap-3 {{ $loop->last ? '' : 'pb-6' }}" @if($step['state'] === 'current') aria-current="step" @endif>
                            @unless($loop->last)
                                <span class="absolute left-4 top-8 -ml-px h-[calc(100%-2rem)] w-0.5 {{ $lineOn ? 'bg-emerald-300' : 'bg-slate-200' }}" aria-hidden="true"></span>
                            @endunless

                            <span class="relative z-10 grid h-8 w-8 shrink-0 place-items-center rounded-full {{ $circle[$step['state']] }}" aria-hidden="true">
                                @if($circleIcon[$step['state']])
                                    <span class="material-symbols-outlined text-[18px]" style="font-variation-settings:'FILL' 1,'wght' 600;">{{ $circleIcon[$step['state']] }}</span>
                                @endif
                            </span>

                            <div class="min-w-0 pt-0.5">
                                <p class="text-sm font-bold {{ $step['state'] === 'upcoming' ? 'text-slate-400' : 'text-slate-900' }}">{{ $step['label'] }}</p>
                                @if($step['note'])
                                    <p class="mt-0.5 text-xs tabular-nums {{ $step['state'] === 'upcoming' ? 'text-slate-400' : 'text-slate-500' }}">{{ $step['note'] }}</p>
                                @endif
                            </div>
                        </li>
                    @endforeach
                </ol>
            </section>
        </aside>
    </div>
</div>
@endsection
