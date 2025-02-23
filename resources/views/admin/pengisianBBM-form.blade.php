<x-app-layout>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <title>Form Pengisian BBM Kendaraan</title>
        <script src="https://cdn.tailwindcss.com"></script>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/11.7.12/sweetalert2.min.css">
        <script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/11.7.12/sweetalert2.all.min.js"></script>
    </head>
    <body class="bg-gray-100">
        <div class="flex justify-center p-8">
            <div class="w-full max-w-lg bg-white p-8 rounded shadow-md">
                <h1 class="text-3xl font-bold mb-6">Form Pengisian BBM Kendaraan</h1>
                <form action="{{ route('admin.pengisianBBM.store') }}" method="POST">
                    @csrf
                    <div class="mb-4">
                        <label class="block text-gray-700">Merek dan Tipe Kendaraan</label>
                        <input type="text" name="merk_tipe" class="w-full p-2 border border-gray-300 rounded bg-gray-100" readonly value="{{ request('merk') . ' ' . request('tipe') }}">
                        <input type="hidden" name="id_kendaraan" value="{{ request('id_kendaraan') }}">
                        <input type="hidden" id="id_peminjaman" name="id_peminjaman" value="{{ request('id_peminjaman') }}">
                    </div>
                    <div class="mb-4">
                        <label class="block text-gray-700">Nomor Plat</label>
                        <input type="text" class="w-full p-2 border border-gray-300 rounded bg-gray-100" readonly value="{{ request('plat') }}">
                    </div>
                    <div class="mb-4">
                        <label class="block text-gray-700">Jenis BBM</label>
                        <select name="jenis_bbm" class="w-full p-2 border border-gray-300 rounded" required>
                            <option value="" disabled selected>Pilih Jenis BBM</option>
                            <option value="Pertalite">Pertalite</option>
                            <option value="Pertamax">Pertamax</option>
                            <option value="Pertamax Turbo">Pertamax Turbo</option>
                            <option value="Dexlite">Dexlite</option>
                            <option value="Pertamina Dex">Pertamina Dex</option>
                        </select>
                    </div>
                    <div class="mb-4">
                        <label class="block text-gray-700">Tanggal Pengisian BBM</label>
                        <input type="date" name="tgl_isi" class="w-full p-2 border border-gray-300 rounded" required>
                    </div>
                    <div class="mb-4">
                        <label class="block text-gray-700">Nominal (Rp)</label>
                        <input type="text" id="nominalInput" name="nominal" class="w-full p-2 border border-gray-300 rounded" required>
                    </div>
                    <div class="flex justify-between">
                        <a href="{{ url()->previous() }}" class="bg-gray-500 text-white px-4 py-2 rounded">Kembali</a>
                        <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
        <script>
            document.getElementById('nominalInput').addEventListener('input', function (e) {
                let value = e.target.value.replace(/\D/g, '');
                e.target.value = value.replace(/\B(?=(\d{3})+(?!\d))/g, ".");
            });
        </script>
    </body>
    </html>
</x-app-layout>
