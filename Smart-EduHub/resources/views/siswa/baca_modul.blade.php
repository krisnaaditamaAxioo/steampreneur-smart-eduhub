@extends('layouts.siswa')

@section('title', 'Baca Materi: ' . $module->title)
@section('page_title', 'Materi Pembelajaran')

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.9.0/styles/atom-one-dark.min.css">
<style>
    /* ===== Rich Text Content ===== */
    .trix-content { font-size: 0.95rem !important; line-height: 1.85 !important; overflow-wrap: anywhere; word-break: break-word; }
    @media (min-width: 640px) { .trix-content { font-size: 1.025rem !important; } }
    .trix-content > *:first-child { margin-top: 0 !important; }
    .trix-content pre { background: #0f172a !important; color: #e2e8f0 !important; padding: 1rem 1.1rem !important; border-radius: 1rem !important; font-family: 'Fira Code', monospace !important; font-size: 0.8125rem !important; overflow-x: auto !important; margin: 1.25rem 0 !important; -webkit-overflow-scrolling: touch; border: 1px solid #1e293b; }
    .trix-content code { background: #fff1e8 !important; color: #EB4325 !important; padding: 0.125rem 0.4rem !important; border-radius: 0.375rem !important; font-family: 'Fira Code', monospace !important; font-size: 0.85em !important; }
    .trix-content pre code { display: block; background: transparent !important; color: #e2e8f0 !important; padding: 0 !important; }
    .trix-content ul { list-style-type: disc !important; padding-left: 1.5rem !important; margin-bottom: 1.5rem !important; }
    .trix-content ol { list-style-type: decimal !important; padding-left: 1.5rem !important; margin-bottom: 1.5rem !important; }
    .trix-content li { margin-bottom: 0.5rem !important; color: #334155; }
    .trix-content li::marker { color: #F6863B; font-weight: 700; }
    .trix-content a { color: #EB4325; text-decoration: underline; text-underline-offset: 3px; font-weight: 700; transition: color 0.2s ease; }
    .trix-content a:hover { color: #8A1E1E; }
    .trix-content strong { font-weight: 700; color: #1e293b; }
    .trix-content h1 { font-size: 1.5rem !important; font-weight: 800 !important; color: #0f172a !important; margin: 2rem 0 1rem !important; letter-spacing: -0.01em; }
    .trix-content h2 { font-size: 1.25rem !important; font-weight: 800 !important; color: #0f172a !important; margin: 1.75rem 0 0.75rem !important; padding-bottom: .4rem; border-bottom: 2px solid #ffe4d3; }
    .trix-content h3 { font-size: 1.125rem !important; font-weight: 700 !important; color: #1e293b !important; margin: 1.25rem 0 0.5rem !important; }
    .trix-content p { margin-bottom: 1.25rem !important; color: #334155 !important; }
    .trix-content blockquote { border-left: 4px solid #F6863B !important; background: #fff7f1; border-radius: 0 1rem 1rem 0; margin: 1.25rem 0 !important; padding: 0.9rem 1.1rem !important; color: #64748b !important; font-style: italic !important; }
    .trix-content hr { border: none !important; border-top: 2px dashed #e2e8f0 !important; margin: 2rem 0 !important; }
    .trix-content img, .trix-content video, .trix-content iframe { max-width: 100% !important; height: auto; border-radius: 1rem; margin: 1rem 0; }
    .trix-content figure { margin: 1.25rem 0; max-width: 100%; }
    .trix-content table { display: block; width: 100%; overflow-x: auto; border-collapse: collapse; margin: 1.25rem 0; -webkit-overflow-scrolling: touch; }
    .trix-content th, .trix-content td { border: 1px solid #e2e8f0; padding: .6rem .8rem; font-size: .875rem; white-space: nowrap; }
    .trix-content th { background: #fff7f1; font-weight: 700; color: #1e293b; }

    /* ===== Video ===== */
    #video-player { position: relative; isolation: isolate; touch-action: auto; -webkit-overflow-scrolling: touch; }
    #video-player iframe { display: block; position: relative; z-index: 1; width: 100%; height: 100%; border: 0; pointer-events: auto; touch-action: manipulation; }

    /* ===== Top reading progress ===== */
    #reading-progress-top { position: fixed; top: 0; left: 0; height: 3px; width: 0; z-index: 9999; background: linear-gradient(90deg, #F6863B, #EB4325); transition: width .2s ease; box-shadow: 0 0 10px rgba(235,67,37,.45); }

    .no-scrollbar::-webkit-scrollbar { display: none; }
    .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }

    @media (hover: none) { .hover-lift:hover { transform: none !important; } }

    @media print {
        aside, header, nav, footer, .no-print, .action-print-hide { display: none !important; }
        main { margin-left: 0 !important; padding: 0 !important; }
        .bg-white { border: none !important; box-shadow: none !important; }
        .max-w-4xl { max-width: 100% !important; }
    }
</style>
@endpush

@section('content')
@php
    $typeLabels = ['text' => 'Materi Teks', 'pdf' => 'Dokumen PDF', 'video' => 'Video', 'link' => 'Link', 'assignment' => 'TUGAS', 'quiz' => 'KUIS / SOAL', 'coding' => 'LATIHAN CODING'];
    $typeIcons  = ['text' => 'article', 'video' => 'video_library', 'link' => 'link', 'quiz' => 'quiz', 'coding' => 'code', 'assignment' => 'assignment', 'pdf' => 'picture_as_pdf'];
    $typeLabel  = $typeLabels[$module->type] ?? strtoupper($module->type);
    $typeIcon   = $typeIcons[$module->type] ?? 'picture_as_pdf';
    $hasQuestions = $module->questions->count() > 0 || in_array($module->type, ['assignment', 'quiz', 'coding']);
@endphp

<div id="reading-progress-top" class="no-print" aria-hidden="true"></div>

<div class="max-w-4xl mx-auto space-y-5 sm:space-y-6 pb-28 px-2 sm:px-0">

    <!-- ===== Header Banner ===== -->
    <div class="relative bg-gradient-to-br from-[#F6863B] via-[#EB4325] to-[#8A1E1E] -mx-2 -mt-4 pt-6 pb-8 px-5 sm:px-8 text-white overflow-hidden shadow-xl shadow-orange-900/10 rounded-b-[2rem] lg:mx-0 lg:mt-0 lg:rounded-[2rem] lg:pt-7 lg:pb-8 no-print">
        <div class="absolute -top-16 -right-16 w-48 h-48 bg-white/10 rounded-full blur-2xl pointer-events-none"></div>
        <div class="absolute -bottom-10 left-10 w-32 h-32 bg-white/10 rounded-full blur-xl pointer-events-none"></div>
        <div class="absolute inset-0 opacity-[0.07] pointer-events-none" style="background-image: radial-gradient(#fff 1px, transparent 1px); background-size: 18px 18px;"></div>

        <div class="relative z-10 flex flex-col sm:flex-row sm:items-center justify-between gap-4 sm:gap-5">
            <div class="flex items-center gap-3.5 min-w-0">
                <div class="w-11 h-11 sm:w-12 sm:h-12 shrink-0 rounded-2xl bg-white/15 flex items-center justify-center backdrop-blur-md border border-white/20 shadow-lg">
                    <span class="material-symbols-outlined text-white text-2xl">auto_stories</span>
                </div>
                <div class="min-w-0">
                    <span class="text-[10px] font-bold text-white/70 block uppercase tracking-[0.2em] leading-none">Ruang Belajar Mandiri</span>
                    <h1 class="text-xl sm:text-2xl font-extrabold tracking-tight mt-1.5 truncate">Modul Pembelajaran</h1>
                </div>
            </div>
            <div class="flex items-center gap-2.5 px-3.5 py-2 bg-white/10 backdrop-blur-md rounded-xl border border-white/20 w-fit">
                <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                <span class="text-[11px] font-bold text-white uppercase tracking-widest">{{ $typeLabel }}</span>
            </div>
        </div>
    </div>

    <!-- ===== Back & Breadcrumb ===== -->
    <div class="flex items-center justify-between gap-3 px-1 no-print">
        <a href="{{ route('siswa.modules', $module->course_id) }}" class="shrink-0 inline-flex items-center gap-1.5 pl-3.5 pr-4 py-2 bg-brand-50 hover:bg-[#EB4325] border border-orange-200/80 rounded-full text-xs font-extrabold uppercase tracking-wider text-[#EB4325] hover:text-white transition-all active:scale-95 shadow-sm">
            <span class="material-symbols-outlined text-base">arrow_back</span>
            <span>Kembali</span>
        </a>
        <div class="hidden sm:flex items-center gap-1.5 text-[10px] font-extrabold text-slate-400 uppercase tracking-widest min-w-0">
            <span class="truncate max-w-[160px]">{{ $module->course->title }}</span>
            <span class="material-symbols-outlined text-[12px] text-slate-300 shrink-0">chevron_right</span>
            <span class="text-[#EB4325] truncate max-w-[220px]">{{ $module->title }}</span>
        </div>
    </div>

    <!-- ===== Progress ===== -->
    <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-4 sm:p-5 no-print">
        <div class="flex items-center justify-between text-[10px] font-extrabold uppercase tracking-wider text-slate-400 mb-2.5">
            <span class="inline-flex items-center gap-1.5">
                <span class="material-symbols-outlined text-sm text-[#F6863B]">trending_up</span>
                Progres modul
            </span>
            <span id="progress-label" class="text-sm font-black text-[#EB4325]">{{ $progress->progress }}%</span>
        </div>
        <div class="h-2.5 bg-slate-100 rounded-full overflow-hidden">
            <div id="progress-bar" class="h-full bg-gradient-to-r from-[#F6863B] to-[#EB4325] rounded-full transition-all duration-500" style="width: {{ $progress->progress }}%"></div>
        </div>
    </div>

    <!-- ===== Main Module Card ===== -->
    <div class="bg-white rounded-2xl sm:rounded-[2.2rem] border border-slate-100 shadow-sm overflow-hidden flex flex-col">

        <!-- Module Header -->
        <div class="p-5 sm:p-10 border-b border-slate-100/80 bg-gradient-to-b from-orange-50/50 to-transparent">
            <div class="space-y-4">
                <div class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-orange-50 text-[#EB4325] rounded-xl text-[10px] font-extrabold uppercase tracking-widest border border-orange-100">
                    <span class="material-symbols-outlined text-sm" style="font-variation-settings: 'FILL' 1;">{{ $typeIcon }}</span>
                    <span>{{ $module->type === 'text' ? 'Materi Teks' : strtoupper($module->type) }}</span>
                </div>
                <div class="space-y-2.5">
                    <h1 class="text-xl sm:text-2xl md:text-3xl font-extrabold text-slate-900 leading-tight tracking-tight break-words">
                        {{ $module->title }}
                    </h1>
                    @if($module->description)
                        <p class="text-[13px] sm:text-sm text-slate-500 font-medium leading-relaxed max-w-3xl">
                            {{ $module->description }}
                        </p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Content Area -->
        <div class="p-5 sm:p-10 space-y-6 sm:space-y-8 flex-1 min-w-0">

            <!-- Video -->
            @if($module->type === 'video' && $module->file_path)
                @php
                    $videoUrl = trim($module->file_path);
                    $videoId = '';
                    if (!empty($videoUrl)) {
                        if (preg_match('/youtu\.be\/([^\?\&\s]+)/i', $videoUrl, $match)) { $videoId = $match[1]; }
                        elseif (preg_match('/youtube\.com\/(?:shorts|live|embed)\/([^\?\&\s]+)/i', $videoUrl, $match)) { $videoId = $match[1]; }
                        elseif (preg_match('/[\?\&]v=([^\&\s]+)/i', $videoUrl, $match)) { $videoId = $match[1]; }
                        if (!empty($videoId)) {
                            $videoId = explode('&', $videoId)[0];
                            $videoId = explode('?', $videoId)[0];
                            $videoId = explode('#', $videoId)[0];
                            $videoId = substr($videoId, 0, 11);
                        }
                    }
                @endphp
                @if(!empty($videoId) && strlen($videoId) === 11)
                    <div id="video-player" class="aspect-video rounded-2xl sm:rounded-[2rem] overflow-hidden border border-slate-100 shadow-xl shadow-orange-950/10 bg-slate-950 -mx-1 sm:mx-0">
                        <iframe class="w-full h-full"
                            src="https://www.youtube.com/embed/{{ $videoId }}?rel=0&autoplay=0&playsinline=1&controls=1&modestbranding=1"
                            title="YouTube video player" frameborder="0" loading="lazy"
                            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                            allowfullscreen webkitallowfullscreen mozallowfullscreen
                            referrerpolicy="strict-origin-when-cross-origin"></iframe>
                    </div>
                    <a href="{{ $videoUrl }}" target="_blank" rel="noopener noreferrer" class="sm:hidden flex items-center justify-center gap-2 w-full px-5 py-3 bg-red-600 hover:bg-red-700 text-white rounded-xl font-bold text-xs text-center transition-colors active:scale-95 no-print">
                        <span class="material-symbols-outlined text-base">smart_display</span>
                        Buka Video di YouTube
                    </a>
                @else
                    <div class="p-5 bg-rose-50 border border-rose-100/60 rounded-2xl flex items-start gap-3 text-rose-800">
                        <span class="material-symbols-outlined text-rose-500 shrink-0">error</span>
                        <p class="text-xs font-bold">Tautan video tidak valid. Silakan hubungi instruktur.</p>
                    </div>
                @endif
            @endif

            <!-- File/Link Download -->
            @if($module->file_path && $module->type !== 'text' && $module->type !== 'video')
                @php
                    $isExternal = $module->type === 'link' || str_starts_with($module->file_path ?? '', 'http');
                    $url = $isExternal ? $module->file_path : asset('storage/'.$module->file_path);
                @endphp
                <div class="bg-gradient-to-r from-orange-50/70 to-slate-50 p-4 sm:p-5 rounded-2xl border border-orange-100/70 flex flex-col sm:flex-row sm:items-center justify-between gap-4 no-print">
                    <div class="flex items-center gap-3.5 min-w-0">
                        <div class="w-12 h-12 rounded-2xl bg-white text-[#EB4325] flex items-center justify-center shrink-0 border border-orange-100 shadow-sm">
                            <span class="material-symbols-outlined text-2xl">{{ $module->type === 'link' ? 'link' : 'draft' }}</span>
                        </div>
                        <div class="text-left min-w-0">
                            <p class="text-sm font-bold text-slate-800">{{ $module->type === 'link' ? 'Tautan Referensi Eksternal' : 'Lampiran Berkas Belajar' }}</p>
                            <p class="text-[10px] text-slate-400 font-extrabold uppercase tracking-wider mt-0.5">Klik untuk mengunduh atau mengunjungi</p>
                        </div>
                    </div>
                    <a href="{{ $url }}" target="_blank" rel="noopener" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-5 py-3 bg-gradient-to-r from-[#F6863B] to-[#EB4325] text-white rounded-xl font-bold text-[11px] uppercase tracking-widest text-center transition-all hover:-translate-y-0.5 hover:shadow-lg hover:shadow-orange-500/30 active:scale-95 shadow-md shadow-orange-500/20 hover-lift">
                        <span class="material-symbols-outlined text-lg">{{ $module->type === 'link' ? 'open_in_new' : 'download' }}</span>
                        {{ $module->type === 'link' ? 'Kunjungi Tautan' : 'Unduh Berkas' }}
                    </a>
                </div>
            @endif

            <!-- Main Text Content -->
            @if($module->content)
                <div class="trix-content max-w-none">
                    {!! $module->content !!}
                </div>
            @elseif($module->type === 'text')
                <div class="py-14 sm:py-16 text-center max-w-xs mx-auto">
                    <div class="w-14 h-14 bg-slate-50 text-slate-300 rounded-2xl flex items-center justify-center mx-auto mb-3 border border-slate-100">
                        <span class="material-symbols-outlined text-2xl">description</span>
                    </div>
                    <h4 class="font-bold text-slate-700 text-sm">Materi Belum Tersedia</h4>
                    <p class="text-[10px] text-slate-400 font-extrabold tracking-tight uppercase mt-1">Belum ada isi materi di dalam modul ini.</p>
                </div>
            @endif

            <!-- Instruksi / Soal Umum -->
            @if($module->question && in_array($module->type, ['assignment', 'quiz', 'coding']))
                <div class="relative bg-orange-50/70 border border-orange-100 rounded-2xl p-4 sm:p-5 overflow-hidden no-print">
                    <span class="absolute left-0 top-0 bottom-0 w-1 bg-gradient-to-b from-[#F6863B] to-[#EB4325]"></span>
                    <div class="flex items-center gap-2 mb-3 text-[#EB4325]">
                        <span class="material-symbols-outlined text-lg">help</span>
                        <h3 class="text-xs font-extrabold uppercase tracking-wider">Soal / Instruksi Umum</h3>
                    </div>
                    <div class="text-sm text-slate-700 leading-relaxed whitespace-pre-line break-words">{{ $module->question }}</div>
                </div>
            @endif

            <!-- ===== Bottom Action Bar ===== -->
            <div class="no-print pt-6 border-t border-slate-100">
                @if(session('success'))
                    <div class="mb-4 p-4 bg-emerald-50 border border-emerald-200 rounded-2xl flex items-center gap-3 text-emerald-800">
                        <span class="material-symbols-outlined text-emerald-600">check_circle</span>
                        <span class="text-xs font-bold">{{ session('success') }}</span>
                    </div>
                @endif

                @if($progress->progress >= 100)
                    <div class="p-5 sm:p-6 bg-gradient-to-br from-emerald-50 to-white border border-emerald-200/80 rounded-2xl sm:rounded-3xl flex flex-col sm:flex-row sm:items-center justify-between gap-4 text-emerald-900">
                        <div class="flex items-start sm:items-center gap-3.5">
                            <div class="w-12 h-12 rounded-2xl bg-emerald-600 text-white flex items-center justify-center shrink-0 shadow-lg shadow-emerald-500/25">
                                <span class="material-symbols-outlined text-2xl font-bold">check_circle</span>
                            </div>
                            <div class="min-w-0">
                                <h4 class="text-sm font-extrabold text-emerald-950 flex items-center gap-1.5">
                                    <span>Modul Telah Selesai (100%)</span>
                                    <span class="material-symbols-outlined text-emerald-600 text-base">verified</span>
                                </h4>
                                <p class="text-xs text-emerald-700 mt-1 leading-relaxed">Progres Anda pada modul ini sudah tercatat 100% selesai.</p>
                            </div>
                        </div>

                        @if($hasQuestions)
                            <a href="{{ route('siswa.module.soal', $module->id) }}" class="w-full sm:w-auto px-5 py-3 bg-gradient-to-r from-[#F6863B] to-[#EB4325] text-white rounded-2xl font-bold text-xs shadow-lg shadow-orange-500/25 flex items-center justify-center gap-2 shrink-0 transition-all hover:-translate-y-0.5 active:scale-95 hover-lift">
                                <span class="material-symbols-outlined text-base">quiz</span>
                                <span>Lihat / Kerjakan Ulang Soal</span>
                            </a>
                        @else
                            <a href="{{ route('siswa.modules', $module->course_id) }}" class="w-full sm:w-auto px-5 py-3 bg-emerald-600 hover:bg-emerald-700 text-white rounded-2xl font-bold text-xs shadow-lg shadow-emerald-600/25 flex items-center justify-center gap-2 shrink-0 transition-all hover:-translate-y-0.5 active:scale-95 hover-lift">
                                <span class="material-symbols-outlined text-base">arrow_back</span>
                                <span>Kembali ke Daftar Modul</span>
                            </a>
                        @endif
                    </div>
                @elseif($hasQuestions)
                    <div class="p-5 sm:p-6 bg-gradient-to-r from-orange-50 to-white rounded-2xl sm:rounded-3xl border border-orange-100 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                        <div class="flex items-start sm:items-center gap-3.5">
                            <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-[#F6863B] to-[#EB4325] text-white flex items-center justify-center shrink-0 shadow-lg shadow-orange-500/25">
                                <span class="material-symbols-outlined text-2xl">quiz</span>
                            </div>
                            <div class="min-w-0">
                                <h4 class="text-sm font-extrabold text-slate-900">Siap Mengerjakan Soal Modul Ini?</h4>
                                <p class="text-xs text-slate-500 mt-1 leading-relaxed">Modul ini memiliki {{ $module->questions->count() }} soal evaluasi. Klik tombol untuk membuka halaman pengerjaan soal.</p>
                            </div>
                        </div>

                        <a href="{{ route('siswa.module.soal', $module->id) }}" class="w-full sm:w-auto px-6 py-3.5 bg-gradient-to-r from-[#F6863B] to-[#EB4325] text-white rounded-2xl font-black text-xs shadow-xl shadow-orange-500/25 flex items-center justify-center gap-2 shrink-0 transition-all hover:-translate-y-0.5 active:scale-95 hover-lift">
                            <span>Lanjut Kerjakan Soal</span>
                            <span class="material-symbols-outlined text-base">arrow_forward</span>
                        </a>
                    </div>
                @else
                    <div class="p-5 sm:p-6 bg-slate-50 rounded-2xl sm:rounded-3xl border border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                        <div class="flex items-start sm:items-center gap-3.5">
                            <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 border border-emerald-100 flex items-center justify-center shrink-0">
                                <span class="material-symbols-outlined text-2xl">menu_book</span>
                            </div>
                            <div class="min-w-0">
                                <h4 class="text-sm font-extrabold text-slate-800">Sudah Selesai Membaca Materi Ini?</h4>
                                <p class="text-xs text-slate-500 mt-1 leading-relaxed">Tandai modul ini sebagai selesai untuk memperbarui progres pembelajaran Anda ke 100%.</p>
                            </div>
                        </div>

                        <form action="{{ route('siswa.module.finish-read', $module->id) }}" method="POST" class="w-full sm:w-auto">
                            @csrf
                            <button type="submit" class="w-full sm:w-auto px-6 py-3.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-2xl font-black text-xs shadow-xl shadow-emerald-500/25 flex items-center justify-center gap-2 shrink-0 transition-all hover:-translate-y-0.5 active:scale-95 hover-lift">
                                <span class="material-symbols-outlined text-base">check_circle</span>
                                <span>Tandai Selesai Membaca</span>
                            </button>
                        </form>
                    </div>
                @endif
            </div>

        </div>

        <!-- Footer -->
        <div class="px-5 sm:px-10 py-5 sm:py-6 bg-slate-50/60 border-t border-slate-100/80 flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <img src="https://ui-avatars.com/api/?name=Axioo+Instructor&background=fff1e8&color=EB4325&bold=true" class="w-10 h-10 rounded-full border border-slate-100" alt="Instructor Profile" loading="lazy">
                <div>
                    <p class="text-[11px] font-extrabold text-slate-700 uppercase tracking-wider leading-tight">Axioo Instructor</p>
                    <p class="text-[9px] text-slate-400 font-extrabold uppercase tracking-widest mt-0.5">Axioo Class Program Team</p>
                </div>
            </div>
            <a href="{{ route('siswa.module.pdf', $module->id) }}" class="w-full sm:w-auto px-4 py-2.5 bg-brand-50 hover:bg-[#EB4325] border border-orange-200/80 text-[#EB4325] hover:text-white rounded-xl text-[10px] font-extrabold uppercase tracking-widest flex items-center justify-center gap-1.5 transition-all active:scale-95 shadow-sm action-print-hide">
                <span class="material-symbols-outlined text-base">picture_as_pdf</span>
                <span>Unduh PDF Materi</span>
            </a>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.9.0/highlight.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.trix-content pre').forEach(function (pre) {
        let code = pre.querySelector(':scope > code');

        if (!code) {
            code = document.createElement('code');
            pre.classList.forEach(function (className) {
                if (className.startsWith('language-')) code.classList.add(className);
            });
            code.textContent = pre.innerText;
            pre.replaceChildren(code);
        }

        const fencedCode = code.textContent.match(/^\s*```([\w+-]+)?[ \t]*\r?\n([\s\S]*?)\r?\n```[ \t]*\s*$/);
        if (fencedCode) {
            code.textContent = fencedCode[2];
            if (fencedCode[1] && !Array.from(code.classList).some(function (name) { return name.startsWith('language-'); })) {
                code.classList.add('language-' + fencedCode[1]);
            }
        }

        if (window.hljs) hljs.highlightElement(code);
    });

    // ===== Progress (scroll-based) =====
    const progressBar   = document.getElementById('progress-bar');
    const progressLabel = document.getElementById('progress-label');
    const topBar        = document.getElementById('reading-progress-top');
    let lastProgress    = {{ $progress->progress }};

    function paint(value) {
        if (progressBar)   progressBar.style.width = value + '%';
        if (progressLabel) progressLabel.textContent = value + '%';
        if (topBar)        topBar.style.width = value + '%';
    }
    paint(lastProgress);

    function saveProgress(value) {
        value = Math.max(lastProgress, Math.min(100, Math.round(value)));
        if (value === lastProgress) return;
        lastProgress = value;
        paint(value);
        fetch('{{ route('siswa.module.progress', $module->id) }}', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'X-Requested-With': 'XMLHttpRequest' },
            body: JSON.stringify({ progress: value })
        }).catch(function () { /* abaikan error jaringan, akan dicoba lagi saat scroll berikutnya */ });
    }

    let ticking = false;
    function calculateProgress() {
        const maxScroll = document.documentElement.scrollHeight - window.innerHeight;
        saveProgress(maxScroll > 0 ? (window.scrollY / maxScroll) * 100 : 100);
        ticking = false;
    }

    window.addEventListener('scroll', function () {
        if (!ticking) { ticking = true; requestAnimationFrame(calculateProgress); }
    }, { passive: true });
    calculateProgress();
});
</script>
@endpush