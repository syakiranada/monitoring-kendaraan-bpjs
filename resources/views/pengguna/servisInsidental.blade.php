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
        <div class="p-6 space-y-12">
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
                                <th class="py-3 px-4 text-left">MEREK DAN TIPE</th>
                                <th class="py-3 px-4 text-left">PLAT</th>
                                <th class="py-3 px-4 text-left">STATUS PEMINJAMAN</th>
                                <th class="py-3 px-4 text-left">AKSI</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($peminjamans as $peminjaman)
                                @if($peminjaman->status_pinjam == 'Disetujui')
                                    <tr class="kendaraan-row cursor-pointer" data-id="{{ $peminjaman->kendaraan->id_kendaraan ?? '' }}">
                                        <td class="py-3 px-4 border-b">
                                            <div>{{ ($peminjaman->kendaraan->merk ?? 'Tidak Diketahui') . ' ' . ($peminjaman->kendaraan->tipe ?? '') }}</div>
                                        </td>      
                                        <td class="py-3 px-4 border-b">{{ $peminjaman->kendaraan->plat_nomor ?? '-' }}</td>
                                        <td class="py-3 px-4 border-b">
                                            <span class="text-xs font-medium px-2.5 py-0.5 rounded text-blue-500 bg-blue-100">
                                                {{ strtoupper($peminjaman->status_pinjam ?? 'TIDAK DIKETAHUI') }}
                                            </span>
                                        </td>
                                        <td class="py-3 px-4 border-b">
                                            <a href="{{ route('servisInsidental.create', [
                                                'id_peminjaman' => $peminjaman->id_peminjaman,
                                                'id_kendaraan'   => $peminjaman->kendaraan->id_kendaraan ?? '',
                                                'merk'           => $peminjaman->kendaraan->merk ?? 'Tidak Diketahui',
                                                'tipe'           => $peminjaman->kendaraan->tipe ?? '',
                                                'plat'           => $peminjaman->kendaraan->plat_nomor ?? '-'
                                            ]) }}" class="text-blue-500 hover:underline">
                                                Input
                                            </a>
                                        </td>
                                    </tr>
                                @endif
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
                                <th class="py-3 px-4 text-left">MEREK DAN TIPE</th>
                                <th class="py-3 px-4 text-left">PLAT</th>
                                <th class="py-3 px-4 text-left">TANGGAL SERVIS INSIDENTAL</th>
                                <th class="py-3 px-4 text-left">STATUS PEMINJAMAN</th>
                                <th class="py-3 px-4 text-left">AKSI</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($servisInsidentals as $servis)
                            <tr class="kendaraan-row cursor-pointer" data-id="{{ $servis->kendaraan->id_kendaraan ?? '' }}">
                                <td class="py-3 px-4 border-b">
                                    <div>{{ ($peminjaman->kendaraan->merk ?? 'Tidak Diketahui') . ' ' . ($peminjaman->kendaraan->tipe ?? '') }}</div>
                                </td>  
                                <td class="py-3 px-4 border-b">{{ $servis->kendaraan->plat_nomor ?? '-' }}</td>
                                <td class="py-3 px-4 border-b">{{ \Carbon\Carbon::parse($servis->tgl_servis)->locale('id')->format('d-m-Y') }}</td>
                                <td class="py-3 px-4 border-b">
                                    @if($servis->id_peminjaman && $servis->peminjaman)
                                        <span class="text-xs font-medium px-2.5 py-0.5 rounded text-{{ 
                                            $servis->peminjaman->status_pinjam == 'Telah Dikembalikan' ? 'green' : 
                                            ($servis->peminjaman->status_pinjam == 'Dibatalkan' ? 'red' : 
                                            ($servis->peminjaman->status_pinjam == 'Ditolak' ? 'red' : 
                                            ($servis->peminjaman->status_pinjam == 'Diperpanjang' ? 'yellow' : 
                                            ($servis->peminjaman->status_pinjam == 'Disetujui' ? 'blue' : 'gray')))) }}-500 bg-{{ 
                                            $servis->peminjaman->status_pinjam == 'Telah Dikembalikan' ? 'green' : 
                                            ($servis->peminjaman->status_pinjam == 'Dibatalkan' ? 'red' : 
                                            ($servis->peminjaman->status_pinjam == 'Ditolak' ? 'red' : 
                                            ($servis->peminjaman->status_pinjam == 'Diperpanjang' ? 'yellow' : 
                                            ($servis->peminjaman->status_pinjam == 'Disetujui' ? 'blue' : 'gray')))) }}-100">
                                            {{ strtoupper($servis->peminjaman->status_pinjam) }}
                                        </span>
                                    @else
                                        <span class="text-xs font-medium px-2.5 py-0.5 rounded text-gray-500 bg-gray-100">
                                            TIDAK TERKAIT PEMINJAMAN
                                        </span>
                                    @endif
                                </td>                                
                                
                                <td class="py-3 px-4 border-b">
                                    <a href="{{ route('servisInsidental.detail', $servis->id_servis_insidental) }}" 
                                        class="text-blue-500 hover:underline">Detail</a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="py-6 text-center text-gray-500">Tidak ada data servis insidental ditemukan</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                {{--  <!-- Pagination -->
                <div class="flex justify-center items-center py-4">
                    <div class="bg-white rounded-lg shadow-md p-2">
                        {{ $servisInsidentals->appends(request()->query())->links('pagination::tailwind') }}
                    </div>
                </div>  --}}
            </div>
        </div>
    </body>
    </html>
    </x-app-layout>