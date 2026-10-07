@extends('layouts.app')

@section('title', 'Buat Akun Inspector — HSE Pantau')

@section('content')
<div class="mx-auto max-w-xl py-2">
    <!-- Header -->
    <div class="mb-6">
        <h1 class="text-2xl font-extrabold tracking-tight text-slate-900 sm:text-3xl">Buat Akun Inspector</h1>
        <p class="mt-1 text-xs text-slate-500">
            Terbitkan otorisasi akun pengawas K3 baru untuk penempatan wilayah kabupaten/site.
        </p>
    </div>

    <!-- Form Card -->
    <div class="rounded-2xl border border-slate-200/90 bg-white p-6 shadow-2xs sm:p-8">
        <form method="POST" action="{{ route('inspector.store') }}" class="space-y-5">
            @csrf

            <div>
                <label for="ins_id" class="block text-xs font-semibold uppercase tracking-wider text-slate-700">
                    Nomor ID Inspector <span class="text-rose-500">*</span>
                </label>
                <div class="relative mt-1">
                    <input 
                        id="ins_id"
                        name="id" 
                        value="{{ old('id') }}" 
                        placeholder="cth: INS-02-002" 
                        class="w-full rounded-xl border border-slate-300 bg-slate-50/50 py-2.5 px-3 text-xs font-bold text-slate-900 transition-all placeholder:text-slate-400 focus:border-slate-900 focus:bg-white focus:outline-hidden"
                    >
                </div>
                <p class="mt-1.5 text-[11px] text-slate-400">
                    Format baku: <code class="text-slate-700">INS-[kodeSite]-[nomor]</code>, contoh <code class="text-slate-700">INS-02-002</code>
                </p>
            </div>

            <div>
                <label class="block text-xs font-semibold uppercase tracking-wider text-slate-700">
                    Wilayah Site Penugasan <span class="text-rose-500">*</span>
                </label>
                <div class="relative mt-1">
                    <select name="site_code" class="w-full appearance-none rounded-xl border border-slate-300 bg-slate-50/50 py-2.5 pr-8 pl-3 text-xs font-semibold text-slate-800 transition-all focus:border-slate-900 focus:bg-white focus:outline-hidden">
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
                <label class="block text-xs font-semibold uppercase tracking-wider text-slate-700">
                    Nama Lengkap Petugas <span class="text-rose-500">*</span>
                </label>
                <input 
                    name="name" 
                    value="{{ old('name') }}" 
                    placeholder="cth: David Prasetyo, S.T."
                    class="mt-1 w-full rounded-xl border border-slate-300 bg-slate-50/50 py-2.5 px-3 text-xs text-slate-800 transition-all placeholder:text-slate-400 focus:border-slate-900 focus:bg-white focus:outline-hidden"
                >
            </div>

            <div class="pt-2">
                <button class="flex w-full items-center justify-center gap-2 rounded-xl bg-slate-900 py-3 text-sm font-semibold text-white shadow-md transition-all hover:bg-slate-800 active:scale-[0.98]">
                    <span>Terbitkan Akun Inspector</span>
                    <svg class="h-4 w-4 text-emerald-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polyline points="20 6 9 17 4 12"/>
                    </svg>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
