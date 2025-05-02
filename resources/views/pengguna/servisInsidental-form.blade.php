<x-app-layout>
    <a href="{{  url()->previous()  }}" class="flex items-center text-blue-600 font-semibold hover:underline mb-5">
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"></path>
        </svg>
        Kembali
    </a>
        <div class="flex justify-center ">
            <!-- Main Content -->
            <div class="w-4/5p-8">
                <div class="bg-white p-8 rounded shadow-md">                
                    <h1 class="text-3xl font-bold mb-8 text-center">Form Input Servis Insidental Kendaraan</h1>
                    <form id="serviceForm" action="{{ route('servisInsidental.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="grid grid-cols-2 gap-4 mb-4">
                            <div>
                                <label class="block text-gray-700">Merk dan Tipe Kendaraan</label>
                                <input type="text" id="merkTipe" name="merk_tipe" 
                                       class="w-full p-2 border border-gray-300 rounded bg-gray-100" 
                                       readonly 
                                       value="{{ request('merk') . ' ' . request('tipe') }}">
                                <input type="hidden" id="id_kendaraan" name="id_kendaraan" value="{{ request('id_kendaraan') }}">
                                <input type="hidden" id="id_peminjaman" name="id_peminjaman" value="{{ request('id_peminjaman') }}">
                            </div>
                            <div>
                                <label class="block text-gray-700">Nomor Plat</label>
                                <input type="text" id="nomorPlat" class="w-full p-2 border border-gray-300 rounded bg-gray-100" 
                                       readonly value="{{ request('plat') }}">
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-4 mb-4">
                            @php
                            $tglMulai = optional($peminjaman)->tgl_mulai;
                            $tglSelesai = optional($peminjaman)->tgl_selesai;
                        @endphp

                            <div>
                                <label class="block text-gray-700">Tanggal Servis</label>
                                <input 
                                type="date" 
                                name="tgl_servis" 
                                class="w-full p-2 border border-gray-300 rounded" 
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
                                <div class="relative">
                                    <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500">Rp</span>
                                    <input type="text" id="hargaInput" name="harga" class="w-full pl-10 p-2 border border-gray-300 rounded" required>
                                </div>
                                <div id="hargaAlert" class="text-red-500 text-sm mt-1"></div>
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
                                        <input type="file" name="bukti_bayar" id="fotoInputBuktiBayar" class="hidden" accept=".jpg, .jpeg, .png, .pdf">
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
                                        <input type="file" name="bukti_fisik" id="fotoInputBuktiFisik" class="hidden" accept=".jpg, .jpeg, .png, .pdf">
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
                        <div class="flex justify-end items-center">
                            {{--  <!-- Tombol Kembali (di kiri) -->
                            <a href="{{ url()->previous() }}" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-700 shadow-md">
                                Kembali
                            </a>  --}}
                        
                            <!-- Tombol Batal dan Simpan (di kanan) -->
                            <div class="flex space-x-2">
                                {{--  <button type="button" onclick="history.back()" class="bg-red-500 text-white px-4 py-2 rounded">Batal</button>  --}}
                                <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-700">Simpan</button>
                            </div>
                        </div>
                    </form>
                    
                </div>
            </div>
        </div>
    
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            function shortenFileName(fileName, maxLength = 15) {
                if (!fileName || typeof fileName !== "string") return "";
                if (fileName.length <= maxLength) return fileName;
            
                const lastDot = fileName.lastIndexOf(".");
                const hasExtension = lastDot !== -1 && lastDot < fileName.length - 1;
                const extension = hasExtension ? fileName.slice(lastDot + 1) : "";
                const baseName = hasExtension ? fileName.slice(0, lastDot) : fileName;
            
                const allowedBaseLength = maxLength - (extension.length + 4);
                const trimmedBase = baseName.slice(0, Math.max(allowedBaseLength, 0));
            
                return trimmedBase + "..." + (extension ? "." + extension : "");
            }

            document.addEventListener('DOMContentLoaded', function() {
                const lokasiInput = document.querySelector('input[name="lokasi"]');
                const deskripsiTextarea = document.querySelector('textarea[name="deskripsi"]');
                const lokasiAlert = document.createElement('div');
                const deskripsiAlert = document.createElement('div');
              
                lokasiAlert.className = 'text-red-500 text-sm mt-1';
                deskripsiAlert.className = 'text-red-500 text-sm mt-1';
              
                lokasiInput.parentNode.insertBefore(lokasiAlert, lokasiInput.nextSibling);
                deskripsiTextarea.parentNode.insertBefore(deskripsiAlert, deskripsiTextarea.nextSibling);
              
                lokasiInput.addEventListener('input', function() {
                  if (lokasiInput.value.length > 100) {
                    lokasiAlert.textContent = 'Lokasi Servis tidak boleh lebih dari 100 karakter.';
                  } else {
                    lokasiAlert.textContent = '';
                  }
                });
              
                deskripsiTextarea.addEventListener('input', function() {
                  if (deskripsiTextarea.value.length > 200) {
                    deskripsiAlert.textContent = 'Deskripsi Servis tidak boleh lebih dari 200 karakter.';
                  } else {
                    deskripsiAlert.textContent = '';
                  }
                });
    
                const hargaInput = document.getElementById('hargaInput');
                const hargaAlert = document.getElementById('hargaAlert');
                const maxHarga = 1000000000000;
                
                hargaInput.addEventListener('input', function (e) {
                    let rawValue = e.target.value.replace(/\D/g, '');
                    let numericValue = parseInt(rawValue) || 0;
                
                    // Cek dan tampilkan alert
                    if (numericValue > maxHarga) {
                        hargaAlert.textContent = 'Nonimal melebihi batas maksimum Rp 1.000.000.000.000.';
                        numericValue = maxHarga;
                    } else {
                        hargaAlert.textContent = '';
                    }
                
                    // Format angka ribuan
                    e.target.value = numericValue.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
                });            
              });

            {{--  document.addEventListener('DOMContentLoaded', function () {
                const dateInput = document.querySelector('input[name="tgl_servis"]');
        
                const minDate = new Date(dateInput.min);
                const maxDate = new Date(dateInput.max);
        
                dateInput.addEventListener('change', function () {
                    const selectedDate = new Date(this.value);
        
                    if (selectedDate < minDate || selectedDate > maxDate) {
                        alert('Tanggal servis harus di antara tanggal peminjaman.');
                        this.value = '';
                    }
                });
            });  --}}
        </script>
        
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
                                    // Redirect ke halaman pengguna.servisInsidental SETELAH klik OK
                                    window.location.href = '/servisInsidental';
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
                    uploadTextBuktiBayar.textContent = shortenFileName(this.files[0].name);
                    removeFileBuktiBayar.classList.remove('hidden');
                }
            });

            removeFileBuktiBayar.addEventListener('click', function(e) {
                e.preventDefault();
                fotoInputBuktiBayar.value = '';
                uploadTextBuktiBayar.textContent = 'Upload';
                removeFileBuktiBayar.classList.add('hidden');
            });

            // File upload handler for bukti bayar
            const fotoInputBuktiFisik = document.getElementById('fotoInputBuktiFisik');
            const uploadLabelBuktiFisik = document.getElementById('uploadLabelBuktiFisik');
            const uploadTextBuktiFisik = document.getElementById('uploadTextBuktiFisik');
            const removeFileBuktiFisik = document.getElementById('removeFileBuktiFisik');

            fotoInputBuktiFisik.addEventListener('change', function() {
                if (this.files.length > 0) {
                    uploadTextBuktiFisik.textContent = shortenFileName(this.files[0].name);
                    removeFileBuktiFisik.classList.remove('hidden');
                }
            });

            removeFileBuktiFisik.addEventListener('click', function(e) {
                e.preventDefault();
                fotoInputBuktiFisik.value = '';
                uploadTextBuktiFisik.textContent = 'Upload';
                removeFileBuktiFisik.classList.add('hidden');
            });
        </script>
    </x-app-layout>