<x-app-layout>
{{-- @extends('layouts.sidebar')
@section('content') --}}

    <div class="min-h-screen flex items-center justify-center py-12 px-4">
        <div class="max-w-2xl w-full bg-white p-6 rounded-lg shadow-lg">
            <h2 class="text-2xl font-bold mb-6 text-center">Form Pembayaran Asuransi Kendaraan</h2>
            <form id="save-form" action="{{ route('asuransi.store') }}" method="POST" enctype="multipart/form-data">
                @csrf    
                @php 
                $currentPage = request()->query('page', 1);
                @endphp 
                <input type="hidden" name="current_page" value="{{ $currentPage }}">        
                <input type="hidden" name="id_kendaraan" value="{{ $kendaraan->id_kendaraan }}">
                <input type="hidden" name="search" value="{{ request()->query('search', '') }}">

                <div class="grid grid-cols-2 gap-4 mb-4">
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

                <div class="grid grid-cols-2 gap-4 mb-4">
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

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Bayar</label>
                    <input type="date" 
                           name="tanggal_bayar" 
                           class="w-full p-2.5 border rounded-lg">
                </div>

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
                
                <div class="mb-6 flex justify-start space-x-4">
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Upload Polis Asuransi</label>
                        <div class="flex flex-col items-center">
                            <label id="uploadLabelPolis" class="cursor-pointer flex flex-col items-center justify-center w-32 h-14 border border-blue-500 text-blue-600 font-medium rounded-lg hover:bg-blue-100 transition">
                                <span id="uploadTextPolis" class="text-sm">Upload File</span>
                                <input type="file" name="foto_polis" id="fotoInputPolis" class="hidden">
                            </label>
                            <a href="#" id="removeFilePolis" class="hidden text-red-600 font-medium text-sm mt-2 hover:underline text-center">Hapus</a>
                        </div>                            
                    </div>
                
                    <div class="h-20 bg-gray-300" style="width: 0.5px;"></div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Upload bukti Pembayaran Asuransi</label>
                        <div class="flex flex-col items-center">
                            <label id="uploadLabelPembayaran" class="cursor-pointer flex flex-col items-center justify-center w-32 h-14 border border-blue-500 text-blue-600 font-medium rounded-lg hover:bg-blue-100 transition">
                                <span id="uploadTextPembayaran" class="text-sm">Upload File</span>
                                <input type="file" name="bukti_bayar_asuransi" id="fotoInputPembayaran" class="hidden">
                            </label>
                            <a href="#" id="removeFilePembayaran" class="hidden text-red-600 font-medium text-sm mt-2 hover:underline text-center">Hapus</a>
                        </div>
                    </div>
            
                    <div class="w-px h-20 bg-gray-300"></div>
                    <div class="mb-4">
                        <p class="font-medium text-gray-700">File requirements:</p>
                        <ul class="text-sm text-gray-600">
                            <li>1. Format: PNG, JPG, atau PDF</li>
                            <li>2. Ukuran maksimal: 5MB</li>
                            <li>3. Harus jelas dan tidak buram</li>
                        </ul>
                    </div>   
                </div>
                
                <div class="flex justify-end space-x-4 mb-2">
                    <button type="button" 
                        onclick="window.location.href='{{ route('asuransi.daftar_kendaraan_asuransi', ['page' => $currentPage, 'search' => request()->query('search', '')]) }}'" 
                        class="bg-red-600 text-white px-6 py-2.5 rounded-lg font-semibold hover:bg-red-700 transition">
                        Batal
                    </button>
                    <button type="submit" id="saveButton" class="bg-blue-600 text-white px-6 py-2.5 rounded-lg font-semibold hover:bg-blue-700 transition">
                        Simpan
                    </button>
                </div>

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

            let tanggalBayar = document.querySelector('input[name="tanggal_bayar"]').value;
            let nominalTagihan = document.querySelector('input[name="nominal_tagihan"]').value;
            let tanggalAwalPerlindungan = document.querySelector('input[name="tgl_perlindungan_awal"]').value;
            let tanggalAkhirPerlindungan = document.querySelector('input[name="tgl_perlindungan_akhir"]').value;
            let fotoPolis = document.getElementById('fotoInputPolis').files.length;
            let fotoPembayaran = document.getElementById('fotoInputPembayaran').files.length;
            if (!tanggalBayar || !nominalTagihan || !tanggalAwalPerlindungan || !tanggalAkhirPerlindungan) {
                showAlert("Mohon isi semua kolom yang wajib sebelum menyimpan", 7000);
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
            Swal.fire({
                title: "Konfirmasi",
                text: "Apakah Anda yakin ingin menyimpan data pembayaran asuransi ini?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Ya",
                cancelButtonText: "Tidak"
            }).then((result) => {
                if (result.isConfirmed) {
                    setTimeout(() => {
                        Swal.fire({
                            title: "Sukses!",
                            text: "Data pembayaran asuransi berhasil disimpan.",
                            icon: "success",
                            confirmButtonColor: "#3085d6",
                            confirmButtonText: "OK"
                        }).then(() => {
                            document.getElementById('save-form').submit();
                        });
                    }, 500);
                }
            });
        });

        function showAlert(message, duration = 5000) {
            let alertDiv = document.getElementById('alertMessage');
            alertDiv.innerHTML = `<span class="font-medium">Peringatan!</span> ${message}`;
            alertDiv.classList.remove('hidden');
            alertDiv.scrollIntoView({ behavior: 'smooth', block: 'center' });
            
            setTimeout(() => {
                alertDiv.classList.add('hidden');
            }, duration);
        }

        function shortenFileName(fileName, maxLength = 13) {
            if (fileName.length > maxLength) {
                return fileName.substring(0, maxLength) + '...';
            }
            return fileName;
        }

        document.getElementById('fotoInputPolis').addEventListener('change', function(event) {
            let fileName = event.target.files[0] ? event.target.files[0].name : "Upload File";
            let shortFileName = shortenFileName(fileName);
            document.getElementById('uploadTextPolis').textContent = shortFileName;
            document.getElementById('removeFilePolis').classList.remove('hidden');
        });

        function handleRemoveFile(fileInputId, uploadTextId, removeButtonId) {
            let fileInput = document.getElementById(fileInputId);
            fileInput.value = ""; 

            document.getElementById(uploadTextId).textContent = "Upload File"; 

            document.getElementById(removeButtonId).classList.add('hidden'); 
        }

        document.getElementById('removeFilePolis').addEventListener('click', function(event) {
            event.preventDefault();
            handleRemoveFile('fotoInputPolis', 'uploadTextPolis', 'removeFilePolis');
        });
        
        document.getElementById('removeFilePembayaran').addEventListener('click', function(event) {
            event.preventDefault();
            handleRemoveFile('fotoInputPembayaran', 'uploadTextPembayaran', 'removeFilePembayaran');
        });

        function validateFileInput(fileInput, allowedTypes, uploadTextId, removeButtonId) {
            let file = fileInput.files[0];

            if (file) {
                if (!allowedTypes.includes(file.type)) {
                    showAlert("File yang diupload harus berupa JPG, PNG, atau PDF!");
                    resetFileInput(fileInput, uploadTextId, removeButtonId);
                    return;
                }

                if (file.size > 5 * 1024 * 1024) {
                    showAlert("Ukuran file tidak boleh lebih dari 5MB!");
                    resetFileInput(fileInput, uploadTextId, removeButtonId);
                    return;
                }

                let shortFileName = shortenFileName(file.name);
                document.getElementById(uploadTextId).textContent = shortFileName;
                document.getElementById(removeButtonId).classList.remove('hidden');
            }
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
{{-- @endsection --}}