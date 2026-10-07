@extends('layouts.app')

@section('title', 'Registrasi Pekerja Baru — HSE Pantau')

@section('content')
<div class="mx-auto max-w-2xl py-2">
    <!-- Header -->
    <div class="mb-6 text-center sm:text-left">
        <h1 class="text-2xl font-extrabold tracking-tight text-slate-900 sm:text-3xl">Registrasi Tenaga Kerja</h1>
        <p class="mt-1 text-xs text-slate-500">
            Lengkapi data diri tenaga kerja lapangan. Nomor ID resmi akan diterbitkan otomatis berdasarkan kode site.
        </p>
    </div>

    <!-- Registration Card -->
    <div class="rounded-2xl border border-slate-200/90 bg-white p-6 shadow-2xs sm:p-8">
        <form method="POST" action="{{ route('pekerja.daftar.post') }}" enctype="multipart/form-data" class="space-y-6">
            @csrf

            <!-- Section 1: Lokasi & Penempatan -->
            <div>
                <div class="flex items-center gap-2 border-b border-slate-100 pb-2 text-xs font-bold uppercase tracking-wider text-slate-700">
                    <svg class="h-4 w-4 text-emerald-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/>
                        <circle cx="12" cy="10" r="3"/>
                    </svg>
                    <span>1. Wilayah Penempatan Proyek</span>
                </div>

                <div class="mt-3.5 grid gap-4 sm:grid-cols-2">
                    <div>
                        <label for="site_select" class="block text-xs font-semibold text-slate-700">
                            Lokasi Site Proyek <span class="text-rose-500">*</span>
                        </label>
                        <div class="relative mt-1">
                            <select name="site_code" id="site_select" class="w-full appearance-none rounded-xl border border-slate-300 bg-slate-50/50 py-2.5 pr-8 pl-3 text-xs font-semibold text-slate-800 transition-all focus:border-slate-900 focus:bg-white focus:outline-hidden">
                                @foreach($sites as $s)
                                    <option value="{{ $s->code }}" @selected(old('site_code') === $s->code)>{{ $s->code }} — {{ $s->name }}</option>
                                @endforeach
                            </select>
                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-2.5 text-slate-400">
                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"/></svg>
                            </div>
                        </div>
                    </div>

                    <div>
                        <label for="mandor_select" class="block text-xs font-semibold text-slate-700">
                            Mandor / Subkontraktor <span class="text-rose-500">*</span>
                        </label>
                        <div class="relative mt-1">
                            <select name="mandor_subkon" id="mandor_select" class="w-full appearance-none rounded-xl border border-slate-300 bg-slate-50/50 py-2.5 pr-8 pl-3 text-xs font-semibold text-slate-800 transition-all focus:border-slate-900 focus:bg-white focus:outline-hidden"></select>
                            <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-2.5 text-slate-400">
                                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"/></svg>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Section 2: Identitas Tenaga Kerja -->
            <div>
                <div class="flex items-center gap-2 border-b border-slate-100 pb-2 text-xs font-bold uppercase tracking-wider text-slate-700">
                    <svg class="h-4 w-4 text-emerald-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                        <circle cx="12" cy="7" r="4"/>
                    </svg>
                    <span>2. Identitas Diri Pekerja</span>
                </div>

                <div class="mt-3.5 space-y-3.5">
                    <div class="grid gap-4 sm:grid-cols-2">
                        <div>
                            <label class="block text-xs font-semibold text-slate-700">
                                Nama Lengkap <span class="text-rose-500">*</span>
                            </label>
                            <input 
                                name="nama" 
                                value="{{ old('nama') }}" 
                                placeholder="cth: Bambang Sutrisno"
                                class="mt-1 w-full rounded-xl border border-slate-300 bg-slate-50/50 px-3 py-2.5 text-xs text-slate-800 transition-all placeholder:text-slate-400 focus:border-slate-900 focus:bg-white focus:outline-hidden"
                            >
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-700">
                                Jenis Pekerjaan / Keahlian <span class="text-rose-500">*</span>
                            </label>
                            <input 
                                name="jenis_pekerjaan" 
                                value="{{ old('jenis_pekerjaan') }}" 
                                placeholder="cth: Tukang Las, Besi, Batu, dsb."
                                class="mt-1 w-full rounded-xl border border-slate-300 bg-slate-50/50 px-3 py-2.5 text-xs text-slate-800 transition-all placeholder:text-slate-400 focus:border-slate-900 focus:bg-white focus:outline-hidden"
                            >
                        </div>
                    </div>

                    <div class="grid gap-4 sm:grid-cols-2">
                        <div>
                            <label class="block text-xs font-semibold text-slate-700">
                                Usia (17 – 65 Tahun) <span class="text-rose-500">*</span>
                            </label>
                            <input 
                                type="number" 
                                name="usia" 
                                value="{{ old('usia') }}" 
                                min="17" 
                                max="65" 
                                placeholder="cth: 28"
                                class="mt-1 w-full rounded-xl border border-slate-300 bg-slate-50/50 px-3 py-2.5 text-xs text-slate-800 transition-all placeholder:text-slate-400 focus:border-slate-900 focus:bg-white focus:outline-hidden"
                            >
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-700">
                                Kota / Daerah Asal <span class="text-rose-500">*</span>
                            </label>
                            <input 
                                name="asal" 
                                value="{{ old('asal') }}" 
                                placeholder="cth: Jember, Jawa Timur"
                                class="mt-1 w-full rounded-xl border border-slate-300 bg-slate-50/50 px-3 py-2.5 text-xs text-slate-800 transition-all placeholder:text-slate-400 focus:border-slate-900 focus:bg-white focus:outline-hidden"
                            >
                        </div>
                    </div>
                </div>
            </div>

            <!-- Section 3: Foto Lapangan (Kamera) -->
            <div>
                <div class="flex items-center gap-2 border-b border-slate-100 pb-2 text-xs font-bold uppercase tracking-wider text-slate-700">
                    <svg class="h-4 w-4 text-emerald-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"/>
                        <circle cx="12" cy="13" r="4"/>
                    </svg>
                    <span>3. Dokumentasi Wajah Lapangan <span class="text-rose-500">*</span></span>
                </div>

                <div class="mt-3.5 rounded-xl border border-dashed border-slate-300 bg-slate-50/60 p-4 transition-all hover:border-slate-400">
                    <div class="flex flex-wrap items-center gap-4">
                        <img id="foto_prev" alt="preview foto" class="h-20 w-20 rounded-xl object-cover ring-2 ring-emerald-500/30" style="display:none">
                        
                        <div class="flex-1">
                            <button type="button" onclick="document.getElementById('foto_kamera').click()" class="inline-flex items-center gap-2 rounded-xl bg-slate-900 px-4 py-2.5 text-xs font-semibold text-white shadow-2xs transition-all hover:bg-slate-800 active:scale-[0.98]">
                                <svg class="h-4 w-4 text-emerald-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"/>
                                    <circle cx="12" cy="13" r="4"/>
                                </svg>
                                <span>Ambil Foto Kamera / Pilih File</span>
                            </button>
                            <div id="foto_nama" class="mt-1.5 text-[11px] font-medium text-slate-500">
                                Belum ada foto diambil (format JPG/PNG, maksimal 2MB).
                            </div>
                        </div>
                    </div>
                    <input type="file" id="foto_kamera" name="foto" accept="image/*" capture="environment" style="display:none">
                </div>
            </div>

            <!-- Section 4: Riwayat Kesehatan Awal -->
            <div>
                <div class="flex items-center gap-2 border-b border-slate-100 pb-2 text-xs font-bold uppercase tracking-wider text-slate-700">
                    <svg class="h-4 w-4 text-emerald-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M22 12h-4l-3 9L9 3l-3 9H2"/>
                    </svg>
                    <span>4. Riwayat Kesehatan Awal</span>
                </div>

                <div class="mt-3.5">
                    <label class="block text-xs font-semibold text-slate-700">
                        Catatan Riwayat Penyakit / Komorbid <span class="text-slate-400 font-normal">(Kosongkan jika sehat & prima)</span>
                    </label>
                    <textarea 
                        name="riwayat_penyakit" 
                        rows="2" 
                        placeholder="cth: Riwayat darah tinggi, alergi debu, asma, dsb."
                        class="mt-1 w-full rounded-xl border border-slate-300 bg-slate-50/50 px-3 py-2.5 text-xs text-slate-800 transition-all placeholder:text-slate-400 focus:border-slate-900 focus:bg-white focus:outline-hidden"
                    >{{ old('riwayat_penyakit') }}</textarea>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="pt-2">
                <button class="flex w-full items-center justify-center gap-2 rounded-xl bg-slate-900 py-3 text-sm font-semibold text-white shadow-md transition-all hover:bg-slate-800 active:scale-[0.98]">
                    <span>Simpan & Terbitkan ID Card Pekerja</span>
                    <svg class="h-4 w-4 text-emerald-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <line x1="5" y1="12" x2="19" y2="12"/>
                        <polyline points="12 5 19 12 12 19"/>
                    </svg>
                </button>
                <p class="mt-2 text-center text-[11px] text-slate-400">
                    Setelah berhasil disimpan, Anda akan langsung dialihkan ke ID Card digital pekerja.
                </p>
            </div>
        </form>
    </div>
</div>

<script>
const mandorPerSite = @json($mandorPerSite);
const siteSel = document.getElementById('site_select');
const mandorSel = document.getElementById('mandor_select');
const oldMandor = @json(old('mandor_subkon'));

function isiMandor() {
    const list = mandorPerSite[siteSel.value] ?? [];
    mandorSel.innerHTML = '';
    list.forEach(function (nama) {
        const o = document.createElement('option');
        o.value = nama;
        o.textContent = nama;
        if (nama === oldMandor) o.selected = true;
        mandorSel.appendChild(o);
    });
}

siteSel.addEventListener('change', isiMandor);
isiMandor();

const fotoKamera = document.getElementById('foto_kamera');
const fotoPrev = document.getElementById('foto_prev');
const fotoNama = document.getElementById('foto_nama');

fotoKamera.addEventListener('change', function () {
    if (!this.files.length) return;
    fotoNama.textContent = 'File terpilih: ' + this.files[0].name;
    fotoNama.classList.add('text-emerald-700', 'font-semibold');
    fotoPrev.src = URL.createObjectURL(this.files[0]);
    fotoPrev.style.display = 'block';
});
</script>
@endsection
