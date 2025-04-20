<x-app-layout>
    <div class="min-h-screen flex items-center justify-center py-8 px-2 sm:px-4">
        <div class="max-w-2xl w-full bg-white p-4 sm:p-6 rounded-lg shadow-lg">
            <h2 class="text-xl sm:text-2xl font-bold mb-4 sm:mb-6 text-center">Form Edit Pembayaran Pajak Kendaraan</h2>
            <form id="save-form" action="{{ route('pajak.update', $pajak->id_pajak) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                @php 
                $currentPage = request()->query('page', 1);
                @endphp 
                <input type="hidden" name="current_page" value="{{ $currentPage }}">
                <input type="hidden" name="id_pajak" value="{{ $pajak->id_pajak }}">
                <input type="hidden" name="search" value="{{ request()->query('search', '') }}">

                <!-- Vehicle Details Section - Responsive Grid -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Detail Kendaraan</label>
                        <input type="text" 
                               value="{{ $pajak->kendaraan->merk }} - {{ $pajak->kendaraan->tipe }}"
                               class="w-full p-2.5 border rounded-lg bg-gray-100" 
                               readonly>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Plat Nomor</label>
                        <input type="text" 
                               value="{{ $pajak->kendaraan->plat_nomor }}"
                               class="w-full p-2.5 border rounded-lg bg-gray-100" 
                               readonly>
                    </div>
                </div>

                <!-- Dates Section - Responsive Grid -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Bayar</label>
                        <input type="date" 
                               name="tanggal_bayar" 
                               value="{{ \Carbon\Carbon::parse($pajak->tgl_bayar)->format('Y-m-d') }}"
                               max="{{ \Carbon\Carbon::now()->format('Y-m-d') }}"
                               class="w-full p-2.5 border rounded-lg">
                    </div>                                       
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Jatuh Tempo</label>
                        <input type="date" 
                               name="tanggal_jatuh_tempo"
                               value="{{ $pajak->tgl_jatuh_tempo }}" 
                               class="w-full p-2.5 border rounded-lg">
                    </div>
                </div>
 
                <!-- Payment Amount Section -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nominal Tagihan</label>
                    <div class="relative">
                        <span class="absolute left-2 top-1/2 transform -translate-y-1/2 text-gray-500">Rp</span>
                        <input type="text" 
                               id="nominal_tagihan"
                               name="nominal_tagihan" 
                               value="{{ $pajak->nominal !== null ? number_format($pajak->nominal, 0, ',', '.') : '' }}"
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
                               value="{{ $pajak->biaya_pajak_lain !== null ? number_format($pajak->biaya_pajak_lain, 0, ',', '.') : '' }}"
                               class="w-full pl-8 p-2.5 border rounded-lg" 
                               oninput="formatRupiah(this)"> 
                    </div>
                </div>                
                
                <!-- File Upload Section - Responsive Layout -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Upload Bukti Pembayaran Pajak</label>
                    <!-- Main container with horizontal layout on larger screens, vertical on smaller -->
                    <div class="flex flex-col md:flex-row md:justify-start md:space-x-4">
                        <!-- Payment Proof Upload -->
                        <div class="mb-4">
                            <div class="flex flex-col items-center">
                                <label id="uploadLabel" class="cursor-pointer flex flex-col items-center justify-center w-32 h-14 border border-blue-500 text-blue-600 font-medium rounded-lg hover:bg-blue-100 transition">
                                    <span id="fileName" class="text-sm text-center px-1">
                                        {{ $pajak->bukti_bayar_pajak ?? "Upload Photo" }}
                                    </span>
                                    <input type="file" name="foto" id="fotoInput" class="hidden">
                                </label>
                                <a href="#" id="removeFile" class="{{ $pajak->bukti_bayar_pajak ? '' : 'hidden' }} text-red-600 font-medium text-sm mt-2 hover:underline text-center">
                                    Hapus
                                </a>
                            </div>
                        </div>
                        
                        <!-- Divider - visible on medium screens and up -->
                        <div class="hidden md:block h-20 bg-gray-300" style="width: 0.5px;"></div>
                        
                        <!-- File Requirements -->
                        <div class="mb-4">
                            <p class="font-medium text-gray-700">Image requirements:</p>
                            <ul class="text-sm text-gray-600">
                                <li>1. Format: PNG, JPG, atau PDF</li>
                                <li>2. Ukuran maksimal: 5MB</li>
                                <li>3. Foto harus jelas dan tidak buram</li>
                            </ul>
                        </div>
                    </div>
                </div>
                
                <!-- Action Buttons - Mobile Optimized -->
                <div class="flex flex-col sm:flex-row sm:justify-end space-y-2 sm:space-y-0 sm:space-x-4 mb-2">
                    <button type="button" 
                        onclick="window.location.href='{{ route('pajak.daftar_kendaraan_pajak', ['page' => $currentPage, 'search' => request()->query('search')]) }}'" 
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
            let formattedValue = new Intl.NumberFormat('id-ID').format(value); 
            input.value = formattedValue;
        }

        document.querySelector('form').addEventListener('submit', function () {
            let nominalTagihan = document.querySelector('input[name="nominal_tagihan"]');
            let biayaLain = document.querySelector('input[name="biaya_lain"]');

            nominalTagihan.value = nominalTagihan.value.replace(/\D/g, '');
            if (biayaLain.value) {
                biayaLain.value = biayaLain.value.replace(/\D/g, '');
            }
        });
    
        document.getElementById('save-form').addEventListener('submit', function (event) {
            event.preventDefault();
            
            let tanggalBayarInput = document.querySelector('input[name="tanggal_bayar"]');
            let tanggalBayar = tanggalBayarInput.value;
            let tanggalJatuhTempo = document.querySelector('input[name="tanggal_jatuh_tempo"]').value;
            let nominalTagihan = document.querySelector('input[name="nominal_tagihan"]').value.replace(/\D/g, ''); 
            let alertDiv = document.getElementById('alertMessage');
            let fotoPembayaran = document.getElementById('fotoInput').files[0];
            let existingPembayaran = "{{ $pajak->bukti_bayar_pajak }}";
            let isPembayaranFileDeleted = !fotoPembayaran && !existingPembayaran;
            let today = new Date().toISOString().split('T')[0]; 
            
            if (tanggalBayar > today) {
                showAlert("Tanggal bayar tidak boleh lebih dari hari ini.");
                return false;
            }
            
            if (!tanggalBayar || !tanggalJatuhTempo || !nominalTagihan || parseInt(nominalTagihan) === 0 || 
                isPembayaranFileDeleted) {
                showAlert("Mohon isi semua kolom yang wajib sebelum menyimpan.");
                return false;
            }
            
            let nominalInput = document.querySelector('input[name="nominal_tagihan"]');
            let biayaLainInput = document.querySelector('input[name="biaya_lain"]');
            
            nominalInput.value = nominalInput.value.replace(/[^\d]/g, '');
            biayaLainInput.value = biayaLainInput.value.replace(/[^\d]/g, '');
            
            // Adjust Sweetalert size for mobile
            const isMobile = window.innerWidth < 768;
            
            Swal.fire({
                title: "Konfirmasi",
                text: "Apakah Anda yakin ingin menyimpan perubahan data pembayaran pajak ini?",
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
                            title: "Berhasil!",
                            text: "Data pembayaran pajak berhasil diperbarui.",
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
                    this.value = ""; 
                }
            });
            
            // Optimize long filenames for mobile display
            let fileNameSpan = document.getElementById('fileName');
            if (fileNameSpan) {
                let fullFileName = fileNameSpan.textContent.trim();
                if (fullFileName !== "Upload Photo") {
                    let shortFileName = shortenFileName(fullFileName, 10);
                    fileNameSpan.textContent = shortFileName;
                }
            }
        });

        document.getElementById('fotoInput').addEventListener('change', function(event) {
            let fileName = event.target.files[0] ? event.target.files[0].name : "Upload Photo";
            let shortFileName = shortenFileName(fileName);
            document.getElementById('fileName').textContent = shortFileName;
            document.getElementById('removeFile').classList.remove('hidden');
        });

        document.getElementById('removeFile').addEventListener('click', function(event) {
            event.preventDefault();
            
            let fileInput = document.getElementById('fotoInput');
            
            if (fileInput.files.length > 0) {
                fileInput.value = ''; 
                document.getElementById('fileName').textContent = "Upload Photo";
                document.getElementById('removeFile').classList.add('hidden');
                return;
            }
            
            let pajakIdElement = document.querySelector('input[name="id_pajak"]');
            if (!pajakIdElement) {
                console.error("Elemen input[name='id_pajak'] tidak ditemukan!");
                return;
            }

            let pajakId = pajakIdElement.value;

            fetch('/pajak/delete-file', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ id: pajakId, file_type: 'bukti_bayar_pajak' })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    console.log("File Pembayaran berhasil dihapus.");
                    document.getElementById('fileName').textContent = "Upload Photo";
                    document.getElementById('removeFile').classList.add('hidden');
                } else if (data.error && data.error !== "File tidak ditemukan") {
                    alert(data.error);
                }
            })
            .catch(error => console.error('Error:', error));
        });

        function showAlert(message) {
            let alertDiv = document.getElementById('alertMessage');
            alertDiv.innerHTML = `<span class="font-medium">Peringatan!</span> ${message}`;
            alertDiv.classList.remove('hidden');
            alertDiv.scrollIntoView({ behavior: 'smooth', block: 'center' });

            setTimeout(() => alertDiv.classList.add('hidden'), 5000);
        }
        
        function validateFileInput(fileInput, allowedTypes) {
            let file = fileInput.files[0];

            if (file) {
                let fileExtension = file.name.split('.').pop().toLowerCase();
                let validExtensions = ['jpg', 'jpeg', 'png', 'pdf'];
                
                if (!validExtensions.includes(fileExtension)) {
                    showAlert("File yang diupload harus berupa JPG, PNG, atau PDF!");
                    fileInput.value = '';
                    return false;
                }
                
                if (file.size > 5 * 1024 * 1024) {
                    showAlert("Ukuran file tidak boleh lebih dari 5MB!");
                    fileInput.value = '';
                    return false;
                }
                
                let shortFileName = shortenFileName(file.name);
                document.getElementById('fileName').textContent = shortFileName;
                document.getElementById('removeFile').classList.remove('hidden');
                return true;
            }
            return true; 
        }
        
        document.getElementById('fotoInput').addEventListener('change', function(event) {
            let allowedTypes = ['image/jpeg', 'image/png', 'application/pdf'];
            validateFileInput(this, allowedTypes);
        });

        function shortenFileName(fileName, maxLength = 15) {
            if (fileName.length > maxLength) {
                const extension = fileName.split('.').pop();
                const fileNameWithoutExt = fileName.substring(0, fileName.lastIndexOf('.'));
                const shortName = fileNameWithoutExt.substring(0, maxLength - extension.length - 4);
                return shortName + "..." + (extension ? "." + extension : "");
            }
            return fileName;
        }
    </script>
</x-app-layout>