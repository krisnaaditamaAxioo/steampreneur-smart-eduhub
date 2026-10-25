@extends('layouts.siswa')

@section('title', 'Detail Transaksi')
@section('page_title', 'Detail Transaksi')

@section('content')
@php
    $isCredit = in_array($transaction->type, ['topup', 'transfer_receive']);

    switch ($transaction->type) {
        case 'topup':            $txLabel = 'Top Up Saldo';         $txFlowLabel = 'Dana Masuk';  $tone = 'emerald'; $txIcon = 'add_card';      break;
        case 'payment':          $txLabel = 'Pembayaran / Belanja'; $txFlowLabel = 'Dana Keluar'; $tone = 'rose';    $txIcon = 'shopping_bag';  break;
        case 'transfer':         $txLabel = 'Transfer Keluar';      $txFlowLabel = 'Dana Keluar'; $tone = 'violet';  $txIcon = 'north_east';    break;
        case 'transfer_receive': $txLabel = 'Transfer Masuk';       $txFlowLabel = 'Dana Masuk';  $tone = 'cyan';    $txIcon = 'south_west';    break;
        case 'withdraw':         $txLabel = 'Tarik Tunai';          $txFlowLabel = 'Dana Keluar'; $tone = 'amber';   $txIcon = 'payments';      break;
        default:                 $txLabel = ucfirst($transaction->type); $txFlowLabel = $isCredit ? 'Dana Masuk' : 'Dana Keluar'; $tone = 'slate'; $txIcon = 'receipt_long';
    }

    $dotColor = match($tone) {
        'emerald' => 'bg-emerald-500',
        'rose'    => 'bg-rose-500',
        'violet'  => 'bg-violet-500',
        'cyan'    => 'bg-cyan-500',
        'amber'   => 'bg-amber-500',
        default   => 'bg-slate-500',
    };
    $iconBox = match($tone) {
        'emerald' => 'bg-emerald-50 text-emerald-600',
        'rose'    => 'bg-rose-50 text-rose-600',
        'violet'  => 'bg-violet-50 text-violet-600',
        'cyan'    => 'bg-cyan-50 text-cyan-600',
        'amber'   => 'bg-amber-50 text-amber-600',
        default   => 'bg-slate-100 text-slate-600',
    };
    $amountColor = $isCredit ? 'text-emerald-600' : ($transaction->type === 'withdraw' ? 'text-amber-600' : 'text-slate-900');

    $student     = $transaction->card->student;
    $studentName = $student->nama_lengkap ?? $student->name ?? '-';
    $nis         = $student->nis_siswa ?? $student->nis ?? null;
    $sign        = $isCredit ? '+' : '-';
    $amountText  = 'Rp ' . number_format($transaction->amount, 0, ',', '.');
    $dateText    = $transaction->created_at->format('d M Y, H:i') . ' WIB';
    $receiptUrl  = route('siswa.finance.transactions.receipt', $transaction);
@endphp

