<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>ID Card Resmi K3 — {{ $worker->id }} ({{ $worker->nama }})</title>
    @fonts
    @vite(['resources/css/app.css'])
    <style>
        @media print {
            body {
                background: #ffffff !important;
                padding: 0 !important;
            }
            .no-print {
                display: none !important;
            }
            .print-card-wrapper {
                box-shadow: none !important;
                margin: 0 auto !important;
                page-break-inside: avoid;
            }
        }
    </style>
</head>
<body class="min-h-screen bg-slate-100 font-sans py-8 px-4 text-slate-800 antialiased">

<div class="mx-auto max-w-sm">
    <!-- Top Action Bar (Screen Only) -->
    <div class="no-print mb-4 flex items-center justify-between">
        <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-slate-600 hover:text-slate-900">
            <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
            <span>Kembali</span>
        </a>
        <div class="flex items-center gap-2">
            <button onclick="window.print()" class="inline-flex items-center gap-1.5 rounded-xl bg-slate-900 px-3.5 py-1.5 text-xs font-semibold text-white shadow-xs transition-all hover:bg-slate-800 active:scale-[0.98]">
                <svg class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <polyline points="6 9 6 2 18 2 18 9"/>
                    <path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/>
                    <rect x="6" y="14" width="12" height="8"/>
                </svg>
                <span>Cetak Kartu</span>
            </button>
        </div>
    </div>

    <!-- Official Physical Badge Card -->
    <div class="print-card-wrapper relative overflow-hidden rounded-2xl border-2 border-slate-300 bg-white shadow-lg">
        <!-- Lanyard Slot Punch Cutout -->
        <div class="mx-auto mt-2 h-2.5 w-14 rounded-full bg-slate-200 ring-1 ring-slate-300/80"></div>

        <!-- Header Ribbon: Official K3 Green -->
        <div class="mt-2 bg-gradient-to-r from-emerald-800 via-emerald-700 to-teal-800 px-4 py-3 text-white">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <div class="flex h-7 w-7 items-center justify-center rounded-lg bg-white text-emerald-800 shadow-2xs">
                        <svg class="h-4 w-4" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M12 2L3.5 5.5V11C3.5 16.5 7.1 21.6 12 23C16.9 21.6 20.5 16.5 20.5 11V5.5L12 2ZM17 12.5H13.5V16H10.5V12.5H7V9.5H10.5V6H13.5V9.5H17V12.5Z"/>
                        </svg>
                    </div>
                    <div>
                        <div class="text-[11px] font-black tracking-wider uppercase">HSE PANTAU • K3 LAPANGAN</div>
                        <div class="text-[9px] font-medium text-emerald-100">KARTU IDENTITAS KESELAMATAN KERJA</div>
                    </div>
                </div>
                <div class="rounded-md bg-emerald-950/40 px-2 py-0.5 text-right text-[10px] font-bold text-emerald-200 ring-1 ring-emerald-400/30">
                    SITE {{ $worker->site_code }}
                </div>
            </div>
        </div>

        <!-- Hazard Sub-stripe -->
        <div class="h-1 bg-amber-400"></div>

        <!-- Card Body -->
        <div class="p-5">
            <div class="flex gap-4">
                <!-- Photo with Holographic/Official Stamp -->
                <div class="relative shrink-0">
                    @if($worker->foto_url)
                        <img src="{{ $worker->foto_url }}" alt="Foto {{ $worker->nama }}" class="h-28 w-24 rounded-xl object-cover ring-2 ring-slate-200 shadow-2xs">
                    @else
                        <div class="flex h-28 w-24 flex-col items-center justify-center rounded-xl bg-gradient-to-br from-slate-800 to-slate-950 text-white shadow-2xs ring-2 ring-slate-200">
                            <span class="text-3xl font-black">{{ strtoupper(substr($worker->nama, 0, 1)) }}</span>
                            <span class="mt-1 text-[9px] font-semibold text-slate-400">TERDAFTAR</span>
                        </div>
                    @endif
                    <div class="absolute -bottom-1.5 -right-1.5 rounded-full bg-emerald-600 px-1.5 py-0.5 text-[8px] font-bold text-white shadow-2xs ring-1 ring-white">
                        K3 PASS
                    </div>
                </div>

                <!-- Worker Identity Details -->
                <div class="flex-1 space-y-1.5">
                    <div>
                        <div class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Nama Tenaga Kerja</div>
                        <div class="text-sm font-extrabold text-slate-900 leading-tight">{{ $worker->nama }}</div>
                    </div>

                    <div>
                        <div class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Jenis Pekerjaan</div>
                        <div class="inline-block rounded-md bg-emerald-50 px-2 py-0.5 text-[11px] font-bold text-emerald-800 ring-1 ring-emerald-600/20">
                            {{ $worker->jenis_pekerjaan }}
                        </div>
                    </div>

                    <div>
                        <div class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Mandor / Subkon</div>
                        <div class="text-xs font-semibold text-slate-700 leading-tight">{{ $worker->mandor_subkon }}</div>
                    </div>

                    <div class="pt-0.5 text-[10px] text-slate-500">
                        Usia: <b class="text-slate-800">{{ $worker->usia }} thn</b> • Asal: <b class="text-slate-800">{{ $worker->asal }}</b>
                    </div>
                </div>
            </div>

            <!-- ID Number & Barcode / QR Section -->
            <div class="mt-5 rounded-xl border border-slate-200 bg-slate-50/80 p-3 text-center shadow-inner">
                <div class="text-[10px] font-bold tracking-widest text-slate-400 uppercase">NOMOR INDUK PEKERJA (ID)</div>
                <div class="mt-0.5 text-2xl font-black tracking-widest text-slate-950">{{ $worker->id }}</div>
                
                <!-- Clean Dynamic SVG Barcode / QR Code Presentation -->
                <div class="mt-3 flex items-center justify-center gap-3">
                    <!-- SVG Barcode lines -->
                    <div class="flex h-10 items-center justify-center gap-0.5 px-2 bg-white rounded-lg border border-slate-200/80">
                        <span class="inline-block h-8 w-1 bg-slate-900"></span>
                        <span class="inline-block h-8 w-0.5 bg-slate-900"></span>
                        <span class="inline-block h-8 w-1.5 bg-slate-900"></span>
                        <span class="inline-block h-8 w-0.5 bg-slate-900"></span>
                        <span class="inline-block h-8 w-1 bg-slate-900"></span>
                        <span class="inline-block h-8 w-2 bg-slate-900"></span>
                        <span class="inline-block h-8 w-0.5 bg-slate-900"></span>
                        <span class="inline-block h-8 w-1 bg-slate-900"></span>
                        <span class="inline-block h-8 w-1.5 bg-slate-900"></span>
                        <span class="inline-block h-8 w-0.5 bg-slate-900"></span>
                        <span class="inline-block h-8 w-1 bg-slate-900"></span>
                        <span class="inline-block h-8 w-0.5 bg-slate-900"></span>
                        <span class="inline-block h-8 w-2 bg-slate-900"></span>
                        <span class="inline-block h-8 w-1 bg-slate-900"></span>
                    </div>

                    <!-- SVG QR Code Icon Box -->
                    <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg border border-slate-200/80 bg-white p-1">
                        <svg viewBox="0 0 24 24" class="h-8 w-8 text-slate-900" fill="currentColor">
                            <path d="M2 2h8v8H2V2zm2 2v4h4V4H4zm10-2h8v8h-8V2zm2 2v4h4V4h-4zM2 14h8v8H2v-8zm2 2v4h4v-4H4zm14 0h4v2h-4v-2zm-4 0h2v4h-2v-4zm2 4h4v2h-4v-2zm-2 2h2v2h-2v-2zm4-4h2v2h-2v-2zm0 4h2v2h-2v-2zm-6-2h2v2h-2v-2zM5 5h2v2H5V5zm12 0h2v2h-2V5zM5 17h2v2H5v-2z"/>
                        </svg>
                    </div>
                </div>

                <div class="mt-2 text-[10px] font-medium text-slate-500">
                    Scan ID atau tunjukkan saat pemeriksaan tensi pagi hari di pos HSE.
                </div>
            </div>

            <!-- Validity & Project Metadata -->
            <div class="mt-4 flex items-center justify-between border-t border-slate-100 pt-3 text-[10px] text-slate-500">
                <div>
                    <div>Penempatan: <b class="text-slate-700">{{ $worker->site->name }}</b></div>
                    <div>Tgl Registrasi: <b class="text-slate-700">{{ $worker->tanggal_regis->format('d/m/Y') }}</b></div>
                </div>
                <div class="text-right">
                    <div class="font-semibold text-emerald-700">STATUS: TERVERIFIKASI</div>
                    <div>Kemenaker K3 Compliant</div>
                </div>
            </div>
        </div>

        <!-- Card Footer Ribbon -->
        <div class="bg-slate-900 px-4 py-2 text-center text-[10px] font-bold tracking-widest text-emerald-400 uppercase">
            ★ UTAMAKAN KESELAMATAN & KESEHATAN KERJA ★
        </div>
    </div>

    <!-- Screen Only Print Notice -->
    <div class="no-print mt-4 text-center">
        <p class="text-[11px] text-slate-500">
            Gunakan tombol <b>Cetak Kartu</b> di atas untuk mencetak ID Card (format otomatis disesuaikan).
        </p>
    </div>
</div>

</body>
</html>
