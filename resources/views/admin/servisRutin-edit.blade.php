<x-app-layout>
    <a href="{{ route('admin.servisRutin') }}" class="flex items-center text-blue-600 font-semibold hover:underline mb-5">
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"></path>
        </svg>
        Kembali
    </a>
        <div class="flex justify-center">
            <!-- Main Content -->
            <div class="w-4/5">
                <div class="bg-white p-8 rounded shadow-md">
                <h1 class="text-2xl font-bold mb-6 text-center">Form Edit Servis Rutin Kendaraan</h1>
                    <form id="serviceForm" action="{{ route('admin.servisRutin.update', $servis->id_servis_rutin) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="grid grid-cols-2 gap-4 mb-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Merk dan Tipe Kendaraan</label>
                                <input type="text" id="merkTipe" name="merk_tipe" 
                                       class="w-full p-2 border border-gray-300 rounded bg-gray-100" 
                                       readonly 
                                       value="{{ $servis->kendaraan->merk }} {{ $servis->kendaraan->tipe }}">
                                <input type="hidden" id="id_kendaraan" name="id_kendaraan" value="{{ $servis->id_kendaraan }}">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Nomor Plat</label>
                                <input type="text" id="nomorPlat" name="plat_nomor" class="w-full p-2 border border-gray-300 rounded bg-gray-100" 
                                       readonly value="{{ $servis->kendaraan->plat_nomor }}">
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-4 mb-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Jadwal Servis</label>
                                <input type="date" id="jadwalServis" name="tgl_servis" class="w-full p-2 border border-gray-300 rounded" 
                                       value="{{ $servis->tgl_servis_selanjutnya }}" readonly>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Tanggal Servis Realtime</label>
                                <input type="date" id="tglServisReal" name="tgl_servis_real" class="w-full p-2 border border-gray-300 rounded" 
                                       value="{{ $servis->tgl_servis_real ?? '' }}" max="{{ \Carbon\Carbon::now()->format('Y-m-d') }}">
                                <p id="warning-tanggal" class="text-red-500 text-sm mt-1 hidden">Tanggal servis wajib diisi!</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Tanggal Servis Selanjutnya</label>
                                <input type="date" id="tglServisSelanjutnya" name="tgl_servis_selanjutnya"
                                       class="w-full p-2 border border-gray-300 rounded bg-gray-100"
                                       value="{{ $servis->tgl_servis_selanjutnya ?? '' }}"
                                       readonly onfocus="this.removeAttribute('readonly')">
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-4 mb-4">
                            <div>
                                <label for="kilometer" class="block text-sm font-medium text-gray-700">Kilometer Penggunaan</label>
                                <input id="kilometer" type="text" name="kilometer"
                                       class="w-full p-2 border border-gray-300 rounded"
                                       value="{{ number_format($servis->kilometer, 0, '', '.') }}"
                                       data-raw="">
                                <div id="kilometerAlert" class="text-red-500 text-sm mt-1"></div>
                                <p id="warning-kilometer" class="text-red-500 text-sm mt-1 hidden">Kilometer kendaraan wajib diisi!</p>
                            </div>
                        
                            <div>
                                <label for="hargaInput" class="block text-sm font-medium text-gray-700">Jumlah Pembayaran</label>
                                <div class="relative">
                                    <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500">Rp</span>
                                    <input id="hargaInput" type="text" name="harga"
                                           class="w-full pl-10 p-2 border border-gray-300 rounded"
                                           value="{{ number_format($servis->harga, 0, '', '.') }}"
                                           data-raw="">
                                </div>
                                <div id="hargaAlert" class="text-red-500 text-sm mt-1"></div>
                                <p id="warning-harga" class="text-red-500 text-sm mt-1 hidden">Jumlah pembayaran wajib diisi!</p>
                            </div>
                        </div>                        
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700">Lokasi Servis</label>
                            <input type="text" name="lokasi" class="w-full p-2 border border-gray-300 rounded" 
                                   value="{{ $servis->lokasi ?? '' }}">
                            <p id="warning-lokasi" class="text-red-500 text-sm mt-1 hidden">Lokasi servis wajib diisi!</p>
                        </div>
                        <div class="mb-6 flex justify-start space-x-4">
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Upload Bukti Pembayaran Servis</label>
                                <div class="flex flex-col items-center">
                                    <label id="uploadLabelBuktiBayar" class="cursor-pointer flex flex-col items-center justify-center w-32 h-14 border border-blue-500 text-blue-600 font-medium rounded-lg hover:bg-blue-100 transition">
                                        <span id="uploadTextBuktiBayar" class="text-sm">Upload File
                                            {{--  {{ $servis->bukti_bayar ? 'Ganti File' : 'Upload File' }}  --}}
                                        </span>
                                        <input type="file" name="bukti_bayar" id="fotoInputBuktiBayar" class="hidden" value="0" accept=".png, .jpg, .jpeg, .pdf">
                                        <input type="hidden" name="remove_bukti_bayar" id="removeFileFlag" value="0">
                                    </label>

                                    @if($servis->bukti_bayar)
                                        {{--  <div class="mt-2 text-sm text-gray-700">File saat ini: {{ basename($servis->bukti_bayar) }}</div>  --}}
                                        <a href="#" id="removeFileBuktiBayar" class="text-red-600 font-medium text-sm mt-1 hover:underline text-center">Remove</a>
                                        <input type="hidden" name="bukti_bayar_lama" value="{{ $servis->bukti_bayar }}">
                                    @else
                                        <a href="#" id="removeFileBuktiBayar" class="hidden text-red-600 font-medium text-sm mt-2 hover:underline text-center">Remove</a>
                                        <input type="hidden" name="bukti_bayar_lama" value="">
                                    @endif
                                </div>
                                <p id="warning-bukti-bayar" class="text-red-500 text-sm mt-1 hidden">Bukti pembayaran wajib diunggah!</p>
                                <p id="warning-bukti-bayar-2mb" class="text-red-500 text-sm mt-1 hidden">Bukti pembayaran melebihi 2MB!</p>
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
                            <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">Simpan Perubahan</button>
                        </div>                        
                    </form>
                </div>
            </div>
        </div>

        <style>
            .swal2-cancel-gray {
                background-color: #6c757d !important;
                color: white !important;
                border: none !important;
            }
            
            .swal2-confirm-blue {
                background-color: #3085d6 !important;
                color: white !important;
                border: none !important;
            }
        </style> 

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

            document.addEventListener('DOMContentLoaded', function () {
                const form = document.getElementById('serviceForm');
                // Ambil tanggal hari ini
                const today = new Date();
                const year = today.getFullYear();
                const month = String(today.getMonth() + 1).padStart(2, '0');
                const day = String(today.getDate()).padStart(2, '0');
                const todayStr = year + '-' + month + '-' + day;

                // Set atribut max pada input tanggal
                const tglServisReal = document.getElementById('tglServisReal');
                if (tglServisReal) {
                tglServisReal.setAttribute('max', todayStr);
               }

                // Lokasi validation
                const lokasiInput = document.querySelector('input[name="lokasi"]');
                const lokasiAlert = document.createElement('div');
                lokasiAlert.className = 'text-red-500 text-sm mt-1';
                if (lokasiInput) {
                lokasiInput.parentNode.insertBefore(lokasiAlert, lokasiInput.nextSibling);

                    lokasiInput.addEventListener('input', function () {
                        lokasiAlert.textContent = lokasiInput.value.length > 100 ? 'Lokasi Servis tidak boleh lebih dari 100 karakter.' : '';
                    });
                }

                // Harga input validation & formatting
                const hargaInput = document.getElementById('hargaInput');
                const hargaAlert = document.getElementById('hargaAlert');
                const maxHarga = 1000000000000;
                
                if (hargaInput && hargaAlert) {
                hargaInput.addEventListener('input', function (e) {
                    const digitsOnly = e.target.value.replace(/\D/g, '');

                    if (!digitsOnly) {
                        e.target.value = '';
                        hargaAlert.textContent = '';
                        return;
                    }

                    const value = Number(digitsOnly);

                    if (value > maxHarga) {
                        hargaAlert.textContent = 'Nominal melebihi batas maksimum Rp 1.000.000.000.000.';
                    } else {
                        hargaAlert.textContent = '';
                    }

                    // Format with thousand separators
                    e.target.value = Math.min(value, maxHarga)
                        .toString()
                        .replace(/\B(?=(\d{3})+(?!\d))/g, ".");
                });
                }

                // Kilometer validation
                const kilometerInput = document.getElementById('kilometer');
                const kilometerAlert = document.getElementById('kilometerAlert');
                const maxKilometer = 999999;
                
                if (kilometerInput && kilometerAlert) {
                    kilometerInput.addEventListener('input', function (e) {
                        const digitsOnly = e.target.value.replace(/\D/g, '');

                        if (!digitsOnly) {
                            e.target.value = '';
                            kilometerAlert.textContent = '';
                            return;
                        }

                        const value = Number(digitsOnly);
            
                        if (value > maxKilometer) {
                        kilometerAlert.textContent = 'Kilometer melebihi batas maksimum 999.999 km.';
                        } else {
                        kilometerAlert.textContent = '';
                    }
            
                        // Format with thousand separators
                        e.target.value = Math.min(value, maxKilometer)
                            .toString()
                            .replace(/\B(?=(\d{3})+(?!\d))/g, ".");
                    });
                }

                // Required fields validation
                const requiredFields = [
                    { selector: 'input[name="tgl_servis_real"]', warningId: 'warning-tanggal' },
                    { selector: 'input[name="harga"]', warningId: 'warning-harga' },
                    { selector: 'input[name="lokasi"]', warningId: 'warning-lokasi' },
                    { selector: 'input[name="kilometer"]', warningId: 'warning-kilometer' },
                    { selector: 'input[name="bukti_bayar"]', warningId: 'warning-bukti-bayar' }
                ];

                // Setup validation for each required field
                requiredFields.forEach(field => {
                    const inputEl = document.querySelector(field.selector);
                    const warningEl = document.getElementById(field.warningId);

                    if (inputEl && warningEl) {
                        // Initially hide all warnings
                        warningEl.classList.add('hidden');
                        
                        // Setup input event listeners to hide warnings when users enter data
                        inputEl.addEventListener('input', () => {
                            if (inputEl.value.trim() !== '') {
                                warningEl.classList.add('hidden');
                            }
                        });

                        // For file inputs use change event
                        if (inputEl.type === 'file') {
                            inputEl.addEventListener('change', () => {
                                if (inputEl.files.length > 0) {
                                    warningEl.classList.add('hidden');
                                }
                            });
                        }
                    }
                });

                // Tanggal Servis validation
                const tglServisInput = document.querySelector('input[name="tgl_servis_real"]'); 
                const warningTanggal = document.getElementById('warning-tanggal');

                if (tglServisInput && warningTanggal) {
                    // Initially hide the warning
                    warningTanggal.classList.add('hidden');
                    
                    tglServisInput.addEventListener('input', function () {
                        if (tglServisInput.value.trim() !== '') {
                            warningTanggal.classList.add('hidden');
                        }
                    });
                }
        
                function validateSingleFile(input, warningId) {
                    const warningElement = document.getElementById(warningId);
                    
                    if (!warningElement) return;
                    
                    if (input.files.length > 0) {
                        const maxSize = 2 * 1024 * 1024;
                        if (input.files[0].size > maxSize) {
                                warningElement.classList.remove('hidden');
                            input.value = '';
                        } else {
                                warningElement.classList.add('hidden');
                        }
                    }
                }

                const fotoBuktiBayar = document.getElementById('fotoInputBuktiBayar');
                const sizeWarning = document.getElementById('warning-bukti-bayar-2mb');
                const requiredWarning = document.getElementById('warning-bukti-bayar');
                const uploadTextBuktiBayar = document.getElementById('uploadTextBuktiBayar');
                const removeFileBuktiBayar = document.getElementById('removeFileBuktiBayar');

                const MAX_SIZE = 2 * 1024 * 1024;

                if (fotoBuktiBayar) {
                    fotoBuktiBayar.addEventListener('change', function () {
                        const file = this.files[0];
                        const maxSize = 2 * 1024 * 1024;

                        {{--  document.getElementById('removeFileFlag').value = '0';  --}}

                        if (!file) {
                            requiredWarning?.classList.remove('hidden');
                            sizeWarning?.classList.add('hidden');
                            uploadTextBuktiBayar.textContent = 'Upload Photo';
                            removeFileBuktiBayar.classList.add('hidden');
                            return;
                        }

                        // Tampilkan nama file dulu
                        uploadTextBuktiBayar.textContent = shortenFileName(file.name);
                        removeFileBuktiBayar.classList.remove('hidden');

                        // Lalu validasi ukuran
                        if (file.size > maxSize) {
                            sizeWarning?.classList.remove('hidden');
                            requiredWarning?.classList.add('hidden');
                        } else {
                            sizeWarning?.classList.add('hidden');
                            requiredWarning?.classList.add('hidden');
                        }
                    });

                    removeFileBuktiBayar.addEventListener('click', function (e) {
                        e.preventDefault();
                        fotoBuktiBayar.value = '';
                        uploadTextBuktiBayar.textContent = 'Upload File';
                        sizeWarning?.classList.add('hidden');
                        requiredWarning?.classList.remove('hidden');
                        removeFileBuktiBayar.classList.add('hidden');
                        document.querySelector('input[name="bukti_bayar_lama"]').value = '';
                    });

                    const buktiBayarLama = "{{ $servis->bukti_bayar ? basename($servis->bukti_bayar) : '' }}";
                    if (buktiBayarLama) {
                        uploadTextBuktiBayar.textContent = shortenFileName(buktiBayarLama, 15);
                        removeFileBuktiBayar.classList.remove('hidden');
                    }
                }
                
                const buktiBayarLama = "{{ $servis->bukti_bayar ? basename($servis->bukti_bayar) : '' }}";
                if (buktiBayarLama) {
                    uploadTextBuktiBayar.textContent = shortenFileName(buktiBayarLama, 15);
                    removeFileBuktiBayar.classList.remove('hidden');
                }
                console.log('Bukti Bayar Path:', '{{ $servis->bukti_bayar ?? 'KOSONG' }}');
                
                // Tambahkan log untuk input hidden
                console.log('Bukti Bayar Lama Input:', 
                    document.querySelector('input[name="bukti_bayar_lama"]').value
                );

                const inputBaru = document.getElementById('fotoInputBuktiBayar');
                const inputLama = document.querySelector('input[name="bukti_bayar_lama"]');

                if (form) {
                    form.addEventListener('submit', function(event) {
                        event.preventDefault();
            
                        // Check all required fields
                        let valid = true;
            
                        requiredFields.forEach(field => {
                            const inputEl = document.querySelector(field.selector);
                            const warningEl = document.getElementById(field.warningId);
                            
                        if (inputEl && warningEl) {
                            if (inputEl.type === 'file') {
                                // khusus untuk file, cek apakah file baru *dan* lama kosong
                                if (inputEl.files.length === 0 && !inputLama.value) {
                                    warningEl.classList.remove('hidden');
                                    valid = false;
                                } else {
                                    warningEl.classList.add('hidden');
                                }
                            } else {
                                // input biasa
                                if (inputEl.value.trim() === '') {
                                    warningEl.classList.remove('hidden');
                                    valid = false;
                                } else {
                                    warningEl.classList.add('hidden');
                                }
                            }
                        }

                        });

                        if (!valid) return;
                        
                        // Konfirmasi edit data
                        Swal.fire({
                            title: "Konfirmasi",
                            text: "Apakah Anda yakin ingin mengubah data servis ini?",
                            icon: "warning",
                            showCancelButton: true,
                            reverseButtons: true,
                            confirmButtonColor: "#3085d6",
                            cancelButtonColor: "#d33",
                            confirmButtonText: "Ya, Ubah!",
                            cancelButtonText: "Batal",
                            customClass: {
                                confirmButton: "swal2-confirm-blue",
                                cancelButton: "swal2-cancel-gray"
                            }
                        }).then((result) => {
                            if (result.isConfirmed) {
                                
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
                                                // Redirect ke halaman admin.servisRutin SETELAH klik OK
                                                window.location.href = '/admin/servisRutin';
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
                // Ambil tanggal hari ini
                const today = new Date();
                const year = today.getFullYear();
                const month = String(today.getMonth() + 1).padStart(2, '0');
                const day = String(today.getDate()).padStart(2, '0');
                const todayStr = year + '-' + month + '-' + day;

                // Set atribut max pada input tanggal
                const tglServisReal = document.getElementById('tgl_servis_real');
                if (tglServisReal) {
                tglServisReal.setAttribute('max', todayStr);
               }

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
        </script>
</x-app-layout>