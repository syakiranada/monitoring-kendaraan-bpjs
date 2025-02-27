{{-- @extends('layouts.sidebar')

@section('content') --}}

<x-app-layout>
    <div class="p-6">
        <input type="hidden" name="page" value="{{ request()->query('page', 1) }}">
        <input type="hidden" name="search" value="{{ request()->query('search') }}">

        <div class="mb-4 flex space-x-2">
            <a href="{{ route('admin.pengajuan-peminjaman.index', ['page' => request('page'), 'search' => request('search')]) }}" class="flex items-center px-5 py-2.5 text-gray-900 bg-white border border-gray-300 rounded-full text-sm hover:bg-gray-100">
                <svg class="w-4 h-4 text-gray-800 mr-2" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m15 19-7-7 7-7"/>
                </svg>              
                Kembali
            </a>
        </div> 
        <h2 class="text-2xl font-bold mb-4">Detail Peminjaman Kendaraan</h2>

        <div class="grid grid-cols-2 gap-6">
            <!-- Informasi Peminjaman -->
            <div class="block p-6 bg-white border border-gray-200 rounded-lg shadow-sm">
                <h3 class="mb-2 text-xl font-bold tracking-tight text-gray-900">Informasi Peminjaman</h3>
                <div class="grid grid-cols-2 gap-y-2 text-gray-700">
                    <p class="font-semibold">Nama Peminjam</p>
                    <p class="ml-4">{{ $peminjaman->user->name }}</p>
                    
                    <p class="font-semibold">Tujuan</p>
                    <p class="ml-4">{{ $peminjaman->tujuan }}</p>
                    
                    <p class="font-semibold">Tanggal & Jam Mulai</p>
                    <p class="ml-4">{{ $peminjaman->tgl_mulai ? \Carbon\Carbon::parse($peminjaman->tgl_mulai)->format('d-m-Y') : '-'}} - {{ $peminjaman->jam_mulai }}</p>
                    
                    <p class="font-semibold">Tanggal & Jam Selesai</p>
                    <p class="ml-4">{{ $peminjaman->tgl_selesai ? \Carbon\Carbon::parse($peminjaman->tgl_selesai)->format('d-m-Y') : '-' }} - {{ $peminjaman->jam_selesai }}</p>
                    
                    <p class="font-semibold">Status Peminjaman</p>
                    <p class="ml-4">
                        <span class="px-2 py-1 text-white text-sm rounded 
                            {{ $peminjaman->status_pinjam == 'Disetujui' ? 'bg-green-500' : 'bg-yellow-500' }}">
                            {{ ucfirst($peminjaman->status_pinjam) }}
                        </span>
                    </p>
                </div>
            </div>

            <!-- Informasi Kendaraan -->
            <div class="block p-6 bg-white border border-gray-200 rounded-lg shadow-sm">
                <h3 class="mb-2 text-xl font-bold tracking-tight text-gray-900">Informasi Kendaraan</h3>
                <div class="grid grid-cols-2 gap-y-2 text-gray-700">
                    <p class="font-semibold">Merek & Tipe</p>
                    <p class="ml-4">{{ $peminjaman->kendaraan->merk }} {{ $peminjaman->kendaraan->tipe }}</p>
                    
                    <p class="font-semibold">Plat Nomor</p>
                    <p class="ml-4">{{ $peminjaman->kendaraan->plat_nomor }}</p>
                    
                    <p class="font-semibold">Warna</p>
                    <p class="ml-4">{{ $peminjaman->kendaraan->warna }}</p>
                </div>
            </div>
        </div>

        {{-- @if ($peminjaman->tgl_kembali_real)
            <!-- Card Pengembalian Kendaraan -->
            <a href="#" class="block p-6 mt-6 bg-white border border-gray-200 rounded-lg shadow-sm hover:bg-gray-100 dark:bg-gray-800 dark:border-gray-700 dark:hover:bg-gray-700">
                <h3 class="mb-2 text-xl font-bold tracking-tight text-gray-900 dark:text-white">Pengembalian Kendaraan</h3>
                <div class="grid grid-cols-2 gap-y-2 text-gray-700 dark:text-gray-400">
                    <p class="font-semibold">Tanggal Kembali Real</p>
                    <p class="ml-4">{{ $peminjaman->tgl_kembali_real }}</p>

                    <p class="font-semibold">Jam Kembali Real</p>
                    <p class="ml-4">{{ $peminjaman->jam_kembali_real }}</p>

                    <p class="font-semibold">Kondisi Kendaraan</p>
                    <p class="ml-4">{{ $peminjaman->kondisi_kendaraan }}</p>

                    <p class="font-semibold">Detail Insiden</p>
                    <p class="ml-4">{{ $peminjaman->detail_insiden ?? '-' }}</p>
                </div>
            </a>
        @endif --}}
    </div>
</x-app-layout>
{{-- @endsection --}}