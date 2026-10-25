@extends('layouts.parent')

@section('title', 'Halaman Form Izin Siswa')
@section('page_title', 'Halaman Form Izin Siswa')

@php
    $items = $permits instanceof \Illuminate\Pagination\AbstractPaginator ? $permits->getCollection() : collect($permits);

    $typeMap = [
        'sakit' => ['label' => 'Sakit',       'icon' => 'medical_services'],
        'izin'  => ['label' => 'Izin',        'icon' => 'family_restroom'],
        'dispen'=> ['label' => 'Dispensasi',  'icon' => 'event_available'],
    ];
    $typeLabel = fn ($t) => $typeMap[$t]['label'] ?? \Illuminate\Support\Str::headline($t);
    $typeIcon  = fn ($t) => $typeMap[$t]['icon'] ?? 'assignment';

    $statusMap = [
        'approved' => ['label' => 'Disetujui', 'icon' => 'check_circle', 'chip' => 'bg-emerald-100 text-emerald-700'],
        'rejected' => ['label' => 'Ditolak',   'icon' => 'cancel',       'chip' => 'bg-rose-100 text-rose-700'],
        'pending'  => ['label' => 'Menunggu',  'icon' => 'schedule',     'chip' => 'bg-amber-100 text-amber-700'],
    ];
    $statusFor = fn ($s) => $statusMap[in_array($s, ['approved', 'rejected']) ? $s : 'pending'];

    $countPending  = $items->filter(fn ($p) => !in_array($p->status, ['approved', 'rejected']))->count();
    $countApproved = $items->where('status', 'approved')->count();
    $countRejected = $items->where('status', 'rejected')->count();

    $fieldBase = 'w-full rounded-xl border bg-white px-4 text-base text-slate-800 shadow-sm transition placeholder:text-slate-400 focus:outline-none focus:ring-4 sm:text-sm';
    $fieldOk   = 'border-slate-200 hover:border-slate-300 focus:border-emerald-500 focus:ring-emerald-500/15';
    $fieldErr  = 'border-rose-400 bg-rose-50/40 focus:border-rose-500 focus:ring-rose-500/15';
    $labelCls  = 'mb-1.5 block text-sm font-semibold text-slate-700';

    $oldType = old('type', 'sakit');

    // Pesan WhatsApp untuk mengabari admin saat pengajuan masih menunggu.
    $waTextFor = function ($permit) use ($parent, $student, $typeLabel) {
        $sd = \Carbon\Carbon::parse($permit->start_date);
        $ed = !empty($permit->end_date) ? \Carbon\Carbon::parse($permit->end_date) : null;
        $multiDay = $ed && !$ed->isSameDay($sd);

        $dateText = $sd->translatedFormat('j F Y') . ($multiDay ? ' s/d ' . $ed->translatedFormat('j F Y') : '');

        return "Assalamualaikum Wr. Wb.\n\n"
            . "Saya " . $parent->name . " (wali dari " . $student->nama_lengkap . ") ingin memberitahu bahwa kami telah mengajukan izin "
            . strtolower($typeLabel($permit->type)) . ".\n\n"
            . "Detail:\n- Tanggal: " . $dateText . "\n- Alasan: " . $permit->reason
            . "\n\nMohon untuk dicek dan disetujui ya. Terima kasih.";
    };

    $waHref = function ($contact, $text) {
        $wa = preg_replace('/[^0-9]/', '', $contact->no_wa);
        if (\Illuminate\Support\Str::startsWith($wa, '0')) {
            $wa = '62' . substr($wa, 1);
        }
        return 'https://wa.me/' . $wa . '?text=' . urlencode($text);
    };
@endphp

@section('content')
<style>
    /* ===== Modal: geser dari bawah ke atas ===== */
    #permit-modal { visibility: hidden; transition: visibility 0s linear .38s; }
    #permit-modal[data-open="true"] { visibility: visible; transition-delay: 0s; }

    #permit-modal .pm-backdrop { opacity: 0; transition: opacity .3s ease; }
    #permit-modal[data-open="true"] .pm-backdrop { opacity: 1; }

    #permit-modal .pm-sheet {
        transform: translateY(100%);
        transition: transform .38s cubic-bezier(.32, .72, 0, 1);
    }
    #permit-modal[data-open="true"] .pm-sheet { transform: translateY(0); }
    #permit-modal .pm-sheet.is-dragging { transition: none; }

    @media (min-width: 640px) {
        #permit-modal .pm-sheet { transform: translateY(110vh); }
        #permit-modal[data-open="true"] .pm-sheet { transform: translateY(0); }
    }
    @media (prefers-reduced-motion: reduce) {
        #permit-modal, #permit-modal .pm-backdrop, #permit-modal .pm-sheet { transition: none; }
    }
