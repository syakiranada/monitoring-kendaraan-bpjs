<x-app-layout>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Kendaraan</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
</head>
<body class="bg-gray-100 font-sans antialiased">
    <div class="flex">
        <!-- Main Content -->
        <div class="flex-1 p-6">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-2xl font-bold text-gray-800">Daftar Kendaraan Dipinjam</h1>
                <div class="relative">
                    <input type="text" class="border border-gray-300 rounded-lg py-2 px-4 pl-10 focus:outline-none focus:ring-2 focus:ring-blue-500" placeholder="Search">
                    <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
                </div>
            </div>
            <div class="bg-white shadow-md rounded-lg p-4 mb-6">
                <table class="w-full">
                    <thead>
                        <tr class="text-left text-gray-500">
                            <th class="py-2 px-4"><input type="checkbox"></th>
                            <th class="py-2 px-4">Merek dan Tipe <i class="fas fa-sort"></i></th>
                            <th class="py-2 px-4">Plat <i class="fas fa-sort"></i></th>
                            <th class="py-2 px-4">Aksi <i class="fas fa-sort"></i></th>
                        </tr>
                    </thead>
                    {{--  <tbody>
                        <tr class="border-t">
                            <td class="py-2 px-4"><input type="checkbox"></td>
                            <td class="py-2 px-4 flex items-center">
                                <div class="w-8 h-8 bg-gray-200 rounded-full flex items-center justify-center mr-3">
                                    <i class="fas fa-car text-gray-500"></i>
                                </div>
                                <div>
                                    <div class="text-gray-800">Avanza</div>
                                    <div class="text-gray-500 text-sm">Toyota</div>
                                </div>
                            </td>
                            <td class="py-2 px-4">H 1234 AB</td>
                            <td class="py-2 px-4"><button class="bg-blue-500 text-white px-4 py-2 rounded-lg">Input BBM</button></td>
                        </tr>
                        <tr class="border-t">
                            <td class="py-2 px-4"><input type="checkbox"></td>
                            <td class="py-2 px-4 flex items-center">
                                <div class="w-8 h-8 bg-gray-200 rounded-full flex items-center justify-center mr-3">
                                    <i class="fas fa-car text-gray-500"></i>
                                </div>
                                <div>
                                    <div class="text-gray-800">Jazz</div>
                                    <div class="text-gray-500 text-sm">Honda</div>
                                </div>
                            </td>
                            <td class="py-2 px-4">H 6789 AB</td>
                            <td class="py-2 px-4"><button class="bg-blue-500 text-white px-4 py-2 rounded-lg">Input BBM</button></td>
                        </tr>
                    </tbody>  --}}
                </table>
            </div>
            <h1 class="text-2xl font-bold text-gray-800 mb-6">Riwayat Pengisian BBM</h1>
            <div class="bg-white shadow-md rounded-lg p-4">
                <table class="w-full">
                    <thead>
                        <tr class="text-left text-gray-500">
                            <th class="py-2 px-4"><input type="checkbox"></th>
                            <th class="py-2 px-4">Merek dan Tipe <i class="fas fa-sort"></i></th>
                            <th class="py-2 px-4">Plat <i class="fas fa-sort"></i></th>
                            <th class="py-2 px-4">Tanggal Pengisian BBM <i class="fas fa-sort"></i></th>
                            <th class="py-2 px-4">Status <i class="fas fa-sort"></i></th>
                            <th class="py-2 px-4">Aksi <i class="fas fa-sort"></i></th>
                        </tr>
                    </thead>
                    {{--  <tbody>
                        <tr class="border-t">
                            <td class="py-2 px-4"><input type="checkbox"></td>
                            <td class="py-2 px-4 flex items-center">
                                <div class="w-8 h-8 bg-gray-200 rounded-full flex items-center justify-center mr-3">
                                    <i class="fas fa-car text-gray-500"></i>
                                </div>
                                <div>
                                    <div class="text-gray-800">Ertiga</div>
                                    <div class="text-gray-500 text-sm">Suzuki</div>
                                </div>
                            </td>
                            <td class="py-2 px-4">H 3456 CD</td>
                            <td class="py-2 px-4">06/01/2025</td>
                            <td class="py-2 px-4">Telah Dikembalikan</td>
                            <td class="py-2 px-4"><button class="bg-gray-500 text-white px-4 py-2 rounded-lg">Detail</button></td>
                        </tr>
                    </tbody>  --}}
                </table>
                <div class="flex justify-between items-center mt-4">
                    <div class="text-gray-500">Previous</div>
                    <div class="flex space-x-2">
                        <button class="bg-gray-200 text-gray-700 px-3 py-1 rounded-lg">1</button>
                        <button class="bg-blue-500 text-white px-3 py-1 rounded-lg">2</button>
                        <button class="bg-gray-200 text-gray-700 px-3 py-1 rounded-lg">3</button>
                        <button class="bg-gray-200 text-gray-700 px-3 py-1 rounded-lg">4</button>
                        <button class="bg-gray-200 text-gray-700 px-3 py-1 rounded-lg">5</button>
                        <span class="text-gray-500">...</span>
                        <button class="bg-gray-200 text-gray-700 px-3 py-1 rounded-lg">11</button>
                    </div>
                    <div class="text-gray-500">Next</div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>

</x-app-layout>