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
                <form id="bbmForm" action="{{ route('admin.pengisianBBM.store') }}" method="POST">
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

        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('bbmForm');
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
                        text: "Apakah Anda yakin ingin menyimpan data pengisian BBM ini?",
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
                                    text: "Data pengisian BBM berhasil disimpan",
                                    icon: "success",
                                    confirmButtonColor: "#3085d6",
                                    confirmButtonText: "OK"
                                }).then(() => {
                                    // Redirect ke halaman admin.pengisianBBM SETELAH klik OK
                                    window.location.href = '/admin/pengisianBBM';
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
                console.error('Form dengan id bbmForm tidak ditemukan');
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

            document.getElementById('nominalInput').addEventListener('input', function (e) {
                let value = e.target.value.replace(/\D/g, '');
                e.target.value = value.replace(/\B(?=(\d{3})+(?!\d))/g, ".");
            });
        </script>
    </body>
    </html>
</x-app-layout>
