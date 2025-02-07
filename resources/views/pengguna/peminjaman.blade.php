<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <title>Peminjaman</title>
</head>
<body>
    <div class="relative p-6">
        <div class="relative overflow-x-auto sm:rounded-lg">
            <h1 class="mt-10 text-xl font-semibold text-gray-900 dark:text-white mb-4">Daftar Peminjaman Kendaraan</h1>
           
            <!-- Wrapper untuk tombol dan search -->
            <a href="{{ route('peminjaman.form')}}"
            <div class="flex justify-between items-center mb-4"> 
                
                <button type="button" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800">
                    + Tambah
                </button>

                <!-- Search -->
                <label for="input-group-search" class="sr-only">Search</label>
                <div class="relative mb-4">
                    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-auto">
                        <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z"/>
                        </svg>
                    </div>
                    <input type="text" id="input-group-search" class="block w-full p-2 pl-10 text-sm text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="Cari">
                </div>
            </div>

            <!-- Tabel -->
           
            <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                    <tr>
                        <th scope="col" class="px-6 py-3">Merek dan Tipe</th>
                        <th scope="col" class="px-6 py-3">Plat</th>
                        <th scope="col" class="px-6 py-3">Tanggal Mulai</th>
                        <th scope="col" class="px-6 py-3">Tanggal Selesai</th>
                        <th scope="col" class="px-6 py-3">Status</th>
                        <th scope="col" class="px-6 py-3">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($daftarPeminjaman as $peminjaman)
                    <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                        <td class="px-6 py-4">{{ $peminjaman->kendaraan->merk }} {{ $peminjaman->kendaraan->tipe }}</td>
                        <td class="px-6 py-4">{{ $peminjaman->kendaraan->plat_nomor }}</td>
                        <td class="px-6 py-4">{{ $peminjaman->tgl_mulai }}</td>
                        <td class="px-6 py-4">{{ $peminjaman->tgl_selesai }}</td>
                        <td class="px-6 py-4">{{ $peminjaman->status_pinjam }}</td>
                        <td class="px-6 py-4">
                            <!-- Tombol Detail (selalu tampil) -->
                            <a 
                            class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800">
                            Detail
                            </a>

                            <!-- Jika status Menunggu Persetujuan -->
                            @if($peminjaman->status_pinjam == 'Menunggu Persetujuan')
                                <a 
                                class="text-white bg-red-600 hover:bg-red-700 focus:ring-4 focus:ring-red-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-red-600 dark:hover:bg-red-700 focus:outline-none dark:focus:ring-red-800">
                                Batal
                                </a>
                            @endif

                            <!-- Jika status Disetujui -->
                            @if($peminjaman->status_pinjam == 'Disetujui')
                                <a 
                                class="text-white bg-red-600 hover:bg-red-700 focus:ring-4 focus:ring-red-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-red-600 dark:hover:bg-red-700 focus:outline-none dark:focus:ring-red-800">
                                Batal
                                </a>
                                <a
                                class="text-white bg-yellow-500 hover:bg-yellow-600 focus:ring-4 focus:ring-yellow-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-yellow-500 dark:hover:bg-yellow-600 focus:outline-none dark:focus:ring-yellow-800">
                                Perpanjang
                                </a>
                                <a 
                                class="text-white bg-green-600 hover:bg-green-700 focus:ring-4 focus:ring-green-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-green-600 dark:hover:bg-green-700 focus:outline-none dark:focus:ring-green-800">
                                Selesai
                                </a>
                            @endif

                            <!-- Jika status Ditolak -->
                            @if($peminjaman->status_pinjam == 'Ditolak')
                                <!-- Hanya tombol Detail -->
                            @endif

                            <!-- Jika status Diperpanjang -->
                            @if($peminjaman->status_pinjam == 'Diperpanjang')
                                <!-- Hanya tombol Detail -->
                            @endif

                            <!-- Jika status Dibatalkan -->
                            @if($peminjaman->status_pinjam == 'Dibatalkan')
                                <!-- Hanya tombol Detail -->
                            @endif

                            <!-- Jika status Telah Dikembalikan -->
                            @if($peminjaman->status_pinjam == 'Telah Dikembalikan')
                                <!-- Hanya tombol Detail -->
                            @endif
                        </td>

                        
                       
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>