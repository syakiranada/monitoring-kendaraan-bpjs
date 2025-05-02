<x-app-layout>
    <!DOCTYPE html>
    <html lang="id">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Beranda Pengguna</title>
        <style>
            @media (max-width: 640px) {
                .alert-icon {
                    min-width: 24px;
                    min-height: 24px;
                }
                .card-icon {
                    min-width: 20px;
                    min-height: 20px;
                }
                .bullet-point {
                    font-size: 16px;
                    display: flex;
                    align-items: center;
                    margin-top: 2px;
                }
                .mobile-table-cell {
                    white-space: normal;
                    word-break: break-word;
                }
                .mobile-status {
                    display: inline-block;
                    width: 100%;
                    text-align: center;
                    margin-top: 4px;
                }
            }
        </style>
    </head>
    <body class="bg-gray-100">
        <div class="max-w-none mx-auto md:p-4">
            <!-- Selamat Datang - Positioned higher -->
            <h1 class="text-xl md:text-2xl font-bold text-gray-800 ml-1 mb-4 md:mb-3">Selamat Datang, {{ $user->name }}!</h1>
            <div class="p-1">
            @if(count($latePeminjaman) > 0 || count($upcomingPeminjaman) > 0)
            <div class="p-4 mb-6 text-red-800 border border-red-300 rounded-lg bg-red-50 shadow-md" role="alert">
                {{-- Bagian Pengembalian Terlambat --}}
                @if(count($latePeminjaman) > 0)
                    <div class="flex items-center gap-2 mb-2">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="size-5 alert-icon">
                            <path d="M4.214 3.227a.75.75 0 0 0-1.156-.955 8.97 8.97 0 0 0-1.856 3.825.75.75 0 0 0 1.466.316 7.47 7.47 0 0 1 1.546-3.186Z" />
                            <path d="M16.942 2.272a.75.75 0 0 0-1.157.955 7.47 7.47 0 0 1 1.547 3.186.75.75 0 0 0 1.466-.316 8.971 8.971 0 0 0-1.856-3.825Z" />
                            <path fill-rule="evenodd" d="M10 2a6 6 0 0 0-6 6c0 1.887-.454 3.665-1.257 5.234a.75.75 0 0 0 .515 1.076 32.91 32.91 0 0 0 3.256.508 3.5 3.5 0 0 0 6.972 0 32.903 32.903 0 0 0 3.256-.508.75.75 0 0 0 .515-1.076A11.448 11.448 0 0 1 16 8a6 6 0 0 0-6-6Z" clip-rule="evenodd" />
                        </svg>
                        <h3 class="text-lg font-semibold">Pengembalian Kendaraan Dinas Terlambat!</h3>
                    </div>
                    <div class="mt-2 mb-2 text-sm space-y-2">
                        <p>Anda telah melewati batas waktu pengembalian kendaraan dinas berikut:</p>
                        <div class="space-y-2 pl-2">
                            @foreach($latePeminjaman as $pinjam)
                                <div class="flex items-center gap-2">
                                    <span class="text-red-600 bullet-point">•</span>
                                    <p class="text-sm">
                                        {{ $pinjam->kendaraan->merk }} {{ $pinjam->kendaraan->tipe }} 
                                        ({{ $pinjam->kendaraan->plat_nomor }}) - 
                                        seharusnya dikembalikan pada 
                                        {{ \Carbon\Carbon::parse($pinjam->tgl_selesai . ' ' . $pinjam->jam_selesai)->format('d-m-Y') }} 
                                        jam {{ \Carbon\Carbon::parse($pinjam->jam_selesai)->format('H:i') }}.
                                    </p>
                                </div>
                            @endforeach
                        </div>
                        <p class="font-medium mt-2">Mohon segera lakukan pengembalian!</p>
                    </div>
                @endif
    
                {{-- Bagian Pengembalian Akan Jatuh Tempo dalam 3 Jam --}}
                @if(count($upcomingPeminjaman) > 0)
                    <div class="flex items-center gap-2 mb-2">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="size-5 alert-icon">
                            <path fill-rule="evenodd" d="M10 2a6 6 0 0 0-6 6c0 1.887-.454 3.665-1.257 5.234a.75.75 0 0 0 .515 1.076 32.91 32.91 0 0 0 3.256.508 3.5 3.5 0 0 0 6.972 0 32.903 32.903 0 0 0 3.256-.508.75.75 0 0 0 .515-1.076A11.448 11.448 0 0 1 16 8a6 6 0 0 0-6-6Z" clip-rule="evenodd" />
                        </svg>
                        <h3 class="text-lg font-semibold">Pengembalian Kendaraan Dinas Dalam 3 Jam!</h3>
                    </div>
                    <div class="mt-2 text-sm space-y-2">
                        <p>Berikut kendaraan yang harus segera dikembalikan:</p>
                        <div class="space-y-2 pl-2">
                            @foreach($upcomingPeminjaman as $pinjam)
                                <div class="flex items-center gap-2">
                                    <span class="text-red-600 bullet-point">•</span>
                                    <p class="text-sm">
                                        {{ $pinjam->kendaraan->merk }} {{ $pinjam->kendaraan->tipe }} 
                                        ({{ $pinjam->kendaraan->plat_nomor }}) - 
                                        harus dikembalikan sebelum 
                                        {{ \Carbon\Carbon::parse($pinjam->tgl_selesai . ' ' . $pinjam->jam_selesai)->format('H:i') }}.
                                    </p>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
            @endif 
    
            <!-- Daftar Peminjaman - Positioned higher and more to the left -->
            <div class="flex items-center mb-2">
                <h2 class="text-2xl font-semibold text-gray-800 dark:text-white">
                    Daftar Peminjaman Kendaraan
                </h2>
            </div>
            <div class = "mt-4"></div>
            <!-- Table positioned higher -->
            <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
                @if(count($peminjaman) > 0)
               
                <table class="w-full text-sm text-left rtl:text-right text-gray-500">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50 font-normal">
                        <tr>
                            <th class="px-6 py-3">Merk dan Tipe</th>
                            <th class="px-6 py-3">Plat</th>
                            <th class="px-6 py-3">Tanggal Kembali</th>
                            <th class="px-6 py-3 hidden sm:table-cell">Jam Kembali</th>
                            <th class="px-6 py-3">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($peminjaman as $pinjam)
                        <tr class="bg-white border-b hover:bg-gray-50">
                            <td class="px-6 py-3 whitespace-nowrap">
                                {{ $pinjam->kendaraan->merk }} {{ $pinjam->kendaraan->tipe }}
                            </td>
                            
                            <td class="px-6 py-3">
                                {{ $pinjam->kendaraan->plat_nomor }}
                            </td>
                            <td class="px-6 py-3">
                                {{ \Carbon\Carbon::parse($pinjam->tgl_selesai)->format('d-m-Y') }}
                                <span class="block text-xs text-gray-500 sm:hidden">
                                    {{ \Carbon\Carbon::parse($pinjam->jam_selesai)->format('H:i') }}
                                </span>
                            </td>
                            <td class="px-6 py-3 hidden sm:table-cell">
                                {{ \Carbon\Carbon::parse($pinjam->jam_selesai)->format('H:i') }}
                            </td>
                            <td class="px-6 py-3">
                                @if(strtoupper($pinjam->status_pinjam) === 'BELUM DIKEMBALIKAN')
                                <span class="bg-red-100 text-red-800 text-xs font-medium px-2.5 py-0.5 rounded-sm border border-red-400">
                                    BELUM DIKEMBALIKAN
                                </span>
                                @else
                                <span class="bg-green-100 text-green-800 text-xs font-medium px-2.5 py-0.5 rounded-sm border border-green-400">
                                    SUDAH DISETUJUI
                                </span>  
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                @else
                <p class="text-sm text-gray-500 text-center py-4">Tidak ada kendaraan yang sedang dipinjam, akan dipinjam, atau belum dikembalikan.</p>
                @endif
            </div>
        </div>
        <div class="mt-4">
            {{  $peminjaman->appends(request()->query())->links() }}
        </div>
    </div>
    </body>
    </html>
</x-app-layout>