<div class="w-full pb-8">

    {{-- ================= Header oranye ================= --}}
    {{-- CATATAN: -mx-4 -mt-4 harus sama dengan padding konten di layouts.siswa (mobile). --}}
    {{-- Di desktop berbentuk banner kartu seperti halaman admin. --}}
    <header class="relative overflow-hidden text-white
                   -mx-4 -mt-4 px-5 pt-6 pb-24 rounded-b-[2.5rem]
                   md:mx-0 md:mt-0 md:px-10 md:pt-9 md:pb-10 md:rounded-[2rem]
                   bg-gradient-to-br from-orange-400 via-red-500 to-red-800
                   shadow-xl shadow-orange-500/20">
        <div class="pointer-events-none absolute -top-20 -right-12 w-72 h-72 md:w-96 md:h-96 rounded-full bg-white/10"></div>
        <div class="pointer-events-none absolute -bottom-20 left-10 w-56 h-56 rounded-full bg-white/5"></div>
        <div class="pointer-events-none absolute top-10 right-24 w-40 h-40 rounded-full bg-white/5 hidden md:block"></div>

        {{-- Baris atas --}}
        <div class="relative flex items-center justify-between gap-4">
            <div class="flex items-center gap-3 md:gap-4 min-w-0">
                <a href="{{ route('siswa.finance.history') }}"
                   aria-label="Kembali ke riwayat"
                   class="shrink-0 w-11 h-11 md:w-12 md:h-12 rounded-full bg-white/15 hover:bg-white/25 backdrop-blur flex items-center justify-center transition active:scale-90 focus:outline-none focus-visible:ring-4 focus-visible:ring-white/30">
                    <span class="material-symbols-outlined text-[24px]">arrow_back</span>
                </a>
                <div class="min-w-0">
                    <p class="text-[11px] md:text-xs font-bold uppercase tracking-[0.25em] text-white/80">Smart EduHub Wallet</p>
                    <h1 class="text-2xl md:text-4xl font-extrabold tracking-tight truncate">Detail Transaksi</h1>
                </div>
            </div>

            {{-- Badge (desktop) --}}
            <span class="hidden md:inline-flex shrink-0 items-center gap-2.5 bg-white text-slate-900 font-semibold text-base px-5 py-2.5 rounded-full shadow-lg shadow-red-900/20">
                <i class="w-3 h-3 rounded-full {{ $dotColor }}"></i>
                {{ $txFlowLabel }}
            </span>
        </div>

        {{-- Desktop: subjudul + tombol aksi --}}
        <div class="relative hidden md:block mt-4">
            <p class="text-white/85 text-lg">Rincian lengkap dan validasi saldo transaksi.</p>
            <div class="mt-6 flex flex-wrap items-center gap-3">
                <a href="{{ $receiptUrl }}" target="_blank"
                   class="inline-flex items-center gap-2 px-5 py-3 rounded-2xl bg-white text-orange-700 font-bold shadow-lg shadow-red-900/20 hover:bg-orange-50 transition active:scale-95 focus:outline-none focus-visible:ring-4 focus-visible:ring-white/40">
                    <span class="material-symbols-outlined text-[22px]">print</span>
                    Cetak Struk
                </a>
                <button type="button" onclick="shareReceipt()"
                        class="inline-flex items-center gap-2 px-5 py-3 rounded-2xl bg-white/15 hover:bg-white/25 border border-white/30 text-white font-bold backdrop-blur transition active:scale-95 focus:outline-none focus-visible:ring-4 focus-visible:ring-white/40">
                    <span class="material-symbols-outlined text-[22px]">ios_share</span>
                    Bagikan Link
                </button>
            </div>
        </div>

        {{-- Mobile: nominal besar --}}
        <div class="relative md:hidden mt-8 text-center">
            <span class="inline-flex items-center gap-2 bg-white text-slate-900 font-semibold text-sm px-4 py-2 rounded-full shadow-lg shadow-red-900/20">
                <i class="w-2.5 h-2.5 rounded-full {{ $dotColor }}"></i>
                {{ $txFlowLabel }}
            </span>
            <p class="mt-5 text-xs font-bold uppercase tracking-[0.25em] text-white/80">Nominal transaksi</p>
            <p class="mt-1 text-4xl font-extrabold tracking-tight break-words">{{ $sign }} {{ $amountText }}</p>
            <p class="mt-2 text-sm text-white/80">{{ $txLabel }} &middot; {{ $dateText }}</p>
        </div>
    </header>

    {{-- ================= Konten ================= --}}
    <div class="relative z-10 -mt-14 md:mt-6 mx-1 md:mx-0 space-y-4 md:space-y-6">

        {{-- Mobile: menu aksi cepat (menimpa header) --}}
        <nav class="md:hidden bg-white rounded-[2rem] shadow-xl shadow-slate-300/40 border border-slate-100 p-4 grid grid-cols-3 gap-2" aria-label="Aksi transaksi">
            <button type="button" onclick="shareReceipt()" class="flex flex-col items-center gap-2 py-2 rounded-2xl active:scale-95 transition focus:outline-none focus-visible:ring-4 focus-visible:ring-orange-500/20">
                <span class="w-14 h-14 rounded-2xl bg-orange-50 text-orange-600 flex items-center justify-center">
                    <span class="material-symbols-outlined text-[28px]" style="font-variation-settings:'FILL' 1">ios_share</span>
                </span>
                <span class="text-sm font-semibold text-slate-700">Bagikan</span>
            </button>
            <a href="{{ $receiptUrl }}" target="_blank" class="flex flex-col items-center gap-2 py-2 rounded-2xl active:scale-95 transition focus:outline-none focus-visible:ring-4 focus-visible:ring-orange-500/20">
                <span class="w-14 h-14 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
                    <span class="material-symbols-outlined text-[28px]" style="font-variation-settings:'FILL' 1">print</span>
                </span>
                <span class="text-sm font-semibold text-slate-700">Cetak</span>
            </a>
            <button type="button" onclick="copyRefCodeDetail()" class="flex flex-col items-center gap-2 py-2 rounded-2xl active:scale-95 transition focus:outline-none focus-visible:ring-4 focus-visible:ring-orange-500/20">
                <span class="w-14 h-14 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center">
                    <span class="material-symbols-outlined text-[28px]" style="font-variation-settings:'FILL' 1">content_copy</span>
                </span>
                <span class="text-sm font-semibold text-slate-700">Salin Ref</span>
            </button>
        </nav>

        <div class="grid lg:grid-cols-3 gap-4 md:gap-6 items-start">

            {{-- ============ Kolom kanan (desktop) / atas (mobile): ringkasan ============ --}}
            <aside class="lg:col-span-1 lg:order-2 space-y-4 md:space-y-6">

                {{-- Ringkasan saldo --}}
                <section class="bg-white rounded-[2rem] shadow-md shadow-slate-200/60 border border-slate-100 p-5 md:p-7">
                    {{-- Nominal (desktop) --}}
                    <div class="hidden md:flex items-center gap-4 pb-6 mb-6 border-b border-slate-100">
                        <span class="shrink-0 w-14 h-14 rounded-2xl {{ $iconBox }} flex items-center justify-center">
                            <span class="material-symbols-outlined text-[28px]" style="font-variation-settings:'FILL' 1">{{ $txIcon }}</span>
                        </span>
                        <div class="min-w-0">
                            <p class="text-sm font-medium text-slate-500">Nominal transaksi</p>
                            <p class="text-2xl xl:text-3xl font-extrabold tracking-tight break-words {{ $amountColor }}">{{ $sign }} {{ $amountText }}</p>
                        </div>
                    </div>

                    <h2 class="flex items-center gap-2 text-lg font-extrabold text-slate-900">
                        <span class="material-symbols-outlined text-orange-600 text-[24px]">account_balance_wallet</span>
                        Perubahan saldo
                    </h2>

                    <div class="mt-4 grid grid-cols-[1fr_auto_1fr] items-center gap-2 md:gap-3">
                        <div class="rounded-2xl bg-slate-50 border border-slate-100 p-3 md:p-4 min-w-0">
                            <p class="text-xs font-medium text-slate-500">Sebelum</p>
                            <p class="mt-1 text-sm md:text-base font-bold text-slate-600 break-words">Rp {{ number_format($transaction->balance_before, 0, ',', '.') }}</p>
                        </div>
                        <span class="material-symbols-outlined text-orange-500 text-[24px]">arrow_forward</span>
                        <div class="rounded-2xl bg-orange-50 border-2 border-orange-200 p-3 md:p-4 min-w-0">
                            <p class="text-xs font-medium text-orange-700">Sesudah</p>
                            <p class="mt-1 text-sm md:text-base font-extrabold text-slate-900 break-words">Rp {{ number_format($transaction->balance_after, 0, ',', '.') }}</p>
                        </div>
                    </div>
                </section>

                {{-- Akun & kartu --}}
                <section class="bg-white rounded-[2rem] shadow-md shadow-slate-200/60 border border-slate-100 p-5 md:p-7">
                    <h2 class="flex items-center gap-2 text-lg font-extrabold text-slate-900">
                        <span class="material-symbols-outlined text-orange-600 text-[24px]">credit_card</span>
                        Akun &amp; kartu
                    </h2>

                    <dl class="mt-3 divide-y divide-slate-100">
                        <div class="flex items-start justify-between gap-4 py-3">
                            <dt class="text-sm text-slate-500 shrink-0">Pemilik rekening</dt>
                            <dd class="text-sm font-bold text-slate-800 text-right break-words min-w-0">{{ $studentName }}</dd>
                        </div>
                        @if($nis)
                        <div class="flex items-start justify-between gap-4 py-3">
                            <dt class="text-sm text-slate-500 shrink-0">NIS siswa</dt>
                            <dd class="text-sm font-mono font-bold text-slate-800 text-right">{{ $nis }}</dd>
                        </div>
                        @endif
                        <div class="flex items-start justify-between gap-4 py-3">
                            <dt class="text-sm text-slate-500 shrink-0">ID kartu / RFID</dt>
                            <dd class="text-sm font-mono font-bold text-slate-800 text-right break-all min-w-0">{{ $transaction->card->card_number }}</dd>
                        </div>
                    </dl>
                </section>
            </aside>

            {{-- ============ Kolom kiri (desktop) / bawah (mobile): rincian ============ --}}
            <div class="lg:col-span-2 lg:order-1 space-y-4 md:space-y-6">

                {{-- Rincian transaksi --}}
                <section class="bg-white rounded-[2rem] shadow-md shadow-slate-200/60 border border-slate-100 p-5 md:p-7">
                    <h2 class="flex items-center gap-2 text-lg md:text-xl font-extrabold text-slate-900">
                        <span class="material-symbols-outlined text-orange-600 text-[24px]">receipt_long</span>
                        Rincian transaksi
                    </h2>

                    <div class="mt-4 grid grid-cols-1 sm:grid-cols-2 gap-3 md:gap-4">
                        <div class="flex items-start gap-3 p-4 rounded-2xl bg-slate-50 border border-slate-100">
                            <span class="shrink-0 w-10 h-10 rounded-xl {{ $iconBox }} flex items-center justify-center">
                                <span class="material-symbols-outlined text-[22px]">{{ $txIcon }}</span>
                            </span>
                            <div class="min-w-0">
                                <p class="text-xs font-medium text-slate-500">Jenis layanan</p>
                                <p class="font-bold text-slate-800 break-words">{{ $txLabel }}</p>
                            </div>
                        </div>

                        <div class="flex items-start gap-3 p-4 rounded-2xl bg-slate-50 border border-slate-100">
                            <span class="shrink-0 w-10 h-10 rounded-xl bg-orange-50 text-orange-600 flex items-center justify-center">
                                <span class="material-symbols-outlined text-[22px]">schedule</span>
                            </span>
                            <div class="min-w-0">
                                <p class="text-xs font-medium text-slate-500">Waktu transaksi</p>
                                <p class="font-bold text-slate-800 break-words">{{ $dateText }}</p>
                            </div>
                        </div>

                        <div class="flex items-center gap-3 p-4 rounded-2xl bg-slate-50 border border-slate-100 sm:col-span-2">
                            <span class="shrink-0 w-10 h-10 rounded-xl bg-orange-50 text-orange-600 flex items-center justify-center">
                                <span class="material-symbols-outlined text-[22px]">tag</span>
                            </span>
                            <div class="min-w-0 flex-1">
                                <p class="text-xs font-medium text-slate-500">Nomor referensi</p>
                                <p id="refNumberDetail" class="font-mono font-bold text-slate-800 break-all">{{ $transaction->reference_number ?? '-' }}</p>
                            </div>
                            <button type="button" onclick="copyRefCodeDetail()" title="Salin kode" aria-label="Salin nomor referensi"
                                    class="shrink-0 w-10 h-10 rounded-xl bg-white border border-slate-200 text-slate-500 hover:text-orange-600 hover:border-orange-200 flex items-center justify-center transition active:scale-90 focus:outline-none focus-visible:ring-4 focus-visible:ring-orange-500/20">
                                <span class="material-symbols-outlined text-[20px]">content_copy</span>
                            </button>
                        </div>
                    </div>
                </section>

                {{-- Keterangan & pelaksana --}}
                <section class="bg-white rounded-[2rem] shadow-md shadow-slate-200/60 border border-slate-100 p-5 md:p-7">
                    <h2 class="flex items-center gap-2 text-lg md:text-xl font-extrabold text-slate-900">
                        <span class="material-symbols-outlined text-orange-600 text-[24px]">badge</span>
                        Keterangan &amp; pelaksana
                    </h2>

                    <div class="mt-4 grid grid-cols-1 sm:grid-cols-2 gap-3 md:gap-4">
                        <div class="flex items-start gap-3 p-4 rounded-2xl bg-slate-50 border border-slate-100">
                            <span class="shrink-0 w-10 h-10 rounded-xl bg-orange-50 text-orange-600 flex items-center justify-center">
                                <span class="material-symbols-outlined text-[22px]">person</span>
                            </span>
                            <div class="min-w-0">
                                <p class="text-xs font-medium text-slate-500">Petugas pelaksana</p>
                                <p class="font-bold text-slate-800 break-words">{{ $transaction->performer_name }}</p>
                            </div>
                        </div>

                        <div class="flex items-start gap-3 p-4 rounded-2xl bg-slate-50 border border-slate-100">
                            <span class="shrink-0 w-10 h-10 rounded-xl bg-orange-50 text-orange-600 flex items-center justify-center">
                                <span class="material-symbols-outlined text-[22px]">notes</span>
                            </span>
                            <div class="min-w-0">
                                <p class="text-xs font-medium text-slate-500">Deskripsi</p>
                                <p class="font-bold text-slate-800 break-words">{{ $transaction->description ?: '-' }}</p>
                            </div>
                        </div>

                        @if($transaction->relatedCard)
                        <div class="flex items-start gap-3 p-4 rounded-2xl bg-slate-50 border border-slate-100 sm:col-span-2">
                            <span class="shrink-0 w-10 h-10 rounded-xl bg-cyan-50 text-cyan-600 flex items-center justify-center">
                                <span class="material-symbols-outlined text-[22px]">swap_horiz</span>
                            </span>
                            <div class="min-w-0">
                                <p class="text-xs font-medium text-slate-500">Hubungan kartu terkait</p>
                                <p class="font-bold text-slate-800 break-words">{{ $transaction->relatedCard->student->name }} &ndash; <span class="font-mono">{{ $transaction->relatedCard->card_number }}</span></p>
                            </div>
                        </div>
                        @endif
                    </div>
                </section>
            </div>
        </div>
    </div>
