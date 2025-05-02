<x-app-layout>
    @php 
    $currentPage = request()->query('page', 1);
    @endphp 
     <a href="{{ route('pajak.daftar_kendaraan_pajak', ['page' => $currentPage, 'search' => request()->query('search')]) }}"
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
            <h2 class="text-xl sm:text-2xl font-bold mb-4 sm:mb-6 text-center">Form Edit Pembayaran Pajak Kendaraan</h2>
            <form id="save-form" action="{{ route('pajak.update', $pajak->id_pajak) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <input type="hidden" name="current_page" value="{{ $currentPage }}">
                <input type="hidden" name="id_pajak" value="{{ $pajak->id_pajak }}">
                <input type="hidden" name="search" value="{{ request()->query('search', '') }}">
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
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Bayar</label>
                        <input type="date" 
                               name="tanggal_bayar" 
                               id="tanggal_bayar"
                               value="{{ \Carbon\Carbon::parse($pajak->tgl_bayar)->format('Y-m-d') }}"
                               max="{{ \Carbon\Carbon::now()->format('Y-m-d') }}"
                               class="w-full p-2.5 border rounded-lg">
                        <p id="warning-tanggal-bayar" class="text-red-500 text-sm mt-1 hidden">Tanggal bayar harus diisi!</p>
                    </div>                                       
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Jatuh Tempo</label>
                        <input type="date" 
                               name="tanggal_jatuh_tempo"
                               id="tanggal_jatuh_tempo" 
                               value="{{ $pajak->tgl_jatuh_tempo }}" 
                               class="w-full p-2.5 border rounded-lg">
                        <p id="warning-tanggal-jatuh-tempo" class="text-red-500 text-sm mt-1 hidden">Tanggal jatuh tempo harus diisi!</p>
                    </div>
                </div>
 
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nominal Tagihan</label>
                    <div class="relative">
                        <span class="absolute left-2 top-1/2 transform -translate-y-1/2 text-gray-500 pointer-events-none">Rp</span>
                        <input type="text"
                               id="nominal_tagihan"
                               name="nominal_tagihan"
                               value="{{ $pajak->nominal !== null ? number_format($pajak->nominal, 0, ',', '.') : '' }}"
                               class="w-full pl-8 p-2.5 border rounded-lg"
                               oninput="formatRupiah(this)">
                    </div>
                    <p id="warning-nominal-tagihan" class="text-red-500 text-sm mt-1 hidden">Nominal tagihan harus diisi!</p>
                </div>
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
                    <p id= warning-biaya-lain class="text-red-500 text-sm mt-1 hidden">Biaya lain melebihi batas maksimum!</p>
                </div>           
                
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Upload Bukti Pembayaran Pajak</label>
                    <div class="flex flex-col md:flex-row md:justify-start md:space-x-4">
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
                                <p id="warning-foto" class="text-red-500 text-sm mt-1 hidden">Bukti bayar harus diupload!</p>
                            </div>
                        </div>
                        
                        <div class="hidden md:block h-20 bg-gray-300" style="width: 0.5px;"></div>
                        
                        <div class="mb-4">
                            <p class="font-medium text-gray-700">Image requirements:</p>
                            <ul class="text-sm text-gray-600">
                                <li>1. Format: PNG, JPG, atau PDF</li>
                                <li>2. Ukuran maksimal: 2MB</li>
                                <li>3. Foto harus jelas dan tidak buram</li>
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
        let fileWasDeleted = false;
        
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
    
    let tanggalBayarInput = document.getElementById('tanggal_bayar');
    let warningTanggalBayar = document.getElementById('warning-tanggal-bayar');
    if (!tanggalBayarInput.value) {
        warningTanggalBayar.classList.remove("hidden");
        tanggalBayarInput.classList.add("border-red-500");
        errors.push("Tanggal bayar harus diisi!");
        isValid = false;
    } else {
        const selectedDate = tanggalBayarInput.value;
        const today = new Date().toISOString().split('T')[0];
        
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
    
    let tanggalJatuhTempoInput = document.getElementById('tanggal_jatuh_tempo');
    let warningTanggalJatuhTempo = document.getElementById('warning-tanggal-jatuh-tempo');
    if (!tanggalJatuhTempoInput.value) {
        warningTanggalJatuhTempo.classList.remove("hidden");
        tanggalJatuhTempoInput.classList.add("border-red-500");
        errors.push("Tanggal jatuh tempo harus diisi!");
        isValid = false;
    } else {
        hideWarning(tanggalJatuhTempoInput, warningTanggalJatuhTempo);
    }
    
    let nominalTagihanInput = document.getElementById('nominal_tagihan');
    let warningNominalTagihan = document.getElementById('warning-nominal-tagihan');
    if (!nominalTagihanInput.value || nominalTagihanInput.value.replace(/[^\d]/g, '') === '0') {
        warningNominalTagihan.classList.remove("hidden");
        nominalTagihanInput.classList.add("border-red-500");
        errors.push("Nominal tagihan harus diisi dan tidak boleh 0!");
        isValid = false;
    } else {
        const numericValue = BigInt(nominalTagihanInput.value.replace(/[^\d]/g, ''));
        if (numericValue > MAX_VALUE) {
            warningNominalTagihan.textContent = "Nominal tagihan melebihi batas maksimum!";
            warningNominalTagihan.classList.remove("hidden");
            nominalTagihanInput.classList.add("border-red-500");
            errors.push("Nominal tagihan melebihi batas maksimum!");
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
    
    let fotoInput = document.getElementById('fotoInput');
    let warningFoto = document.getElementById('warning-foto');
    let existingFoto = "{{ $pajak->bukti_bayar_pajak }}";
    
    if ((!fotoInput.files[0] && !existingFoto) || (fileWasDeleted && !fotoInput.files[0])) {
        warningFoto.textContent = "Bukti pembayaran pajak harus diupload!";
        warningFoto.classList.remove("hidden");
        document.getElementById('uploadLabel').classList.add("border-red-500");
        errors.push("Bukti pembayaran pajak harus diupload!");
        isValid = false;
    } else if (fotoInput.files[0]) {
        let file = fotoInput.files[0];
        let fileExtension = file.name.split('.').pop().toLowerCase();
        let validExtensions = ['jpg', 'jpeg', 'png', 'pdf'];
        
        if (!validExtensions.includes(fileExtension)) {
            warningFoto.textContent = "File yang diupload harus berupa JPG, PNG, atau PDF!";
            warningFoto.classList.remove("hidden");
            document.getElementById('uploadLabel').classList.add("border-red-500");
            errors.push("File yang diupload harus berupa JPG, PNG, atau PDF!");
            isValid = false;
        } else if (file.size > 2 * 1024 * 1024) {
            warningFoto.textContent = "Ukuran file tidak boleh lebih dari 2MB!";
            warningFoto.classList.remove("hidden");
            document.getElementById('uploadLabel').classList.add("border-red-500");
            errors.push("Ukuran file tidak boleh lebih dari 2MB!");
            isValid = false;
        } else {
            hideWarning(document.getElementById('uploadLabel'), warningFoto);
        }
    } else {
        hideWarning(document.getElementById('uploadLabel'), warningFoto);
    }
    
    return { isValid, errors };
}

document.addEventListener('DOMContentLoaded', function() {
    const MAX_VALUE = BigInt("9223372036854775807");
    const tanggalBayarInput = document.getElementById('tanggal_bayar');
    const tanggalJatuhTempoInput = document.getElementById('tanggal_jatuh_tempo');
    const nominalTagihanInput = document.getElementById('nominal_tagihan');
    const fotoInput = document.getElementById('fotoInput');
    const biayaLainInput = document.getElementById('biaya_lain');
    
    tanggalBayarInput.addEventListener('input', function() {
        const warningElement = document.getElementById('warning-tanggal-bayar');
        if (this.value) {
            hideWarning(this, warningElement);
            const selectedDate = this.value;
            const today = new Date().toISOString().split('T')[0];
            
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
    
    tanggalJatuhTempoInput.addEventListener('input', function() {
        const warningElement = document.getElementById('warning-tanggal-jatuh-tempo');
        if (this.value) {
            hideWarning(this, warningElement);
        } else {
            warningElement.classList.remove("hidden");
            this.classList.add("border-red-500");
        }
    });
    
    nominalTagihanInput.addEventListener('input', function() {
        const warningElement = document.getElementById('warning-nominal-tagihan');
        const value = this.value.replace(/[^\d]/g, '');
        
        if (value && BigInt(value) > 0n) {
            if (BigInt(value) > MAX_VALUE) {
                warningElement.textContent = "Nominal tagihan melebihi batas maksimum!";
                warningElement.classList.remove("hidden");
                this.classList.add("border-red-500");
            } else {
                hideWarning(this, warningElement);
            }
        } else {
            warningElement.textContent = "Nominal tagihan harus diisi dan tidak boleh 0!";
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
    
    fotoInput.addEventListener('change', function() {
        const warningElement = document.getElementById('warning-foto');
        const uploadLabel = document.getElementById('uploadLabel');
        
        if (this.files[0]) {
            let file = this.files[0];
            let fileExtension = file.name.split('.').pop().toLowerCase();
            let validExtensions = ['jpg', 'jpeg', 'png', 'pdf'];
            
            if (!validExtensions.includes(fileExtension)) {
                warningElement.textContent = "File yang diupload harus berupa JPG, PNG, atau PDF!";
                warningElement.classList.remove("hidden");
                uploadLabel.classList.add("border-red-500");
                this.value = '';
                document.getElementById('fileName').textContent = "Upload Photo";
                document.getElementById('removeFile').classList.add('hidden');
                return;
            }
            
            if (file.size > 2 * 1024 * 1024) {
                warningElement.textContent = "Ukuran file tidak boleh lebih dari 2MB!";
                warningElement.classList.remove("hidden");
                uploadLabel.classList.add("border-red-500");
                this.value = '';
                document.getElementById('fileName').textContent = "Upload Photo";
                document.getElementById('removeFile').classList.add('hidden');
                return;
            }
            
            hideWarning(uploadLabel, warningElement);
            let shortFileName = shortenFileName(file.name);
            document.getElementById('fileName').textContent = shortFileName;
            document.getElementById('removeFile').classList.remove('hidden');
        }
    });
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
            
            hideWarning(document.getElementById('uploadLabel'), document.getElementById('warning-foto'));
            
            validateFileInput(this, ['image/jpeg', 'image/png', 'application/pdf']);
        });
        document.getElementById('removeFile').addEventListener('click', function(event) {
            event.preventDefault();
            
            fileWasDeleted = true;
            
            let fileInput = document.getElementById('fotoInput');
            let warningFoto = document.getElementById('warning-foto');
            
            if (fileInput.files.length > 0) {
                fileInput.value = ''; 
                document.getElementById('fileName').textContent = "Upload Photo";
                document.getElementById('removeFile').classList.add('hidden');
                
                let existingFoto = "{{ $pajak->bukti_bayar_pajak }}";
                if (!existingFoto) {
                    warningFoto.classList.remove("hidden");
                    document.getElementById('uploadLabel').classList.add("border-red-500");
                }
                
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
                    
                    warningFoto.classList.remove("hidden");
                    document.getElementById('uploadLabel').classList.add("border-red-500");
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
                    document.getElementById('fileName').textContent = "Upload Photo";
                    return false;
                }
                
                if (file.size > 2 * 1024 * 1024) {
                    showAlert("Ukuran file tidak boleh lebih dari 2MB!");
                    fileInput.value = '';
                    document.getElementById('fileName').textContent = "Upload Photo";
                    return false;
                }
                
                let shortFileName = shortenFileName(file.name);
                document.getElementById('fileName').textContent = shortFileName;
                document.getElementById('removeFile').classList.remove('hidden');
                
                hideWarning(document.getElementById('uploadLabel'), document.getElementById('warning-foto'));
                return true;
            }
            return true; 
        }

        function shortenFileName(fileName, maxLength = 15) {
            if (fileName.length > maxLength) {
                const extension = fileName.split('.').pop();
                const fileNameWithoutExt = fileName.substring(0, fileName.lastIndexOf('.'));
                const shortName = fileNameWithoutExt.substring(0, maxLength - extension.length - 4);
                return shortName + "..." + (extension ? "." + extension : "");
            }
            return fileName;
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
                    errorContent = '<ul class="text-left mt-2">';
                    validation.errors.forEach(error => {
                        errorContent += `<li>- ${error}</li>`;
                    });
                    errorContent += '</ul>';
                }
                
                Swal.fire({
    title: "Gagal",
    html: `
        <p>Mohon periksa kembali isian form:</p>
        <div style="text-align: center;">
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
                text: "Apakah Anda yakin ingin menyimpan perubahan data pembayaran pajak ini?",
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
    </script>
</x-app-layout>