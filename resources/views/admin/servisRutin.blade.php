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
                                <div>{{ $servis->kendaraan->tipe ?? 'Tidak Diketahui' }}</div>
                                <div class="text-sm text-gray-500">{{ $servis->kendaraan->merk ?? 'Tidak Diketahui' }}</div>
                            </td>
                            <td class="py-3 px-4 border-b">{{ $servis->kendaraan->plat_nomor ?? '-' }}</td>
                            <td class="py-3 px-4 border-b">{{ \Carbon\Carbon::parse($servis->tgl_servis_real)->locale('id')->format('d-m-Y') }}</td>
                            <td class="py-3 px-4 border-b text-{{ $servis->status == 'SUDAH' ? 'green' : 'red' }}-500">
                                {{ strtoupper($servis->status) }}
                            </td>
                            <td class="py-3 px-4 border-b">
                                <a href="{{ route('admin.servisRutin.detail', $servis->id_servis_rutin) }}" 
                                    class="text-blue-500 hover:underline">Detail</a>
                                <a href="{{ route('admin.servisRutin.create')}}" 
                                    class="text-gray-500 hover:underline">Input</a>
                                <a href="#" class="text-blue-500 hover:underline">Edit</a>
                                <a href="#" class="text-red-500 hover:underline">Hapus</a>
                            </td>
                        </tr>
                        @endforeach

                    </tbody>
                </table>
                
                <!-- Pagination -->
                <div class="p-4 bg-gray-100 flex justify-between items-center">
                    {{ $servisRutins->links() }}
                </div>
            </div>
        </div>
    </div>
</body>
</html>

</x-app-layout>
