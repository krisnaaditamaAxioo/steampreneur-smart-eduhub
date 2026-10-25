<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Struk {{ $transaction->reference_number ?? 'Transaksi' }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@500;700&family=Material+Symbols+Outlined" rel="stylesheet" />
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/html2canvas@1.4.1/dist/html2canvas.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
        .font-mono {
            font-family: 'JetBrains Mono', monospace;
        }
        @media print {
            @page {
                size: portrait;
                margin: 0;
            }
            body {
                background: white !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            .print\:hidden {
                display: none !important;
            }
            #receipt-wrapper {
                padding: 0 !important;
            }
            #receipt-content {
                box-shadow: none !important;
                border: none !important;
                border-radius: 0 !important;
            }
        }
    </style>
</head>
<body class="bg-slate-50 text-slate-900 selection:bg-brand-500 selection:text-white">
    @php
        $isPublicReceipt = $isPublicReceipt ?? false;
        $isCredit = in_array($transaction->type, ['topup', 'transfer_receive']);
        switch ($transaction->type) {
            case 'topup':            $txTitle = 'Top Up Saldo'; break;
            case 'payment':          $txTitle = 'Pembayaran'; break;
            case 'transfer':         $txTitle = 'Transfer Keluar'; break;
            case 'transfer_receive': $txTitle = 'Transfer Masuk'; break;
            case 'withdraw':         $txTitle = 'Tarik Tunai'; break;
            default:                 $txTitle = ucfirst($transaction->type);
        }
        $amountColor = $isCredit ? 'text-emerald-600' : 'text-rose-600';
    @endphp
    
    <div id="receipt-wrapper" class="min-h-screen py-4 sm:py-10 px-0 sm:px-6 flex flex-col justify-center items-center">
        <div class="w-full max-w-md mx-auto space-y-4">
            
            <!-- Navigation Action Bar -->
            <div class="flex items-center justify-between px-4 sm:px-0 print:hidden">
                @unless($isPublicReceipt)
                <a href="{{ route('siswa.finance.transactions.show', $transaction) }}" class="inline-flex items-center gap-2 px-4 py-2.5 bg-white border border-slate-200 hover:bg-slate-50 active:scale-95 rounded-2xl text-xs font-bold uppercase tracking-wider text-slate-600 transition-all">
                    <span class="material-symbols-outlined text-sm">arrow_back</span>
                    <span>Kembali</span>
                </a>
                @else
                <span></span>
                @endunless
                
                <div class="flex items-center gap-2">
                    <button type="button" onclick="downloadAsImage()" class="inline-flex items-center gap-2 px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 active:scale-95 rounded-2xl text-xs font-bold uppercase tracking-wider text-white shadow-md shadow-emerald-100 transition-all">
                        <span class="material-symbols-outlined text-sm">download</span>
                        <span>Unduh</span>
                    </button>
                    <button type="button" onclick="window.print()" class="inline-flex items-center gap-2 px-4 py-2.5 bg-[#F6863B] hover:bg-[#EB4325] active:scale-95 rounded-2xl text-xs font-bold uppercase tracking-wider text-white shadow-md shadow-brand-100 transition-all">
                        <span class="material-symbols-outlined text-sm">print</span>
                        <span>Cetak</span>
                    </button>
                </div>
            </div>

            <!-- Main Receipt Ticket Card -->
            <div id="receipt-content" class="bg-white rounded-none sm:rounded-[2.5rem] shadow-xl sm:shadow-2xl shadow-slate-100 border-y sm:border border-slate-100 overflow-hidden relative">
                
                <!-- Ticket Header Part (Purple/blue Gradient) -->
                <div class="bg-gradient-to-br from-[#F6863B] via-[#EB4325] to-[#8A1E1E] text-white p-6 sm:p-8 relative">
                    <!-- Decorative Circle Ornaments inside Card -->
                    <div class="absolute -top-12 -right-12 w-32 h-32 bg-white/10 rounded-full blur-2xl"></div>
                    
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="text-[10px] uppercase tracking-[0.25em] text-brand-200 font-extrabold">E-Receipt Digital</p>
                            <h1 class="text-2xl font-black tracking-tight mt-1">{{ $txTitle }}</h1>
                        </div>
                        <div class="p-2.5 bg-white/10 rounded-xl backdrop-blur-md">
                            <span class="material-symbols-outlined block text-brand-200">receipt_long</span>
                        </div>
                    </div>
                    
                    <div class="mt-8 flex items-center justify-between text-xs text-brand-100">
                        <div>
                            <span class="opacity-60 block text-[9px] uppercase tracking-wider">Tanggal Transaksi</span>
                            <span class="font-semibold">{{ $transaction->created_at->format('d M Y, H:i') }} WIB</span>
                        </div>
                        <div class="text-right">
                            <span class="opacity-60 block text-[9px] uppercase tracking-wider">Status</span>
                            <span class="inline-flex items-center gap-1 bg-emerald-500/20 text-emerald-300 font-bold px-2 py-0.5 rounded-md text-[10px] uppercase">
                                Berhasil
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Ticket Middle Stub: Perforated tear line effect -->
                <div class="relative flex items-center bg-white h-6 overflow-hidden">
                    <!-- Left circular cutout -->
                    <div class="absolute -left-3 w-6 h-6 bg-slate-50 rounded-full border-r border-slate-200 sm:border-slate-100"></div>
                    <!-- Perforated dashed line -->
                    <div class="w-full border-t-2 border-dashed border-slate-200 mx-5"></div>
                    <!-- Right circular cutout -->
                    <div class="absolute -right-3 w-6 h-6 bg-slate-50 rounded-full border-l border-slate-200 sm:border-slate-100"></div>
                </div>

                <!-- Ticket Bottom Body Part -->
                <div class="p-6 sm:p-8 pt-2 space-y-6 bg-white">
                    
                    <!-- Dynamic Big Amount Display -->
                    <div class="text-center pb-6 border-b border-slate-100">
                        <p class="text-[10px] uppercase tracking-[0.2em] text-slate-400 font-black">Jumlah Nominal</p>
                        <p class="text-3xl sm:text-4xl font-black mt-2 tracking-tight {{ $amountColor }}">
                            {{ $isCredit ? '+' : '-' }} Rp {{ number_format($transaction->amount, 0, ',', '.') }}
                        </p>
                    </div>

                    <!-- Transaction Details Meta-List -->
                    <div class="space-y-4 text-xs">
                        <div class="flex justify-between items-center gap-4">
                            <span class="text-slate-400 font-medium uppercase tracking-wider text-[9px]">Nomor Referensi</span>
                            <div class="flex items-center gap-1.5">
                                <span class="font-mono font-bold text-slate-800" id="refNumber">{{ $transaction->reference_number ?? '-' }}</span>
                                <button onclick="copyRefCode()" class="p-1 text-slate-400 hover:text-slate-600 active:scale-90 transition-all rounded print:hidden" title="Salin kode">
                                    <span class="material-symbols-outlined text-[14px]">content_copy</span>
                                </button>
                            </div>
                        </div>

                        <div class="flex justify-between items-start gap-4">
                            <span class="text-slate-400 font-medium uppercase tracking-wider text-[9px] mt-0.5">Pemilik Rekening</span>
                            <span class="font-bold text-slate-800 text-right">{{ $transaction->card->student->nama_lengkap ?? $transaction->card->student->name }}</span>
                        </div>

                        @if(isset($transaction->card->student->nis_siswa) || isset($transaction->card->student->nis))
                        <div class="flex justify-between items-center gap-4">
                            <span class="text-slate-400 font-medium uppercase tracking-wider text-[9px]">NIS Siswa</span>
                            <span class="font-mono font-bold text-slate-800">{{ $transaction->card->student->nis_siswa ?? $transaction->card->student->nis ?? '-' }}</span>
                        </div>
                        @endif

                        <div class="flex justify-between items-center gap-4">
                            <span class="text-slate-400 font-medium uppercase tracking-wider text-[9px]">Nomor Kartu / RFID</span>
                            <span class="font-mono font-bold text-slate-800">{{ $transaction->card->card_number }}</span>
                        </div>

                        <div class="flex justify-between items-center gap-4">
                            <span class="text-slate-400 font-medium uppercase tracking-wider text-[9px]">Saldo Sebelum</span>
                            <span class="font-bold text-slate-700">Rp {{ number_format($transaction->balance_before, 0, ',', '.') }}</span>
                        </div>

                        <div class="flex justify-between items-center gap-4">
                            <span class="text-slate-400 font-medium uppercase tracking-wider text-[9px]">Saldo Sesudah</span>
                            <span class="font-bold text-slate-900 text-sm">Rp {{ number_format($transaction->balance_after, 0, ',', '.') }}</span>
                        </div>

                        <div class="flex justify-between items-start gap-4">
                            <span class="text-slate-400 font-medium uppercase tracking-wider text-[9px] mt-0.5">Petugas Keuangan</span>
                            <span class="font-bold text-slate-800 text-right">{{ $transaction->performer_name }}</span>
                        </div>

                        @if($transaction->relatedCard)
                        <div class="flex justify-between items-start gap-4 p-3 bg-slate-50 rounded-xl border border-slate-100">
                            <span class="text-slate-400 font-medium uppercase tracking-wider text-[9px] mt-0.5">Hubungan Kartu</span>
                            <span class="font-bold text-slate-800 text-right text-[11px]">{{ $transaction->relatedCard->student->nama_lengkap ?? $transaction->relatedCard->student->name }} - {{ $transaction->relatedCard->card_number }}</span>
                        </div>
                        @endif
                    </div>

                    <!-- Description Segment -->
                    <div class="border-t border-slate-100 pt-5 space-y-1.5">
                        <span class="text-slate-400 font-medium uppercase tracking-wider text-[9px]">Keterangan Transaksi</span>
                        <p class="text-xs font-semibold text-slate-700 leading-relaxed bg-slate-50 p-3 rounded-2xl border border-slate-100">{{ $transaction->description ?: 'Tanpa keterangan tambahan.' }}</p>
                    </div>

                    <!-- Decorative Digital Barcode Bar -->
                    <div class="pt-4 flex flex-col items-center justify-center space-y-2 opacity-50">
                        <div class="flex gap-[2px] h-8 items-center">
                            @for ($i = 0; $i < 32; $i++)
                                <div class="bg-slate-900 rounded-sm" style="width: {{ rand(1, 4) }}px; height: 100%;"></div>
                            @endfor
                        </div>
                        <span class="text-[8px] font-mono tracking-[0.4em] text-slate-500 uppercase">{{ $transaction->reference_number ?? 'TRANSACTION' }}</span>
                    </div>

                </div>
            </div>
            
            <!-- Toast notification container -->
            <div id="toast" class="hidden fixed bottom-4 left-1/2 -translate-x-1/2 bg-slate-900 text-white text-xs font-semibold px-4 py-2.5 rounded-xl shadow-lg flex items-center gap-2 animate-bounce z-50">
                <span class="material-symbols-outlined text-sm text-emerald-400">check_circle</span>
                <span id="toast-text">Berhasil disalin</span>
            </div>

        </div>
    </div>

    <!-- Alert Modal -->
    <div id="receiptCustomAlertModal" class="hidden fixed inset-0 bg-slate-950/60 z-[60] flex items-center justify-center p-4 backdrop-blur-sm">
        <div class="bg-white rounded-3xl shadow-2xl max-w-xs w-full overflow-hidden border border-slate-100 p-6 text-center">
            <div class="w-12 h-12 rounded-full bg-rose-50 text-rose-500 flex items-center justify-center mx-auto mb-4">
                <span class="material-symbols-outlined text-2xl">warning</span>
            </div>
            <h3 class="text-base font-bold text-slate-900 mb-2">Terjadi Kendala</h3>
            <p id="receiptCustomAlertMessage" class="text-xs text-slate-500 leading-relaxed mb-6"></p>
            <button type="button" onclick="closeReceiptCustomAlert()" class="w-full py-3 bg-slate-900 hover:bg-slate-800 text-white rounded-xl font-bold text-xs transition-all uppercase tracking-wider">
                Mengerti
            </button>
        </div>
    </div>

    <script>
        function showReceiptCustomAlert(message) {
            document.getElementById('receiptCustomAlertMessage').textContent = message;
            document.getElementById('receiptCustomAlertModal').classList.remove('hidden');
        }

        function closeReceiptCustomAlert() {
            document.getElementById('receiptCustomAlertModal').classList.add('hidden');
        }

        function copyRefCode() {
            const refText = document.getElementById('refNumber').textContent;
            
            // Create fallback temp input for copying in iFrames / mobile
            const tempInput = document.createElement('input');
            tempInput.value = refText;
            document.body.appendChild(tempInput);
            tempInput.select();
            document.execCommand('copy');
            document.body.removeChild(tempInput);

            // Show Toast Alert
            const toast = document.getElementById('toast');
            toast.classList.remove('hidden');
            setTimeout(() => {
                toast.classList.add('hidden');
            }, 2000);
        }

        async function downloadAsImage() {
            const receiptContent = document.getElementById('receipt-content');
            
            try {
                // Ensure all fonts are loaded
                await document.fonts.ready;
                
                // Use a smaller scale for better performance/compatibility
                const canvas = await html2canvas(receiptContent, {
                    scale: 2,
                    useCORS: true,
                    allowTaint: true,
                    backgroundColor: '#ffffff',
                    logging: true, // Temporarily log to debug
                    imageTimeout: 15000
                });
                
                const link = document.createElement('a');
                link.download = 'struk-{{ $transaction->reference_number ?? date('YmdHis') }}.png';
                link.href = canvas.toDataURL('image/png');
                
                // Append to body and click for compatibility
                document.body.appendChild(link);
                link.click();
                document.body.removeChild(link);
                
            } catch (error) {
                console.error('Download error:', error);
                showReceiptCustomAlert('Gagal mengunduh gambar: ' + (error.message || 'Terjadi kesalahan'));
            }
        }

        document.getElementById('receiptCustomAlertModal').addEventListener('click', (e) => {
            if (e.target === document.getElementById('receiptCustomAlertModal')) closeReceiptCustomAlert();
        });
    </script>
</body>
</html>