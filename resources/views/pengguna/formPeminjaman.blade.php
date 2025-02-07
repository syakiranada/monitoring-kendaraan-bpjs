<x-app-layout>
    <div class="min-h-screen flex items-center justify-center py-12 px-4">
        <div class="max-w-2xl w-full bg-white p-6 rounded-lg shadow-lg">
            <h2 class="text-2xl font-bold mb-6 text-center">Form Peminjaman</h2>
            <form id="mockup-form">
                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Detail Kendaraan</label>
                        <input type="text" value="Toyota Avanza - 2020" class="w-full p-2.5 border rounded-lg bg-gray-100" readonly>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Plat Nomor</label>
                        <input type="text" value="B 1234 XYZ" class="w-full p-2.5 border rounded-lg bg-gray-100" readonly>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Bayar</label>
                    <input type="date" class="w-full p-2.5 border rounded-lg" required>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nominal Tagihan</label>
                    <div class="relative">
                        <span class="absolute left-2 top-1/2 transform -translate-y-1/2 text-gray-500">Rp</span>
                        <input type="text" class="w-full pl-8 p-2.5 border rounded-lg" required>
                    </div>
                </div>

                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Upload Bukti Pembayaran Pajak</label>
                    <input type="file" class="w-full p-2.5 border rounded-lg">
                </div>
                
                <div class="flex justify-end space-x-4 mb-2">
                    <button type="button" class="bg-red-600 text-white px-6 py-2.5 rounded-lg font-semibold hover:bg-red-700 transition">Batal</button>
                    <button type="submit" class="bg-blue-600 text-white px-6 py-2.5 rounded-lg font-semibold hover:bg-blue-700 transition">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
