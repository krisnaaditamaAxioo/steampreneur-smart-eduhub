<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Absensi - {{ $student->nama_lengkap }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
        @media print {
            .no-print { display: none !important; }
            body { background: white !important; padding: 0 !important; }
            .print-container { width: 100% !important; max-width: 100% !important; margin: 0 !important; padding: 0 !important; box-shadow: none !important; }
            .table-scroll { overflow: visible !important; }
            table { border-collapse: collapse !important; width: 100% !important; }
            thead { display: table-header-group; }
            th, td { border: 1px solid #000 !important; padding: 4px !important; font-size: 9px !important; }
            @page { size: portrait; margin: 1cm; }
        }
    </style>
</head>
<body class="bg-slate-50 text-slate-900">
    <div class="no-print sticky top-0 z-50 bg-white border-b border-slate-200 px-8 py-4 flex justify-between items-center shadow-sm">
        <div class="flex items-center gap-4">
            <button onclick="window.print()" class="flex items-center gap-2 px-4 py-2 bg-orange-600 text-white rounded-xl font-bold text-sm hover:bg-orange-700">
                <span class="material-symbols-outlined text-[20px]">print</span>
                Cetak Laporan
            </button>
            <button onclick="window.close()" class="px-4 py-2 border border-slate-200 text-slate-600 rounded-xl font-bold text-sm hover:bg-slate-50">Tutup</button>
        </div>
        <div class="text-slate-400 font-bold text-xs uppercase tracking-widest">Mode Pratinjau Cetak</div>
    </div>

    <main class="print-container max-w-[900px] mx-auto bg-white p-4 sm:p-12 my-4 sm:my-8 shadow-lg">
        <header class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-5 border-b-4 border-slate-900 pb-6 mb-8">
            <div class="flex items-center gap-6">
                <div class="w-16 h-16 bg-orange-600 flex items-center justify-center rounded-xl">
                    <span class="material-symbols-outlined text-[40px] text-white">school</span>
                </div>
                <div>
                    <h1 class="text-2xl font-black uppercase tracking-tight">{{ \App\Models\Setting::get('school_name', 'SMART AXIOO CLASS PROGRAM') }}</h1>
                    <p class="text-xs font-medium text-slate-500">{{ \App\Models\Setting::get('school_address', 'Jl. Raya Utama No. 123, Indonesia') }}</p>
                    <p class="text-xs font-medium text-slate-500">Telp: {{ \App\Models\Setting::get('school_phone', '-') }} | Email: {{ \App\Models\Setting::get('school_email', '-') }}</p>
                </div>
            </div>
            <div class="text-left sm:text-right">
                <h2 class="text-lg font-black text-orange-600">LAPORAN ABSENSI SISWA</h2>
                <p class="text-[10px] font-bold mt-1 uppercase text-slate-400">Tanggal Cetak: {{ date('d F Y') }}</p>
            </div>
        </header>

        <section class="grid grid-cols-1 sm:grid-cols-2 gap-5 sm:gap-8 mb-8 bg-slate-50 p-4 sm:p-6 rounded-2xl border border-slate-100 print:bg-transparent print:border-none print:p-0">
            <div class="space-y-1 text-xs">
                <p><span class="inline-block w-28 font-bold uppercase text-slate-400">Nama Siswa</span>: <strong>{{ $student->nama_lengkap }}</strong></p>
                <p><span class="inline-block w-28 font-bold uppercase text-slate-400">Angkatan</span>: <strong>{{ $student->angkatan ?? '-' }}</strong></p>
                <p><span class="inline-block w-28 font-bold uppercase text-slate-400">Periode</span>: <strong>{{ \Carbon\Carbon::parse($start_date)->format('d M Y') }} - {{ \Carbon\Carbon::parse($end_date)->format('d M Y') }}</strong></p>
            </div>
            <div class="text-left sm:text-right text-xs">
                <p><span class="font-bold uppercase text-slate-400">Keterangan</span></p>
                <p class="text-[10px] text-slate-500 font-bold mt-1">H = Hadir, O = Ontime, T = Telat, I = Izin, S = Sakit, A = Alpa</p>
            </div>
        </section>

        @php
            $ontime = 0;
            $late = 0;
            $izin = 0;
            $sakit = 0;
            $absent = 0;
        @endphp

        <div class="table-scroll overflow-x-auto overscroll-x-contain mb-8">
        <table class="w-full min-w-[780px] border-collapse border border-slate-900">
            <thead>
                <tr class="bg-slate-100">
                    <th class="border border-slate-900 p-2 text-center" rowspan="2">No</th>
                    <th class="border border-slate-900 p-2" rowspan="2">Tanggal</th>
                    <th class="border border-slate-900 p-2" rowspan="2">Jam Masuk</th>
                    <th class="border border-slate-900 p-2 text-center" rowspan="2">Status</th>
                    <th class="border border-slate-900 p-2" rowspan="2">Keterangan</th>
                    <th class="border border-slate-900 p-2 text-center" colspan="6">Kode Rekap</th>
                </tr>
                <tr class="bg-slate-50">
                    <th class="border border-slate-900 p-1 text-center">H</th>
                    <th class="border border-slate-900 p-1 text-center">O</th>
                    <th class="border border-slate-900 p-1 text-center">T</th>
                    <th class="border border-slate-900 p-1 text-center">I</th>
                    <th class="border border-slate-900 p-1 text-center">S</th>
                    <th class="border border-slate-900 p-1 text-center">A</th>
                </tr>
            </thead>
            <tbody>
                @foreach($dates as $index => $date)
                    @php
                        $attendance = $attendances->first(fn ($item) => $item->date === $date);
                        $isHoliday = \App\Models\Holiday::checkHoliday($date);
                        $isFuture = \Carbon\Carbon::parse($date)->isFuture();
                        $status = '-';
                        $code = '';
                        $checkIn = '-';
                        $notes = '-';

                        if ($isHoliday) {
                            $status = 'Libur';
                            $code = 'L';
                        } elseif ($attendance) {
                            $checkIn = $attendance->check_in ?: '-';
                            $notes = $attendance->location ?: '-';
                            if ($attendance->status === 'present') { $status = 'Ontime'; $code = 'O'; $ontime++; }
                            elseif ($attendance->status === 'late') { $status = 'Telat'; $code = 'T'; $late++; }
                            elseif ($attendance->status === 'excused') {
                                if (strpos(strtolower($attendance->location ?? ''), 'sakit') !== false) { $status = 'Sakit'; $code = 'S'; $sakit++; }
                                else { $status = 'Izin'; $code = 'I'; $izin++; }
                            } elseif ($attendance->status === 'absent') { $status = 'Alpa'; $code = 'A'; $absent++; }
                        } elseif (!$isFuture) {
                            $status = 'Alpa';
                            $code = 'A';
                            $absent++;
                        }
                    @endphp
                    <tr>
                        <td class="border border-slate-900 p-2 text-center">{{ $index + 1 }}</td>
                        <td class="border border-slate-900 p-2">{{ \Carbon\Carbon::parse($date)->translatedFormat('l, d F Y') }}</td>
                        <td class="border border-slate-900 p-2 text-center font-mono">{{ $checkIn }}</td>
                        <td class="border border-slate-900 p-2 text-center font-bold">{{ $status }}</td>
                        <td class="border border-slate-900 p-2">{{ $notes }}</td>
                        <td class="border border-slate-900 p-2 text-center">{{ $code === 'H' ? '1' : '-' }}</td>
                        <td class="border border-slate-900 p-2 text-center">{{ $code === 'O' ? '1' : '-' }}</td>
                        <td class="border border-slate-900 p-2 text-center">{{ $code === 'T' ? '1' : '-' }}</td>
                        <td class="border border-slate-900 p-2 text-center">{{ $code === 'I' ? '1' : '-' }}</td>
                        <td class="border border-slate-900 p-2 text-center">{{ $code === 'S' ? '1' : '-' }}</td>
                        <td class="border border-slate-900 p-2 text-center">{{ $code === 'A' ? '1' : '-' }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr class="font-bold bg-slate-50">
                    <td class="border border-slate-900 p-2 text-right" colspan="5">Total Rekap</td>
                    <td class="border border-slate-900 p-2 text-center">{{ $ontime + $late }}</td>
                    <td class="border border-slate-900 p-2 text-center">{{ $ontime }}</td>
                    <td class="border border-slate-900 p-2 text-center">{{ $late }}</td>
                    <td class="border border-slate-900 p-2 text-center">{{ $izin }}</td>
                    <td class="border border-slate-900 p-2 text-center">{{ $sakit }}</td>
                    <td class="border border-slate-900 p-2 text-center">{{ $absent }}</td>
                </tr>
            </tfoot>
        </table>
        </div>

        <!-- <section class="grid grid-cols-3 gap-12 mt-12">
            <div class="text-center text-xs">
                <p class="mb-16">Mengetahui,<br>Kepala Sekolah</p>
                <div class="w-40 border-b border-slate-900 mx-auto mb-1"></div>
                <p class="font-bold uppercase">{{ $headmaster }}</p>
                <p>NIP. {{ $headmaster_nip }}</p>
            </div>
            <div></div>
            <div class="text-center text-xs">
                <p class="mb-16">{{ \Carbon\Carbon::parse($end_date)->format('d F Y') }}<br>Admin Akademik</p>
                <div class="w-40 border-b border-slate-900 mx-auto mb-1"></div>
                <p class="font-bold uppercase">{{ $headmaster }}</p>
                <p>NIP. {{ $headmaster_nip }}</p>
            </div>
        </section> -->
    </main>
</body>
</html>
