@extends('layouts.siswa')

@section('title', 'Riwayat Transaksi')
@section('page_title', 'Riwayat Transaksi')

@section('content')
{{--
    CATATAN LAYOUT:
    Header memakai teknik "full-bleed" (w-screen + margin negatif) agar menempel ke
    atas, kiri, dan kanan layar. Jika layouts.siswa punya padding di <main>,
    sesuaikan nilai -mt-* di bawah (mis. -mt-4 sm:-mt-6 lg:-mt-8) dengan padding atas tersebut.
--}}
<div class="relative left-1/2 right-1/2 -mx-[50vw] w-screen -mt-4 sm:-mt-6 lg:-mt-8 pb-28 sm:pb-10">

    {{-- ===== HEADER GRADIENT ===== --}}
    <header class="relative overflow-hidden bg-gradient-to-br from-orange-500 via-red-500 to-red-800 text-white rounded-b-[2rem] sm:rounded-b-[2.5rem] shadow-lg shadow-orange-500/20">
        {{-- dekorasi lingkaran --}}
        <div class="pointer-events-none absolute -top-16 -right-12 w-56 h-56 rounded-full bg-white/10"></div>
        <div class="pointer-events-none absolute top-24 -left-16 w-44 h-44 rounded-full bg-white/10"></div>
        <div class="pointer-events-none absolute -bottom-10 right-1/4 w-32 h-32 rounded-full bg-black/10"></div>

        <div class="relative max-w-3xl mx-auto px-5 sm:px-6 pt-[max(1.25rem,env(safe-area-inset-top))] pb-8 sm:pb-10">
            <div class="flex items-center justify-between">
                <a href="{{ route('siswa.finance') }}"
                   class="group inline-flex items-center gap-2 pl-2 pr-4 py-1.5 rounded-full bg-white/15 hover:bg-white/25 backdrop-blur text-xs font-bold uppercase tracking-wider transition-colors">
                    <span class="material-symbols-outlined text-base transition-transform group-hover:-translate-x-0.5">arrow_back</span>
                    <span>Kembali</span>
                </a>

                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-white text-orange-600 text-xs font-extrabold shadow-sm">
                    <span class="material-symbols-outlined text-base">receipt_long</span>
                    Struk
                </span>
            </div>

            <div class="text-center mt-6 sm:mt-8">
                <p class="text-[10px] sm:text-xs font-bold uppercase tracking-[0.2em] text-orange-100/80">Keuangan Siswa</p>
                <h1 class="mt-1 text-2xl sm:text-3xl font-extrabold tracking-tight">Riwayat Transaksi</h1>
                <p class="mt-1.5 text-xs sm:text-sm text-orange-50/80 max-w-sm mx-auto">
                    Lihat detail semua transaksi dan struk Anda di satu tempat.
                </p>
            </div>
        </div>
    </header>

    {{-- ===== DAFT AR TRANSAKSI ===== --}}
    <div class="max-w-3xl mx-auto px-4 sm:px-6 mt-5 space-y-3">
        @forelse($transactions as $transaction)
        @php
            $isCredit = in_array($transaction->type, ['topup', 'transfer_receive']);
            $transactionTypes = [
                'topup' => [
                    'icon' => 'add_card',
                    'color' => 'bg-emerald-50 text-emerald-600',
                    'label' => 'Top Up Saldo',
                ],
                'payment' => [
                    'icon' => 'shopping_cart',
                    'color' => 'bg-rose-50 text-rose-600',
                    'label' => 'Pembelian / Belanja',
                ],
                'transfer' => [
                    'icon' => 'send',
                    'color' => 'bg-orange-50 text-orange-600',
                    'label' => 'Transfer Keluar',
                ],
                'transfer_receive' => [
                    'icon' => 'call_received',
                    'color' => 'bg-cyan-50 text-cyan-600',
                    'label' => 'Transfer Masuk',
                ],
                'withdraw' => [
                    'icon' => 'point_of_sale',
                    'color' => 'bg-amber-50 text-amber-600',
                    'label' => 'Tarik Tunai',
                ],
            ];
            $typeDetails = $transactionTypes[$transaction->type] ?? [
                'icon' => 'receipt',
                'color' => 'bg-orange-50 text-orange-600',
                'label' => ucfirst($transaction->type),
            ];
            $txIcon = $typeDetails['icon'];
            $txColorClass = $typeDetails['color'];
            $txLabel = $typeDetails['label'];
            $badgeClass = $isCredit
                ? 'bg-emerald-50 text-emerald-700'
                : ($transaction->type === 'withdraw' ? 'bg-amber-50 text-amber-700' : 'bg-rose-50 text-rose-700');
        @endphp

        <a href="{{ route('siswa.finance.transactions.show', $transaction) }}"
           class="group block bg-white rounded-2xl border border-slate-100 p-4 shadow-sm shadow-slate-200/50 hover:shadow-md hover:shadow-orange-500/10 hover:border-orange-200 active:scale-[0.99] transition-all duration-200">
            <div class="flex items-center gap-3">
                <div class="w-11 h-11 rounded-2xl flex items-center justify-center shrink-0 {{ $txColorClass }}">
                    <span class="material-symbols-outlined text-xl">{{ $txIcon }}</span>
                </div>

                <div class="min-w-0 flex-1">
                    <div class="flex items-start justify-between gap-2">
                        <p class="text-sm font-bold text-slate-800 group-hover:text-orange-600 transition-colors truncate">
                            {{ $txLabel }}
                        </p>
                        <span class="shrink-0 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-extrabold {{ $badgeClass }}">
                            {{ $isCredit ? '+' : '-' }} Rp {{ number_format($transaction->amount, 0, ',', '.') }}
                        </span>
                    </div>

                    <div class="mt-1 flex items-center justify-between gap-2">
                        <div class="flex flex-wrap items-center gap-x-2 text-[11px] text-slate-400 min-w-0">
                            <span>{{ $transaction->created_at->format('d M Y, H:i') }}</span>
                            @if($transaction->reference_number)
                                <span class="text-slate-300">•</span>
                                <span class="font-mono truncate max-w-[110px] sm:max-w-[200px]">{{ $transaction->reference_number }}</span>
                            @endif
                        </div>
                        <p class="shrink-0 text-[10px] font-semibold text-slate-400">
                            Saldo: Rp {{ number_format($transaction->balance_after, 0, ',', '.') }}
                        </p>
                    </div>

                    @if($transaction->description)
                    <p class="mt-1.5 text-[11px] text-slate-500 line-clamp-1">{{ $transaction->description }}</p>
                    @endif
                </div>

                <span class="material-symbols-outlined text-slate-300 group-hover:text-orange-500 group-hover:translate-x-0.5 transition-all text-lg hidden sm:block">chevron_right</span>
            </div>
        </a>
        @empty
        <div class="bg-white rounded-2xl border border-slate-100 shadow-sm py-16 text-center">
            <div class="w-14 h-14 bg-orange-50 text-orange-500 rounded-2xl flex items-center justify-center mx-auto mb-3">
                <span class="material-symbols-outlined text-2xl">receipt_long</span>
            </div>
            <p class="text-sm font-bold text-slate-700">Belum Ada Transaksi</p>
            <p class="text-xs text-slate-400 mt-1 max-w-xs mx-auto">Riwayat pembayaran atau pengisian saldo Anda akan muncul di sini.</p>
        </div>
        @endforelse

        @if(method_exists($transactions, 'links'))
        <div class="pt-2">
            {{ $transactions->links() }}
        </div>
        @endif
    </div>
</div>
@endsection