{{-- resources/views/dashboard.blade.php --}}
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Pengguna</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="container mx-auto p-4">
        <h1 class="text-2xl font-bold mb-6">Selamat Datang, {{ $user->name }}!</h1>

        {{-- Alert Merah untuk Kendaraan yang Terlambat --}}
        @if(count($latePeminjaman) > 0)
        <div id="alert-additional-content-2" class="p-4 mb-4 text-red-800 border border-red-300 rounded-lg bg-red-50">
            <div class="flex items-center">
                <svg class="w-4 h-4 me-2" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z"/>
                </svg>
                <h3 class="text-lg font-medium">Pengembalian Kendaraan Dinas Terlambat!</h3>
                </div>
                <div class="mt-2 mb-4 text-sm">
                    Anda telah melewati batas waktu pengembalian kendaraan dinas berikut:
                    <ul class="list-disc pl-5">
                        @foreach($latePeminjaman as $pinjam)
                        <li>
                            {{ $pinjam->kendaraan->merk }} {{ $pinjam->kendaraan->tipe }} 
                            ({{ $pinjam->kendaraan->plat_nomor }}) - 
                            seharusnya dikembalikan pada 
                            {{ \Carbon\Carbon::parse($pinjam->tgl_selesai . ' ' . $pinjam->jam_selesai)->format('d-m-Y') }} jam {{ \Carbon\Carbon::parse($pinjam->jam_selesai)->format('H:i') }}.
                        </li>
                        
                        @endforeach
                    </ul>
                    Mohon segera lakukan pengembalian!
                </div>

        </div>
        @endif

        {{-- Tabel Daftar Peminjaman --}}
        <div class="bg-white rounded-lg shadow-md p-4">
            <h2 class="text-xl font-semibold mb-4">Daftar Peminjaman Kendaraan</h2>
            <div class="relative overflow-x-auto">
                @if(count($peminjaman) > 0)
                    <table class="w-full text-sm text-left text-gray-500">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3">Merk dan Tipe</th>
                                <th scope="col" class="px-6 py-3">Plat</th>
                                <th scope="col" class="px-6 py-3">Tanggal Kembali</th>
                                <th scope="col" class="px-6 py-3">Jam Kembali</th>
                                <th scope="col" class="px-6 py-3">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($peminjaman as $pinjam)
                            <tr class="bg-white border-b">
                                <th scope="row" class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap">
                                    {{ $pinjam->kendaraan->merk }} {{ $pinjam->kendaraan->tipe }}
                                </th>
                                <td class="px-6 py-4">
                                    {{ $pinjam->kendaraan->plat_nomor }}
                                </td>
                                <td class="px-6 py-4">
                                    {{ \Carbon\Carbon::parse($pinjam->tgl_selesai)->format('d-m-Y') }}
                                </td>
                                <td class="px-6 py-4">
                                   {{ \Carbon\Carbon::parse($pinjam->jam_selesai)->format('H:i') }}
                                </td>
                                <td class="px-6 py-4">
                                    @if(strtoupper($pinjam->status_pinjam) === 'BELUM DIKEMBALIKAN')
                                        <span class="bg-red-100 text-red-800 text-xs font-medium px-2.5 py-0.5 rounded">
                                            BELUM DIKEMBALIKAN
                                        </span>
                                    @else
                                        <span class="bg-green-100 text-green-800 text-xs font-medium px-2.5 py-0.5 rounded">
                                            SUDAH DISETUJUI
                                        </span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <p class="text-gray-500 text-center py-4">Tidak ada kendaraan yang sedang dipinjam, akan dipinjam, atau belum dikembalikan.</p>
                @endif
            </div>
        </div>
    </div>
</body>
</html>
