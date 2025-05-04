<x-app-layout>
    <a href="{{  url()->previous()  }}" class="flex items-center text-blue-600 font-semibold hover:underline mb-5">
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"></path>
        </svg>
        Kembali
    </a>
        <div class="flex justify-center">
            <!-- Main Content -->
            <div class="w-4/5">
                <div class="bg-white p-8 rounded shadow-md">
                <h1 class="text-3xl font-bold mb-8 text-center">Form Edit Servis Rutin Kendaraan</h1>
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
                                       value="{{ $servis->tgl_servis_real ?? '' }}" max="{{ \Carbon\Carbon::now()->format('Y-m-d') }}" required>
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
                                <label for="kilometer" class="block text-gray-700">Kilometer Penggunaan</label>
                                <input id="kilometer" type="text" name="kilometer"
                                       class="w-full p-2 border border-gray-300 rounded"
                                       value="{{ number_format($servis->kilometer, 0, '', '.') }}"
                                       required data-raw="">
                                <div id="kilometerAlert" class="text-red-500 text-sm mt-1"></div>
                            </div>
                        
                            <div>
                                <label for="hargaInput" class="block text-gray-700">Jumlah Pembayaran</label>
                                <div class="relative">
                                    <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500">Rp</span>
                                    <input id="hargaInput" type="text" name="harga"
                                           class="w-full pl-10 p-2 border border-gray-300 rounded"
                                           value="{{ number_format($servis->harga, 0, '', '.') }}"
                                           required data-raw="">
                                </div>
                                <div id="hargaAlert" class="text-red-500 text-sm mt-1"></div>
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
                                        <span id="uploadTextBuktiBayar" class="text-sm">Upload File
                                            {{--  {{ $servis->bukti_bayar ? 'Ganti File' : 'Upload File' }}  --}}
                                        </span>
                                        <input type="file" name="bukti_bayar" id="fotoInputBuktiBayar" class="hidden" value="0" accept=".png, .jpg, .jpeg, .pdf">
                                    </label>
                                    @if($servis->bukti_bayar)
                                        {{--  <div class="mt-2 text-sm text-gray-700">File saat ini: {{ basename($servis->bukti_bayar) }}</div>  --}}
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
                            {{--  <button type="button" onclick="history.back()" class="bg-red-500 text-white px-4 py-2 rounded mr-2">Batal</button>  --}}
                            <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">Simpan Perubahan</button>
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

            document.addEventListener('DOMContentLoaded', function () {
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

                const bindValidation = (input, alertBox, maxLength, message) => {
                    input.addEventListener('input', () => {
                        if (input.value.length > maxLength) {
                            alertBox.textContent = message;
                        } else {
                            alertBox.textContent = '';
                        }
                    });
                };
            
                const bindNumericFormatter = (input, alertBox, maxVal, message) => {
                    input.addEventListener('input', (e) => {
                        let raw = e.target.value.replace(/\D/g, '');
                        let num = parseInt(raw) || 0;
            
                        if (num > maxVal) {
                            alertBox.textContent = message;
                            num = maxVal;
                        } else {
                            alertBox.textContent = '';
                        }
            
                        e.target.value = num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
                    });
                };
            
                // DOM elements
                const lokasiInput = document.querySelector('input[name="lokasi"]');
                const deskripsiTextarea = document.querySelector('textarea[name="deskripsi"]');
                const hargaInput = document.getElementById('hargaInput');
                const kilometerInput = document.getElementById('kilometer');
            
                // Alerts
                const lokasiAlert = document.createElement('div');
                const deskripsiAlert = document.createElement('div');
                lokasiAlert.className = deskripsiAlert.className = 'text-red-500 text-sm mt-1';
                lokasiInput?.parentNode.insertBefore(lokasiAlert, lokasiInput.nextSibling);
                deskripsiTextarea?.parentNode.insertBefore(deskripsiAlert, deskripsiTextarea.nextSibling);
            
                // Bind text length validations
                if (lokasiInput) bindValidation(lokasiInput, lokasiAlert, 100, 'Lokasi Servis tidak boleh lebih dari 100 karakter.');
                if (deskripsiTextarea) bindValidation(deskripsiTextarea, deskripsiAlert, 200, 'Deskripsi Servis tidak boleh lebih dari 200 karakter.');
            
                // Bind numeric validations and formatters
                bindNumericFormatter(hargaInput, document.getElementById('hargaAlert'), 1_000_000_000_000, 'Nominal melebihi batas maksimum Rp 1.000.000.000.000.');
                bindNumericFormatter(kilometerInput, document.getElementById('kilometerAlert'), 999_999, 'Kilometer melebihi batas maksimum 999.999 km');            

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
                    
                    // Tambahkan event listener untuk form submission
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
                            reverseButtons: true,
                            confirmButtonColor: "#3085d6",
                            cancelButtonColor: "#d33",
                            confirmButtonText: "Ya, Ubah!",
                            cancelButtonText: "Batal"
                        }).then((result) => {
                            if (result.isConfirmed) {
                                {{--  // Tampilkan loading
                                Swal.fire({
                                    title: "Memproses...",
                                    text: "Mohon tunggu sebentar",
                                    allowOutsideClick: false,
                                    didOpen: () => {
                                        Swal.showLoading();
                                    }
                                });  --}}
                                
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
    
            document.addEventListener('DOMContentLoaded', function() {
                const fotoInputBuktiBayar = document.getElementById('fotoInputBuktiBayar');
                const uploadTextBuktiBayar = document.getElementById('uploadTextBuktiBayar');
                const removeFileBuktiBayar = document.getElementById('removeFileBuktiBayar');
                const removeInputBuktiBayar = document.getElementById('removeInputBuktiBayar'); // hidden input untuk backend
            
                fotoInputBuktiBayar.addEventListener('change', function () {
                    if (this.files.length > 0) {
                        uploadTextBuktiBayar.textContent = shortenFileName(this.files[0].name);
                        removeFileBuktiBayar.classList.remove('hidden');
                    }
                });
            
                removeFileBuktiBayar.addEventListener('click', function (e) {
                    e.preventDefault();
            
                    // Set input hidden untuk menandai penghapusan file di backend
                    if (removeInputBuktiBayar) {
                        removeInputBuktiBayar.value = '1';
                    }
            
                    // Reset UI upload
                    fotoInputBuktiBayar.value = '';
                    uploadTextBuktiBayar.textContent = '{{ $servis->bukti_bayar ? "Upload File" : "Upload File" }}';
                    removeFileBuktiBayar.classList.add('hidden');
                });
                
                const buktiBayarLama = "{{ $servis->bukti_bayar ? basename($servis->bukti_bayar) : '' }}";
                if (buktiBayarLama) {
                    uploadTextBuktiBayar.textContent = shortenFileName(buktiBayarLama, 15);
                    removeFileBuktiBayar.classList.remove('hidden');
                }
            });

            {{--  document.getElementById('hargaInput').addEventListener('input', function (e) {
                let value = e.target.value.replace(/\D/g, '');
                e.target.value = value.replace(/\B(?=(\d{3})+(?!\d))/g, ".");
            });

            document.getElementById('kilometer').addEventListener('input', function (e) {
                let value = e.target.value.replace(/\D/g, '');
                e.target.value = value.replace(/\B(?=(\d{3})+(?!\d))/g, ".");
            });  --}}
        </script>
</x-app-layout>