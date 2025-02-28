<x-app-layout>

    <!DOCTYPE html>
    <html lang="en">
    <head>
        <title>Form Input Servis Rutin Kendaraan</title>
        <script src="https://cdn.tailwindcss.com"></script>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    </head>
    <body class="bg-gray-100">
        <div class="flex">
            <!-- Main Content -->
            <div class="w-4/5 p-8">
                <h1 class="text-3xl font-bold mb-8">Form Edit Servis Rutin Kendaraan</h1>
                <div class="bg-white p-8 rounded shadow-md">
                    <h2 class="text-xl font-semibold mb-4">Detail Servis</h2>
                    <form id="serviceForm" action="{{ route('admin.servisRutin.update', $servis->id_servis_rutin) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="grid grid-cols-2 gap-4 mb-4">
                            <div>
                                <label class="block text-gray-700">Merk dan Tipe Kendaraan</label>
                                <input type="text" id="merkTipe" name="merk_tipe" 
                                       class="w-full p-2 border border-gray-300 rounded bg-gray-100" 
                                       readonly 
                                       value="{{ $servis->kendaraan->merk }} {{ $servis->kendaraan->tipe }}">
                                <input type="hidden" id="id_kendaraan" name="id_kendaraan" value="{{ $servis->id_kendaraan }}">
                            </div>
                            <div>
                                <label class="block text-gray-700">Nomor Plat</label>
                                <input type="text" id="nomorPlat" name="plat_nomor" class="w-full p-2 border border-gray-300 rounded bg-gray-100" 
                                       readonly value="{{ $servis->kendaraan->plat_nomor }}">
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-4 mb-4">
                            <div>
                                <label class="block text-gray-700">Jadwal Servis</label>
                                <input type="date" id="jadwalServis" name="tgl_servis" class="w-full p-2 border border-gray-300 rounded" 
                                       value="{{ $servis->tgl_servis_selanjutnya }}" readonly>
                            </div>
                            <div>
                                <label class="block text-gray-700">Tanggal Servis Realtime</label>
                                <input type="date" id="tglServisReal" name="tgl_servis_real" class="w-full p-2 border border-gray-300 rounded" 
                                       value="{{ $servis->tgl_servis_real ?? '' }}" required>
                            </div>
                            <div>
                                <label class="block text-gray-700">Tanggal Servis Selanjutnya</label>
                                <input type="date" id="tglServisSelanjutnya" name="tgl_servis_selanjutnya"
                                       class="w-full p-2 border border-gray-300 rounded bg-gray-100"
                                       value="{{ $servis->tgl_servis_selanjutnya ?? '' }}"
                                       readonly onfocus="this.removeAttribute('readonly')">
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-4 mb-4">
                            <div>
                                <label class="block text-gray-700">Kilometer Penggunaan</label>
                                <input id="kilometer" type="text" name="kilometer" inputmode="numeric" pattern="\d*" 
                                       class="w-full p-2 border border-gray-300 rounded" 
                                       value="{{ $servis->kilometer ?? '' }}" required data-raw="">
                            </div>
                            <div>
                                <label class="block text-gray-700">Jumlah Pembayaran</label>
                                <input id="harga" type="text" name="harga" inputmode="numeric" pattern="\d*" 
                                       class="w-full p-2 border border-gray-300 rounded" 
                                       value="{{ $servis->harga ?? '' }}" required data-raw="">
                            </div>
                        </div>
                        <div class="mb-4">
                            <label class="block text-gray-700">Lokasi Servis</label>
                            <input type="text" name="lokasi" class="w-full p-2 border border-gray-300 rounded" 
                                   value="{{ $servis->lokasi ?? '' }}" required>
                        </div>
                        <div class="mb-6 flex justify-start space-x-4">
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Upload Bukti Pembayaran Servis</label>
                                <div class="flex flex-col items-center">
                                    <label id="uploadLabelBuktiBayar" class="cursor-pointer flex flex-col items-center justify-center w-32 h-14 border border-blue-500 text-blue-600 font-medium rounded-lg hover:bg-blue-100 transition">
                                        <span id="uploadTextBuktiBayar" class="text-sm">
                                            {{ $servis->bukti_bayar ? 'Ganti File' : 'Upload File' }}
                                        </span>
                                        <input type="file" name="bukti_bayar" id="fotoInputBuktiBayar" class="hidden" accept=".png, .jpg, .jpeg, .pdf">
                                    </label>
                                    @if($servis->bukti_bayar)
                                        <div class="mt-2 text-sm text-gray-700">File saat ini: {{ basename($servis->bukti_bayar) }}</div>
                                        <a href="#" id="removeFileBuktiBayar" class="text-red-600 font-medium text-sm mt-1 hover:underline text-center">Remove</a>
                                    @else
                                        <a href="#" id="removeFileBuktiBayar" class="hidden text-red-600 font-medium text-sm mt-2 hover:underline text-center">Remove</a>
                                    @endif
                                </div>
                            </div>
                            <div class="h-20 bg-gray-300" style="width: 0.5px;"></div>
                            <div class="mb-4">
                                <p class="font-medium text-gray-700">Image requirements:</p>
                                <ul class="text-sm text-gray-600">
                                    <li>1. Format: PNG, JPG, JPEG, atau PDF</li>
                                    <li>2. Ukuran maksimal: 2MB</li>
                                    <li>3. Harus jelas dan tidak buram</li>
                                </ul>
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
            
                    if (result.isConfirmed) {
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
                        
                        // Konfirmasi edit data
                        Swal.fire({
                            title: "Konfirmasi",
                            text: "Apakah Anda yakin ingin mengubah data servis ini?",
                            icon: "warning",
                            showCancelButton: true,
                            confirmButtonColor: "#3085d6",
                            cancelButtonColor: "#d33",
                            confirmButtonText: "Ya, Ubah!",
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
                                
                                const formData = new FormData(form);
                                formData.append('ajax', 'true');
                                formData.append('_method', 'PUT');

                                // Tambahkan CSRF token
                                const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                                formData.append('_token', csrfToken);

                                fetch(form.action, {
                                    method: 'PUT', // Tetap menggunakan POST, Laravel akan mengenali method spoofing
                                    body: formData,
                                    headers: {
                                        'X-Requested-With': 'XMLHttpRequest'
                                    }
                                })
                                .then(response => {
                                    // Anggap saja berhasil jika status 2xx, bahkan jika bukan JSON
                                    fetch(form.action, { method: 'PUT', body: formData, headers: { 'X-Requested-With': 'XMLHttpRequest' } })
    .then(response => response.json()) // Pastikan response dikembalikan dalam JSON
    .then(data => {
        if (data.errors) {
            let errorMessages = Object.values(data.errors).flat().join('\n');
            Swal.fire({
                title: "Gagal!",
                text: errorMessages,
                icon: "error",
                confirmButtonColor: "#d33",
                confirmButtonText: "OK"
            });
            throw new Error(errorMessages);
        }
        Swal.fire({
            title: "Berhasil!",
            text: "Data servis berhasil diperbarui",
            icon: "success",
            confirmButtonColor: "#3085d6",
            confirmButtonText: "OK"
        }).then(() => { window.location.href = '/admin/servisRutin'; });
    })
    .catch(error => {
        console.error('Error:', error);
    });

                                })
                                .then(data => {
                                    // Notifikasi sukses
                                    Swal.fire({
                                        title: "Berhasil!",
                                        text: "Data servis berhasil diperbarui",
                                        icon: "success",
                                        confirmButtonColor: "#3085d6",
                                        confirmButtonText: "OK"
                                    }).then(() => {
                                        // Redirect ke halaman admin.servisRutin SETELAH klik OK
                                        window.location.href = '/admin/servisRutin';
                                    });
                                })
                                .catch(error => {
                                    console.error('Error:', error);
                                    // Notifikasi gagal
                                    Swal.fire({
                                        title: "Gagal!",
                                        text: "Terjadi kesalahan saat memperbarui data",
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

            // Ambil elemen input
            const tglServisReal = document.getElementById('tglServisReal');
            const tglServisSelanjutnya = document.getElementById('tglServisSelanjutnya');
            const id_kendaraan = document.getElementById('id_kendaraan');

            // Tambahkan event listener untuk perubahan pada tanggal servis realtime
            tglServisReal.addEventListener('change', async function () {
                // Ambil nilai tanggal yang dimasukkan
                const tanggalServis = tglServisReal.value;

                if (!tanggalServis) {
                    // Jika tanggal kosong, kosongkan juga tanggal servis selanjutnya
                    tglServisSelanjutnya.value = '';
                    return;
                }

                try {
                    // Ambil ID kendaraan dari input hidden
                    const kendaraanId = id_kendaraan.value;

                    if (!kendaraanId) {
                        throw new Error('ID Kendaraan tidak ditemukan');
                    }

                    // Fetch data frekuensi servis dari server
                    const response = await fetch(`/api/kendaraan/${kendaraanId}`);

                    if (!response.ok) {
                        throw new Error('Gagal mengambil data kendaraan');
                    }

                    const data = await response.json();

                    // Ambil frekuensi servis (dalam bulan)
                    const frekuensiServis = parseInt(data.frekuensi_servis);

                    if (isNaN(frekuensiServis) || frekuensiServis <= 0) {
                        throw new Error('Frekuensi servis tidak valid');
                    }

                    // Hitung tanggal servis selanjutnya dengan menambah bulan dari frekuensi_servis
                    const tanggalServisObj = new Date(tanggalServis);
                    const hariAwal = tanggalServisObj.getDate(); // Simpan tanggal awal

                    tanggalServisObj.setMonth(tanggalServisObj.getMonth() + frekuensiServis);

                    // Cek apakah tanggal berubah akibat akhir bulan (misal, 31 Januari → 28 Februari)
                    if (tanggalServisObj.getDate() !== hariAwal) {
                        // Atur ke tanggal terakhir bulan itu jika terjadi perubahan otomatis
                        tanggalServisObj.setDate(0);
                    }

                    // Format tanggal untuk input type="date" (YYYY-MM-DD)
                    const tahun = tanggalServisObj.getFullYear();
                    const bulan = String(tanggalServisObj.getMonth() + 1).padStart(2, '0'); // getMonth() mulai dari 0
                    const hari = String(tanggalServisObj.getDate()).padStart(2, '0');

                    // Set nilai tanggal servis selanjutnya
                    tglServisSelanjutnya.value = `${tahun}-${bulan}-${hari}`;

                } catch (error) {
                    console.error('Error:', error);
                    alert('Terjadi kesalahan saat menghitung tanggal servis selanjutnya: ' + error.message);
                }
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
                uploadTextBuktiBayar.textContent = '{{ $servis->bukti_bayar ? "Ganti File" : "Upload Photo" }}';
                removeFileBuktiBayar.classList.add('hidden');
                
                @if(!$servis->bukti_bayar)
                removeFileBuktiBayar.classList.add('hidden');
                @endif
            });
        </script>
    </body>
    </html>
</x-app-layout>