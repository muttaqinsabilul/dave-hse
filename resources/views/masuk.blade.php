@extends('layouts.app')

@section('title', 'Gerbang Masuk — HSE Pantau')

@section('content')
<div class="mx-auto max-w-3xl py-4">
    <!-- Hero / Welcome Header (Badge QR removed as requested) -->
    <div class="text-center">
        <h1 class="text-2xl font-extrabold tracking-tight text-slate-900 sm:text-3xl">
            Sistem Pemantauan K3 Lapangan
        </h1>
        <p class="mx-auto mt-2 max-w-lg text-sm text-slate-600">
            Pilih jalur akses sesuai kewenangan. Petugas HSE untuk administrasi & pengawasan, atau Pekerja untuk cek kesehatan harian.
        </p>
    </div>

    <!-- Dual Path Cards -->
    <div class="mt-8 grid gap-6 md:grid-cols-2">
        <!-- Card 1: HSE & Inspector -->
        <div class="relative flex flex-col justify-between overflow-hidden rounded-2xl border border-slate-200/90 bg-white p-6 shadow-xs">
            <!-- Accent Top Stripe -->
            <div class="absolute inset-x-0 top-0 h-1 bg-gradient-to-r from-slate-800 to-slate-950"></div>

            <div>
                <div class="flex items-center justify-between">
                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-slate-900 text-white shadow-xs">
                        <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                            <path d="M9 12l2 2 4-4"/>
                        </svg>
                    </div>
                    <span class="rounded-full bg-slate-100 px-2.5 py-1 text-[11px] font-semibold text-slate-700">
                        Akses Pengawas
                    </span>
                </div>

                <h2 class="mt-4 text-lg font-bold text-slate-900">Petugas HSE & Inspector</h2>
                <p class="mt-1 text-xs leading-relaxed text-slate-500">
                    Akses rekam tensi pekerja, input matriks bahaya IBPR harian, dan monitoring analitik proyek.
                </p>

                <form method="POST" action="{{ route('masuk.post') }}" class="mt-5 space-y-3.5">
                    @csrf
                    <input type="hidden" name="kind" value="hse">
                    <div>
                        <label for="hse_id" class="block text-xs font-semibold uppercase tracking-wider text-slate-600">
                            Nomor ID HSE
                        </label>
                        <div class="relative mt-1">
                            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">
                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                                    <path d="M7 11V7a5 5 0 0 1 10 0v4"/>
                                </svg>
                            </div>
                            <input 
                                id="hse_id"
                                name="id" 
                                value="{{ old('id') }}" 
                                placeholder="cth: ADM-001 atau INS-01-001" 
                                class="w-full rounded-xl border border-slate-300 bg-slate-50/50 py-2.5 pr-3 pl-9 text-sm text-slate-800 transition-all placeholder:text-slate-400 focus:border-slate-900 focus:bg-white focus:ring-2 focus:ring-slate-900/10 focus:outline-hidden"
                            >
                        </div>
                    </div>

                    <!-- Demo quick links for reviewer convenience -->
                    <div class="flex items-center gap-1.5 pt-1 text-[11px] text-slate-500">
                        <span class="text-slate-400">Pintasan ID:</span>
                        <button type="button" onclick="document.getElementById('hse_id').value='ADM-001'" class="rounded-md bg-slate-100 px-1.5 py-0.5 text-slate-700 hover:bg-slate-200">ADM-001</button>
                        <button type="button" onclick="document.getElementById('hse_id').value='INS-01-001'" class="rounded-md bg-slate-100 px-1.5 py-0.5 text-slate-700 hover:bg-slate-200">INS-01-001</button>
                    </div>

                    <button class="mt-2 flex w-full items-center justify-center gap-2 rounded-xl bg-slate-900 py-2.5 text-sm font-semibold text-white shadow-xs transition-all hover:bg-slate-800 active:scale-[0.98]">
                        <span>Masuk Sebagai HSE</span>
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <line x1="5" y1="12" x2="19" y2="12"/>
                            <polyline points="12 5 19 12 12 19"/>
                        </svg>
                    </button>
                </form>
            </div>

            <div class="mt-6 border-t border-slate-100 pt-3 text-[11px] text-slate-400">
                Otoritas tingkat tinggi untuk manajemen site dan pelaporan K3.
            </div>
        </div>

        <!-- Card 2: Tenaga Kerja Lapangan -->
        <div class="relative flex flex-col justify-between overflow-hidden rounded-2xl border border-slate-200/90 bg-white p-6 shadow-xs">
            <!-- Accent Top Stripe -->
            <div class="absolute inset-x-0 top-0 h-1 bg-gradient-to-r from-emerald-500 to-teal-600"></div>

            <div>
                <div class="flex items-center justify-between">
                    <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-emerald-600 text-white shadow-xs">
                        <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                            <circle cx="12" cy="7" r="4"/>
                        </svg>
                    </div>
                    <span class="rounded-full bg-emerald-50 px-2.5 py-1 text-[11px] font-semibold text-emerald-800 ring-1 ring-emerald-600/20">
                        Tenaga Kerja
                    </span>
                </div>

                <h2 class="mt-4 text-lg font-bold text-slate-900">Pekerja Lapangan</h2>
                <p class="mt-1 text-xs leading-relaxed text-slate-500">
                    Masukkan ID pekerja Anda untuk melihat hasil cek tensi hari ini dan menampilkan ID Card digital.
                </p>

                <form method="POST" action="{{ route('masuk.post') }}" class="mt-5 space-y-3.5">
                    @csrf
                    <input type="hidden" name="kind" value="pekerja">
                    <div>
                        <label for="pekerja_id" class="block text-xs font-semibold uppercase tracking-wider text-slate-600">
                            ID Pekerja
                        </label>
                        <div class="relative mt-1">
                            <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">
                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <rect x="2" y="7" width="20" height="14" rx="2" ry="2"/>
                                    <path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/>
                                </svg>
                            </div>
                            <input 
                                id="pekerja_id"
                                name="id" 
                                placeholder="cth: 01-001" 
                                class="w-full rounded-xl border border-slate-300 bg-slate-50/50 py-2.5 pr-3 pl-9 text-sm text-slate-800 transition-all placeholder:text-slate-400 focus:border-emerald-600 focus:bg-white focus:ring-2 focus:ring-emerald-600/10 focus:outline-hidden"
                            >
                        </div>
                    </div>

                    <!-- Demo quick link -->
                    <div class="flex items-center gap-1.5 pt-1 text-[11px] text-slate-500">
                        <span class="text-slate-400">Pintasan ID:</span>
                        <button type="button" onclick="document.getElementById('pekerja_id').value='01-001'" class="rounded-md bg-slate-100 px-1.5 py-0.5 text-slate-700 hover:bg-slate-200">01-001</button>
                    </div>

                    <button class="mt-2 flex w-full items-center justify-center gap-2 rounded-xl bg-emerald-600 py-2.5 text-sm font-semibold text-white shadow-xs transition-all hover:bg-emerald-700 active:scale-[0.98]">
                        <span>Masuk Sebagai Pekerja</span>
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <line x1="5" y1="12" x2="19" y2="12"/>
                            <polyline points="12 5 19 12 12 19"/>
                        </svg>
                    </button>
                </form>

                <div class="relative my-4 flex items-center justify-center">
                    <div class="absolute inset-x-0 border-t border-slate-200"></div>
                    <span class="relative bg-white px-2 text-[11px] font-medium text-slate-400">belum punya akun?</span>
                </div>

                <a href="{{ route('pekerja.daftar') }}" class="flex w-full items-center justify-center gap-2 rounded-xl border border-slate-300 bg-white py-2 text-center text-xs font-semibold text-slate-700 shadow-2xs transition-all hover:border-emerald-500 hover:bg-emerald-50/50 hover:text-emerald-800 active:scale-[0.98]">
                    <svg class="h-4 w-4 text-emerald-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                        <circle cx="8.5" cy="7" r="4"/>
                        <line x1="20" y1="8" x2="20" y2="14"/>
                        <line x1="23" y1="11" x2="17" y2="11"/>
                    </svg>
                    <span>Daftar Pekerja Baru & Terbitkan ID</span>
                </a>
            </div>

            <div class="mt-4 border-t border-slate-100 pt-3 text-[11px] text-slate-400">
                Pendaftaran baru memerlukan foto kamera langsung di site.
            </div>
        </div>
    </div>

    <!-- Security & Compliance Note -->
    <div class="mt-8 rounded-2xl border border-slate-200/80 bg-white/70 p-4 shadow-2xs">
        <div class="flex items-center gap-3">
            <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-lg bg-emerald-100 text-emerald-700">
                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="10"/>
                    <line x1="12" y1="16" x2="12" y2="12"/>
                    <line x1="12" y1="8" x2="12.01" y2="8"/>
                </svg>
            </div>
            <div class="text-xs text-slate-600">
                <span class="font-semibold text-slate-800">Standar Protokol K3:</span> Seluruh tenaga kerja wajib melakukan pemeriksaan tekanan darah (tensi) & suhu tubuh setiap pagi sebelum memasuki area konstruksi.
            </div>
        </div>
    </div>
</div>
@endsection
