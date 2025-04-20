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
                <h1 class="text-3xl font-bold mb-8">Form Edit Servis Insidental Kendaraan</h1>
                <div class="bg-white p-8 rounded shadow-md">
                    <h2 class="text-xl font-semibold mb-4">Detail Servis</h2>
                    <form id="serviceForm" action="{{ route('servisInsidental.update', $servis->id_servis_insidental) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="grid grid-cols-2 gap-4 mb-4">
                            <div>
                                <label class="block text-gray-700">Merk dan Tipe Kendaraan</label>
                                <input type="text" class="w-full p-2 border border-gray-300 rounded bg-gray-100" readonly value="{{ $servis->kendaraan->merk . ' ' . $servis->kendaraan->tipe }}">
                            </div>
                            <div>
                                <label class="block text-gray-700">Nomor Plat</label>
                                <input type="text" class="w-full p-2 border border-gray-300 rounded bg-gray-100" readonly value="{{ $servis->kendaraan->plat_nomor }}">
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-4 mb-4">
                        @php
                            $tglMulai = optional($servis->peminjaman)->tgl_mulai;
                            $tglSelesai = optional($servis->peminjaman)->tgl_selesai;
                        @endphp

                            <div>
                                <label class="block text-gray-700">Tanggal Servis</label>
                                <input 
                                type="date" 
                                name="tgl_servis" 
                                class="w-full p-2 border border-gray-300 rounded"
                                value="{{ $servis->tgl_servis }}" 
                                required
                                @if($tglMulai) min="{{ \Carbon\Carbon::parse($tglMulai)->format('Y-m-d') }}" @endif
                                @if($tglSelesai) max="{{ \Carbon\Carbon::parse($tglSelesai)->format('Y-m-d') }}" @endif
                            >
                            <small class="text-gray-500 text-sm">
                                Pilih tanggal antara {{ \Carbon\Carbon::parse($tglMulai)->format('d M Y') }} dan {{ \Carbon\Carbon::parse($tglSelesai)->format('d M Y') }}.
                            </small>
                            </div>

                            <div>
                                <label class="block text-gray-700">Jumlah Pembayaran</label>
                                <input type="text" id="hargaInput" name="harga" class="w-full p-2 border border-gray-300 rounded" value="{{ number_format($servis->harga, 0, '', '.') }}" required>
                            </div>
                        </div>
                        <div class="mb-4">
                            <label class="block text-gray-700">Lokasi Servis</label>
                            <input type="text" name="lokasi" class="w-full p-2 border border-gray-300 rounded" value="{{ $servis->lokasi }}" required>
                        </div>
                        <div class="mb-4">
                            <label class="block text-gray-700">Deskripsi Servis</label>
                            <textarea name="deskripsi" class="w-full p-2 border border-gray-300 rounded" rows="3" required>{{ $servis->deskripsi ?? '' }}</textarea>
                        </div>
                        <div class="mb-6 flex justify-start space-x-4">
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Upload Bukti Pembayaran Servis</label>
                                <div class="flex flex-col items-center">
                                    <label id="uploadLabelBuktiBayar" class="cursor-pointer flex flex-col items-center justify-center w-32 h-14 border border-blue-500 text-blue-600 font-medium rounded-lg hover:bg-blue-100 transition">
                                        <span id="uploadTextBuktiBayar" class="text-sm">
                                            {{ $servis->bukti_bayar ? 'Ganti File' : 'Upload File' }}
                                        </span>
                                        <input type="file" name="bukti_bayar" id="fotoInputBuktiBayar" class="hidden" accept=".jpg, .jpeg, .png, .pdf">
                                    </label>
                                    @if($servis->bukti_bayar)
                                        <div class="mt-2 text-sm text-gray-700">File saat ini: {{ basename($servis->bukti_bayar) }}</div>
                                        <a href="#" id="removeFileBuktiBayar" class="text-red-600 font-medium text-sm mt-1 hover:underline text-center">Remove</a>
                                        <input type="hidden" name="bukti_bayar_lama" value="{{ $servis->bukti_bayar }}">
                                    @else
                                        <a href="#" id="removeFileBuktiBayar" class="hidden text-red-600 font-medium text-sm mt-2 hover:underline text-center">Remove</a>
                                        <input type="hidden" name="bukti_bayar_lama" value="">
                                    @endif
                                </div>
                            </div>
                        
                            <div class="h-20 bg-gray-300" style="width: 0.5px;"></div>
                        
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Upload Bukti Fisik Servis</label>
                                <div class="flex flex-col items-center">
                                    <label id="uploadLabelBuktiFisik" class="cursor-pointer flex flex-col items-center justify-center w-32 h-14 border border-blue-500 text-blue-600 font-medium rounded-lg hover:bg-blue-100 transition">
                                        <span id="uploadTextBuktiFisik" class="text-sm">
                                            {{ $servis->bukti_fisik ? 'Ganti File' : 'Upload File' }}
                                        </span>
                                        <input type="file" name="bukti_fisik" id="fotoInputBuktiFisik" class="hidden" accept=".jpg, .jpeg, .png, .pdf">
                                    </label>
                                    @if($servis->bukti_fisik)
                                        <div class="mt-2 text-sm text-gray-700">File saat ini: {{ basename($servis->bukti_fisik) }}</div>
                                        <a href="#" id="removeFileBuktiFisik" class="text-red-600 font-medium text-sm mt-1 hover:underline text-center">Remove</a>
                                        <input type="hidden" name="bukti_fisik_lama" value="{{ $servis->bukti_fisik }}">
                                    @else
                                        <a href="#" id="removeFileBuktiFisik" class="hidden text-red-600 font-medium text-sm mt-2 hover:underline text-center">Remove</a>
                                        <input type="hidden" name="bukti_fisik_lama" value="">
                                    @endif
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

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                console.log('Bukti Bayar Path:', '{{ $servis->bukti_bayar ?? 'KOSONG' }}');
                console.log('Bukti Fisik Path:', '{{ $servis->bukti_fisik ?? 'KOSONG' }}');
                
                // Tambahkan log untuk input hidden
                console.log('Bukti Bayar Lama Input:', 
                    document.querySelector('input[name="bukti_bayar_lama"]').value
                );
                console.log('Bukti Fisik Lama Input:', 
                    document.querySelector('input[name="bukti_fisik_lama"]').value
                );
            });
        </script>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const form = document.getElementById('serviceForm');
                if (form) {
                    // Fungsi validasi untuk semua file pada form
                    function validateFileSize() {
                        // Ambil semua file input di form
                        const fileInputs = form.querySelectorAll('input[type="file"]');
                        let isValid = true;
                        
                        // Periksa setiap file input
                        fileInputs.forEach(function(fileInput) {
                            if (fileInput.files.length > 0) {
                                const maxSize = 2 * 1024 * 1024; // 2MB
                                if (fileInput.files[0].size > maxSize) {
                                    // Jika ada file yang melebihi ukuran maksimal
                                    isValid = false;
                                    console.log('File terlalu besar:', fileInput.name, fileInput.files[0].size);
                                }
                            }
                        });
                        
                        return isValid;
                    }
                    
                    // Fungsi validasi untuk satu file pada saat input berubah
                    function validateSingleFile(input) {
                        if (input.files.length > 0) {
                            const maxSize = 2 * 1024 * 1024; // 2MB
                            if (input.files[0].size > maxSize) {
                                Swal.fire({
                                    title: "Gagal!",
                                    text: `File ${input.name} melebihi ukuran maksimal 2MB`,
                                    icon: "error",
                                    confirmButtonColor: "#d33",
                                    confirmButtonText: "OK"
                                });
                                input.value = ''; // Reset input file
                            }
                        }
                    }
                    
                    // Event listener untuk validasi file saat dipilih
                    document.getElementById('fotoInputBuktiBayar').addEventListener('change', function() {
                        validateSingleFile(this);
                    });
                    
                    document.getElementById('fotoInputBuktiFisik').addEventListener('change', function() {
                        validateSingleFile(this);
                    });
                    
                    // Event listener untuk form submission
                    form.addEventListener('submit', function(event) {
                        // Hentikan form submission terlebih dahulu
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
                                    method: 'POST', // Gunakan POST dengan method spoofing di Laravel
                                    body: formData,
                                    headers: {
                                        'X-Requested-With': 'XMLHttpRequest',
                                        'Accept': 'application/json' // Minta server mengembalikan JSON
                                    }
                                })
                                .then(response => {
                                    // Periksa header content-type untuk menentukan format respons
                                    const contentType = response.headers.get('content-type');
                                    if (contentType && contentType.includes('application/json')) {
                                        return response.json().then(data => {
                                            return { status: response.status, data: data };
                                        });
                                    } else {
                                        // Jika bukan JSON, anggap sebagai teks biasa
                                        return response.text().then(text => {
                                            // Jika respons sukses tapi bukan JSON, anggap berhasil
                                            if (response.ok) {
                                                return { status: response.status, data: { success: true } };
                                            } else {
                                                throw new Error('Response format is not JSON: ' + text.substring(0, 100));
                                            }
                                        });
                                    }
                                })
                                .then(result => {
                                    const { status, data } = result;
                                    
                                    if (status >= 200 && status < 300) {
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
                                        } else {
                                            // Notifikasi sukses
                                            Swal.fire({
                                                title: "Berhasil!",
                                                text: "Data servis berhasil diperbarui",
                                                icon: "success",
                                                confirmButtonColor: "#3085d6",
                                                confirmButtonText: "OK"
                                            }).then(() => {
                                                // Redirect ke halaman servisRutin SETELAH klik OK
                                                window.location.href = '/servisInsidental';
                                            });
                                        }
                                    } else {
                                        throw new Error('Server returned error status: ' + status);
                                    }
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