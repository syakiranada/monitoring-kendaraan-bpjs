<x-app-layout>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Detail Peminjaman</title>
</head>
<body class="bg-gray-50">
<a href="{{  route('peminjaman', ['page' => request('page'), 'search' => request('search')])  }}" class="flex items-center text-blue-600 font-semibold hover:underline mb-5">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"></path>
            </svg>
            Kembali
    </a>
    <div class="container mx-auto p-4">
        <!-- Judul Halaman -->
        <!-- <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">Detail Peminjaman</h1> -->
        <h1 class="text-2xl font-bold text-gray-900 mb-4">Detail Peminjaman</h1>

        <!-- Grid untuk Kartu Detail -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Kartu Detail Kendaraan -->
            <div class="block p-6 bg-white border border-gray-200 rounded-lg shadow-sm">
                <h5 class="mb-4 text-lg font-bold text-black">
                {{ $peminjaman->kendaraan->merk }} {{ $peminjaman->kendaraan->tipe }} - {{ $peminjaman->kendaraan->plat_nomor }}
                </h5>

                <!-- Grid untuk Detail Kendaraan -->
                <div class="space-y-3">
                    <!-- Mulai Peminjaman -->
                    <div class="flex flex-col sm:flex-row items-start detail-item w-full text-sm">
                        <p class="text-gray-700 sm:w-56 detail-label font-bold">Mulai Peminjaman</p>
                        <p class="text-gray-900 break-words sm:max-w-[calc(100%-14rem)]">
                            {{ \Carbon\Carbon::parse($peminjaman->tgl_mulai)->format('d-m-Y') }} 
                            {{ \Carbon\Carbon::parse($peminjaman->jam_mulai)->format('H.i') }}
                        </p>

                    </div>

                    <!-- Selesai -->
                    <div class="flex flex-col sm:flex-row items-start detail-item w-full text-sm">
                        <p class="text-gray-700 sm:w-56 detail-label font-bold">Selesai Peminjaman</p>
                        <p class="text-gray-900 break-words sm:max-w-[calc(100%-14rem)]">{{ \Carbon\Carbon::parse($peminjaman->tgl_selesai)->format('d-m-Y') }} {{ \Carbon\Carbon::parse($peminjaman->jam_selesai)->format('H.i') }}</p>
                    </div>


                    <!-- Tujuan -->
                    <div class="flex flex-col sm:flex-row items-start detail-item w-full text-sm">
                        <p class="text-gray-700 sm:w-56 detail-label font-bold">Tujuan</p>
                        <p class="text-gray-900 break-words sm:max-w-[calc(100%-14rem)]">{{ $peminjaman->tujuan }}</p>
                    </div>

                    <!-- Waktu pengembalian -->
                    <div class="flex flex-col sm:flex-row items-start detail-item w-full text-sm">
                        <p class="text-gray-700 sm:w-56 detail-label font-bold">Waktu Pengembalian Real</p>
                        <p class="text-gray-900 break-words sm:max-w-[calc(100%-14rem)]">
                            {{ $peminjaman->tgl_kembali_real ? \Carbon\Carbon::parse($peminjaman->tgl_kembali_real)->format('d-m-Y') : '-' }}
                            {{ $peminjaman->jam_kembali_real ? \Carbon\Carbon::parse($peminjaman->jam_kembali_real)->format('H.i') : '' }}
                        </p>
                    </div>

                    <!-- Kondisi -->
                    <div class="flex flex-col sm:flex-row items-start detail-item w-full text-sm">
                        <p class="text-gray-700 sm:w-56 detail-label font-bold">Kondisi Kendaraan</p>
                        <p class="text-gray-900 break-words sm:max-w-[calc(100%-14rem)]">{{ $peminjaman->kondisi_kendaraan ?? '-' }}</p>
                    </div>

                    <!-- Insiden -->
                    <div class="flex flex-col sm:flex-row items-start detail-item w-full text-sm">
                        <p class="text-gray-700 sm:w-56 detail-label font-bold">Detail Insiden</p>
                        <p class="text-gray-900 break-words sm:max-w-[calc(100%-14rem)]">{{ $peminjaman->detail_insiden ?? '-' }}</p>
                    </div>

                    <!-- Status Pinjam -->
                    <div class="flex flex-col sm:flex-row items-start detail-item w-full text-sm">
                        <p class="text-gray-700 sm:w-56 detail-label font-bold">Status Pinjam</p>
                        <p class="text-gray-900 sm:max-w-[calc(100%-14rem)] uppercase">
                            <span class="
                                @if($peminjaman->status_pinjam == 'Menunggu Persetujuan' || $peminjaman->status_pinjam == 'MENUNGGU PERSETUJUAN') bg-blue-100 text-blue-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded-sm  border border-blue-400
                            
                                @elseif($peminjaman->status_pinjam == 'Disetujui' || $peminjaman->status_pinjam == 'DISETUJUI') bg-green-100 text-green-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded-sm border border-green-400
                                
                                @elseif($peminjaman->status_pinjam == 'Dibatalkan' || $peminjaman->status_pinjam == 'DIBATALKAN') bg-gray-100 text-gray-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded-sm border border-gray-500
                                
                                @elseif($peminjaman->status_pinjam == 'Ditolak' || $peminjaman->status_pinjam == 'DITOLAK') bg-red-100 text-red-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded-sm  border border-red-400

                                @elseif($peminjaman->status_pinjam == 'Diperpanjang' || $peminjaman->status_pinjam == 'DIPERPANJANG') bg-yellow-100 text-yellow-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded-sm  border border-yellow-300

                                @elseif($peminjaman->status_pinjam == 'Telah Dikembalikan' || $peminjaman->status_pinjam == 'TELAH DIKEMBALIKAN') bg-gray-100 text-gray-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded-sm border border-gray-500
    
                                @endif">
                                {{ $peminjaman->status_pinjam }}
                            </span>
                        </p>
                    </div>
                </div>
                
            </div>
        </div>
    </div>
</body>
</html>
</x-app-layout>