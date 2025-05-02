<x-app-layout>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <title>Daftar Kendaraan</title>
</head>
<body>
    <div class="relative p-6">
        <div class="relative overflow-x-auto sm:rounded-lg">
            <!-- <h1 class="mt-10 text-xl font-semibold text-gray-900 dark:text-white mb-4">Daftar Kendaraan Dinas</h1> -->
            <!-- <h1 class="text-2xl font-bold mb-4">Daftar Kendaraan Dinas</h1> -->
            <!-- Wrapper untuk  dan search -->
            <!-- <div class="flex justify-end mb-4"> -->
                <!-- Search -->
                <!-- <label for="input-group-search" class="sr-only">Search</label>
                <div class="relative mb-4">
                    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-auto">
                        <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z"/>
                        </svg>
                    </div>
                    <input type="text" id="input-group-search" class="block w-full p-2 pl-10 text-sm text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="Cari">
                </div>
            </div> -->
            <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center mb-4 gap-2">
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Daftar Kendaraan Dinas</h1>


                <!-- <div class="relative w-full sm:w-auto">
                    <input type="text" id="input-group-search"
                        class="block w-full p-2 pl-10 text-sm text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"
                        placeholder="Cari">
                    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                        <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none"
                            viewBox="0 0 20 20">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z" />
                        </svg>
                    </div>
                </div> -->

                <form action="{{ route('kendaraan') }}" method="GET" class="flex p-2 pl-10 text-sm">
                    <div class="relative">
                        <input 
                            type="text" 
                            name="search"
                            class="block w-full p-2 pl-10 text-sm text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" 
                            placeholder="Cari"
                            value="{{ request('search') }}"
                        >
                        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                            <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none"
                                viewBox="0 0 20 20">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z" />
                            </svg>
                        </div>
                    </div>
                </form>

            </div>


            <!-- Tabel -->
            <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                    <tr>
                        <th scope="col" class="px-4 py-2 sm:px-6 sm:py-3 text-xs sm:text-sm">Merek dan Tipe</th>
                        <th scope="col" class="px-4 py-2  sm:px-6 sm:py-3 text-xs sm:text-sm">Warna</th>
                        <th scope="col" class="px-4 py-2 sm:px-6 sm:py-3 text-xs sm:text-sm">Kapasitas</th>
                        <th scope="col" class="px-4 py-2  sm:px-6 sm:py-3 text-xs sm:text-sm">Plat</th>
                        <th scope="col" class="px-6 py-3 sm:px-6 sm:py-3 text-xs sm:text-sm">Status</th>
                        <th scope="col" class="px-6 py-3 sm:px-6 sm:py-3 text-xs sm:text-sm">Aksi</th>
                        
                    </tr>
                </thead>
                <tbody>
                    @foreach ($kendaraan as $item)
                    <tr class="bg-white border-b  hover:bg-gray-50 dark:hover:bg-gray-600">
                        <td class="px-6 py-4 whitespace-nowrap">{{ $item->merk }} {{ $item->tipe }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">{{ $item->warna }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">{{ $item->kapasitas }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">{{ $item->plat_nomor }}</td>
                        <td class="px-6 py-4 uppercase whitespace-nowrap">
                            <span class="
                                @if($item->status_ketersediaan == 'Tersedia') bg-green-100 text-green-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded-sm dark:bg-gray-700 dark:text-green-400 border border-green-400
                                @elseif($item->status_ketersediaan == 'Tidak Tersedia') bg-red-100 text-red-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded-sm dark:bg-gray-700 dark:text-red-400 border border-red-400
                                @else bg-green-100 text-green-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded-sm dark:bg-gray-700 dark:text-green-400 border border-green-400

                                @endif">
                                {{ $item->status_ketersediaan }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                        <!-- <a href="{{ route('kendaraan.detail', $item->id_kendaraan) }}" 
                            <button type="button" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-2 py-1.5 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800">Detail</button> -->
                            <a  href="{{ route('kendaraan.getDetail', $item->id_kendaraan) }}" class="font-medium text-blue-600 dark:text-blue-500 hover:underline">Detail</a>
                        </td>
                        
                    @endforeach
                </tbody>
            </table>
            <!-- Pagination -->
            @if ($kendaraan->hasPages())
                <div class="mt-4 flex justify-end">
                    {{ $kendaraan->appends(request()->query())->links('vendor.pagination.tailwind') }}
                </div>
            @endif

        </div>
    </div>
</body>
</html>
<!-- <script>
  document.addEventListener("DOMContentLoaded", function () {
    const searchInput = document.getElementById("input-group-search");
    const tableRows = document.querySelectorAll("tbody tr");

    searchInput.addEventListener("input", function () {
      const searchValue = searchInput.value.toLowerCase();

      tableRows.forEach(row => {
        // Dapatkan semua <td> dalam baris, kecuali kolom aksi (asumsikan kolom terakhir)
        const cells = row.querySelectorAll("td");
        let rowText = "";
        // Ambil semua cell kecuali cell terakhir
        for (let i = 0; i < cells.length - 1; i++) {
          rowText += cells[i].textContent.toLowerCase() + " ";
        }
        // Tampilkan atau sembunyikan baris berdasarkan apakah teks cocok
        if (rowText.includes(searchValue)) {
          row.style.display = "";
        } else {
          row.style.display = "none";
        }
      });
    });
  });
</script> -->

</x-app-layout>