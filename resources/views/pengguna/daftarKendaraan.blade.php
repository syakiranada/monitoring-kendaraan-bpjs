<x-app-layout>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet"> -->
    <!-- <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">  -->
    <title>Daftar Kendaraan</title>
</head>
<body>
    <div class="relative p-4">
        <div class="relative overflow-x-auto sm:rounded-lg">
            <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center mb-4 gap-2">
                <h1 class="text-2xl font-bold text-gray-900">Daftar Kendaraan Dinas</h1>
                <form action="{{ route('kendaraan') }}" method="GET" class="flex text-sm w-full sm:w-auto">
                    <div class="relative w-full">
                        <input 
                            type="text" 
                            name="search"
                            class="block w-full p-2 pl-10 text-sm text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500" 
                            placeholder="Cari"
                            value="{{ request()->query('search') }}"
                        >
                        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                            <svg class="w-4 h-4 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none"
                                viewBox="0 0 20 20">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z" />
                            </svg>
                        </div>
                    </div>
                </form>
            </div>


            <!-- Tabel -->
            <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
            <table class="w-full text-sm text-left  rtl:text-right text-gray-500">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50 ">
                    <tr>
                        <th scope="col" class="px-6 py-3 sm:px-6 sm:py-3 text-xs sm:text-xs">Merek dan Tipe</th>
                        <th scope="col" class="px-6 py-3  sm:px-6 sm:py-3 text-xs sm:text-xs">Warna</th>
                        <th scope="col" class="px-6 py-3 sm:px-6 sm:py-3 text-xs sm:text-xs">Kapasitas</th>
                        <th scope="col" class="px-6 py-3  sm:px-6 sm:py-3 text-xs sm:text-xs">Plat</th>
                        <th scope="col" class="px-6 py-3 sm:px-6 sm:py-3 text-xs sm:text-xs">Status</th>
                        <th scope="col" class="px-6 py-3 sm:px-6 sm:py-3 text-xs sm:text-xs">Aksi</th>
                        
                    </tr>
                </thead>
                <tbody>
                    @forelse ($kendaraan as $item)
                    <tr class="bg-white border-b  hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">{{ $item->merk }} {{ $item->tipe }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">{{ $item->warna }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">{{ $item->kapasitas }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">{{ $item->plat_nomor }}</td>
                        <td class="px-6 py-4 uppercase whitespace-nowrap">
                            <span class="
                                @if($item->status_ketersediaan == 'Tersedia' || $item->status_ketersediaan === 'TERSEDIA') bg-green-100 text-green-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded-sm border border-green-400
                                @elseif($item->status_ketersediaan === 'Tidak Tersedia' || $item->status_ketersediaan === 'TIDAK TERSEDIA') bg-red-100 text-red-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded-sm  border border-red-400
                                @else bg-green-100 text-green-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded-sm border border-green-400

                                @endif">
                                {{ $item->status_ketersediaan }}
                            </span>
                        <td class="px-6 py-4 whitespace-nowrap">
                        <!-- <a href="{{ route('kendaraan.detail', $item->id_kendaraan) }}" 
                            <button type="button" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-2 py-1.5 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800">Detail</button> -->
                            <!-- <a  href="{{ route('kendaraan.getDetail', $item->id_kendaraan) }}" class="font-medium text-blue-600 hover:underline">Detail</a> -->
                            <a href="{{ route('kendaraan.getDetail', ['id' => $item->id_kendaraan, 'page' => request('page'), 'search' => request()->query('search')]) }}" class="font-medium text-blue-600 hover:underline">Detail</a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <!-- <td colspan="8" class="text-center px-6 py-4">Data tidak ditemukan</td> -->
                        <td colspan="6" class="text-sm text-gray-500 text-center py-4 bg-white">Data tidak ditemukan</td>
                    </tr>                        
                    @endforelse
                </tbody>
            </table>
            </div>
            <!-- Pagination -->
            @if ($kendaraan->hasPages())
                <div class="mt-4">
                    <!-- // {{ $kendaraan->appends(request()->query())->links('vendor.pagination.tailwind') }} -->
                    <!-- {{ $kendaraan->appends(['search' => request()->query('search', '')])->links() }} -->
                    {{ $kendaraan->onEachSide(1)->links() }}
                </div>
            @endif

        </div>
    </div>
</body>
</html>
</x-app-layout>