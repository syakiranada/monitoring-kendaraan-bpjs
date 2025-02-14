<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <title>Form Peminjaman</title>
</head>
<body class="bg-gray-100">

    <div class="relative min-h-screen flex items-center justify-center py-12 px-4">
        <div class="max-w-2xl w-full bg-white p-6 rounded-lg shadow-lg">
            <h2 class="text-2xl font-bold mb-6 text-center">Form Perpanjangan</h2>

            @if(session('success'))
                <div class="bg-green-100 text-green-800 p-3 rounded mb-4">
                    {{ session('success') }}
                </div>
            @endif

            <form id="form-perpanjangan"action="{{ route('peminjaman.simpan') }}" method="POST">
                @csrf
                <!-- Tanggal & Jam Mulai Perpanjangan DISABLED OTOMATIS DARI TANGGAL SELESAI SEBELUMNYA --> 
                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div>
                        <label for="tgl_mulai" class="block text-sm font-medium text-gray-700 mb-1">Tanggal Mulai Pinjam</label>
                        <input type="date" id="tgl_mulai" name="tgl_mulai" class="w-full p-2.5 border rounded-lg bg-white-100" required>
                        <p id="warning-tgl-mulai" class="text-red-500 text-sm mt-1 hidden">Tanggal mulai harus diisi!</p>
                    </div>
                    <div>
                        <label for="jam_mulai" class="block text-sm font-medium text-gray-700 mb-1">Jam Mulai Pinjam</label>
                        <input type="time" id="jam_mulai" name="jam_mulai" class="w-full p-2.5 border rounded-lg bg-white-100" required>
                        <p id="warning-jam-mulai" class="text-red-500 text-sm mt-1 hidden">Jam mulai harus diisi!</p>
                    </div>
                </div>

                <!-- Tanggal & Jam Selesai Peminjaman -->
                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div>
                        <label for="tgl_selesai" class="block text-sm font-medium text-gray-700 mb-1">Tanggal Selesai Pinjam</label>
                        <input type="date" id="tgl_selesai" name="tgl_selesai" class="w-full p-2.5 border rounded-lg bg-white-100" required>
                        <p id="warning-tgl-selesai" class="text-red-500 text-sm mt-1 hidden">Tanggal selesai harus setelah tanggal mulai!</p>
                    </div>
                    <div>
                        <label for="jam_selesai" class="block text-sm font-medium text-gray-700 mb-1">Jam Selesai Pinjam</label>
                        <input type="time" id="jam_selesai" name="jam_selesai" class="w-full p-2.5 border rounded-lg bg-white-100" required>
                        <p id="warning-jam-selesai" class="text-red-500 text-sm mt-1 hidden">Jam selesai harus setelah jam mulai!</p>
                    </div>
                </div>

               <!-- Pilihan Kendaraan DISABLED OTOMATIS SAMA KAYA YG SEBELUMNYA-->
                <div class="mb-4">
                <label for="pilih-kendaraan" class="block text-sm font-medium text-gray-700 mb-1">Pilih Kendaraan</label>
                    <select id="pilih-kendaraan" name="kendaraan" class="w-full p-2.5 border rounded-lg bg-white" disabled>
                        <option value="" disabled selected>Pilih Kendaraan</option>
                    </select>
                    <p id="warning-kendaraan" class="text-red font-bold text-sm mt-1 hidden">Silakan isi tanggal & jam terlebih dahulu</p>
                </div>

                <!-- Tujuan Peminjaman -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tujuan</label>
                    <input type="text" name="tujuan" class="w-full p-2.5 border rounded-lg" placeholder="Masukkan tujuan peminjaman" required>
                </div>

                <!-- Tombol Submit -->
                <div class="flex justify-end space-x-4 mb-2">
                    <button id="btn-batal" type="button" class="bg-red-600 text-white px-6 py-2.5 rounded-lg font-semibold hover:bg-red-700 transition">Batal</button>
                    <button type="submit" class="bg-blue-600 text-white px-6 py-2.5 rounded-lg font-semibold hover:bg-blue-700 transition">Simpan</button>
                </div>
            </form>
        </div>
    </div>

</body>
</html>