</style>

<div class="mx-auto max-w-6xl space-y-6">

    {{-- ================= Notifikasi sukses ================= --}}
    @if(session('success'))
        <div role="status" class="flex items-start gap-3 rounded-2xl border border-emerald-200 bg-emerald-50 p-4 text-emerald-900">
            <span class="material-symbols-outlined mt-0.5 text-2xl text-emerald-600" style="font-variation-settings:'FILL' 1;">check_circle</span>
            <p class="text-sm font-semibold leading-relaxed">{{ session('success') }}</p>
        </div>
    @endif

    @if(session('error'))
        <div role="alert" class="flex items-start gap-3 rounded-2xl border border-rose-200 bg-rose-50 p-4 text-rose-900">
            <span class="material-symbols-outlined mt-0.5 text-2xl text-rose-600" style="font-variation-settings:'FILL' 1;">error</span>
            <p class="text-sm font-semibold leading-relaxed">{{ session('error') }}</p>
        </div>
    @endif

    {{-- ================= Ringkasan + tombol ajukan ================= --}}
    <section class="relative overflow-hidden rounded-3xl border border-emerald-100 bg-white p-5 shadow-sm sm:p-6">
        <div class="flex flex-col gap-5 sm:flex-row sm:items-center sm:justify-between">
            <div class="min-w-0">
                <h2 class="text-lg font-extrabold tracking-tight text-slate-900 sm:text-xl">Pengajuan izin siswa</h2>
                <p class="mt-1 text-sm text-slate-500">
                    Ajukan izin sakit atau keperluan untuk <span class="font-semibold text-slate-700">{{ $student->nama_lengkap }}</span>.
                </p>
            </div>
            <button type="button" data-open-permit
                    class="hidden items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-emerald-600 to-emerald-500 px-6 py-3 text-sm font-semibold text-white shadow-lg shadow-emerald-600/25 transition hover:-translate-y-0.5 hover:shadow-xl focus:outline-none focus-visible:ring-4 focus-visible:ring-emerald-500/30 active:translate-y-0 active:scale-[0.98] sm:inline-flex">
                <span class="material-symbols-outlined text-[20px]">add</span>
                Ajukan izin
            </button>
        </div>

        <div class="mt-5 grid grid-cols-3 gap-2 sm:gap-3">
            @foreach([
                ['Menunggu',  $countPending,  'bg-amber-50 text-amber-700 border-amber-100',       'schedule'],
                ['Disetujui', $countApproved, 'bg-emerald-50 text-emerald-700 border-emerald-100', 'check_circle'],
                ['Ditolak',   $countRejected, 'bg-rose-50 text-rose-700 border-rose-100',          'cancel'],
            ] as [$lbl, $n, $cls, $ic])
                <div class="flex items-center gap-2.5 rounded-2xl border {{ $cls }} p-3 sm:p-4">
                    <span class="hidden h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-white/80 sm:flex">
                        <span class="material-symbols-outlined text-[22px]" style="font-variation-settings:'FILL' 1;">{{ $ic }}</span>
                    </span>
                    <div class="min-w-0">
                        <p class="text-xl font-extrabold leading-none tabular-nums sm:text-2xl">{{ $n }}</p>
                        <p class="mt-1 truncate text-xs font-medium">{{ $lbl }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    </section>

    {{-- ================= Riwayat ================= --}}
    <section aria-labelledby="h-riwayat" class="pb-20 sm:pb-0">
        <h3 id="h-riwayat" class="mb-4 px-1 text-base font-bold text-slate-900">Riwayat pengajuan izin</h3>

        <div class="overflow-hidden rounded-3xl border border-emerald-100 bg-white shadow-sm">

            {{-- Tabel (tablet ke atas) --}}
            <div class="hidden overflow-x-auto md:block">
                <table class="w-full border-collapse text-left text-sm">
                    <thead>
                        <tr class="border-b border-emerald-100 bg-emerald-50/60 text-slate-600">
                            <th scope="col" class="px-6 py-3.5 font-semibold">Tanggal</th>
                            <th scope="col" class="px-6 py-3.5 font-semibold">Jenis</th>
                            <th scope="col" class="px-6 py-3.5 font-semibold">Keterangan</th>
                            <th scope="col" class="px-6 py-3.5 font-semibold">Status</th>
                            <th scope="col" class="px-6 py-3.5 text-right font-semibold">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($permits as $permit)
                            @php
                                $m  = $statusFor($permit->status);
                                $sd = \Carbon\Carbon::parse($permit->start_date);
                                $ed = !empty($permit->end_date) ? \Carbon\Carbon::parse($permit->end_date) : null;
                            @endphp
                            <tr class="transition-colors hover:bg-emerald-50/40">
                                <td class="whitespace-nowrap px-6 py-4 tabular-nums text-slate-700">
                                    {{ $sd->translatedFormat('d M Y') }}
                                    @if($ed && !$ed->isSameDay($sd))
                                        <span class="text-slate-400">–</span> {{ $ed->translatedFormat('d M Y') }}
                                    @endif
                                </td>
                                <td class="whitespace-nowrap px-6 py-4">
                                    <span class="inline-flex items-center gap-1.5 text-slate-700">
                                        <span class="material-symbols-outlined text-[18px] text-emerald-600">{{ $typeIcon($permit->type) }}</span>
                                        <span class="font-semibold">{{ $typeLabel($permit->type) }}</span>
                                    </span>
                                </td>
                                <td class="max-w-xs px-6 py-4 text-slate-600">
                                    <span class="line-clamp-2">{{ $permit->reason }}</span>
                                </td>
                                <td class="whitespace-nowrap px-6 py-4">
                                    <span class="inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-xs font-semibold {{ $m['chip'] }}">
                                        <span class="material-symbols-outlined text-[15px]" style="font-variation-settings:'FILL' 1;">{{ $m['icon'] }}</span>
                                        {{ $m['label'] }}
                                    </span>
                                </td>
                                <td class="whitespace-nowrap px-6 py-4 text-right">
                                    @if(!in_array($permit->status, ['approved', 'rejected'], true))
                                        @php $waText = $waTextFor($permit); @endphp
                                        <div class="flex items-center justify-end gap-2">
                                            @foreach($contacts as $contact)
                                                <a href="{{ $waHref($contact, $waText) }}" target="_blank" rel="noopener"
                                                   title="Chat WhatsApp {{ $contact->name }}"
                                                   class="inline-flex items-center gap-1.5 rounded-lg border border-emerald-200 bg-emerald-50 px-2.5 py-2 text-xs font-semibold text-emerald-800 transition hover:bg-emerald-100">
                                                    <span class="material-symbols-outlined text-[16px]">chat</span>
                                                    Chat WA
                                                </a>
                                            @endforeach
                                            <form action="{{ route('parent.permit.destroy', $permit) }}" method="POST" class="inline"
                                                  onsubmit="return confirm('Hapus pengajuan izin ini? Tindakan tidak dapat dibatalkan.')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" title="Hapus pengajuan"
                                                        class="inline-flex items-center gap-1.5 rounded-lg border border-rose-200 bg-rose-50 px-2.5 py-2 text-xs font-semibold text-rose-700 transition hover:bg-rose-100">
                                                    <span class="material-symbols-outlined text-[16px]">delete</span>
                                                    Hapus
                                                </button>
                                            </form>
                                        </div>
                                    @else
                                        <span class="text-slate-300">—</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-14 text-center">
                                    <span class="mx-auto mb-3 flex h-12 w-12 items-center justify-center rounded-full bg-emerald-50 text-emerald-400">
                                        <span class="material-symbols-outlined text-3xl">assignment</span>
                                    </span>
                                    <p class="text-sm font-semibold text-slate-600">Belum ada pengajuan izin</p>
                                    <button type="button" data-open-permit class="mt-3 inline-flex items-center gap-1 rounded-lg px-3 py-1.5 text-sm font-semibold text-emerald-700 hover:bg-emerald-50">
                                        <span class="material-symbols-outlined text-[18px]">add</span> Ajukan izin pertama
                                    </button>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Daftar (ponsel) --}}
            <ul class="divide-y divide-slate-100 md:hidden">
                @forelse($permits as $permit)
                    @php
                        $m  = $statusFor($permit->status);
                        $sd = \Carbon\Carbon::parse($permit->start_date);
                        $ed = !empty($permit->end_date) ? \Carbon\Carbon::parse($permit->end_date) : null;
                    @endphp
                    <li class="flex items-start gap-3 px-4 py-4">
                        <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-2xl bg-emerald-50 text-emerald-600">
                            <span class="material-symbols-outlined text-[22px]">{{ $typeIcon($permit->type) }}</span>
                        </span>
                        <div class="min-w-0 flex-1">
                            <div class="flex items-start justify-between gap-2">
                                <p class="text-sm font-semibold text-slate-800">Izin {{ strtolower($typeLabel($permit->type)) }}</p>
                                <span class="inline-flex shrink-0 items-center gap-1 rounded-full px-2.5 py-0.5 text-xs font-semibold {{ $m['chip'] }}">
                                    <span class="material-symbols-outlined text-[14px]" style="font-variation-settings:'FILL' 1;">{{ $m['icon'] }}</span>
                                    {{ $m['label'] }}
                                </span>
                            </div>
                            <p class="mt-0.5 text-xs tabular-nums text-slate-500">
                                {{ $sd->translatedFormat('d M Y') }}@if($ed && !$ed->isSameDay($sd)) – {{ $ed->translatedFormat('d M Y') }}@endif
                            </p>
                            <p class="mt-1.5 text-sm leading-relaxed text-slate-600">{{ $permit->reason }}</p>

                            @if(!in_array($permit->status, ['approved', 'rejected'], true))
                                @php $waText = $waTextFor($permit); @endphp
                                <div class="mt-3 flex flex-wrap items-center gap-2">
                                    @foreach($contacts as $contact)
                                        <a href="{{ $waHref($contact, $waText) }}" target="_blank" rel="noopener"
                                           class="inline-flex items-center gap-1.5 rounded-full border border-emerald-200 bg-emerald-50 px-3 py-1.5 text-xs font-semibold text-emerald-800 transition active:scale-95">
                                            <span class="material-symbols-outlined text-[16px]">chat</span>
                                            Chat WA
                                        </a>
                                    @endforeach
                                    <form action="{{ route('parent.permit.destroy', $permit) }}" method="POST"
                                          onsubmit="return confirm('Hapus pengajuan izin ini? Tindakan tidak dapat dibatalkan.')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="inline-flex items-center gap-1.5 rounded-full border border-rose-200 bg-rose-50 px-3 py-1.5 text-xs font-semibold text-rose-700 transition active:scale-95">
                                            <span class="material-symbols-outlined text-[16px]">delete</span>
                                            Hapus
                                        </button>
                                    </form>
                                </div>
                            @endif
                        </div>
                    </li>
                @empty
                    <li class="px-6 py-12 text-center">
                        <span class="mx-auto mb-3 flex h-12 w-12 items-center justify-center rounded-full bg-emerald-50 text-emerald-400">
                            <span class="material-symbols-outlined text-3xl">assignment</span>
                        </span>
                        <p class="text-sm font-semibold text-slate-600">Belum ada pengajuan izin</p>
                        <p class="mt-1 text-xs text-slate-400">Ketuk tombol + untuk mengajukan izin.</p>
                    </li>
                @endforelse
            </ul>
        </div>
    </section>
