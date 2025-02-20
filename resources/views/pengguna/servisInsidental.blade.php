<x-app-layout>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Kendaraan</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
</head>
<body class="bg-gray-100 font-sans">
    <div class="p-6 space-y-12"> <!-- Gunakan space-y-12 untuk memberi jarak antar tabel -->
        <!-- Tabel Daftar Kendaraan Dipinjam -->
        <div>
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-3xl font-bold">Daftar Kendaraan Dipinjam</h1>
                <div class="relative">
                    <input type="text" class="border rounded-lg py-2 px-4 pl-10 w-64" placeholder="Search">
                    <i class="fas fa-search absolute left-3 top-3 text-gray-500"></i>
                </div>
            </div>
            <div class="bg-white shadow-md rounded-lg overflow-hidden">
                <table class="min-w-full bg-white">
                    <thead class="bg-gray-100 text-gray-600">
                        <tr>
                            <th class="py-3 px-4 text-left">Merek dan Tipe</th>
                            <th class="py-3 px-4 text-left">Plat</th>
                            <th class="py-3 px-4 text-left">Status Peminjaman</th>
                            <th class="py-3 px-4 text-left">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($servisInsidentals as $servis)
                        <tr class="kendaraan-row cursor-pointer" data-id="{{ $servis->kendaraan->id_kendaraan ?? '' }}">
                            <td class="py-3 px-4 border-b">
                                <div>{{ $servis->kendaraan->tipe ?? 'Tidak Diketahui' }}</div>
                                <div class="text-sm text-gray-500">{{ $servis->kendaraan->merk ?? 'Tidak Diketahui' }}</div>
                            </td>
                            <td class="py-3 px-4 border-b">{{ $servis->kendaraan->plat_nomor ?? '-' }}</td>
                            <td class="py-3 px-4 border-b text-{{ in_array($servis->peminjaman?->status_pinjam, ['Disetujui', 'Diperpanjang']) ? 'green' : 'blue' }}-500">
                                {{ strtoupper($servis->peminjaman?->status_pinjam) }}
                            </td>
                            <td class="py-3 px-4 border-b">
                                <a href="{{ route('servisInsidental.create')}}" 
                                    class="text-blue-500 hover:underline">Input</a>
                            </td>
                        </tr>
                        @endforeach

                    </tbody>
                </table>
            </div>
        </div>

        <!-- Tabel Daftar Servis Insidental -->
        <div>
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-3xl font-bold">Daftar Servis Insidental Kendaraan</h1>
                <div class="relative">
                    <input type="text" class="border rounded-lg py-2 px-4 pl-10 w-64" placeholder="Search">
                    <i class="fas fa-search absolute left-3 top-3 text-gray-500"></i>
                </div>
            </div>
            <div class="bg-white shadow-md rounded-lg overflow-hidden">
                <table class="min-w-full bg-white">
                    <thead class="bg-gray-100 text-gray-600">
                        <tr>
                            <th class="py-3 px-4 text-left">Merek dan Tipe</th>
                            <th class="py-3 px-4 text-left">Plat</th>
                            <th class="py-3 px-4 text-left">Tanggal Servis Insidental</th>
                            <th class="py-3 px-4 text-left">Status Peminjaman</th>
                            <th class="py-3 px-4 text-left">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($servisInsidentals as $servis)
                        <tr class="kendaraan-row cursor-pointer" data-id="{{ $servis->kendaraan->id_kendaraan ?? '' }}">
                            <td class="py-3 px-4 border-b">
                                <div>{{ $servis->kendaraan->tipe ?? 'Tidak Diketahui' }}</div>
                                <div class="text-sm text-gray-500">{{ $servis->kendaraan->merk ?? 'Tidak Diketahui' }}</div>
                            </td>
                            <td class="py-3 px-4 border-b">{{ $servis->kendaraan->plat_nomor ?? '-' }}</td>
                            <td class="py-3 px-4 border-b">{{ \Carbon\Carbon::parse($servis->tgl_servis_real)->locale('id')->format('d-m-Y') }}</td>
                            <td class="py-3 px-4 border-b text-{{ $servis->status == 'SUDAH' ? 'green' : 'red' }}-500">
                                {{ strtoupper($servis->status) }}
                            </td>
                            <td class="py-3 px-4 border-b">
                                <a href="{{ route('servisInsidental.detail', $servis->id_servis_insidental) }}" 
                                    class="text-blue-500 hover:underline">Detail</a>
                            </td>
                        </tr>
                        @endforeach

                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>

</html>

</x-app-layout>