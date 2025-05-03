<x-app-layout>
    @php 
    $currentPage = request()->query('page', 1);
    @endphp 
    <a href="{{ route('asuransi.daftar_kendaraan_asuransi', ['page' => $currentPage, 'search' => request()->query('search', '')]) }}"
        class="flex items-center text-blue-600 font-semibold hover:underline mb-5">
         <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"
              xmlns="http://www.w3.org/2000/svg">
             <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"></path>
         </svg>
         Kembali
     </a>
    <div class="min-h-screen flex items-start justify-center pt-4 sm:pt-3 px-4 sm:px-8 pb-8">
        <div class="w-full max-w-2xl">
        <div class="max-w-2xl w-full bg-white p-4 sm:p-6 rounded-lg shadow-lg">
            <h2 class="text-xl sm:text-2xl font-bold mb-4 sm:mb-6 text-center">Form Edit Pembayaran Asuransi Kendaraan</h2>
            <form id="save-form" action="{{ route('asuransi.update', $asuransi->id_asuransi) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <input type="hidden" name="current_page" value="{{ $currentPage }}">
                <input type="hidden" name="id_asuransi" value="{{ $asuransi->id_asuransi }}">
                <input type="hidden" name="search" value="{{ request()->query('search', '') }}">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Detail Kendaraan</label>
                        <input type="text" 
                               value="{{ $asuransi->kendaraan->merk }} - {{ $asuransi->kendaraan->tipe }}"
                               class="w-full p-2.5 border rounded-lg bg-gray-100" 
                               readonly>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Plat Nomor</label>
                        <input type="text" 
                               value="{{ $asuransi->kendaraan->plat_nomor }}"
                               class="w-full p-2.5 border rounded-lg bg-gray-100" 
                               readonly>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Perlindungan Awal</label>
                        <input type="date" 
                               name="tgl_perlindungan_awal"
                               id="tgl_perlindungan_awal"
                               value="{{ $asuransi->tgl_perlindungan_awal }}"
                               class="w-full p-2.5 border rounded-lg">
                        <p id="warning-tanggal-perlindungan-awal" class="text-red-500 text-sm mt-1 hidden">Tanggal perlindungan awal harus diisi!</p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Perlindungan Akhir</label>
                        <input type="date" 
                               name="tgl_perlindungan_akhir"
                               id="tgl_perlindungan_akhir" 
                               value="{{ $asuransi->tgl_perlindungan_akhir }}"
                               class="w-full p-2.5 border rounded-lg">
                        <p id="warning-tanggal-perlindungan-akhir" class="text-red-500 text-sm mt-1 hidden">Tanggal perlindungan akhir harus diisi!</p>
                    </div>
                </div>
 
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Bayar</label>
                    <input type="date" 
                           name="tanggal_bayar"
                           id="tanggal_bayar" 
                           value="{{ $asuransi->tgl_bayar }}"
                           max="{{ \Carbon\Carbon::now()->format('Y-m-d') }}"
                           class="w-full p-2.5 border rounded-lg">
                    <p id="warning-tanggal-bayar" class="text-red-500 text-sm mt-1 hidden">Tanggal bayar harus diisi!</p>
                </div>
                
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nominal Asuransi</label>
                    <div class="relative">
                        <span class="absolute left-2 top-1/2 transform -translate-y-1/2 text-gray-500">Rp</span>
                        <input type="text" 
                               id="nominal_tagihan"
                               name="nominal_tagihan" 
                               value="{{ $asuransi->nominal !== null ? number_format($asuransi->nominal, 0, ',', '.') : '' }}"
                               class="w-full pl-8 p-2.5 border rounded-lg"
                               oninput="formatRupiah(this)">
                    </div>
                    <p id="warning-nominal-tagihan" class="text-red-500 text-sm mt-1 hidden">Nominal asuransi harus diisi dan tidak boleh 0!</p>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Biaya Lainnya</label>
                    <div class="relative">
                        <span class="absolute left-2 top-1/2 transform -translate-y-1/2 text-gray-500">Rp</span>
                        <input type="text" 
                               id="biaya_lain"
                               name="biaya_lain" 
                               value="{{ $asuransi->biaya_lain !== null ? number_format($asuransi->biaya_lain, 0, ',', '.') : '' }}"
                               class="w-full pl-8 p-2.5 border rounded-lg" 
                               oninput="formatRupiah(this)"> 
                    </div>
                    <p id= warning-biaya-lain class="text-red-500 text-sm mt-1 hidden">Biaya lain melebihi batas maksimum!</p>
                </div>
               
                <div class="mb-6">
                    <div class="flex flex-col md:flex-row md:justify-start md:space-x-4">
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Upload Polis Asuransi</label>
                            <div class="flex flex-col items-center">
                                <label id="uploadLabelPolis" class="cursor-pointer flex flex-col items-center justify-center w-32 h-14 border border-blue-500 text-blue-600 font-medium rounded-lg hover:bg-blue-100 transition">
                                    <span id="uploadTextPolis" class="text-sm text-center px-1">
                                        {{ $asuransi->polis ? $asuransi->polis : "Upload File" }}
                                    </span>
                                    <input type="file" name="foto_polis" id="fotoInputPolis" class="hidden">
                                </label>
                                <a href="#" id="removeFilePolis" class="{{ $asuransi->polis ? '' : 'hidden' }} text-red-600 font-medium text-sm mt-2 hover:underline text-center">Hapus</a>
                                <p id="warning-foto-polis" class="text-red-500 text-sm mt-1 hidden">Polis asuransi harus diupload!</p>
                            </div>                            
                        </div>
                        <div class="hidden md:block h-20 bg-gray-300" style="width: 0.5px;"></div>
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Upload bukti Pembayaran Asuransi</label>
                            <div class="flex flex-col items-center">
                                <label id="uploadLabelPembayaran" class="cursor-pointer flex flex-col items-center justify-center w-32 h-14 border border-blue-500 text-blue-600 font-medium rounded-lg hover:bg-blue-100 transition">
                                    <span id="uploadTextPembayaran" class="text-sm text-center px-1">
                                        {{ $asuransi->bukti_bayar_asuransi ? $asuransi->bukti_bayar_asuransi : "Upload File" }}
                                    </span>
                                    <input type="file" name="bukti_bayar_asuransi" id="fotoInputPembayaran" class="hidden">
                                </label>
                                <a href="#" id="removeFilePembayaran" class="{{ $asuransi->bukti_bayar_asuransi ? '' : 'hidden' }} text-red-600 font-medium text-sm mt-2 hover:underline text-center">Hapus</a>
                                <p id="warning-foto-pembayaran" class="text-red-500 text-sm mt-1 hidden">Bukti pembayaran asuransi harus diupload!</p>
                            </div>
                        </div>
                        
                        <div class="hidden md:block h-20 bg-gray-300" style="width: 0.5px;"></div>
                        
                        <div class="mb-4">
                            <p class="font-medium text-gray-700">File requirements:</p>
                            <ul class="text-sm text-gray-600">
                                <li>1. Format: PNG, JPG, atau PDF</li>
                                <li>2. Ukuran maksimal: 2MB</li>
                                <li>3. Harus jelas dan tidak buram</li>
                            </ul>
                        </div>
                    </div>
                </div>
                
                <div class="flex flex-col sm:flex-row sm:justify-end space-y-2 sm:space-y-0 sm:space-x-4 mb-2">
                    <button type="submit" id="saveButton" 
                        class="bg-blue-600 text-white px-6 py-2.5 rounded-lg font-semibold hover:bg-blue-700 transition w-fit min-w-[100px] sm:w-auto">
                        Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var today = new Date();
            var year = today.getFullYear();
            var month = String(today.getMonth() + 1).padStart(2, '0');
            var day = String(today.getDate()).padStart(2, '0');
            var todayStr = year + '-' + month + '-' + day;
            var tanggalBayarInput = document.getElementById('tanggal_bayar');
            if (tanggalBayarInput) {
            tanggalBayarInput.setAttribute('max', todayStr);
            }
        });
        let polisFileWasDeleted = false;
        let pembayaranFileWasDeleted = false;
        function formatRupiah(input) {
            let value = input.value.replace(/[^\d]/g, ''); 
            let formattedValue = new Intl.NumberFormat('id-ID').format(value); 
            input.value = formattedValue;
        }
        function showWarning(input, warningElement) {
            if (!input.value) {
                warningElement.classList.remove("hidden");
                input.classList.add("border-red-500");
                return false;
            }
            return true;
        }
        function hideWarning(input, warningElement) {
            warningElement.classList.add("hidden");
            input.classList.remove("border-red-500");
        }
        function validateInputs() {
            let errors = [];
            let isValid = true;
            const MAX_VALUE = BigInt("9223372036854775807");
            let tglPerlindunganAwalInput = document.getElementById('tgl_perlindungan_awal');
            let warningTglPerlindunganAwal = document.getElementById('warning-tanggal-perlindungan-awal');
            if (!tglPerlindunganAwalInput.value) {
                warningTglPerlindunganAwal.classList.remove("hidden");
                tglPerlindunganAwalInput.classList.add("border-red-500");
                errors.push("Tanggal perlindungan awal harus diisi!");
                isValid = false;
            } else {
                hideWarning(tglPerlindunganAwalInput, warningTglPerlindunganAwal);
            }
            
            let tglPerlindunganAkhirInput = document.getElementById('tgl_perlindungan_akhir');
            let warningTglPerlindunganAkhir = document.getElementById('warning-tanggal-perlindungan-akhir');
            if (!tglPerlindunganAkhirInput.value) {
                warningTglPerlindunganAkhir.classList.remove("hidden");
                tglPerlindunganAkhirInput.classList.add("border-red-500");
                errors.push("Tanggal perlindungan akhir harus diisi!");
                isValid = false;
            }  else if (tglPerlindunganAwalInput.value && tglPerlindunganAkhirInput.value && 
                (new Date(tglPerlindunganAwalInput.value) >= new Date(tglPerlindunganAkhirInput.value))) {
                warningTglPerlindunganAkhir.textContent = "Tanggal akhir tidak boleh lebih kecil atau sama dengan tanggal awal!";
                warningTglPerlindunganAkhir.classList.remove("hidden");
                tglPerlindunganAkhirInput.classList.add("border-red-500");
                errors.push("Tanggal perlindungan akhir tidak boleh lebih kecil atau sama dengan tanggal awal!");
                isValid = false;
            } else {
                hideWarning(tglPerlindunganAkhirInput, warningTglPerlindunganAkhir);
            }
          
            let tanggalBayarInput = document.getElementById('tanggal_bayar');
            let warningTanggalBayar = document.getElementById('warning-tanggal-bayar');
            if (!tanggalBayarInput.value) {
                warningTanggalBayar.classList.remove("hidden");
                tanggalBayarInput.classList.add("border-red-500");
                errors.push("Tanggal bayar harus diisi!");
                isValid = false;
            } else {
                const selectedDate = tanggalBayarInput.value;
                const today = new Date();
                today.setHours(0, 0, 0, 0);
                
                if (selectedDate > today) {
                    warningTanggalBayar.textContent = "Tanggal bayar tidak boleh lebih dari hari ini!";
                    warningTanggalBayar.classList.remove("hidden");
                    tanggalBayarInput.classList.add("border-red-500");
                    errors.push("Tanggal bayar tidak boleh lebih dari hari ini!");
                    isValid = false;
                } else {
                    hideWarning(tanggalBayarInput, warningTanggalBayar);
                }
            }
            
            let nominalTagihanInput = document.getElementById('nominal_tagihan');
            let warningNominalTagihan = document.getElementById('warning-nominal-tagihan');
            if (!nominalTagihanInput.value || nominalTagihanInput.value.replace(/[^\d]/g, '') === '0') {
                warningNominalTagihan.classList.remove("hidden");
                nominalTagihanInput.classList.add("border-red-500");
                errors.push("Nominal asuransi harus diisi dan tidak boleh 0!");
                isValid = false;
            } else {
                const numericValue = BigInt(nominalTagihanInput.value.replace(/[^\d]/g, ''));
                if (numericValue > MAX_VALUE) {
                    warningNominalTagihan.textContent = "Nominal asuransi melebihi batas maksimum!";
                    warningNominalTagihan.classList.remove("hidden");
                    nominalTagihanInput.classList.add("border-red-500");
                    errors.push("Nominal asuransi melebihi batas maksimum!");
                    isValid = false;
                } else {
                    hideWarning(nominalTagihanInput, warningNominalTagihan);
                }
            }
          
            let biayaLainInput = document.getElementById('biaya_lain');
            if (biayaLainInput && biayaLainInput.value) {
                let warningBiayaLain = document.getElementById('warning-biaya-lain');
                const numericValue = BigInt(biayaLainInput.value.replace(/[^\d]/g, ''));
                if (numericValue > MAX_VALUE) {
                    warningBiayaLain.textContent = "Biaya lain melebihi batas maksimum!";
                    warningBiayaLain.classList.remove("hidden");
                    biayaLainInput.classList.add("border-red-500");
                    errors.push("Biaya lain melebihi batas maksimum!");
                    isValid = false;
                } else {
                    hideWarning(biayaLainInput, warningBiayaLain);
                }
            }
           
            let fotoPolisinput = document.getElementById('fotoInputPolis');
            let warningFotoPolis = document.getElementById('warning-foto-polis');
            let existingPolis = "{{ $asuransi->polis }}";
            
            if ((!fotoPolisinput.files[0] && !existingPolis) || (polisFileWasDeleted && !fotoPolisinput.files[0])) {
                warningFotoPolis.textContent = "Polis asuransi harus diupload!";
                warningFotoPolis.classList.remove("hidden");
                document.getElementById('uploadLabelPolis').classList.add("border-red-500");
                errors.push("Polis asuransi harus diupload!");
                isValid = false;
            } else if (fotoPolisinput.files[0]) {
                let file = fotoPolisinput.files[0];
                let fileExtension = file.name.split('.').pop().toLowerCase();
                let validExtensions = ['jpg', 'jpeg', 'png', 'pdf'];
                
                if (!validExtensions.includes(fileExtension)) {
                    warningFotoPolis.textContent = "File yang diupload harus berupa JPG, PNG, atau PDF!";
                    warningFotoPolis.classList.remove("hidden");
                    document.getElementById('uploadLabelPolis').classList.add("border-red-500");
                    errors.push("File yang diupload harus berupa JPG, PNG, atau PDF!");
                    isValid = false;
                } else if (file.size > 2 * 1024 * 1024) {
                    warningFotoPolis.textContent = "Ukuran file tidak boleh lebih dari 2MB!";
                    warningFotoPolis.classList.remove("hidden");
                    document.getElementById('uploadLabelPolis').classList.add("border-red-500");
                    errors.push("Ukuran file tidak boleh lebih dari 2MB!");
                    isValid = false;
                } else {
                    hideWarning(document.getElementById('uploadLabelPolis'), warningFotoPolis);
                }
            } else {
                hideWarning(document.getElementById('uploadLabelPolis'), warningFotoPolis);
            }
           
            let fotoPembayaranInput = document.getElementById('fotoInputPembayaran');
            let warningFotoPembayaran = document.getElementById('warning-foto-pembayaran');
            let existingPembayaran = "{{ $asuransi->bukti_bayar_asuransi }}";
            
            if ((!fotoPembayaranInput.files[0] && !existingPembayaran) || (pembayaranFileWasDeleted && !fotoPembayaranInput.files[0])) {
                warningFotoPembayaran.textContent = "Bukti pembayaran asuransi harus diupload!";
                warningFotoPembayaran.classList.remove("hidden");
                document.getElementById('uploadLabelPembayaran').classList.add("border-red-500");
                errors.push("Bukti pembayaran asuransi harus diupload!");
                isValid = false;
            } else if (fotoPembayaranInput.files[0]) {
                let file = fotoPembayaranInput.files[0];
                let fileExtension = file.name.split('.').pop().toLowerCase();
                let validExtensions = ['jpg', 'jpeg', 'png', 'pdf'];
                
                if (!validExtensions.includes(fileExtension)) {
                    warningFotoPembayaran.textContent = "File yang diupload harus berupa JPG, PNG, atau PDF!";
                    warningFotoPembayaran.classList.remove("hidden");
                    document.getElementById('uploadLabelPembayaran').classList.add("border-red-500");
                    errors.push("File yang diupload harus berupa JPG, PNG, atau PDF!");
                    isValid = false;
                } else if (file.size > 2 * 1024 * 1024) {
                    warningFotoPembayaran.textContent = "Ukuran file tidak boleh lebih dari 2MB!";
                    warningFotoPembayaran.classList.remove("hidden");
                    document.getElementById('uploadLabelPembayaran').classList.add("border-red-500");
                    errors.push("Ukuran file tidak boleh lebih dari 2MB!");
                    isValid = false;
                } else {
                    hideWarning(document.getElementById('uploadLabelPembayaran'), warningFotoPembayaran);
                }
            } else {
                hideWarning(document.getElementById('uploadLabelPembayaran'), warningFotoPembayaran);
            }
            
            return { isValid, errors };
        }
        

        document.addEventListener('DOMContentLoaded', function() {
            const MAX_VALUE = BigInt("9223372036854775807");
            const tglPerlindunganAwalInput = document.getElementById('tgl_perlindungan_awal');
            const tglPerlindunganAkhirInput = document.getElementById('tgl_perlindungan_akhir');
            const tanggalBayarInput = document.getElementById('tanggal_bayar');
            const nominalTagihanInput = document.getElementById('nominal_tagihan');
            const biayaLainInput = document.getElementById('biaya_lain');
            const fotoPolisInput = document.getElementById('fotoInputPolis');
            const fotoPembayaranInput = document.getElementById('fotoInputPembayaran');
            
            tglPerlindunganAwalInput.addEventListener('input', function() {
                const warningElement = document.getElementById('warning-tanggal-perlindungan-awal');
                if (this.value) {
                    hideWarning(this, warningElement);
                    if (tglPerlindunganAkhirInput.value && new Date(this.value) >=new Date(tglPerlindunganAkhirInput.value)) {
                        const warningAkhirElement = document.getElementById('warning-tanggal-perlindungan-akhir');
                        warningAkhirElement.textContent = "Tanggal akhir tidak boleh lebih kecil atau sama dengan tanggal awal!";
                        warningAkhirElement.classList.remove("hidden");
                        tglPerlindunganAkhirInput.classList.add("border-red-500");
                    }
                } else {
                    warningElement.textContent = "Tanggal perlindungan awal harus diisi!";
                    warningElement.classList.remove("hidden");
                    this.classList.add("border-red-500");
                }
            });
            
            tglPerlindunganAkhirInput.addEventListener('input', function() {
                const warningElement = document.getElementById('warning-tanggal-perlindungan-akhir');
                if (this.value) {
                    if (tglPerlindunganAwalInput.value && new Date(tglPerlindunganAwalInput.value) >= new Date(this.value)) {
                        warningElement.textContent = "Tanggal akhir tidak boleh lebih kecil atau sama dengan tanggal awal!";
                        warningElement.classList.remove("hidden");
                        this.classList.add("border-red-500");
                    } else {
                        hideWarning(this, warningElement);
                    }
                } else {
                    warningElement.textContent = "Tanggal perlindungan akhir harus diisi!";
                    warningElement.classList.remove("hidden");
                    this.classList.add("border-red-500");
                }
            });
            
            tanggalBayarInput.addEventListener('input', function() {
                const warningElement = document.getElementById('warning-tanggal-bayar');
                if (this.value) {
                    hideWarning(this, warningElement);
                    const selectedDate = this.value;
                    const today = new Date();
                    today.setHours(0, 0, 0, 0);
                    
                    if (selectedDate > today) {
                        warningElement.textContent = "Tanggal bayar tidak boleh lebih dari hari ini!";
                        warningElement.classList.remove("hidden");
                        this.classList.add("border-red-500");
                    }
                } else {
                    warningElement.textContent = "Tanggal bayar harus diisi!";
                    warningElement.classList.remove("hidden");
                    this.classList.add("border-red-500");
                }
            });
            
            nominalTagihanInput.addEventListener('input', function() {
                const warningElement = document.getElementById('warning-nominal-tagihan');
                const value = this.value.replace(/[^\d]/g, '');
                
                if (value && parseInt(value) > 0) {
                    if (BigInt(value) > MAX_VALUE) {
                        warningElement.textContent = "Nominal asuransi melebihi batas maksimum!";
                        warningElement.classList.remove("hidden");
                        this.classList.add("border-red-500");
                    } else {
                        hideWarning(this, warningElement);
                    }
                } else {
                    warningElement.textContent = "Nominal asuransi harus diisi dan tidak boleh 0!";
                    warningElement.classList.remove("hidden");
                    this.classList.add("border-red-500");
                }
            });
            
            if (biayaLainInput) {
                biayaLainInput.addEventListener('input', function() {
                    const warningElement = document.getElementById('warning-biaya-lain');
                    const value = this.value.replace(/[^\d]/g, '');
                    
                    if (value) {
                        if (BigInt(value) > MAX_VALUE) {
                            warningElement.textContent = "Biaya lain melebihi batas maksimum!";
                            warningElement.classList.remove("hidden");
                            this.classList.add("border-red-500");
                        } else {
                            hideWarning(this, warningElement);
                        }
                    } else {
                        hideWarning(this, warningElement);
                    }
                });
            }
            
            fotoPolisInput.addEventListener('change', function() {
                const warningElement = document.getElementById('warning-foto-polis');
                const uploadLabel = document.getElementById('uploadLabelPolis');
                
                if (this.files[0]) {
                    let file = this.files[0];
                    let fileExtension = file.name.split('.').pop().toLowerCase();
                    let validExtensions = ['jpg', 'jpeg', 'png', 'pdf'];
                    
                    if (!validExtensions.includes(fileExtension)) {
                        warningElement.textContent = "File yang diupload harus berupa JPG, PNG, atau PDF!";
                        warningElement.classList.remove("hidden");
                        uploadLabel.classList.add("border-red-500");
                        this.value = '';
                        document.getElementById('uploadTextPolis').textContent = "Upload File";
                        document.getElementById('removeFilePolis').classList.add('hidden');
                        return;
                    }
                    
                    if (file.size > 2 * 1024 * 1024) {
                        warningElement.textContent = "Ukuran file tidak boleh lebih dari 2MB!";
                        warningElement.classList.remove("hidden");
                        uploadLabel.classList.add("border-red-500");
                        this.value = '';
                        document.getElementById('uploadTextPolis').textContent = "Upload File";
                        document.getElementById('removeFilePolis').classList.add('hidden');
                        return;
                    }
                    
                    hideWarning(uploadLabel, warningElement);
                    let shortFileName = shortenFileName(file.name);
                    document.getElementById('uploadTextPolis').textContent = shortFileName;
                    document.getElementById('removeFilePolis').classList.remove('hidden');
                    
                    polisFileWasDeleted = false;
                }
            });
            
            fotoPembayaranInput.addEventListener('change', function () {
                const warningElement = document.getElementById('warning-foto-pembayaran');
                const uploadLabel = document.getElementById('uploadLabelPembayaran');
                const removeButton = document.getElementById('removeFilePembayaran');
                const uploadText = document.getElementById('uploadTextPembayaran');
                
                removeButton.classList.add('hidden');
                uploadText.textContent = "Upload File";
                if (!this.files || !this.files[0]) {
                    return;
                }
                
                let file = this.files[0];
                let fileExtension = file.name.split('.').pop().toLowerCase();
                let validExtensions = ['jpg', 'jpeg', 'png', 'pdf'];
                if (!validExtensions.includes(fileExtension)) {
                    warningElement.textContent = "File yang diupload harus berupa JPG, PNG, atau PDF!";
                    warningElement.classList.remove("hidden");
                    uploadLabel.classList.add("border-red-500");
                    this.value = '';
                    return;
                }
                
                if (file.size > 2 * 1024 * 1024) {
                    warningElement.textContent = "Ukuran file tidak boleh lebih dari 2MB!";
                    warningElement.classList.remove("hidden");
                    uploadLabel.classList.add("border-red-500");
                    this.value = '';
                    return;
                }
                
                hideWarning(uploadLabel, warningElement);
                let shortFileName = shortenFileName(file.name);
                uploadText.textContent = shortFileName;
                
                removeButton.classList.remove('hidden');
                pembayaranFileWasDeleted = false;
            });
            
            let polisSpan = document.getElementById('uploadTextPolis');
            if (polisSpan) {
                let fullPolisFileName = polisSpan.textContent.trim();
                if (fullPolisFileName !== "Upload File") {
                    let shortPolisFileName = shortenFileName(fullPolisFileName, 10);
                    polisSpan.textContent = shortPolisFileName;
                }
            }

            let pembayaranSpan = document.getElementById('uploadTextPembayaran');
            if (pembayaranSpan) {
                let fullPembayaranFileName = pembayaranSpan.textContent.trim();
                if (fullPembayaranFileName !== "Upload File") {
                    let shortPembayaranFileName = shortenFileName(fullPembayaranFileName, 10);
                    pembayaranSpan.textContent = shortPembayaranFileName;
                }
            }
            
            document.getElementById('saveButton').addEventListener('click', function(event) {
                console.log('Form submit event triggered'); 
                event.preventDefault();
                let nominalTagihan = document.querySelector('input[name="nominal_tagihan"]');
                let biayaLain = document.querySelector('input[name="biaya_lain"]');

                nominalTagihan.value = nominalTagihan.value.replace(/\D/g, '');
                if (biayaLain.value) {
                    biayaLain.value = biayaLain.value.replace(/\D/g, '');
                }
                
                const validation = validateInputs();
                if (!validation.isValid) {
                    const isMobile = window.innerWidth < 768;
                    let errorContent = '';
                    if (validation.errors.length > 0) {
                        errorContent = validation.errors.map(error => `<li>- ${error}</li>`).join('');
                    }
                    
                    Swal.fire({
                        title: "Gagal",
                        html: `
                            <p>Mohon periksa kembali isian form:</p>
                            <div style="text-align: left;">
                                <ul style="display: inline-block; text-align: left; margin: 0 auto;">
                                    ${errorContent}
                                </ul>
                            </div>
                        `,
                        icon: "error",
                        confirmButtonColor: "#3085d6",
                        confirmButtonText: "OK",
                        width: isMobile ? '90%' : '32em'
                    });
                    
                    return false;
                }
                const isMobile = window.innerWidth < 768;
                
                Swal.fire({
                    title: "Konfirmasi",
                    text: "Apakah Anda yakin ingin menyimpan perubahan data pembayaran asuransi ini?",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#3085d6",
                    confirmButtonText: "Ya, simpan!",
                    cancelButtonText: "Batal",
                    width: isMobile ? '90%' : '32em',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        setTimeout(() => {
                            Swal.fire({
                                title: "Berhasil!",
                                text: "Data pembayaran asuransi berhasil diperbarui.",
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
       
            function shortenFileName(fileName, maxLength = 15) {
                if (fileName.length > maxLength) {
                    const extension = fileName.split('.').pop();
                    const fileNameWithoutExt = fileName.substring(0, fileName.lastIndexOf('.'));
                    const shortName = fileNameWithoutExt.substring(0, maxLength - extension.length - 4);
                    return shortName + "..." + (extension ? "." + extension : "");
                }
                return fileName;
            }
            
            function showAlert(message) {
                let alertDiv = document.getElementById('alertMessage');
                if (!alertDiv) {
                    alertDiv = document.createElement('div');
                    alertDiv.id = 'alertMessage';
                    alertDiv.className = 'p-4 mb-4 text-sm text-yellow-800 rounded-lg bg-yellow-50 hidden';
                    document.querySelector('.max-w-2xl.w-full.bg-white').prepend(alertDiv);
                }
                
                alertDiv.innerHTML = `<span class="font-medium">Peringatan!</span> ${message}`;
                alertDiv.classList.remove('hidden');
                alertDiv.scrollIntoView({ behavior: 'smooth', block: 'center' });

                setTimeout(() => alertDiv.classList.add('hidden'), 5000);
            }

        // Fixed event listeners for the remove buttons

// For Policy File (Polis)
document.getElementById('removeFilePolis').addEventListener('click', function(event) {
    event.preventDefault();
    
    let fileInput = document.getElementById('fotoInputPolis');
    let warningFotoPolis = document.getElementById('warning-foto-polis');
    
    if (fileInput.files.length > 0) {
        fileInput.value = ''; 
        document.getElementById('uploadTextPolis').textContent = "Upload File";
        this.classList.add('hidden');
        
        // Show warning message after removing the file
        warningFotoPolis.textContent = "Polis asuransi harus diupload!";
        warningFotoPolis.classList.remove("hidden");
        document.getElementById('uploadLabelPolis').classList.add("border-red-500");
        return;
    }
    
    polisFileWasDeleted = true;

    let asuransiIdElement = document.querySelector('input[name="id_asuransi"]');
    if (!asuransiIdElement) {
        console.error("Elemen input[name='id_asuransi'] tidak ditemukan!");
        return;
    }

    let asuransiId = asuransiIdElement.value;

    fetch('/asuransi/delete-file', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({ id: asuransiId, file_type: 'polis' })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            console.log("File Polis berhasil dihapus.");
            document.getElementById('uploadTextPolis').textContent = "Upload File";
            this.classList.add('hidden');
            
            // Show warning message after successfully deleting the file from server
            warningFotoPolis.textContent = "Polis asuransi harus diupload!";
            warningFotoPolis.classList.remove("hidden");
            document.getElementById('uploadLabelPolis').classList.add("border-red-500");
        }
    })
    .catch(error => console.error('Error:', error));
});

// For Payment Proof File (Pembayaran)
document.getElementById('removeFilePembayaran').addEventListener('click', function(event) {
    event.preventDefault();
    
    let fileInput = document.getElementById('fotoInputPembayaran');
    let warningFotoPembayaran = document.getElementById('warning-foto-pembayaran');

    if (fileInput.files.length > 0) {
        fileInput.value = ''; 
        document.getElementById('uploadTextPembayaran').textContent = "Upload File";
        this.classList.add('hidden');
        
        // Show warning message after removing the file
        warningFotoPembayaran.textContent = "Bukti pembayaran asuransi harus diupload!";
        warningFotoPembayaran.classList.remove("hidden");
        document.getElementById('uploadLabelPembayaran').classList.add("border-red-500");
        return;
    }

    pembayaranFileWasDeleted = true;

    let asuransiIdElement = document.querySelector('input[name="id_asuransi"]');
    if (!asuransiIdElement) {
        console.error("Elemen input[name='id_asuransi'] tidak ditemukan!");
        return;
    }

    let asuransiId = asuransiIdElement.value;

    fetch('/asuransi/delete-file', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({ id: asuransiId, file_type: 'bukti_bayar_asuransi' })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            console.log("File Pembayaran berhasil dihapus.");
            document.getElementById('uploadTextPembayaran').textContent = "Upload File";
            this.classList.add('hidden');
            
            // Show warning message after successfully deleting the file from server
            warningFotoPembayaran.textContent = "Bukti pembayaran asuransi harus diupload!";
            warningFotoPembayaran.classList.remove("hidden");
            document.getElementById('uploadLabelPembayaran').classList.add("border-red-500");
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
                    
                    if (file.size > 2 * 1024 * 1024) {
                        showAlert("Ukuran file tidak boleh lebih dari 2MB!");
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
        });
        
    </script>
</x-app-layout>