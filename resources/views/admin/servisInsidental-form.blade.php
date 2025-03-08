<x-app-layout>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Form Input Servis Insidental Kendaraan</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <!-- SweetAlert2 CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/11.7.12/sweetalert2.min.css">
    <!-- SweetAlert2 JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/11.7.12/sweetalert2.all.min.js"></script>
</head>   
<body class="bg-gray-100">
    <div class="flex">
        <!-- Main Content -->
        <div class="w-4/5 p-8">
            <h1 class="text-3xl font-bold mb-8">Form Input Servis Insidental Kendaraan</h1>
            <div class="bg-white p-8 rounded shadow-md">
                <h2 class="text-xl font-semibold mb-4">Detail Servis</h2>
                <form id="serviceForm" action="{{ route('admin.servisInsidental.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block text-gray-700">Merk dan Tipe Kendaraan</label>
                            <input type="text" id="merkTipe" name="merk_tipe" 
                                   class="w-full p-2 border border-gray-300 rounded bg-gray-100" 
                                   readonly 
                                   value="{{ request('merk') . ' ' . request('tipe') }}">
                            <input type="hidden" id="id_kendaraan" name="id_kendaraan" value="{{ request('id_kendaraan') }}">
                            <input type="hidden" id="id_peminjaman" name="id_peminjaman" value="">
                        </div>
                        <div>
                            <label class="block text-gray-700">Nomor Plat</label>
                            <input type="text" id="nomorPlat" class="w-full p-2 border border-gray-300 rounded bg-gray-100" 
                                   readonly value="{{ request('plat') }}">
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <div>
                            <label class="block text-gray-700">Tanggal Servis</label>
                            <input type="date" name="tgl_servis" class="w-full p-2 border border-gray-300 rounded" required>
                        </div>
                        <div>
                            <label class="block text-gray-700">Jumlah Pembayaran</label>
                            <input type="text" id="hargaInput" name="harga" class="w-full p-2 border border-gray-300 rounded" required>
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
                        <!-- Upload Bukti Pembayaran Servis -->
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Upload Bukti Pembayaran Servis</label>
                            <div class="flex flex-col items-center">
                                <label id="uploadLabelBuktiBayar" class="cursor-pointer flex flex-col items-center justify-center w-32 h-14 border border-blue-500 text-blue-600 font-medium rounded-lg hover:bg-blue-100 transition">
                                    <span id="uploadTextBuktiBayar" class="text-sm">Upload</span>
                                    <input type="file" name="bukti_bayar" id="fotoInputBuktiBayar" class="hidden" accept=".jpg, .jpeg, .png, .pdf" required>
                                </label>
                                <p id="fileNameBuktiBayar" class="text-sm mt-2 text-gray-600 hidden"></p>
                                <a href="#" id="removeFileBuktiBayar" class="hidden text-red-600 font-medium text-sm mt-2 hover:underline text-center">Remove</a>
                            </div>
                        </div>
                    
                        <!-- Garis Pemisah -->
                        <div class="h-20 bg-gray-300" style="width: 0.5px;"></div>
                    
                        <!-- Upload Bukti Fisik Servis -->
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Upload Bukti Fisik Servis</label>
                            <div class="flex flex-col items-center">
                                <label id="uploadLabelBuktiFisik" class="cursor-pointer flex flex-col items-center justify-center w-32 h-14 border border-blue-500 text-blue-600 font-medium rounded-lg hover:bg-blue-100 transition">
                                    <span id="uploadTextBuktiFisik" class="text-sm">Upload</span>
                                    <input type="file" name="bukti_fisik" id="fotoInputBuktiFisik" class="hidden" accept=".jpg, .jpeg, .png, .pdf" required>
                                </label>
                                <p id="fileNameBuktiFisik" class="text-sm mt-2 text-gray-600 hidden"></p>
                                <a href="#" id="removeFileBuktiFisik" class="hidden text-red-600 font-medium text-sm mt-2 hover:underline text-center">Remove</a>
                            </div>
                        </div>
                    
                        <!-- Garis Pemisah -->
                        <div class="h-20 bg-gray-300" style="width: 0.5px;"></div>
                    
                        <!-- Image Requirements -->
                        <div>
                            <p class="font-medium text-gray-700">Image requirements:</p>
                            <ul class="text-sm text-gray-600">
                                <li>1. Format: PNG, JPG, atau PDF</li>
                                <li>2. Ukuran maksimal: 2MB</li>
                                <li>3. Foto harus jelas dan tidak buram</li>
                            </ul>
                        </div>
                    </div>
                    
                    <!-- Tombol submit dan navigasi -->
                    <div class="flex justify-between items-center">
                        <!-- Tombol Kembali (di kiri) -->
                        <a href="{{ url()->previous() }}" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-700 shadow-md">
                            Kembali
                        </a>
                    
                        <!-- Tombol Batal dan Simpan (di kanan) -->
                        <div class="flex space-x-2">
                            <button type="button" onclick="history.back()" class="bg-red-500 text-white px-4 py-2 rounded">Batal</button>
                            <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-700">Simpan</button>
                        </div>
                    </div>
                </form>
                
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('serviceForm');
            if (form) {
                // Menambahkan fungsi validasi ukuran file
                function validateFileSize() {
                    const fileInput = form.querySelector('input[type="file"]');
                    if (fileInput && fileInput.files.length > 0) {
                        const maxSize = 2 * 1024 * 1024;
                        if (fileInput.files[0].size > maxSize) {
                            return false;
                        }
                    }
                    return true;
                }
        
                form.addEventListener('submit', function(event) {
                    event.preventDefault();
                    
                    // Validasi ukuran file
                    if (!validateFileSize()) {
                        Swal.fire({
                            title: "Gagal!",
                            text: "Ukuran file tidak boleh melebihi 2MB",
                            icon: "error",
                            confirmButtonColor: "#d33",
                            confirmButtonText: "OK"
                        });
                        return;
                    }
                    
                    // Konfirmasi simpan data
                    Swal.fire({
                        title: "Konfirmasi",
                        text: "Apakah Anda yakin ingin menyimpan data servis ini?",
                        icon: "warning",
                        showCancelButton: true,
                        confirmButtonColor: "#3085d6",
                        cancelButtonColor: "#d33",
                        confirmButtonText: "Ya, Simpan!",
                        cancelButtonText: "Batal"
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // Tampilkan loading
                            Swal.fire({
                                title: "Memproses...",
                                text: "Mohon tunggu sebentar",
                                allowOutsideClick: false,
                                didOpen: () => {
                                    Swal.showLoading();
                                }
                            });
                            
                            // Menggunakan AJAX untuk submit form
                            const formData = new FormData(form);
                            
                            // Tambahkan parameter ajax=true untuk memudahkan deteksi di server
                            formData.append('ajax', 'true');
                            
                            fetch(form.action, {
                                method: form.method,
                                body: formData,
                                headers: {
                                    'X-Requested-With': 'XMLHttpRequest'
                                }
                            })
                            .then(response => {
                                // Anggap saja berhasil jika status 2xx, bahkan jika bukan JSON
                                if (response.ok) {
                                    return { status: 'success' };
                                } else {
                                    throw new Error('Server error');
                                }
                            })
                            .then(data => {
                                // Notifikasi sukses
                                Swal.fire({
                                    title: "Berhasil!",
                                    text: "Data servis berhasil disimpan",
                                    icon: "success",
                                    confirmButtonColor: "#3085d6",
                                    confirmButtonText: "OK"
                                }).then(() => {
                                    // Redirect ke halaman admin.servisInsidental SETELAH klik OK
                                    window.location.href = '/admin/servisInsidental';
                                });
                            })
                            .catch(error => {
                                console.error('Error:', error);
                                // Notifikasi gagal
                                Swal.fire({
                                    title: "Gagal!",
                                    text: "Terjadi kesalahan saat menyimpan data",
                                    icon: "error",
                                    confirmButtonColor: "#d33",
                                    confirmButtonText: "OK"
                                });
                            });
                        }
                    });
                });
            } else {
                console.error('Form dengan id serviceForm tidak ditemukan');
            }
        });
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Cek apakah ada flash message success
            <?php if (session('success')): ?>
                Swal.fire({
                    title: "Berhasil!",
                    text: "<?php echo session('success'); ?>",
                    icon: "success",
                    confirmButtonColor: "#3085d6",
                    confirmButtonText: "OK"
                });
            <?php endif; ?>
            
            // Cek apakah ada flash message error
            <?php if (session('error')): ?>
                Swal.fire({
                    title: "Gagal!",
                    text: "<?php echo session('error'); ?>",
                    icon: "error",
                    confirmButtonColor: "#d33",
                    confirmButtonText: "OK"
                });
            <?php endif; ?>
        });

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

        document.getElementById('hargaInput').addEventListener('input', function (e) {
            let value = e.target.value.replace(/\D/g, '');
            e.target.value = value.replace(/\B(?=(\d{3})+(?!\d))/g, ".");
        });

        // File upload handler for bukti bayar
        const fotoInputBuktiBayar = document.getElementById('fotoInputBuktiBayar');
        const uploadLabelBuktiBayar = document.getElementById('uploadLabelBuktiBayar');
        const uploadTextBuktiBayar = document.getElementById('uploadTextBuktiBayar');
        const removeFileBuktiBayar = document.getElementById('removeFileBuktiBayar');

        fotoInputBuktiBayar.addEventListener('change', function() {
            if (this.files.length > 0) {
                uploadTextBuktiBayar.textContent = this.files[0].name;
                removeFileBuktiBayar.classList.remove('hidden');
            }
        });

        removeFileBuktiBayar.addEventListener('click', function(e) {
            e.preventDefault();
            fotoInputBuktiBayar.value = '';
            uploadTextBuktiBayar.textContent = 'Upload Photo';
            removeFileBuktiBayar.classList.add('hidden');
        });

        // File upload handler for bukti bayar
        const fotoInputBuktiFisik = document.getElementById('fotoInputBuktiFisik');
        const uploadLabelBuktiFisik = document.getElementById('uploadLabelBuktiFisik');
        const uploadTextBuktiFisik = document.getElementById('uploadTextBuktiFisik');
        const removeFileBuktiFisik = document.getElementById('removeFileBuktiFisik');

        fotoInputBuktiFisik.addEventListener('change', function() {
            if (this.files.length > 0) {
                uploadTextBuktiFisik.textContent = this.files[0].name;
                removeFileBuktiFisik.classList.remove('hidden');
            }
        });

        removeFileBuktiFisik.addEventListener('click', function(e) {
            e.preventDefault();
            fotoInputBuktiFisik.value = '';
            uploadTextBuktiFisik.textContent = 'Upload Photo';
            removeFileBuktiFisik.classList.add('hidden');
        });
    </script>
</body>
</html>
</x-app-layout>