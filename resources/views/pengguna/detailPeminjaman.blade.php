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
        <h1 class="text-xl font-semibold text-gray-900 dark:text-white mb-6">Detail Peminjaman</h1>

        <!-- Grid untuk Kartu Detail -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Kartu Detail Kendaraan -->
            <div class="block p-6 bg-white border border-gray-200 rounded-lg shadow-sm dark:bg-gray-800 dark:border-gray-700">
            <a href="{{ route('peminjaman') }}" class="bg-blue-500 text-white px-6 py-2 rounded-lg ">Kembali</a>
                <h5 class="mb-4 mt-4 text-2xl font-bold tracking-tight text-gray-900 dark:text-white">
                {{ $peminjaman->kendaraan->merk }} {{ $peminjaman->kendaraan->tipe }} - {{ $peminjaman->kendaraan->plat_nomor }}
                </h5>

                <!-- Grid untuk Detail Kendaraan -->
                <div class="space-y-3">
                    <!-- Mulai Peminjaman -->
                    <div class="flex justify-between">
                        <p class="font-normal text-gray-700 dark:text-gray-400">Mulai Peminjaman</p>
                        <p class="font-normal text-gray-900 dark:text-white">{{ \Carbon\Carbon::parse($peminjaman->tgl_mulai)->format('d-m-y') }} {{ $peminjaman->jam_mulai }}</p>
                    </div>

                    <!-- Selesai -->
                    <div class="flex justify-between">
                        <p class="font-normal text-gray-700 dark:text-gray-400">Selesai Peminjaman</p>
                        <p class="font-normal text-gray-900 dark:text-white">{{ \Carbon\Carbon::parse($peminjaman->tgl_selesai)->format('d-m-y') }} {{ $peminjaman->jam_selesai }}</p>
                    </div>


                    <!-- Tujuan -->
                    <div class="flex justify-between">
                        <p class="font-normal text-gray-700 dark:text-gray-400">Tujuan</p>
                        <p class="font-normal text-gray-900 dark:text-white">{{ $peminjaman->tujuan }}</p>
                    </div>

                    <!-- Waktu pengembalian -->
                    <div class="flex justify-between">
                        <p class="font-normal text-gray-700 dark:text-gray-400">Waktu Pengembalian Real</p>
                        <p class="font-normal text-gray-900 dark:text-white">{{ $peminjaman->tgl_kembali_real ?? '-'}} {{ $peminjaman->jam_kembali_real }}</p>
                    </div>

                    <!-- Kondisi -->
                    <div class="flex justify-between">
                        <p class="font-normal text-gray-700 dark:text-gray-400">Kondisi Kendaraan</p>
                        <p class="font-normal text-gray-900 dark:text-white">{{ $peminjaman->kondisi_kendaraan ?? '-' }}</p>
                    </div>

                    <!-- Insiden -->
                    <div class="flex justify-between">
                        <p class="font-normal text-gray-700 dark:text-gray-400">Detail Insiden</p>
                        <p class="font-normal text-gray-900 dark:text-white">{{ $peminjaman->detail_insiden ?? '-' }}</p>
                    </div>

                    <!-- Status Pinjam -->
                    <div class="flex justify-between">
                        <p class="font-normal text-gray-700 dark:text-gray-400">Status Pinjam</p>
                        <p class="font-normal text-gray-900 dark:text-white">{{ $peminjaman->status_pinjam }}</p>
                    </div>
                </div>
                
            </div>
        </div>
    </div>
</body>
</html>
</x-app-layout>