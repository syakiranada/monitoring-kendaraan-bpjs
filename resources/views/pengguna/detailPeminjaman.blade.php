<x-app-layout>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <title>Detail Peminjaman</title>
</head>
<body class="bg-gray-50 dark:bg-gray-900">
    <div class="container mx-auto p-6">
        <!-- Judul Halaman -->
        <!-- <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">Detail Peminjaman</h1> -->
        <a href="{{  url()->previous()  }}" class="flex items-center text-blue-600 font-semibold hover:underline mb-5">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"></path>
            </svg>
            Back
        </a>

        <!-- Grid untuk Kartu Detail -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Kartu Detail Kendaraan -->
            <div class="block p-6 bg-white border border-gray-200 rounded-lg shadow-sm dark:bg-gray-800 dark:border-gray-700">
                <h5 class="mb-4 text-2xl font-bold text-black dark:text-white">
                {{ $peminjaman->kendaraan->merk }} {{ $peminjaman->kendaraan->tipe }} - {{ $peminjaman->kendaraan->plat_nomor }}
                </h5>

                <!-- Grid untuk Detail Kendaraan -->
                <div class="space-y-3">
                    <!-- Mulai Peminjaman -->
                    <div class="flex justify-between">
                        <p class="font-normal text-gray-700 dark:text-gray-400">Mulai Peminjaman</p>
                        <p class="font-normal text-gray-900 dark:text-white text-right">
                            {{ \Carbon\Carbon::parse($peminjaman->tgl_mulai)->format('d-m-Y') }} 
                            {{ \Carbon\Carbon::parse($peminjaman->jam_mulai)->format('H.i') }}
                        </p>

                    </div>

                    <!-- Selesai -->
                    <div class="flex justify-between">
                        <p class="font-normal text-gray-700 dark:text-gray-400">Selesai Peminjaman</p>
                        <p class="font-normal text-gray-900 dark:text-white text-right">{{ \Carbon\Carbon::parse($peminjaman->tgl_selesai)->format('d-m-Y') }} {{ \Carbon\Carbon::parse($peminjaman->jam_selesai)->format('H.i') }}</p>
                    </div>


                    <!-- Tujuan -->
                    <div class="flex justify-between">
                        <p class="font-normal text-gray-700 dark:text-gray-400">Tujuan</p>
                        <p class="font-normal text-gray-900 dark:text-white text-right">{{ $peminjaman->tujuan }}</p>
                    </div>

                    <!-- Waktu pengembalian -->
                    <div class="flex justify-between">
                        <p class="font-normal text-gray-700 dark:text-gray-400">Waktu Pengembalian Real</p>
                        <p class="font-normal text-gray-900 dark:text-white text-right">
                            {{ $peminjaman->tgl_kembali_real ? \Carbon\Carbon::parse($peminjaman->tgl_kembali_real)->format('d-m-Y') : '-' }}
                            {{ $peminjaman->jam_kembali_real ? \Carbon\Carbon::parse($peminjaman->jam_kembali_real)->format('H.i') : '' }}
                        </p>
                    </div>

                    <!-- Kondisi -->
                    <div class="flex justify-between">
                        <p class="font-normal text-gray-700 dark:text-gray-400">Kondisi Kendaraan</p>
                        <p class="font-normal text-gray-900 dark:text-white text-right">{{ $peminjaman->kondisi_kendaraan ?? '-' }}</p>
                    </div>

                    <!-- Insiden -->
                    <div class="flex justify-between">
                        <p class="font-normal text-gray-700 dark:text-gray-400">Detail Insiden</p>
                        <p class="font-normal text-gray-900 dark:text-white text-right">{{ $peminjaman->detail_insiden ?? '-' }}</p>
                    </div>

                    <!-- Status Pinjam -->
                    <div class="flex justify-between">
                        <p class="font-normal text-gray-700 dark:text-gray-400">Status Pinjam</p>
                        <p class="font-normal text-gray-900 dark:text-white text-right uppercase">
                            <span class="
                                @if($peminjaman->status_pinjam == 'Menunggu Persetujuan') bg-blue-100 text-blue-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded-sm dark:bg-gray-700 dark:text-blue-400 border border-blue-400
                            
                                @elseif($peminjaman->status_pinjam == 'Disetujui') bg-green-100 text-green-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded-sm dark:bg-gray-700 dark:text-green-400 border border-green-400
                                
                                @elseif($peminjaman->status_pinjam == 'Dibatalkan') bg-red-100 text-red-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded-sm dark:bg-gray-700 dark:text-red-400 border border-red-400
                                
                                @elseif($peminjaman->status_pinjam == 'Ditolak') bg-red-100 text-red-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded-sm dark:bg-gray-700 dark:text-red-400 border border-red-400

                                @elseif($peminjaman->status_pinjam == 'Diperpanjang') bg-yellow-100 text-yellow-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded-sm dark:bg-gray-700 dark:text-yellow-300 border border-yellow-300

                                @elseif($peminjaman->status_pinjam == 'Telah Dikembalikan') bg-gray-100 text-gray-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded-sm dark:bg-gray-700 dark:text-gray-400 border border-gray-500
    
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