</div>

{{-- ================= Tombol mengambang (ponsel) ================= --}}
<button type="button" data-open-permit aria-label="Ajukan izin"
        class="fixed right-4 z-40 flex h-14 items-center gap-2 rounded-full bg-gradient-to-r from-emerald-600 to-emerald-500 px-5 text-sm font-semibold text-white shadow-xl shadow-emerald-700/30 transition active:scale-95 focus:outline-none focus-visible:ring-4 focus-visible:ring-emerald-500/40 sm:hidden"
        style="bottom: calc(5.5rem + env(safe-area-inset-bottom));">
    <span class="material-symbols-outlined text-[22px]">add</span>
    Ajukan izin
</button>

{{-- ================= Modal form (bottom sheet) ================= --}}
<div id="permit-modal" data-open="{{ $errors->any() ? 'true' : 'false' }}" class="fixed inset-0 z-[60]"
     role="dialog" aria-modal="true" aria-labelledby="permit-title">

    <div class="pm-backdrop absolute inset-0 bg-emerald-950/55 backdrop-blur-sm" data-close-permit></div>

    <div class="absolute inset-0 flex items-end justify-center sm:items-center sm:p-6" data-close-permit>
        <div class="pm-sheet flex max-h-[92dvh] w-full flex-col overflow-hidden rounded-t-[2rem] bg-white shadow-2xl sm:max-w-xl sm:rounded-[2rem]" id="permit-sheet">

            {{-- Pegangan geser + judul --}}
            <div class="shrink-0 border-b border-slate-100 px-5 pb-4 pt-3 sm:px-7 sm:pt-5" id="permit-handle">
                <div class="mx-auto mb-3 h-1.5 w-11 rounded-full bg-slate-200 sm:hidden"></div>
                <div class="flex items-start justify-between gap-3">
                    <div class="min-w-0">
                        <h2 id="permit-title" class="text-lg font-extrabold tracking-tight text-slate-900">Ajukan izin siswa</h2>
                        <p class="mt-0.5 truncate text-sm text-slate-500">Untuk {{ $student->nama_lengkap }}</p>
                    </div>
                    <button type="button" data-close-permit aria-label="Tutup"
                            class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-slate-100 text-slate-500 transition hover:bg-slate-200 hover:text-slate-800 focus:outline-none focus-visible:ring-4 focus-visible:ring-slate-300/60">
                        <span class="material-symbols-outlined text-[22px]">close</span>
                    </button>
                </div>
            </div>

            <form id="permit-form" action="{{ route('parent.permit.store') }}" method="POST" enctype="multipart/form-data" class="flex min-h-0 flex-1 flex-col">
                @csrf

                <div class="min-h-0 flex-1 space-y-5 overflow-y-auto overscroll-contain px-5 py-5 sm:px-7">

                    {{-- Jenis izin --}}
                    <fieldset>
                        <legend class="{{ $labelCls }}">Jenis izin <span class="text-rose-500" aria-hidden="true">*</span></legend>
                        <div class="grid grid-cols-3 gap-2.5">
                            @foreach([
                                ['sakit',  'Sakit',        'medical_services'],
                                ['izin',   'Keperluan',    'family_restroom'],
                                ['dispen', 'Dispensasi',   'event_available'],
                            ] as [$val, $lbl, $ic])
                                <label class="relative block cursor-pointer">
                                    <input type="radio" name="type" value="{{ $val }}" class="peer sr-only" required {{ $oldType === $val ? 'checked' : '' }}>
                                    <div class="flex flex-col items-center gap-1.5 rounded-xl border border-slate-200 bg-white px-2 py-3 text-center shadow-sm transition hover:border-slate-300
                                                peer-checked:border-emerald-500 peer-checked:bg-emerald-50 peer-checked:text-emerald-800 peer-checked:ring-4 peer-checked:ring-emerald-500/10
                                                peer-focus-visible:ring-4 peer-focus-visible:ring-emerald-500/30 text-slate-600">
                                        <span class="material-symbols-outlined text-[24px]">{{ $ic }}</span>
                                        <span class="text-xs font-semibold leading-tight">{{ $lbl }}</span>
                                    </div>
                                </label>
                            @endforeach
                        </div>
                        <p class="mt-1.5 text-xs text-slate-500" id="type-hint">Sakit (medis)</p>
                        @error('type') <p class="mt-1.5 text-sm font-medium text-rose-600">{{ $message }}</p> @enderror
                    </fieldset>

                    {{-- Tanggal --}}
                    <div class="grid grid-cols-1 gap-4 min-[420px]:grid-cols-2">
                        <div>
                            <label for="start_date" class="{{ $labelCls }}">Mulai tanggal <span class="text-rose-500" aria-hidden="true">*</span></label>
                            <input type="date" id="start_date" name="start_date" required
                                   value="{{ old('start_date', date('Y-m-d')) }}"
                                   class="{{ $fieldBase }} {{ $errors->has('start_date') ? $fieldErr : $fieldOk }} h-12">
                            @error('start_date') <p class="mt-1.5 text-sm font-medium text-rose-600">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label for="end_date" class="{{ $labelCls }}">Sampai tanggal</label>
                            <input type="date" id="end_date" name="end_date"
                                   value="{{ old('end_date', date('Y-m-d')) }}"
                                   class="{{ $fieldBase }} {{ $errors->has('end_date') ? $fieldErr : $fieldOk }} h-12">
                            @error('end_date') <p class="mt-1.5 text-sm font-medium text-rose-600">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    {{-- Alasan --}}
                    <div>
                        <label for="reason" class="{{ $labelCls }}">Alasan / keterangan <span class="text-rose-500" aria-hidden="true">*</span></label>
                        <textarea id="reason" name="reason" rows="3" required placeholder="Jelaskan alasan izin secara singkat…"
                                  class="{{ $fieldBase }} {{ $errors->has('reason') ? $fieldErr : $fieldOk }} resize-y py-3">{{ old('reason') }}</textarea>
                        @error('reason') <p class="mt-1.5 text-sm font-medium text-rose-600">{{ $message }}</p> @enderror
                    </div>

                    {{-- Bukti --}}
                    <div>
                        <span class="{{ $labelCls }}">Foto surat dokter / bukti pendukung <span class="font-normal text-slate-400">(opsional)</span></span>
                        <label for="proof_photo" class="group flex cursor-pointer items-center gap-4 rounded-2xl border-2 border-dashed border-slate-200 bg-slate-50/60 p-4 transition hover:border-emerald-400 hover:bg-emerald-50/50 focus-within:border-emerald-500 focus-within:ring-4 focus-within:ring-emerald-500/15">
                            <span id="proof-thumb-wrap" class="flex h-14 w-14 shrink-0 items-center justify-center overflow-hidden rounded-xl bg-white text-emerald-600 ring-1 ring-slate-200">
                                <span id="proof-icon" class="material-symbols-outlined text-[28px]">add_photo_alternate</span>
                                <img id="proof-thumb" alt="Pratinjau bukti" class="hidden h-full w-full object-cover">
                            </span>
                            <span class="min-w-0">
                                <span id="proof-name" class="block truncate text-sm font-semibold text-slate-700">Pilih atau ambil foto</span>
                                <span class="block text-xs text-slate-500">Format gambar (JPG, PNG, atau WebP)</span>
                            </span>
                            <input type="file" id="proof_photo" name="proof_photo" accept="image/*" class="sr-only">
                        </label>
                        @error('proof_photo') <p class="mt-1.5 text-sm font-medium text-rose-600">{{ $message }}</p> @enderror
                    </div>
                </div>

                {{-- Tombol aksi (menempel di bawah modal) --}}
                <div class="shrink-0 border-t border-slate-100 bg-white px-5 pt-4 sm:px-7"
                     style="padding-bottom: max(1rem, env(safe-area-inset-bottom));">
                    <div class="flex gap-3">
                        <button type="button" data-close-permit
                                class="inline-flex h-12 flex-1 items-center justify-center rounded-xl bg-white px-5 text-sm font-semibold text-slate-600 ring-1 ring-inset ring-slate-200 transition hover:bg-slate-50 focus:outline-none focus-visible:ring-4 focus-visible:ring-slate-300/50 active:scale-[0.98] sm:flex-none">
                            Batal
                        </button>
                        <button type="submit" id="permit-submit"
                                class="inline-flex h-12 flex-[2] items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-emerald-600 to-emerald-500 px-6 text-sm font-semibold text-white shadow-lg shadow-emerald-600/25 transition hover:shadow-xl focus:outline-none focus-visible:ring-4 focus-visible:ring-emerald-500/30 active:scale-[0.98] disabled:cursor-not-allowed disabled:opacity-70 sm:flex-1">
                            <span class="material-symbols-outlined text-[20px]">send</span>
                            <span>Kirim pengajuan</span>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
