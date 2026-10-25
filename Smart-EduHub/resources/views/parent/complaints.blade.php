@extends('layouts.parent')

@section('title', 'Layanan Pengaduan')
@section('page_title', 'Form Pengaduan & Layanan Wali Murid')

@php
    $statusMap = [
        'resolved'    => ['pill' => 'bg-emerald-50 text-emerald-700 ring-emerald-200', 'dot' => 'bg-emerald-500'],
        'in_progress' => ['pill' => 'bg-sky-50 text-sky-700 ring-sky-200',             'dot' => 'bg-sky-500'],
        'pending'     => ['pill' => 'bg-amber-50 text-amber-700 ring-amber-200',       'dot' => 'bg-amber-500'],
    ];
    $inputClass = 'w-full rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-3 text-base text-slate-800 placeholder:text-slate-400 transition focus:border-emerald-500 focus:bg-white focus:outline-none focus:ring-4 focus:ring-emerald-500/10 lg:text-sm';
@endphp

@section('content')
<div class="space-y-6 pb-28 lg:pb-6">

    {{-- Notifikasi sukses --}}
    @if(session('success'))
        <div role="status" class="flex items-center gap-3 rounded-2xl border border-emerald-200 bg-gradient-to-r from-emerald-50 to-white p-4 shadow-sm">
            <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-emerald-500 text-white shadow-md shadow-emerald-200">
                <span class="material-symbols-outlined text-xl">check_circle</span>
            </span>
            <p class="text-sm font-semibold text-emerald-900">{{ session('success') }}</p>
        </div>
    @endif

    <div class="grid grid-cols-1 items-start gap-6 lg:grid-cols-12 lg:gap-8">

        {{-- ============ Form pengaduan: bottom sheet di mobile, kartu biasa di desktop ============ --}}
        <div id="sheetBackdrop" data-close-sheet
             class="pointer-events-none fixed inset-0 z-[55] bg-slate-900/50 opacity-0 backdrop-blur-[2px] transition-opacity duration-300 lg:hidden"></div>

        <section id="complaintSheet"
                 aria-labelledby="complaintFormTitle"
                 class="fixed inset-x-0 bottom-0 z-[60] max-h-[92vh] translate-y-full overflow-y-auto overscroll-contain rounded-t-[2rem] bg-white shadow-2xl transition-transform duration-300 ease-out
                        lg:static lg:z-auto lg:col-span-5 lg:max-h-none lg:translate-y-0 lg:overflow-visible lg:rounded-3xl lg:border lg:border-slate-100 lg:shadow-sm lg:sticky lg:top-6">

            {{-- Handle (geser ke bawah untuk menutup) --}}
            <div id="sheetHandle" class="flex cursor-grab touch-none justify-center pt-3 pb-1 lg:hidden" aria-hidden="true">
                <span class="h-1.5 w-12 rounded-full bg-slate-200"></span>
            </div>

            <div class="space-y-5 px-5 pb-8 pt-3 lg:p-6">
                <div class="flex items-start justify-between gap-3">
                    <div class="flex items-start gap-3">
                        <span class="flex h-11 w-11 shrink-0 items-center justify-center rounded-2xl bg-emerald-50 text-emerald-600">
                            <span class="material-symbols-outlined text-2xl">edit_note</span>
                        </span>
                        <div>
                            <h2 id="complaintFormTitle" class="text-lg font-extrabold leading-tight text-slate-900">Kirim pengaduan</h2>
                            <p class="mt-1 text-xs leading-relaxed text-slate-500">Sampaikan keluhan, masukan, atau kendala sekolah secara resmi.</p>
                        </div>
                    </div>
                    <button type="button" data-close-sheet aria-label="Tutup form"
                            class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-slate-100 text-slate-500 transition hover:bg-slate-200 focus:outline-none focus-visible:ring-2 focus-visible:ring-emerald-500 lg:hidden">
                        <span class="material-symbols-outlined text-xl">close</span>
                    </button>
                </div>

                @if($errors->any())
                    <div role="alert" class="rounded-2xl border border-rose-200 bg-rose-50 p-3.5 text-xs text-rose-800">
                        <p class="font-bold">Pengaduan belum terkirim. Periksa kembali:</p>
                        <ul class="mt-1 list-inside list-disc space-y-0.5">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('parent.complaints.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                    @csrf

                    <div class="space-y-1.5">
                        <label for="complaintTitle" class="text-xs font-bold text-slate-700">
                            Judul pengaduan <span class="text-rose-500">*</span>
                        </label>
                        <input id="complaintTitle" type="text" name="title" value="{{ old('title') }}" required
                               placeholder="Contoh: Kendala pembayaran atau kehadiran siswa"
                               class="{{ $inputClass }}">
                    </div>

                    <div class="space-y-1.5">
                        <label for="complaintDesc" class="text-xs font-bold text-slate-700">
                            Detail pengaduan <span class="text-rose-500">*</span>
                        </label>
                        <textarea id="complaintDesc" name="description" rows="4" required
                                  placeholder="Tuliskan keluhan atau masukan Anda secara rinci..."
                                  class="{{ $inputClass }} resize-none">{{ old('description') }}</textarea>
                    </div>

                    <div class="space-y-1.5">
                        <span class="text-xs font-bold text-slate-700">Foto bukti <span class="font-medium text-slate-400">(opsional)</span></span>
                        <label for="complaintPhoto"
                               class="flex cursor-pointer items-center gap-3 rounded-xl border border-dashed border-slate-300 bg-slate-50 px-3.5 py-3 transition hover:border-emerald-400 hover:bg-emerald-50/50 focus-within:border-emerald-500 focus-within:ring-4 focus-within:ring-emerald-500/10">
                            <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-white text-emerald-600 ring-1 ring-slate-200">
                                <span class="material-symbols-outlined text-xl">add_photo_alternate</span>
                            </span>
                            <span class="min-w-0">
                                <span id="photoName" class="block truncate text-sm font-semibold text-slate-700">Pilih foto</span>
                                <span class="block text-xs text-slate-400">Format gambar (JPG, PNG)</span>
                            </span>
                            <input id="complaintPhoto" type="file" name="proof_photo" accept="image/*" class="sr-only">
                        </label>
                    </div>

                    <button type="submit"
                            class="flex w-full items-center justify-center gap-2 rounded-xl bg-emerald-600 py-3.5 text-sm font-bold text-white shadow-lg shadow-emerald-600/25 transition hover:bg-emerald-700 focus:outline-none focus-visible:ring-4 focus-visible:ring-emerald-500/30 active:scale-[0.99]">
                        <span class="material-symbols-outlined text-lg">send</span>
                        Kirim pengaduan
                    </button>
                </form>
            </div>
        </section>

        {{-- ============ Riwayat pengaduan ============ --}}
        <section class="overflow-hidden rounded-3xl border border-slate-100 bg-white shadow-sm lg:col-span-7">
            <header class="flex items-center gap-3 border-b border-slate-100 px-5 py-4">
                <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-emerald-50 text-emerald-600">
                    <span class="material-symbols-outlined text-xl">history</span>
                </span>
                <h2 class="text-base font-extrabold text-slate-900">Riwayat pengaduan Anda</h2>
            </header>

            {{-- Mobile: kartu --}}
            <ul class="divide-y divide-slate-100 lg:hidden">
                @forelse($complaints as $c)
                    @php $st = $statusMap[$c->status] ?? $statusMap['pending']; @endphp
                    <li class="space-y-3 px-5 py-4">
                        <div class="flex items-start justify-between gap-3">
                            <div class="min-w-0">
                                <p class="text-sm font-bold leading-snug text-slate-800">{{ $c->title }}</p>
                                <p class="mt-0.5 flex items-center gap-1 text-xs text-slate-400">
                                    <span class="material-symbols-outlined text-sm">calendar_today</span>
                                    {{ $c->created_at->translatedFormat('d M Y') }}
                                </p>
                            </div>
                            <span class="inline-flex shrink-0 items-center gap-1.5 rounded-full px-2.5 py-1 text-[11px] font-semibold ring-1 ring-inset {{ $st['pill'] }}">
                                <span class="h-1.5 w-1.5 rounded-full {{ $st['dot'] }}"></span>
                                {{ $c->status_text }}
                            </span>
                        </div>

                        @if($c->response)
                            <div class="rounded-2xl bg-emerald-50/70 p-3 ring-1 ring-emerald-100">
                                <p class="flex items-center gap-1 text-xs font-bold text-emerald-800">
                                    <span class="material-symbols-outlined text-sm">support_agent</span>
                                    Tanggapan admin
                                </p>
                                <p class="mt-1 text-xs leading-relaxed text-emerald-900/80">{{ $c->response }}</p>
                            </div>
                        @else
                            <p class="text-xs italic text-slate-400">Belum ada tanggapan dari admin.</p>
                        @endif

                        {{-- Aksi: detail & hapus --}}
                        <div class="flex items-center justify-between gap-2 border-t border-slate-100 pt-3">
                            <a href="{{ route('parent.complaints.show', $c) }}"
                               class="inline-flex min-h-[40px] items-center gap-0.5 text-xs font-bold text-emerald-700 transition hover:text-emerald-800">
                                Lihat detail
                                <span class="material-symbols-outlined text-lg" aria-hidden="true">chevron_right</span>
                            </a>
                            <form action="{{ route('parent.complaints.destroy', $c) }}" method="POST"
                                  onsubmit="return confirm('Hapus pengaduan ini? Tindakan tidak dapat dibatalkan.')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" aria-label="Hapus pengaduan {{ $c->title }}"
                                        class="inline-flex min-h-[40px] items-center gap-1.5 rounded-full border border-rose-200 bg-rose-50 px-3.5 text-xs font-bold text-rose-700 transition hover:bg-rose-100 active:scale-95">
                                    <span class="material-symbols-outlined text-base" aria-hidden="true">delete</span>
                                    Hapus
                                </button>
                            </form>
                        </div>
                    </li>
                @empty
                    <li class="flex flex-col items-center gap-2 px-5 py-12 text-center">
                        <span class="flex h-14 w-14 items-center justify-center rounded-2xl bg-slate-50 text-slate-300">
                            <span class="material-symbols-outlined text-3xl">inbox</span>
                        </span>
                        <p class="text-sm font-semibold text-slate-600">Belum ada pengaduan</p>
                        <p class="text-xs text-slate-400">Ketuk tombol tambah untuk mengirim pengaduan pertama Anda.</p>
                    </li>
                @endforelse
            </ul>

            {{-- Desktop: tabel --}}
            <div class="hidden overflow-x-auto lg:block">
                <table class="w-full border-collapse text-left text-sm">
                    <thead>
                        <tr class="border-b border-slate-100 bg-slate-50/60 text-xs font-semibold text-slate-500">
                            <th class="px-5 py-3">Tanggal</th>
                            <th class="px-5 py-3">Judul</th>
                            <th class="px-5 py-3">Status</th>
                            <th class="px-5 py-3">Tanggapan admin</th>
                            <th class="px-5 py-3 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($complaints as $c)
                            @php $st = $statusMap[$c->status] ?? $statusMap['pending']; @endphp
                            <tr class="align-top transition-colors hover:bg-slate-50/70">
                                <td class="whitespace-nowrap px-5 py-4 text-xs text-slate-500">
                                    {{ $c->created_at->translatedFormat('d M Y') }}
                                </td>
                                <td class="px-5 py-4 font-semibold text-slate-800">
                                    {{ $c->title }}
                                </td>
                                <td class="px-5 py-4">
                                    <span class="inline-flex items-center gap-1.5 whitespace-nowrap rounded-full px-2.5 py-1 text-[11px] font-semibold ring-1 ring-inset {{ $st['pill'] }}">
                                        <span class="h-1.5 w-1.5 rounded-full {{ $st['dot'] }}"></span>
                                        {{ $c->status_text }}
                                    </span>
                                </td>
                                <td class="px-5 py-4 text-xs leading-relaxed {{ $c->response ? 'text-slate-600' : 'italic text-slate-400' }}">
                                    {{ $c->response ?: 'Belum ada tanggapan' }}
                                </td>
                                <td class="whitespace-nowrap px-5 py-4 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <a href="{{ route('parent.complaints.show', $c) }}"
                                           class="inline-flex min-h-[40px] items-center gap-1 rounded-full border border-emerald-200 bg-emerald-50 px-3.5 text-xs font-bold text-emerald-700 transition hover:bg-emerald-100">
                                            <span class="material-symbols-outlined text-base" aria-hidden="true">visibility</span>
                                            Detail
                                        </a>
                                        <form action="{{ route('parent.complaints.destroy', $c) }}" method="POST" class="inline"
                                              onsubmit="return confirm('Hapus pengaduan ini? Tindakan tidak dapat dibatalkan.')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                    class="inline-flex min-h-[40px] items-center gap-1.5 rounded-full border border-rose-200 bg-rose-50 px-3.5 text-xs font-bold text-rose-700 transition hover:bg-rose-100 active:scale-95">
                                                <span class="material-symbols-outlined text-base" aria-hidden="true">delete</span>
                                                Hapus
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-5 py-14">
                                    <div class="flex flex-col items-center gap-2 text-center">
                                        <span class="flex h-14 w-14 items-center justify-center rounded-2xl bg-slate-50 text-slate-300">
                                            <span class="material-symbols-outlined text-3xl">inbox</span>
                                        </span>
                                        <p class="text-sm font-semibold text-slate-600">Belum ada pengaduan</p>
                                        <p class="text-xs text-slate-400">Isi formulir di samping untuk mengirim pengaduan pertama Anda.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>

    </div>
</div>

{{-- Tombol melayang (mobile saja) --}}
<button type="button" id="openComplaint" aria-controls="complaintSheet" aria-expanded="false"
        class="fixed bottom-24 right-5 z-40 flex items-center gap-2 rounded-full bg-emerald-600 py-3.5 pl-4 pr-5 text-sm font-bold text-white shadow-xl shadow-emerald-700/30 transition hover:bg-emerald-700 focus:outline-none focus-visible:ring-4 focus-visible:ring-emerald-500/40 active:scale-95 lg:hidden">
    <span class="material-symbols-outlined text-xl">add</span>
    Tambah pengaduan
</button>

<script>
    (function () {
        var sheet    = document.getElementById('complaintSheet');
        var backdrop = document.getElementById('sheetBackdrop');
        var fab      = document.getElementById('openComplaint');
        var handle   = document.getElementById('sheetHandle');
        var photo    = document.getElementById('complaintPhoto');
        var photoLbl = document.getElementById('photoName');
        var desktop  = window.matchMedia('(min-width: 1024px)');
        var isOpen   = false;

        function sync() {
            // Saat tertutup di mobile, form tidak boleh bisa difokus lewat keyboard
            if (desktop.matches || isOpen) { sheet.removeAttribute('inert'); }
            else { sheet.setAttribute('inert', ''); }
        }

        function openSheet() {
            if (desktop.matches) return;
            isOpen = true;
            sheet.classList.remove('translate-y-full');
            backdrop.classList.remove('opacity-0', 'pointer-events-none');
            document.body.classList.add('overflow-hidden');
            fab.setAttribute('aria-expanded', 'true');
            sync();
        }

        function closeSheet() {
            isOpen = false;
            sheet.classList.add('translate-y-full');
            backdrop.classList.add('opacity-0', 'pointer-events-none');
            document.body.classList.remove('overflow-hidden');
            fab.setAttribute('aria-expanded', 'false');
            sync();
        }

        fab.addEventListener('click', openSheet);
        document.querySelectorAll('[data-close-sheet]').forEach(function (el) {
            el.addEventListener('click', closeSheet);
        });
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape' && isOpen) closeSheet();
        });
        desktop.addEventListener('change', function () {
            if (desktop.matches) closeSheet();
            sync();
        });

        // Geser handle ke bawah untuk menutup
        var startY = null, dy = 0;
        handle.addEventListener('touchstart', function (e) {
            startY = e.touches[0].clientY;
            sheet.style.transition = 'none';
        }, { passive: true });
        handle.addEventListener('touchmove', function (e) {
            if (startY === null) return;
            dy = Math.max(0, e.touches[0].clientY - startY);
            sheet.style.transform = 'translateY(' + dy + 'px)';
        }, { passive: true });
        handle.addEventListener('touchend', function () {
            if (startY === null) return;
            sheet.style.transition = '';
            sheet.style.transform = '';
            if (dy > 100) closeSheet();
            startY = null; dy = 0;
        });

        // Nama file foto
        if (photo) {
            photo.addEventListener('change', function () {
                photoLbl.textContent = photo.files.length ? photo.files[0].name : 'Pilih foto';
            });
        }

        sync();

        // Buka otomatis di mobile jika validasi gagal agar pesan error terlihat
        @if($errors->any())
            openSheet();
        @endif
    })();
</script>
@endsection