<x-app-layout>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Form Input Servis Insidental Kendaraan</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
</head>
<body class="bg-gray-100">
    <div class="flex">
        <!-- Main Content -->
        <div class="w-4/5 p-8">
            <h1 class="text-3xl font-bold mb-8">Form Input Servis Insidental Kendaraan</h1>
            <div class="bg-white p-8 rounded shadow-md">
                <h2 class="text-xl font-semibold mb-4">Detail Servis</h2>
                <form action="{{ route('servisInsidental.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block text-gray-700">Merk dan Tipe Kendaraan</label>
                            <input type="text" id="merkTipe" class="w-full p-2 border border-gray-300 rounded bg-gray-100" readonly>
                            <input type="hidden" id="id_kendaraan" name="id_kendaraan">
                        </div>
                        <div>
                            <label class="block text-gray-700">Nomor Plat</label>
                            <input type="text" id="nomorPlat" class="w-full p-2 border border-gray-300 rounded bg-gray-100" readonly>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block text-gray-700">Tanggal Servis</label>
                            <input type="date" name="tgl_servis" class="w-full p-2 border border-gray-300 rounded" required>
                        </div>
                        <div>
                            <label class="block text-gray-700">Jumlah Pembayaran</label>
                            <input type="number" name="harga" class="w-full p-2 border border-gray-300 rounded" required>
                        </div>
                    </div>
                    <div class="mb-4">
                        <label class="block text-gray-700">Lokasi Servis</label>
                        <input type="text" name="lokasi" class="w-full p-2 border border-gray-300 rounded" required>
                    </div>
                    <div class="mb-4">
                        <label class="block text-gray-700">Deskripsi Servis</label>
                        <textarea name="deskripsi" class="w-full p-2 border border-gray-300 rounded" rows="3" required></textarea>
                    </div>
                    <div class="mb-6 flex justify-start space-x-4">
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Upload Bukti Pembayaran Servis</label>
                            <div class="flex flex-col items-center">
                                <label id="uploadLabelBuktiBayar" class="cursor-pointer flex flex-col items-center justify-center w-32 h-14 border border-blue-500 text-blue-600 font-medium rounded-lg hover:bg-blue-100 transition">
                                    <span id="uploadTextBuktiBayar" class="text-sm">Upload Photo</span>
                                    <input type="file" name="bukti_bayar" id="fotoInputBuktiBayar" class="hidden">
                                </label>
                                <a href="#" id="removeFileBuktiBayar" class="hidden text-red-600 font-medium text-sm mt-2 hover:underline text-center">Remove</a>
                            </div>
                        </div>
                        <div class="h-20 bg-gray-300" style="width: 0.5px;"></div>
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Upload Bukti Fisik Servis</label>
                            <div class="flex flex-col items-center">
                                <label id="uploadLabelBuktiFisik" class="cursor-pointer flex flex-col items-center justify-center w-32 h-14 border border-blue-500 text-blue-600 font-medium rounded-lg hover:bg-blue-100 transition">
                                    <span id="uploadTextBuktiFisik" class="text-sm">Upload Photo</span>
                                    <input type="file" name="bukti_fisik" id="fotoInputBuktiFisik" class="hidden">
                                </label>
                                <a href="#" id="removeFileBuktiFisik" class="hidden text-red-600 font-medium text-sm mt-2 hover:underline text-center">Remove</a>
                            </div>
                        </div>
                    </div>
                    <div class="flex justify-end">
                        <button type="button" onclick="history.back()" class="bg-red-500 text-white px-4 py-2 rounded mr-2">Batal</button>
                        <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        document.querySelectorAll(".kendaraan-row").forEach(row => {
            row.addEventListener("click", async function () {
                let id_kendaraan = this.getAttribute("data-id");
                if (!id_kendaraan) return;

                try {
                    let response = await fetch(`/api/kendaraan/${id_kendaraan}`);
                    let data = await response.json();

                    document.getElementById("merkTipe").value = `${data.merk} ${data.tipe}`;
                    document.getElementById("nomorPlat").value = data.plat_nomor;
                    document.getElementById("id_kendaraan").value = id_kendaraan;
                } catch (error) {
                    console.error("Error fetching kendaraan data:", error);
                }
            });
        });
    });
</script>

</x-app-layout>
