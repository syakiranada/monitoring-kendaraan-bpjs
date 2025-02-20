<x-app-layout>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Pengguna</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="max-w-6xl mx-auto p-8">
        <h1 class="text-2xl font-bold mb-6">Selamat Datang, {{ $user->name }}!</h1>
        @if(count($latePeminjaman) > 0)
        <div class="p-4 mb-6 text-red-800 border border-red-300 rounded-lg bg-red-50" role="alert">
            <div class="flex items-center gap-2 mb-2">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="size-5">
                    <path d="M4.214 3.227a.75.75 0 0 0-1.156-.955 8.97 8.97 0 0 0-1.856 3.825.75.75 0 0 0 1.466.316 7.47 7.47 0 0 1 1.546-3.186ZM16.942 2.272a.75.75 0 0 0-1.157.955 7.47 7.47 0 0 1 1.547 3.186.75.75 0 0 0 1.466-.316 8.971 8.971 0 0 0-1.856-3.825Z" />
                    <path fill-rule="evenodd" d="M10 2a6 6 0 0 0-6 6c0 1.887-.454 3.665-1.257 5.234a.75.75 0 0 0 .515 1.076 32.91 32.91 0 0 0 3.256.508 3.5 3.5 0 0 0 6.972 0 32.903 32.903 0 0 0 3.256-.508.75.75 0 0 0 .515-1.076A11.448 11.448 0 0 1 16 8a6 6 0 0 0-6-6Z" clip-rule="evenodd" />
                </svg>
                <h3 class="text-lg font-semibold">Pengembalian Kendaraan Dinas Terlambat!</h3>
            </div>
            <div class="mt-2 text-sm space-y-2">
                <p>Anda telah melewati batas waktu pengembalian kendaraan dinas berikut:</p>
                <div class="space-y-2 pl-2">
                    @foreach($latePeminjaman as $pinjam)
                    <div class="flex items-center gap-2">
                        <span class="text-red-600">•</span>
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
        </div>
        @endif
        
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-4">
            <div class="flex items-center gap-2 mb-4">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="size-5">
                    <path fill-rule="evenodd" d="M6 4.75A.75.75 0 0 1 6.75 4h10.5a.75.75 0 0 1 0 1.5H6.75A.75.75 0 0 1 6 4.75ZM6 10a.75.75 0 0 1 .75-.75h10.5a.75.75 0 0 1 0 1.5H6.75A.75.75 0 0 1 6 10Zm0 5.25a.75.75 0 0 1 .75-.75h10.5a.75.75 0 0 1 0 1.5H6.75a.75.75 0 0 1-.75-.75ZM1.99 4.75a1 1 0 0 1 1-1h.01a1 1 0 0 1 0 2H3a1 1 0 0 1-1.01-1Zm0 5.25a1 1 0 0 1 1-1h.01a1 1 0 0 1 0 2H3a1 1 0 0 1-1.01-1Zm0 5.25a1 1 0 0 1 1-1h.01a1 1 0 0 1 0 2H3a1 1 0 0 1-1.01-1Z" clip-rule="evenodd" />
                </svg>
                <h2 class="text-lg font-semibold text-gray-900">Daftar Peminjaman Kendaraan</h2>
            </div>
            <div class="relative overflow-x-auto">
                @if(count($peminjaman) > 0)
                    <table class="w-full text-sm text-left text-gray-500">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                            <tr>
                                <th scope="col" class="px-4 py-3">Merk dan Tipe</th>
                                <th scope="col" class="px-4 py-3">Plat</th>
                                <th scope="col" class="px-4 py-3">Tanggal Kembali</th>
                                <th scope="col" class="px-4 py-3">Jam Kembali</th>
                                <th scope="col" class="px-4 py-3">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($peminjaman as $pinjam)
                            <tr class="bg-white border-b">
                                <th scope="row" class="px-4 py-3 font-medium text-gray-900 whitespace-nowrap">
                                    {{ $pinjam->kendaraan->merk }} {{ $pinjam->kendaraan->tipe }}
                                </th>
                                <td class="px-4 py-3">
                                    {{ $pinjam->kendaraan->plat_nomor }}
                                </td>
                                <td class="px-4 py-3">
                                    {{ \Carbon\Carbon::parse($pinjam->tgl_selesai)->format('d-m-Y') }}
                                </td>
                                <td class="px-4 py-3">
                                   {{ \Carbon\Carbon::parse($pinjam->jam_selesai)->format('H:i') }}
                                </td>
                                <td class="px-4 py-3">
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
                    <p class="text-sm text-gray-500 text-center py-4">Tidak ada kendaraan yang sedang dipinjam, akan dipinjam, atau belum dikembalikan.</p>
                @endif
            </div>
        </div>
    </div>
</body>
</html>
</x-app-layout>