(function () {
    var modal  = document.getElementById('permit-modal');
    var sheet  = document.getElementById('permit-sheet');
    var form   = document.getElementById('permit-form');
    var submit = document.getElementById('permit-submit');
    var submitHtml = submit.innerHTML;
    var lastFocus = null;

    function isOpen() { return modal.dataset.open === 'true'; }

    function open() {
        lastFocus = document.activeElement;
        modal.dataset.open = 'true';
        document.body.style.overflow = 'hidden';
        setTimeout(function () {
            var first = form.querySelector('input[type="radio"]:checked') || form.querySelector('input, textarea');
            if (first) first.focus({ preventScroll: true });
        }, 380);
    }
    function close() {
        modal.dataset.open = 'false';
        document.body.style.overflow = '';
        sheet.style.transform = '';
        if (lastFocus && lastFocus.focus) lastFocus.focus({ preventScroll: true });
    }

    document.querySelectorAll('[data-open-permit]').forEach(function (b) { b.addEventListener('click', open); });

    // Tutup: tombol, atau klik di luar sheet (bukan di dalamnya)
    modal.addEventListener('click', function (e) {
        var closer = e.target.closest('[data-close-permit]');
        if (!closer) return;
        if (closer.contains(sheet) && e.target.closest('#permit-sheet')) return; // klik di dalam sheet
        close();
    });
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && isOpen()) close();
    });

    // Fokus tetap di dalam modal
    modal.addEventListener('keydown', function (e) {
        if (e.key !== 'Tab' || !isOpen()) return;
        var f = sheet.querySelectorAll('button, [href], input:not([type="hidden"]), textarea, select');
        f = Array.prototype.filter.call(f, function (el) { return !el.disabled && el.offsetParent !== null; });
        if (!f.length) return;
        var first = f[0], last = f[f.length - 1];
        if (e.shiftKey && document.activeElement === first) { e.preventDefault(); last.focus(); }
        else if (!e.shiftKey && document.activeElement === last) { e.preventDefault(); first.focus(); }
    });

    // Geser ke bawah untuk menutup (ponsel)
    (function () {
        var handle = document.getElementById('permit-handle');
        var startY = null, dy = 0;
        handle.addEventListener('pointerdown', function (e) {
            if (window.innerWidth >= 640 || e.target.closest('button')) return;
            startY = e.clientY; dy = 0;
            sheet.classList.add('is-dragging');
            handle.setPointerCapture(e.pointerId);
        });
        handle.addEventListener('pointermove', function (e) {
            if (startY === null) return;
            dy = Math.max(0, e.clientY - startY);
            sheet.style.transform = 'translateY(' + dy + 'px)';
        });
        function end() {
            if (startY === null) return;
            sheet.classList.remove('is-dragging');
            var shouldClose = dy > 110;
            startY = null;
            if (shouldClose) { close(); } else { sheet.style.transform = ''; }
        }
        handle.addEventListener('pointerup', end);
        handle.addEventListener('pointercancel', end);
    })();

    // Petunjuk jenis izin
    var hints = { sakit: 'Sakit (medis)', izin: 'Izin keperluan keluarga atau hal penting', dispen: 'Dispensasi untuk kegiatan' };
    var hintEl = document.getElementById('type-hint');
    function updateHint() {
        var c = form.querySelector('input[name="type"]:checked');
        hintEl.textContent = c ? (hints[c.value] || '') : '';
    }
    form.querySelectorAll('input[name="type"]').forEach(function (r) { r.addEventListener('change', updateHint); });
    updateHint();

    // Tanggal selesai tidak boleh sebelum tanggal mulai
    var sd = document.getElementById('start_date'), ed = document.getElementById('end_date');
    function syncDates() {
        ed.min = sd.value;
        if (ed.value && ed.value < sd.value) ed.value = sd.value;
    }
    sd.addEventListener('change', syncDates);
    syncDates();

    // Pratinjau foto bukti
    var file = document.getElementById('proof_photo');
    var thumb = document.getElementById('proof-thumb');
    var icon = document.getElementById('proof-icon');
    var nameEl = document.getElementById('proof-name');
    file.addEventListener('change', function () {
        var f = this.files && this.files[0];
        if (!f) {
            thumb.classList.add('hidden'); icon.classList.remove('hidden');
            nameEl.textContent = 'Pilih atau ambil foto';
            return;
        }
        nameEl.textContent = f.name;
        var r = new FileReader();
        r.onload = function (e) { thumb.src = e.target.result; thumb.classList.remove('hidden'); icon.classList.add('hidden'); };
        r.readAsDataURL(f);
    });

    // Cegah kirim ganda
    form.addEventListener('submit', function () {
        setTimeout(function () {
            submit.disabled = true;
            submit.innerHTML = '<svg class="h-5 w-5 animate-spin" viewBox="0 0 24 24" fill="none" aria-hidden="true">' +
                '<circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" class="opacity-25"></circle>' +
                '<path d="M4 12a8 8 0 018-8" stroke="currentColor" stroke-width="4" stroke-linecap="round"></path></svg><span>Mengirim…</span>';
        }, 0);
    });
    window.addEventListener('pageshow', function (e) {
        if (e.persisted) { submit.disabled = false; submit.innerHTML = submitHtml; }
    });

    // Jika ada error validasi dari server, modal sudah terbuka: kunci scroll halaman
    if (isOpen()) document.body.style.overflow = 'hidden';
})();
</script>
@endsection