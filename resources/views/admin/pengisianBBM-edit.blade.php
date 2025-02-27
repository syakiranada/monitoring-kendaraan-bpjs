<x-app-layout>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <title>Edit Pengisian BBM Kendaraan</title>
        <script src="https://cdn.tailwindcss.com"></script>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/11.7.12/sweetalert2.min.css">
        <script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/11.7.12/sweetalert2.all.min.js"></script>
    </head>
    <body class="bg-gray-100">
        <div class="flex justify-center p-8">
            <div class="w-full max-w-lg bg-white p-8 rounded shadow-md">
                <h1 class="text-3xl font-bold mb-6">Edit Pengisian BBM Kendaraan</h1>
                <form action="{{ route('admin.pengisianBBM.update', $bbm->id_bbm) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="mb-4">
                        <label class="block text-gray-700">Merek dan Tipe Kendaraan</label>
                        <input type="text" class="w-full p-2 border border-gray-300 rounded bg-gray-100" readonly value="{{ $bbm->kendaraan->merek . ' ' . $bbm->kendaraan->tipe }}">
                    </div>
                    <div class="mb-4">
                        <label class="block text-gray-700">Nomor Plat</label>
                        <input type="text" class="w-full p-2 border border-gray-300 rounded bg-gray-100" readonly value="{{ $bbm->kendaraan->plat_nomor }}">
                    </div>
                    <div class="mb-4">
                        <label class="block text-gray-700">Jenis BBM</label>
                        <select name="jenis_bbm" class="w-full p-2 border border-gray-300 rounded" required>
                            <option value="Pertalite" {{ $bbm->jenis_bbm == 'Pertalite' ? 'selected' : '' }}>Pertalite</option>
                            <option value="Pertamax" {{ $bbm->jenis_bbm == 'Pertamax' ? 'selected' : '' }}>Pertamax</option>
                            <option value="Pertamax Turbo" {{ $bbm->jenis_bbm == 'Pertamax Turbo' ? 'selected' : '' }}>Pertamax Turbo</option>
                            <option value="Dexlite" {{ $bbm->jenis_bbm == 'Dexlite' ? 'selected' : '' }}>Dexlite</option>
                            <option value="Pertamina Dex" {{ $bbm->jenis_bbm == 'Pertamina Dex' ? 'selected' : '' }}>Pertamina Dex</option>
                        </select>
                    </div>
                    <div class="mb-4">
                        <label class="block text-gray-700">Tanggal Pengisian BBM</label>
                        <input type="date" name="tgl_isi" class="w-full p-2 border border-gray-300 rounded" value="{{ $bbm->tgl_isi }}" required>
                    </div>
                    <div class="mb-4">
                        <label class="block text-gray-700">Nominal (Rp)</label>
                        <input type="text" id="nominalInput" name="nominal" class="w-full p-2 border border-gray-300 rounded" value="{{ number_format($bbm->nominal, 0, '', '.') }}" required>
                    </div>
                    <div class="flex justify-between">
                        <a href="{{ route('admin.pengisianBBM') }}" class="bg-gray-500 text-white px-4 py-2 rounded">Kembali</a>
                        <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">Simpan Perubahan</button>
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