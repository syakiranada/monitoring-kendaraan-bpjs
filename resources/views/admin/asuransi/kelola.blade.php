<x-app-layout>
    <div class="min-h-screen flex items-center justify-center py-8 px-2 sm:px-4">
        <div class="max-w-2xl w-full bg-white p-4 sm:p-6 rounded-lg shadow-lg">
            <h2 class="text-xl sm:text-2xl font-bold mb-4 sm:mb-6 text-center">Form Pembayaran Asuransi Kendaraan</h2>
            <form id="save-form" action="{{ route('asuransi.store') }}" method="POST" enctype="multipart/form-data">
                @csrf    
                @php 
                $currentPage = request()->query('page', 1);
                @endphp 
                <input type="hidden" name="current_page" value="{{ $currentPage }}">        
                <input type="hidden" name="id_kendaraan" value="{{ $kendaraan->id_kendaraan }}">
                <input type="hidden" name="search" value="{{ request()->query('search', '') }}">

                <!-- Vehicle Details Section - Responsive Grid -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Detail Kendaraan</label>
                        <input type="text" 
                               value="{{ $kendaraan->merk }} - {{ $kendaraan->tipe }}"
                               class="w-full p-2.5 border rounded-lg bg-gray-100" 
                               readonly>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Plat Nomor</label>
                        <input type="text" 
                               value="{{ $kendaraan->plat_nomor }}"
                               class="w-full p-2.5 border rounded-lg bg-gray-100" 
                               readonly>
                    </div>
                </div>

                <!-- Protection Dates Section - Responsive Grid -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Perlindungan Awal</label>
                        <input type="date" 
                               name="tgl_perlindungan_awal" 
                               class="w-full p-2.5 border rounded-lg">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Perlindungan Akhir</label>
                        <input type="date" 
                            name="tgl_perlindungan_akhir" 
                            class="w-full p-2.5 border rounded-lg">
                    </div>
                </div>
 
                <!-- Payment Date Section -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Bayar</label>
                    <input type="date" 
                           name="tanggal_bayar" 
                           class="w-full p-2.5 border rounded-lg"
                           max="{{ \Carbon\Carbon::now()->format('Y-m-d') }}">
                </div>
                
                <!-- Payment Amount Section -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nominal Asuransi</label>
                    <div class="relative">
                        <span class="absolute left-2 top-1/2 transform -translate-y-1/2 text-gray-500">Rp</span>
                        <input type="text" 
                               id="nominal_tagihan"
                               name="nominal_tagihan" 
                               class="w-full pl-8 p-2.5 border rounded-lg" 
                               oninput="formatRupiah(this)">
                    </div>
                </div>
                
                <!-- Additional Costs Section -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Biaya Lainnya</label>
                    <div class="relative">
                        <span class="absolute left-2 top-1/2 transform -translate-y-1/2 text-gray-500">Rp</span>
                        <input type="text" 
                               id="biaya_lain"
                               name="biaya_lain" 
                               class="w-full pl-8 p-2.5 border rounded-lg" 
                               oninput="formatRupiah(this)"> 
                    </div>
                </div>
                
                <!-- File Upload Section - Responsive Layout -->
                <div class="mb-6">
                    <!-- Main container with horizontal layout on larger screens, vertical on smaller -->
                    <div class="flex flex-col md:flex-row md:justify-start md:space-x-4">
                        <!-- Insurance Policy Upload -->
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Upload Polis Asuransi</label>
                            <div class="flex flex-col items-center">
                                <label id="uploadLabelPolis" class="cursor-pointer flex flex-col items-center justify-center w-32 h-14 border border-blue-500 text-blue-600 font-medium rounded-lg hover:bg-blue-100 transition">
                                    <span id="uploadTextPolis" class="text-sm text-center px-1">Upload File</span>
                                    <input type="file" name="foto_polis" id="fotoInputPolis" class="hidden">
                                </label>
                                <a href="#" id="removeFilePolis" class="hidden text-red-600 font-medium text-sm mt-2 hover:underline text-center">Hapus</a>
                            </div>                            
                        </div>
                        
                        <!-- Divider - visible on medium screens and up -->
                        <div class="hidden md:block h-20 bg-gray-300" style="width: 0.5px;"></div>
                
                        <!-- Payment Proof Upload -->
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Upload bukti Pembayaran Asuransi</label>
                            <div class="flex flex-col items-center">
                                <label id="uploadLabelPembayaran" class="cursor-pointer flex flex-col items-center justify-center w-32 h-14 border border-blue-500 text-blue-600 font-medium rounded-lg hover:bg-blue-100 transition">
                                    <span id="uploadTextPembayaran" class="text-sm text-center px-1">Upload File</span>
                                    <input type="file" name="bukti_bayar_asuransi" id="fotoInputPembayaran" class="hidden">
                                </label>
                                <a href="#" id="removeFilePembayaran" class="hidden text-red-600 font-medium text-sm mt-2 hover:underline text-center">Hapus</a>
                            </div>
                        </div>
                        
                        <!-- Divider - visible on medium screens and up -->
                        <div class="hidden md:block h-20 bg-gray-300" style="width: 0.5px;"></div>
                        
                        <!-- File Requirements -->
                        <div class="mb-4">
                            <p class="font-medium text-gray-700">File requirements:</p>
                            <ul class="text-sm text-gray-600">
                                <li>1. Format: PNG, JPG, atau PDF</li>
                                <li>2. Ukuran maksimal: 5MB</li>
                                <li>3. Harus jelas dan tidak buram</li>
                            </ul>
                        </div>
                    </div>
                </div>
                
                <!-- Action Buttons - Mobile Optimized -->
                <div class="flex flex-col sm:flex-row sm:justify-end space-y-2 sm:space-y-0 sm:space-x-4 mb-2">
                    <button type="button" 
                        onclick="window.location.href='{{ route('asuransi.daftar_kendaraan_asuransi', ['page' => $currentPage, 'search' => request()->query('search', '')]) }}'" 
                        class="bg-red-600 text-white px-6 py-2.5 rounded-lg font-semibold hover:bg-red-700 transition w-fit min-w-[100px] sm:w-auto">
                        Batal
                    </button>
                    <button type="submit" id="saveButton" 
                        class="bg-blue-600 text-white px-6 py-2.5 rounded-lg font-semibold hover:bg-blue-700 transition w-fit min-w-[100px] sm:w-auto">
                        Simpan
                    </button>
                </div>
                
                <!-- Alert Message -->
                <div id="alertMessage" class="hidden p-4 mb-4 text-sm text-red-800 rounded-lg bg-red-50" role="alert">
                    <span class="font-medium">Peringatan!</span> Mohon isi semua kolom yang wajib sebelum menyimpan.
                </div>
                
            </form>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function formatRupiah(input) {
            let value = input.value.replace(/[^\d]/g, '');
            let hiddenInput = document.getElementById(input.id + '_hidden');
            if (!hiddenInput) {
                hiddenInput = document.createElement('input');
                hiddenInput.type = 'hidden';
                hiddenInput.name = input.name;
                hiddenInput.id = input.id + '_hidden';
                input.parentNode.appendChild(hiddenInput);
            }
            
            hiddenInput.value = value;
            
            if (value.length > 0) {
                value = parseInt(value).toLocaleString('id-ID');
            }
            input.value = value ? value : '';
        }

        document.getElementById('save-form').addEventListener('submit', function(event) {
            event.preventDefault();

            let tanggalBayarInput = document.querySelector('input[name="tanggal_bayar"]');
            let tanggalBayar = tanggalBayarInput.value;
            let nominalTagihan = document.querySelector('input[name="nominal_tagihan"]').value;
            let tanggalAwalPerlindungan = document.querySelector('input[name="tgl_perlindungan_awal"]').value;
            let tanggalAkhirPerlindungan = document.querySelector('input[name="tgl_perlindungan_akhir"]').value;
            let fotoPolis = document.getElementById('fotoInputPolis').files.length;
            let fotoPembayaran = document.getElementById('fotoInputPembayaran').files.length;
            let alertDiv = document.getElementById('alertMessage');
            
            let today = new Date().toISOString().split('T')[0]; 
            if (tanggalBayar > today) {
                showAlert("Tanggal bayar tidak boleh lebih dari hari ini.", 7000);
                return;
            }

            if (!tanggalBayar || !nominalTagihan || !tanggalAwalPerlindungan || !tanggalAkhirPerlindungan) {
                showAlert("Mohon isi semua kolom yang wajib sebelum menyimpan.", 7000);
                return;
            }

            if (fotoPolis === 0 || fotoPembayaran === 0) {
                showAlert("Mohon unggah file Polis dan Bukti Pembayaran sebelum menyimpan!", 7000);
                return;
            }

            let tglAwal = new Date(tanggalAwalPerlindungan);
            let tglAkhir = new Date(tanggalAkhirPerlindungan);

            if (tglAkhir <= tglAwal) {
                showAlert("Tanggal perlindungan akhir harus lebih besar dari tanggal perlindungan awal!", 7000);
                return;
            }

            let nominalInput = document.querySelector('input[name="nominal_tagihan"]');
            let biayaLainInput = document.querySelector('input[name="biaya_lain"]');

            nominalInput.value = nominalInput.value.replace(/[^\d]/g, '');
            biayaLainInput.value = biayaLainInput.value.replace(/[^\d]/g, '');

            // Adjust Sweetalert size for mobile
            const isMobile = window.innerWidth < 768;
            
            Swal.fire({
                title: "Konfirmasi",
                text: "Apakah Anda yakin ingin menyimpan data pembayaran asuransi ini?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Ya",
                cancelButtonText: "Tidak",
                width: isMobile ? '90%' : '32em'
            }).then((result) => {
                if (result.isConfirmed) {
                    setTimeout(() => {
                        Swal.fire({
                            title: "Sukses!",
                            text: "Data pembayaran asuransi berhasil disimpan.",
                            icon: "success",
                            confirmButtonColor: "#3085d6",
                            confirmButtonText: "OK",
                            width: isMobile ? '90%' : '32em'
                        }).then(() => {
                            document.getElementById('save-form').submit();
                        });
                    }, 500);
                }
            });
        });

        document.addEventListener('DOMContentLoaded', function() {
            const tanggalBayarInput = document.querySelector('input[name="tanggal_bayar"]');
            
            tanggalBayarInput.addEventListener('change', function() {
                const selectedDate = this.value;
                const today = new Date().toISOString().split('T')[0];

                if (selectedDate > today) {
                    showAlert("Tanggal bayar tidak boleh lebih dari hari ini.");
                    this.value = today; 
                }
            });
        });

        function shortenFileName(fileName, maxLength = 13) {
            if (fileName.length > maxLength) {
                const extension = fileName.split('.').pop();
                const fileNameWithoutExt = fileName.substring(0, fileName.lastIndexOf('.'));
                const shortName = fileNameWithoutExt.substring(0, maxLength - extension.length - 4);
                return shortName + "..." + (extension ? "." + extension : "");
            }
            return fileName;
        }

        function showAlert(message, duration = 5000) {
            let alertDiv = document.getElementById('alertMessage');
            alertDiv.innerHTML = `<span class="font-medium">Peringatan!</span> ${message}`;
            alertDiv.classList.remove('hidden');
            alertDiv.scrollIntoView({ behavior: 'smooth', block: 'center' });
            
            setTimeout(() => {
                alertDiv.classList.add('hidden');
            }, duration);
        }

        document.getElementById('fotoInputPolis').addEventListener('change', function(event) {
            let fileName = event.target.files[0] ? event.target.files[0].name : "Upload File";
            let shortFileName = shortenFileName(fileName);
            document.getElementById('uploadTextPolis').textContent = shortFileName;
            document.getElementById('removeFilePolis').classList.remove('hidden');
        });

        document.getElementById('fotoInputPembayaran').addEventListener('change', function(event) {
            let fileName = event.target.files[0] ? event.target.files[0].name : "Upload File";
            let shortFileName = shortenFileName(fileName);
            document.getElementById('uploadTextPembayaran').textContent = shortFileName;
            document.getElementById('removeFilePembayaran').classList.remove('hidden');
        });

        document.getElementById('removeFilePolis').addEventListener('click', function(event) {
            event.preventDefault();
            let fileInput = document.getElementById('fotoInputPolis');
            fileInput.value = ""; 
            document.getElementById('uploadTextPolis').textContent = "Upload File"; 
            document.getElementById('removeFilePolis').classList.add('hidden'); 
        });
        
        document.getElementById('removeFilePembayaran').addEventListener('click', function(event) {
            event.preventDefault();
            let fileInput = document.getElementById('fotoInputPembayaran');
            fileInput.value = ""; 
            document.getElementById('uploadTextPembayaran').textContent = "Upload File"; 
            document.getElementById('removeFilePembayaran').classList.add('hidden'); 
        });

        function validateFileInput(fileInput, allowedTypes, uploadTextId, removeButtonId) {
            let file = fileInput.files[0];

            if (file) {
                let fileExtension = file.name.split('.').pop().toLowerCase();
                let validExtensions = ['jpg', 'jpeg', 'png', 'pdf'];
                
                if (!validExtensions.includes(fileExtension)) {
                    showAlert("File yang diupload harus berupa JPG, PNG, atau PDF!");
                    resetFileInput(fileInput, uploadTextId, removeButtonId);
                    return false;
                }
                
                if (file.size > 5 * 1024 * 1024) {
                    showAlert("Ukuran file tidak boleh lebih dari 5MB!");
                    resetFileInput(fileInput, uploadTextId, removeButtonId);
                    return false;
                }
                
                let shortFileName = shortenFileName(file.name);
                document.getElementById(uploadTextId).textContent = shortFileName;
                document.getElementById(removeButtonId).classList.remove('hidden');
                return true;
            }
            return true; 
        }

        function resetFileInput(fileInput, uploadTextId, removeButtonId) {
            fileInput.value = ''; 
            document.getElementById(uploadTextId).textContent = "Upload File"; 
            document.getElementById(removeButtonId).classList.add('hidden'); 
        }

        document.getElementById('fotoInputPolis').addEventListener('change', function(event) {
            let allowedTypes = ['image/jpeg', 'image/png', 'application/pdf'];
            validateFileInput(this, allowedTypes, 'uploadTextPolis', 'removeFilePolis');
        });

        document.getElementById('fotoInputPembayaran').addEventListener('change', function(event) {
            let allowedTypes = ['image/jpeg', 'image/png', 'application/pdf'];
            validateFileInput(this, allowedTypes, 'uploadTextPembayaran', 'removeFilePembayaran');
        });
    </script>
</x-app-layout>