</div>

{{-- Toast --}}
<div id="toastDetail" role="status" aria-live="polite"
     class="hidden fixed bottom-24 md:bottom-8 left-1/2 -translate-x-1/2 max-w-[90vw] bg-slate-900 text-white text-sm font-semibold px-5 py-3 rounded-full shadow-xl items-center gap-2 z-50">
    <span class="material-symbols-outlined text-[20px] text-emerald-400" id="toastIcon">check_circle</span>
    <span id="toastText">Teks berhasil disalin</span>
</div>

<script>
    let toastTimer = null;

    function showToast(message, isError = false) {
        const toast = document.getElementById('toastDetail');
        const toastText = document.getElementById('toastText');
        const toastIcon = document.getElementById('toastIcon');

        toastText.textContent = message;
        toastIcon.textContent = isError ? 'warning' : 'check_circle';
        toastIcon.className = 'material-symbols-outlined text-[20px] ' + (isError ? 'text-rose-400' : 'text-emerald-400');

        toast.classList.remove('hidden');
        toast.classList.add('flex');

        clearTimeout(toastTimer);
        toastTimer = setTimeout(() => {
            toast.classList.add('hidden');
            toast.classList.remove('flex');
        }, 2500);
    }

    // Salin teks: Clipboard API, fallback execCommand
    async function copyText(text) {
        if (navigator.clipboard && window.isSecureContext) {
            await navigator.clipboard.writeText(text);
            return;
        }
        const tempInput = document.createElement('input');
        tempInput.value = text;
        document.body.appendChild(tempInput);
        tempInput.select();
        const ok = document.execCommand('copy');
        document.body.removeChild(tempInput);
        if (!ok) throw new Error('copy failed');
    }

    async function copyRefCodeDetail() {
        const refText = document.getElementById('refNumberDetail').textContent.trim();
        try {
            await copyText(refText);
            showToast('Nomor referensi berhasil disalin.');
        } catch (e) {
            showToast('Gagal menyalin nomor referensi.', true);
        }
    }

    async function shareReceipt() {
        const url = @json($receiptUrl);

        if (navigator.share) {
            try {
                await navigator.share({
                    title: 'Detail Transaksi',
                    text: 'Berikut adalah tautan bukti transaksi digital saya.',
                    url: url,
                });
                return;
            } catch (error) {
                if (error && error.name === 'AbortError') {
                    return;
                }
            }
        }

        try {
            await copyText(url);
            showToast('Tautan struk disalin ke papan klip.');
        } catch (error) {
            showToast('Gagal menyalin tautan secara otomatis.', true);
        }
    }
</script>
@endsection