@extends('layouts.app')

@section('title', 'Tambah Subkontraktor / Mandor — HSE Pantau')

@section('content')
<div class="mx-auto max-w-xl py-2">
    <!-- Header -->
    <div class="mb-6">
        <h1 class="text-2xl font-extrabold tracking-tight text-slate-900 sm:text-3xl">Tambah Subkon / Mandor</h1>
        <p class="mt-1 text-xs text-slate-500">
            Daftarkan rekanan kontraktor atau mandor lapangan baru untuk penempatan site proyek.
        </p>
    </div>

    <!-- Form Card -->
    <div class="rounded-2xl border border-slate-200/90 bg-white p-6 shadow-2xs sm:p-8">
        <form method="POST" action="{{ route('subkon.store') }}" class="space-y-5">
            @csrf

            <!-- Site Selection -->
            <div>
                <label class="block text-xs font-semibold uppercase tracking-wider text-slate-700">
                    Wilayah Site Penugasan <span class="text-rose-500">*</span>
                </label>
                <div class="relative mt-1">
                    <select name="site_code" class="w-full appearance-none rounded-xl border border-slate-300 bg-slate-50/50 py-2.5 pr-8 pl-3 text-xs font-semibold text-slate-800 transition-all focus:border-slate-900 focus:bg-white focus:outline-hidden" required>
                        @foreach($sites as $s)
                            <option value="{{ $s->code }}" @selected(old('site_code') === $s->code)>{{ $s->code }} — {{ $s->name }}</option>
                        @endforeach
                    </select>
                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-2.5 text-slate-400">
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"/></svg>
                    </div>
                </div>
            </div>

            <!-- Nama Rekanan / Mandor -->
            <div>
                <label for="nama_input" class="block text-xs font-semibold uppercase tracking-wider text-slate-700">
                    Nama Subkontraktor / Mandor <span class="text-rose-500">*</span>
                </label>
                <div class="relative mt-1">
                    <input 
                        id="nama_input"
                        name="nama" 
                        value="{{ old('nama') }}" 
                        placeholder="cth: Subkon Berkah Jaya atau Mandor Sutrisno" 
                        required
                        class="w-full rounded-xl border border-slate-300 bg-slate-50/50 py-2.5 px-3 text-xs font-medium text-slate-900 transition-all placeholder:text-slate-400 focus:border-slate-900 focus:bg-white focus:outline-hidden"
                    >
                </div>
                <p class="mt-1.5 text-[11px] text-slate-400">
                    Masukkan nama PT / CV vendor subkon, atau nama perseorangan mandor tim lapangan.
                </p>
            </div>

            <!-- Bidang Pekerjaan -->
            <div>
                <label for="bidang_input" class="block text-xs font-semibold uppercase tracking-wider text-slate-700">
                    Spesialisasi / Bidang Pekerjaan
                </label>
                <input 
                    id="bidang_input"
                    name="bidang" 
                    value="{{ old('bidang') }}" 
                    placeholder="cth: Pekerjaan Pembesian & Struktur Beton"
                    class="mt-1 w-full rounded-xl border border-slate-300 bg-slate-50/50 py-2.5 px-3 text-xs text-slate-800 transition-all placeholder:text-slate-400 focus:border-slate-900 focus:bg-white focus:outline-hidden"
                >
            </div>

            <!-- Kontak / PIC -->
            <div>
                <label for="kontak_input" class="block text-xs font-semibold uppercase tracking-wider text-slate-700">
                    Kontak / Nomor Telepon PIC
                </label>
                <input 
                    id="kontak_input"
                    name="kontak" 
                    value="{{ old('kontak') }}" 
                    placeholder="cth: 081234567890 (Pak Bambang)"
                    class="mt-1 w-full rounded-xl border border-slate-300 bg-slate-50/50 py-2.5 px-3 text-xs text-slate-800 transition-all placeholder:text-slate-400 focus:border-slate-900 focus:bg-white focus:outline-hidden"
                >
            </div>

            <!-- Submit Button -->
            <div class="pt-2">
                <button class="flex w-full items-center justify-center gap-2 rounded-xl bg-slate-900 py-3 text-sm font-semibold text-white shadow-md transition-all hover:bg-slate-800 active:scale-[0.98]">
                    <span>Simpan Data Subkontraktor</span>
                    <svg class="h-4 w-4 text-emerald-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polyline points="20 6 9 17 4 12"/>
                    </svg>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
