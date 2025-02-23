<x-app-layout>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Pengisian BBM</title>
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
                                            <a href="{{ route('pengisianBBM.create', [
                                                'id_peminjaman' => $peminjaman->id_peminjaman,
                                                'id_kendaraan'   => $peminjaman->kendaraan->id_kendaraan ?? '',
                                                'merk'           => $peminjaman->kendaraan->merk ?? 'Tidak Diketahui',
                                                'tipe'           => $peminjaman->kendaraan->tipe ?? '',
                                                'plat'           => $peminjaman->kendaraan->plat_nomor ?? '-'
                                            ]) }}" class="text-blue-500 hover:underline">
                                                Input BBM
                                            </a>
                                        </td>
                                    </tr>
                                @endif
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
    
            <!-- Tabel Daftar Riwayat BBM -->
            <div>
                <div class="flex justify-between items-center mb-6">
                    <h1 class="text-3xl font-bold">Riwayat Pengisian BBM</h1>
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
                                <th class="py-3 px-4 text-left">TANGGAL PENGISIAN BBM</th>
                                <th class="py-3 px-4 text-left">STATUS PEMINJAMAN</th>
                                <th class="py-3 px-4 text-left">AKSI</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($pengisianBBMs as $bbm)
                            <tr class="kendaraan-row cursor-pointer" data-id="{{ $bbm->kendaraan->id_kendaraan ?? '' }}">
                                <td class="py-3 px-4 border-b">
                                    <div>{{ ($bbm->kendaraan->merk ?? 'Tidak Diketahui') . ' ' . ($bbm->kendaraan->tipe ?? '') }}</div>
                                </td>  
                                <td class="py-3 px-4 border-b">{{ $bbm->kendaraan->plat_nomor ?? '-' }}</td>
                                <td class="py-3 px-4 border-b">{{ \Carbon\Carbon::parse($bbm->tgl_isi)->locale('id')->format('d-m-Y') }}</td>
                                <td class="py-3 px-4 border-b">
                                    @if($bbm->id_peminjaman && $bbm->peminjaman)
                                        <span class="text-xs font-medium px-2.5 py-0.5 rounded text-{{ 
                                            $bbm->peminjaman->status_pinjam == 'Telah Dikembalikan' ? 'green' : 
                                            ($bbm->peminjaman->status_pinjam == 'Dibatalkan' ? 'red' : 
                                            ($bbm->peminjaman->status_pinjam == 'Ditolak' ? 'red' : 
                                            ($bbm->peminjaman->status_pinjam == 'Diperpanjang' ? 'yellow' : 
                                            ($bbm->peminjaman->status_pinjam == 'Disetujui' ? 'blue' : 'gray')))) }}-500 bg-{{ 
                                            $bbm->peminjaman->status_pinjam == 'Telah Dikembalikan' ? 'green' : 
                                            ($bbm->peminjaman->status_pinjam == 'Dibatalkan' ? 'red' : 
                                            ($bbm->peminjaman->status_pinjam == 'Ditolak' ? 'red' : 
                                            ($bbm->peminjaman->status_pinjam == 'Diperpanjang' ? 'yellow' : 
                                            ($bbm->peminjaman->status_pinjam == 'Disetujui' ? 'blue' : 'gray')))) }}-100">
                                            {{ strtoupper($bbm->peminjaman->status_pinjam) }}
                                        </span>
                                    @else
                                        <span class="text-xs font-medium px-2.5 py-0.5 rounded text-gray-500 bg-gray-100">
                                            TIDAK TERKAIT PEMINJAMAN
                                        </span>
                                    @endif
                                </td>                                
                                
                                <td class="py-3 px-4 border-b">
                                    <a href="{{ route('pengisianBBM.detail', $bbm->id_bbm) }}" 
                                        class="text-blue-500 hover:underline">Detail</a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="py-6 text-center text-gray-500">Tidak ada data pengisian BBM ditemukan</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <!-- Pagination -->
                <div class="flex justify-center items-center py-4">
                    <div class="bg-white rounded-lg shadow-md p-2">
                        {{ $pengisianBBMs->appends(request()->query())->links('pagination::tailwind') }}
                    </div>
                </div>
            </div>
        </div>
    </body>
    </html>
    </x-app-layout>