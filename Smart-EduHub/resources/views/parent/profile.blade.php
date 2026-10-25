@extends('layouts.parent')

@section('title', 'Profil Saya')
@section('page_title', 'Profil Wali Murid')

@section('content')
@php
    $avatar = 'https://ui-avatars.com/api/?name=' . urlencode($parent->name) . '&background=059669&color=fff&bold=true&size=256';
    $student = $parent->student;

    $personal = [
        ['label' => 'Alamat Email',        'value' => $parent->email ?? '-',  'icon' => 'mail',         'wrap' => true],
        ['label' => 'Nomor HP / WhatsApp', 'value' => $parent->phone ?? '-',  'icon' => 'phone_iphone', 'wrap' => false],
        ['label' => 'Jenis Kelamin',       'value' => $parent->gender === 'L' ? 'Laki-laki' : ($parent->gender === 'P' ? 'Perempuan' : '-'), 'icon' => 'wc', 'wrap' => false],
        ['label' => 'Wali dari',           'value' => $student->nama_lengkap ?? '-', 'icon' => 'school',  'wrap' => false],
    ];
@endphp

<style>
    /* Mencegah kotak data melebar karena teks panjang di grid/flex */
    .min-w-0-all > * { min-width: 0; }
</style>

<div class="w-full max-w-3xl lg:max-w-5xl mx-auto space-y-4 sm:space-y-6 min-w-0">

    <!-- ===== Notifikasi berhasil ===== -->
    @if(session('success'))
        <div id="profile-alert" class="bg-emerald-50 border border-emerald-100 text-emerald-800 p-3.5 sm:p-4 rounded-2xl flex items-center gap-3 shadow-sm">
            <span class="material-symbols-outlined text-emerald-600 shrink-0">check_circle</span>
            <span class="text-xs font-bold tracking-tight flex-1 min-w-0 break-words">{{ session('success') }}</span>
            <button type="button" onclick="document.getElementById('profile-alert').remove()" class="shrink-0 w-8 h-8 rounded-full flex items-center justify-center text-emerald-500 hover:bg-emerald-100 transition" aria-label="Tutup">
                <span class="material-symbols-outlined text-base">close</span>
            </button>
        </div>
    @endif

    @if($errors->any())
        <div class="bg-rose-50 border border-rose-100 text-rose-700 p-3.5 sm:p-4 rounded-2xl shadow-sm">
            <div class="flex items-center gap-3">
                <span class="material-symbols-outlined text-rose-500 shrink-0">error</span>
                <p class="text-xs font-bold">Periksa kembali data yang Anda masukkan.</p>
            </div>
            <ul class="list-disc list-inside text-[11px] mt-2 space-y-0.5">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <!-- ===== Layout: 1 kolom (HP/tablet) → 2 kolom (laptop) ===== -->
    <div class="grid lg:grid-cols-12 gap-4 sm:gap-6 items-start min-w-0">

        <!-- ===== Kartu Identitas ===== -->
        <aside class="lg:col-span-5 min-w-0">
            <div class="bg-white rounded-2xl sm:rounded-[2.2rem] border border-slate-100 shadow-sm overflow-hidden">
                <!-- Cover -->
                <div class="relative h-20 min-[400px]:h-24 sm:h-28 bg-gradient-to-r from-emerald-100 via-emerald-50 to-teal-50 overflow-hidden">
                    <div class="absolute -right-10 -top-10 w-40 h-40 rounded-full bg-gradient-to-br from-emerald-400/25 to-teal-500/20 blur-xl"></div>
                    <div class="absolute left-10 -bottom-10 w-28 h-28 rounded-full bg-emerald-300/20 blur-xl"></div>
                </div>

                <div class="px-4 min-[400px]:px-5 sm:px-8 lg:px-6 pb-5 sm:pb-8 lg:pb-6">
                    <div class="flex flex-col sm:flex-row lg:flex-col items-center sm:items-end lg:items-center gap-3 sm:gap-6 lg:gap-4 -mt-10 min-[400px]:-mt-12 sm:-mt-14">
                        <!-- Avatar -->
                        <div class="relative shrink-0">
                            <img src="{{ $avatar }}" alt="Avatar {{ $parent->name }}" class="w-20 h-20 min-[400px]:w-24 min-[400px]:h-24 sm:w-28 sm:h-28 rounded-3xl border-4 border-white object-cover shadow-xl shadow-emerald-900/10 bg-white">
                            <span class="absolute -bottom-1 -right-1 w-5 h-5 bg-emerald-500 border-[3px] border-white rounded-full"></span>
                        </div>

                        <!-- Nama & badge -->
                        <div class="flex-1 min-w-0 w-full text-center sm:text-left lg:text-center sm:pb-1">
                            <h2 class="text-lg min-[400px]:text-xl sm:text-2xl font-extrabold tracking-tight text-slate-900 leading-tight break-words">{{ $parent->name }}</h2>
                            <div class="flex flex-wrap items-center justify-center sm:justify-start lg:justify-center gap-2 mt-2.5">
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 bg-emerald-50 text-emerald-700 border border-emerald-100 rounded-full text-[9px] sm:text-[10px] font-extrabold uppercase tracking-widest">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                    Wali Murid
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Data ringkas -->
                    <div class="grid grid-cols-1 gap-1 sm:gap-2 mt-5 sm:mt-6 min-w-0-all">
                        @foreach($personal as $row)
                            <div class="flex items-center gap-3 p-3 rounded-2xl bg-slate-50 border border-slate-100 min-w-0">
                                <div class="w-10 h-10 rounded-xl bg-white text-emerald-600 flex items-center justify-center shrink-0 border border-emerald-100/60">
                                    <span class="material-symbols-outlined text-xl">{{ $row['icon'] }}</span>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <span class="text-[10px] font-extrabold text-slate-400 uppercase tracking-wider block">{{ $row['label'] }}</span>
                                    <p class="font-bold text-slate-800 text-[13px] sm:text-sm mt-0.5 {{ $row['wrap'] ? 'break-all' : 'break-words' }}">{{ $row['value'] }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </aside>

        <!-- ===== Form Ubah Profil ===== -->
        <section class="lg:col-span-7 min-w-0">
            <form action="{{ route('parent.profile.update') }}" method="POST" class="bg-white rounded-2xl sm:rounded-[2rem] border border-slate-100 shadow-sm overflow-hidden">
                @csrf
                @method('PUT')

                <div class="px-4 sm:px-6 py-3.5 sm:py-4 bg-slate-50/60 border-b border-slate-100 flex items-center gap-2.5">
                    <span class="w-8 h-8 rounded-xl bg-emerald-100 text-emerald-700 flex items-center justify-center shrink-0">
                        <span class="material-symbols-outlined text-lg">edit_square</span>
                    </span>
                    <h3 class="text-[10px] sm:text-[11px] font-extrabold text-slate-500 uppercase tracking-[0.15em]">Ubah Data Profil</h3>
                </div>

                <div class="p-4 sm:p-6 space-y-5">
                    <p class="-mt-1 text-[11px] text-slate-400">Biarkan password kosong jika tidak ingin mengubahnya.</p>

                    <div class="grid sm:grid-cols-2 gap-4">
                        <div class="space-y-1.5">
                            <label for="name" class="text-[11px] font-extrabold text-slate-500 uppercase tracking-wider">Nama Lengkap <span class="text-rose-500">*</span></label>
                            <input id="name" type="text" name="name" required value="{{ old('name', $parent->name) }}"
                                   class="w-full bg-slate-50 border border-slate-200 rounded-xl text-sm py-2.5 px-3 text-slate-800 focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/15 focus:outline-none @error('name') border-rose-300 @enderror">
                        </div>

                        <div class="space-y-1.5">
                            <label for="email" class="text-[11px] font-extrabold text-slate-500 uppercase tracking-wider">Alamat Email <span class="text-rose-500">*</span></label>
                            <input id="email" type="email" name="email" required value="{{ old('email', $parent->email) }}"
                                   class="w-full bg-slate-50 border border-slate-200 rounded-xl text-sm py-2.5 px-3 text-slate-800 focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/15 focus:outline-none @error('email') border-rose-300 @enderror">
                        </div>

                        <div class="space-y-1.5">
                            <label for="phone" class="text-[11px] font-extrabold text-slate-500 uppercase tracking-wider">Nomor HP / WhatsApp</label>
                            <input id="phone" type="text" name="phone" value="{{ old('phone', $parent->phone) }}"
                                   class="w-full bg-slate-50 border border-slate-200 rounded-xl text-sm py-2.5 px-3 text-slate-800 focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/15 focus:outline-none @error('phone') border-rose-300 @enderror">
                        </div>

                        <div class="space-y-1.5">
                            <label for="gender" class="text-[11px] font-extrabold text-slate-500 uppercase tracking-wider">Jenis Kelamin</label>
                            <select id="gender" name="gender"
                                    class="w-full bg-slate-50 border border-slate-200 rounded-xl text-sm py-2.5 px-3 text-slate-800 focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/15 focus:outline-none">
                                <option value="">-- Pilih --</option>
                                <option value="L" @selected(old('gender', $parent->gender) === 'L')>Laki-laki</option>
                                <option value="P" @selected(old('gender', $parent->gender) === 'P')>Perempuan</option>
                            </select>
                        </div>
                    </div>

                    <div class="border-t border-slate-100 pt-5 space-y-4">
                        <p class="text-[11px] font-extrabold text-slate-500 uppercase tracking-[0.15em]">Ubah Password (Opsional)</p>
                        <div class="grid sm:grid-cols-2 gap-4">
                            <div class="space-y-1.5">
                                <label for="password" class="text-[11px] font-extrabold text-slate-500 uppercase tracking-wider">Password Baru</label>
                                <input id="password" type="password" name="password" autocomplete="new-password" placeholder="Minimal 6 karakter"
                                       class="w-full bg-slate-50 border border-slate-200 rounded-xl text-sm py-2.5 px-3 text-slate-800 focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/15 focus:outline-none @error('password') border-rose-300 @enderror">
                            </div>
                            <div class="space-y-1.5">
                                <label for="password_confirmation" class="text-[11px] font-extrabold text-slate-500 uppercase tracking-wider">Konfirmasi Password</label>
                                <input id="password_confirmation" type="password" name="password_confirmation" autocomplete="new-password" placeholder="Ulangi password baru"
                                       class="w-full bg-slate-50 border border-slate-200 rounded-xl text-sm py-2.5 px-3 text-slate-800 focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/15 focus:outline-none">
                            </div>
                        </div>
                    </div>

                    <div class="pt-1">
                        <button type="submit" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-6 py-3 rounded-xl bg-gradient-to-r from-emerald-600 to-emerald-500 text-white text-xs font-extrabold uppercase tracking-widest shadow-lg shadow-emerald-600/25 hover:-translate-y-0.5 active:scale-[0.98] transition-all">
                            <span class="material-symbols-outlined text-lg">save</span>
                            Simpan Perubahan
                        </button>
                    </div>
                </div>
            </form>
        </section>
    </div>
</div>
@endsection
