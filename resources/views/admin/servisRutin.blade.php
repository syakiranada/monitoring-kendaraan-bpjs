<x-app-layout>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Daftar Servis Rutin Kendaraan</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
</head>
<body class="bg-gray-100 font-sans leading-normal tracking-normal">
    <div class="flex">
        <!-- Main content -->
        <div class="flex-1 p-6">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-3xl font-bold">Daftar Servis Rutin Kendaraan</h1>
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
                            <th class="py-3 px-4 text-left">Tanggal Servis Rutin</th>
                            <th class="py-3 px-4 text-left">Status</th>
                            <th class="py-3 px-4 text-left">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($servisRutins as $servis)
                        <tr class="kendaraan-row cursor-pointer" data-id="{{ $servis->kendaraan->id_kendaraan ?? '' }}">
                            <td class="py-3 px-4 border-b">
                                <div>{{ ($servis->kendaraan->merk ?? 'Tidak Diketahui') . ' ' . ($servis->kendaraan->tipe ?? '') }}</div>
                            </td> 
                            <td class="py-3 px-4 border-b">{{ $servis->kendaraan->plat_nomor ?? '-' }}</td>
                            <td class="py-3 px-4 border-b">{{ \Carbon\Carbon::parse($servis->tgl_servis_real)->locale('id')->format('d-m-Y') }}</td>
                            <td class="py-3 px-4 border-b">
                                @php
                                    $tglServis = \Carbon\Carbon::parse($servis->tgl_servis_selanjutnya);
                                    $hariIni = \Carbon\Carbon::now();
                                    $selisihHari = $hariIni->diffInDays($tglServis, false);
                            
                                    if ($selisihHari <= 30 && $selisihHari > 0) {
                                        $status = 'Mendekati Jatuh Tempo';
                                        $color = 'yellow';
                                    } elseif ($selisihHari <= 0) {
                                        $status = 'Jatuh Tempo';
                                        $color = 'red';
                                    } else {
                                        $status = 'Sudah Dibayar';
                                        $color = 'green';
                                    }
                                @endphp
                            
                                <span class="text-xs font-medium px-2.5 py-0.5 rounded text-{{ $color }}-500 bg-{{ $color }}-100">
                                    {{ strtoupper($status) }}
                                </span>
                            </td>
                            
                            <td class="py-3 px-4 border-b">
                                <a href="{{ route('admin.servisRutin.detail', $servis->id_servis_rutin) }}" 
                                    class="text-blue-500 hover:underline">Detail</a>
                                <a href="{{ route('admin.servisRutin.create', [
                                    'id_kendaraan' => $servis->kendaraan->id_kendaraan ?? '',
                                    'merk' => $servis->kendaraan->merk ?? 'Tidak Diketahui',
                                    'tipe' => $servis->kendaraan->tipe ?? '',
                                    'plat' => $servis->kendaraan->plat_nomor ?? '-',
                                    'jadwal_servis' => $servis->tgl_servis_selanjutnya ?? '-'
                                ]) }}" class="text-gray-500 hover:underline">Input</a>
                                {{--  <a href="{{ route('admin.servisRutin.edit', [
                                    'servis' => $servis,
                                    'id_kendaraan' => $servis->kendaraan->id_kendaraan ?? '',
                                    'merk' => $servis->kendaraan->merk ?? 'Tidak Diketahui',
                                    'tipe' => $servis->kendaraan->tipe ?? '',
                                    'plat' => $servis->kendaraan->plat_nomor ?? '-',
                                    'jadwal_servis' => $servis->tgl_servis_selanjutnya ?? '-'
                                ]) }}" class="text-blue-500 hover:underline">Edit</a>  --}}
                                <a href="{{ route('admin.servisRutin.edit', ['id' => $servis->id_servis_rutin]) }}" class="text-blue-500 hover:underline">
                                    Edit
                                </a>
                                
                                <form action="{{ route('admin.servisRutin.destroy', $servis->id_servis_rutin) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-500 hover:underline"
                                        onclick="return confirm('Apakah Anda yakin ingin menghapus data ini?')">Hapus</button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                
                <!-- Pagination -->
                <div class="flex justify-center items-center py-4">
                    <div class="bg-white rounded-lg shadow-md p-2">
                        {{ $servisRutins->links('pagination::tailwind') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>

</x-app-layout>
