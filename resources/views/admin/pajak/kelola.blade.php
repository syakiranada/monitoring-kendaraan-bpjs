{{-- <x-app-layout> --}}
    @extends('layouts.sidebar')

@section('content')

    <div class="min-h-screen flex items-center justify-center py-12 px-4">
        <div class="max-w-2xl w-full bg-white p-6 rounded-lg shadow-lg">
            <h2 class="text-2xl font-bold mb-6 text-center">Form Pembayaran Pajak Kendaraan</h2>
            <form id = "save-form" action="{{ route('pajak.store') }}" method="POST" enctype="multipart/form-data">
                @csrf    
                @php 
                $currentPage = request()->query('page', 1);
                @endphp 
                <input type="hidden" name="current_page" value="{{ $currentPage }}">        
                <input type="hidden" name="id_kendaraan" value="{{ $kendaraan->id_kendaraan }}">

                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Detail Kendaraan </label>
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
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Bayar</label>
                    <input type="date" 
                           name="tanggal_bayar" 
                           class="w-full p-2.5 border rounded-lg">
                    </div>
                    <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Jatuh Tempo</label>
                    <input type="date" 
                           name="tanggal_jatuh_tempo"
                           value="{{ $tgl_jatuh_tempo }}" 
                           class="w-full p-2.5 border rounded-lg">
                    </div>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nominal Tagihan</label>
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
                
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Upload Bukti Pembayaran Pajak</label>
                    <div class="flex items-center space-x-4 justify-start">
                        <div class="flex flex-col items-center">
                            <label id="uploadLabel" class="cursor-pointer flex flex-col items-center justify-center w-32 h-14 border border-blue-500 text-blue-600 font-medium rounded-lg hover:bg-blue-100 transition">
                                <span id="uploadText" class="text-sm">Upload Photo</span>
                                <input type="file" name="foto" id="fotoInput" class="hidden">
                            </label>
                            <a href="#" id="removeFile" class="hidden text-red-600 font-medium text-sm mt-2 hover:underline text-center">Hapus</a>
                        </div>

                        <div class="w-px h-16 bg-gray-300"></div>
                        <div>
                            <p class="font-medium text-gray-700">Image requirements:</p>
                            <ul class="text-sm text-gray-600">
                                <li>1. Format: PNG, JPG, atau PDF</li>
                                <li>2. Ukuran maksimal: 5MB</li>
                                <li>3. Foto harus jelas dan tidak buram</li>
                            </ul>
                        </div>
                    </div>
                </div>
                
                <div class="flex justify-end space-x-4 mb-2">
                    <button type="button" onclick="window.location.href='{{ route('pajak.daftar_kendaraan_pajak', ['page' => $currentPage]) }}'" class="bg-red-600 text-white px-6 py-2.5 rounded-lg font-semibold hover:bg-red-700 transition">
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
            if (value.length > 0) {
                value = parseInt(value).toLocaleString();
            }
            input.value = value ? value : '';
        }
    
       document.getElementById('save-form').addEventListener('submit', function (event) {
        console.log('Button clicked');

        let tanggalBayar = document.querySelector('input[name="tanggal_bayar"]').value;
        let tanggalJatuhTempo = document.querySelector('input[name="tanggal_jatuh_tempo"]').value;
        let nominalTagihan = document.querySelector('input[name="nominal_tagihan"]').value;
        let buktiBayar = document.querySelector('input[name="foto"]').files.length;

        console.log('Form values:', {
            tanggalBayar,
            tanggalJatuhTempo,
            nominalTagihan,
            buktiBayar
        });

        if (!tanggalBayar || !tanggalJatuhTempo || !nominalTagihan || buktiBayar === 0) {
            console.log('Validation failed');
            let alertDiv = document.getElementById('alertMessage');
            alertDiv.classList.remove('hidden');
            setTimeout(() => alertDiv.classList.add('hidden'), 10000);
            return;
        }

        console.log('Validation passed, showing SweetAlert');

        Swal.fire({
            title: "Konfirmasi",
            text: "Apakah Anda yakin ingin menyimpan data pembayaran pajak ini?",
            icon: "warning", 
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Ya",
            cancelButtonText: "Tidak"
        }).then((result) => {
            console.log('SweetAlert result:', result);
            if (result.isConfirmed) {
                console.log('User confirmed, showing success message');
                Swal.fire({
                    title: "Berhasil!",
                    text: "Data pembayaran pajak berhasil disimpan.",
                    icon: "success"
                }).then(() => {
                    console.log('Submitting form');
                    event.target.submit();  
                    console.log('Form submitted');
                });
            } else {
                console.log('User cancelled');
            }
        });
    });

        document.getElementById('fotoInput').addEventListener('change', function(event) {
            let fileName = event.target.files[0] ? event.target.files[0].name : "Upload Photo";
        document.getElementById('uploadText').textContent = fileName;
        document.getElementById('removeFile').classList.remove('hidden');
        });

        document.getElementById('removeFile').addEventListener('click', function(event) {
            let fileInput = document.getElementById('fotoInput');
            fileInput.value = ""; 

            document.getElementById('uploadText').textContent = "Upload Photo";

            this.classList.add('hidden');
        });
    </script>
{{-- </x-app-layout> --}}
@endsection
