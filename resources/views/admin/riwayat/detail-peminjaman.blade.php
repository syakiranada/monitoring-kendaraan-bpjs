<x-app-layout>
    <div class="p-6">
        {{-- @php 
            $currentPage = request()->query('page');
        @endphp 
        <input type="hidden" name="current_page" value="{{ $currentPage }}"> --}}
        <input type="hidden" name="page" value="{{ request()->query('page', 1) }}">
        <input type="hidden" name="search" value="{{ request()->query('search') }}">

        {{-- <div class="mb-4 flex space-x-2">
            <a href="{{ route('admin.riwayat.peminjaman', ['page' => request('page'), 'search' => request('search')]) }}" class="flex items-center px-5 py-2.5 text-gray-900 bg-white border border-gray-300 rounded-lg text-sm hover:bg-gray-100">
                <svg class="w-4 h-4 text-gray-800 mr-2" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m15 19-7-7 7-7"/>
                </svg>              
                Kembali
            </a> 
        </div> --}}
        <!-- Button Back -->
        <a href="{{  route('admin.riwayat.peminjaman', ['page' => request('page'), 'search' => request('search')])  }}" class="flex items-center text-blue-600 font-semibold hover:underline mb-5">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"></path>
            </svg>
            Kembali
        </a> 
        <h2 class="text-2xl font-bold mb-4">Detail Peminjaman Kendaraan</h2>  

        {{-- <div class="grid grid-cols-2 gap-6"> --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Informasi Peminjaman -->
            <div class="block p-6 bg-white border border-gray-200 rounded-lg shadow-sm">
                <h3 class="mb-2 text-xl font-bold tracking-tight text-gray-900">Informasi Peminjaman</h3>
                {{-- <div class="grid grid-cols-2 gap-y-2 text-gray-700"> --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-y-2 text-gray-700">
                    <p class="font-semibold">Nama Peminjam</p>
                    <p class="ml-4">{{ $peminjaman->user->name }}</p>
                    
                    <p class="font-semibold">Tujuan</p>
                    <p class="ml-4">{{ $peminjaman->tujuan }}</p>
                    
                    <p class="font-semibold">Tanggal & Jam Mulai</p>
                    <p class="ml-4">{{ $peminjaman->tgl_mulai }} - {{ $peminjaman->jam_mulai }}</p>
                    
                    <p class="font-semibold">Tanggal & Jam Selesai</p>
                    <p class="ml-4">{{ $peminjaman->tgl_selesai }} - {{ $peminjaman->jam_selesai }}</p>
                    
                    <p class="font-semibold">Status Peminjaman</p>
                    <p class="ml-4 whitespace-nowrap uppercase">
                        <span class="text-xs font-medium me-2 px-2.5 py-0.5 rounded-sm
                            {{ 
                                $peminjaman->status_pinjam == 'Disetujui' ? 'bg-green-100 text-green-800 border border-green-400' : 
                                ($peminjaman->status_pinjam == 'Menunggu Persetujuan' ? 'bg-blue-100 text-blue-800 border border-blue-400' : 
                                ($peminjaman->status_pinjam == 'Diperpanjang' ? 'bg-yellow-100 text-yellow-800 border border-yellow-300' : 
                                ($peminjaman->status_pinjam == 'Telah Dikembalikan' ? 'bg-gray-100 text-gray-800 border border-gray-500' : 
                                ($peminjaman->status_pinjam == 'Ditolak' ? 'bg-red-100 text-red-800 border border-red-400' : 
                                ($peminjaman->status_pinjam == 'Dibatalkan' ? 'bg-gray-100 text-gray-800 border border-gray-500' : ''))))) }}">
                            {{ ucfirst($peminjaman->status_pinjam) }}
                        </span>
                    </p>
                </div>
            </div>

            <!-- Informasi Kendaraan -->
            <div class="block p-6 bg-white border border-gray-200 rounded-lg shadow-sm">
                <h3 class="mb-2 text-xl font-bold tracking-tight text-gray-900">Informasi Kendaraan</h3>
                {{-- <div class="grid grid-cols-2 gap-y-2 text-gray-700"> --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-y-2 text-gray-700">
                    <p class="font-semibold">Merek & Tipe</p>
                    <p class="ml-4">{{ $peminjaman->kendaraan->merk }} {{ $peminjaman->kendaraan->tipe }}</p>
                    
                    <p class="font-semibold">Plat Nomor</p>
                    <p class="ml-4">{{ $peminjaman->kendaraan->plat_nomor }}</p>
                    
                    <p class="font-semibold">Warna</p>
                    <p class="ml-4">{{ $peminjaman->kendaraan->warna }}</p>
                </div>
            </div>
        </div>

        @if ($peminjaman->status_pinjam == 'Telah Dikembalikan')
            <!-- Card Pengembalian Kendaraan -->
            <div class="block p-6 mt-6 bg-white border border-gray-200 rounded-lg shadow-sm">
                <h3 class="mb-2 text-xl font-bold tracking-tight text-gray-900">Pengembalian Kendaraan</h3>
                {{-- <div class="grid grid-cols-2 gap-y-2 text-gray-700"> --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-y-2 text-gray-700">
                    <p class="font-semibold">Tanggal Kembali Real</p>
                    <p class="ml-4">{{ $peminjaman->tgl_kembali_real ?? '-' }}</p>

                    <p class="font-semibold">Jam Kembali Real</p>
                    <p class="ml-4">{{ $peminjaman->jam_kembali_real ?? '-' }}</p>

                    <p class="font-semibold">Kondisi Kendaraan</p>
                    <p class="ml-4">{{ $peminjaman->kondisi_kendaraan ?? '-' }}</p>

                    <p class="font-semibold">Detail Insiden</p>
                    <p class="ml-4">{{ $peminjaman->detail_insiden ?? '-' }}</p>
                </div>
            </div>
        @endif
    </div>
</x-app-layout>