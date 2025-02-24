<x-app-layout>
{{-- @extends('layouts.sidebar')
@section('content') --}}
    <div class="min-h-screen flex items-center justify-center py-12 px-4">
        <div class="max-w-2xl w-full bg-white p-6 rounded-lg shadow-lg">
            <h2 class="text-2xl font-bold mb-6 text-center">Form Edit Pembayaran Pajak Kendaraan</h2>
            <form id = "save-form" action="{{ route('pajak.update', $pajak->id_pajak) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                @php 
                $currentPage = request()->query('page', 1);
                @endphp 
                <input type="hidden" name="current_page" value="{{ $currentPage }}">
                <input type="hidden" name="id_pajak" value="{{ $pajak->id_pajak }}">
                <input type="hidden" name="search" value="{{ request()->query('search', '') }}">

                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Detail Kendaraan </label>
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

                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Bayar</label>
                    <input type="date" 
                           name="tanggal_bayar" 
                            value="{{ $pajak->tgl_bayar }}"
                           class="w-full p-2.5 border rounded-lg">
                    </div>
                    <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Jatuh Tempo Selanjutnya</label>
                    <input type="date" 
                           name="tanggal_jatuh_tempo"
                           value="{{ $pajak->tgl_jatuh_tempo }}" 
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
                               value="{{ $pajak->nominal !== null ? number_format($pajak->nominal, 0, ',', '.') : '' }}"
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
                               value="{{ $pajak->biaya_pajak_lain !== null ? number_format($pajak->biaya_pajak_lain, 0, ',', '.') : '' }}"
                               class="w-full pl-8 p-2.5 border rounded-lg" 
                               oninput="formatRupiah(this)"> 
                    </div>
                </div>                
                
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Upload Bukti Pembayaran Pajak</label>
                    <div class="flex items-center space-x-4 justify-start">
                        <div class="flex flex-col items-center">
                            <label id="uploadLabel" class="cursor-pointer flex flex-col items-center justify-center w-32 h-14 border border-blue-500 text-blue-600 font-medium rounded-lg hover:bg-blue-100 transition">
                            <span id="fileName" class="text-sm">
                                {{ $pajak->bukti_bayar_pajak ?? "Upload Photo" }}
                            </span>
                            <input type="file" name="foto" id="fotoInput" class="hidden">
                        </label>
                        <a href="#" id="removeFile" class="{{ $pajak->bukti_bayar_pajak ? '' : 'hidden' }} text-red-600 font-medium text-sm mt-2 hover:underline text-center">
                            Hapus
                        </a>

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
                    <button type="button" onclick="window.location.href='{{ route('pajak.daftar_kendaraan_pajak', ['page' => $currentPage, 'search' => request()->query('search')]) }}'" class="bg-red-600 text-white px-6 py-2.5 rounded-lg font-semibold hover:bg-red-700 transition">
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
            let tanggalBayar = document.querySelector('input[name="tanggal_bayar"]').value;
            let tanggalJatuhTempo = document.querySelector('input[name="tanggal_jatuh_tempo"]').value;
            let nominalTagihan = document.querySelector('input[name="nominal_tagihan"]').value.replace(/\D/g, ''); // Ambil angka saja
            let alertDiv = document.getElementById('alertMessage');
            let fotoPembayaran = document.getElementById('fotoInput').files[0];
            let existingPembayaran = "{{ $pajak->bukti_bayar_pajak }}";
            let isPembayaranFileDeleted = !fotoPembayaran && !existingPembayaran;

            if (!tanggalBayar || !tanggalJatuhTempo || !nominalTagihan || parseInt(nominalTagihan) === 0 || 
            isPembayaranFileDeleted) {
                alertDiv.classList.remove('hidden'); 
                setTimeout(() => alertDiv.classList.add('hidden'), 10000); 
                return; 
            }

            let nominalInput = document.querySelector('input[name="nominal_tagihan"]');
            let biayaLainInput = document.querySelector('input[name="biaya_lain"]');
            
            nominalInput.value = nominalInput.value.replace(/[^\d]/g, '');
            biayaLainInput.value = biayaLainInput.value.replace(/[^\d]/g, '');

            Swal.fire({
                title: "Konfirmasi",
                text: "Apakah Anda yakin ingin menyimpan perubahan data pembayaran pajak ini?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Ya",
                cancelButtonText: "Tidak"
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: "Berhasil!",
                        text: "Data pembayaran pajak berhasil diperbarui.",
                        icon: "success"
                    }).then(() => {
                        event.target.submit();
                    });
                }
            });
        });

        document.getElementById('fotoInput').addEventListener('change', function(event) {
            let file = event.target.files[0];
            if (file) {
                document.getElementById('fileName').textContent = file.name;
                document.getElementById('removeFile').classList.remove('hidden');
            }
        });

        document.getElementById('removeFile').addEventListener('click', function(event) {
        event.preventDefault();
        let pajakIdElement = document.querySelector('input[name="id_pajak"]');
        if (!pajakIdElement) {
            console.error("Elemen input[name='id_pajak'] tidak ditemukan!");
            return;
        }

        let pajakId =pajakIdElement.value;

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
                    // Setelah berhasil menghapus, setel status bahwa file telah dihapus
                    console.log("File Pembayaran berhasil dihapus.");
                    document.getElementById('fotoInput').value = '';
                    document.getElementById('fileName').textContent = "Upload File";
                    document.getElementById('removeFile').classList.add('hidden');
                    location.reload();
                    
                    // Update status untuk validasi
                    isPembayaranFileDeleted = true; // Menetapkan bahwa file telah dihapus
                    console.log("Status Pembayaran Setelah Dihapus: ", isPembayaranFileDeleted);
                } else {
                    alert(data.error);
                }
            })
            .catch(error => console.error('Error:', error));
        });

        document.addEventListener("DOMContentLoaded", function () {
            let fileSpan = document.getElementById("fileName"); 
            let fullFileName = fileSpan.textContent.trim(); 

            let shortFileName = fullFileName.replace("bukti_bayar_pajak/", "");

            if (shortFileName.length > 7) {
                shortFileName = shortFileName.substring(0, 3) + "...";
            }
            fileSpan.textContent = "bukti_bayar/" + shortFileName;
        });

        function showAlert(message) {
            let alertDiv = document.getElementById('alertMessage');
            alertDiv.innerHTML = `<span class="font-medium">Peringatan!</span> ${message}`;
            alertDiv.classList.remove('hidden');
            alertDiv.scrollIntoView({ behavior: 'smooth', block: 'center' });

            setTimeout(() => alertDiv.classList.add('hidden'), 5000);
        }

            function validateFileInput(fileInput, allowedTypes, maxSizeMB, uploadTextId, removeButtonId) {
                let file = fileInput.files[0];

                if (file) {
                    if (!allowedTypes.includes(file.type)) {
                        showAlert("File yang diupload harus berupa JPG, PNG, atau PDF!");
                        fileInput.value = '';
                        return;
                    }

                    if (file.size > maxSizeMB * 1024 * 1024) {
                        showAlert(`Ukuran file tidak boleh lebih dari ${maxSizeMB}MB!`);
                        fileInput.value = ''; 
                        return;
                    }

                    let shortFileName = shortenFileName(file.name);
                    document.getElementById(uploadTextId).textContent = shortFileName;
                    document.getElementById(removeButtonId).classList.remove('hidden');
                }
            }

            document.getElementById('fotoInput').addEventListener('change', function() {
                let allowedTypes = ['image/jpeg', 'image/png', 'application/pdf'];
                let maxSizeMB = 5; 
                validateFileInput(this, allowedTypes, maxSizeMB, 'uploadText', 'removeFile');
            });

            document.getElementById('removeFile').addEventListener('click', function(event) {
                event.preventDefault();
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
                        document.getElementById('fotoInput').value = '';
                        document.getElementById('uploadText').textContent = "Upload File";
                        document.getElementById('removeFile').classList.add('hidden');
                        location.reload();
                    } else {
                        showAlert(data.error);
                    }
                })
                .catch(error => console.error('Error:', error));
            });

            function shortenFileName(fileName, maxLength = 15) {
                return fileName.length > maxLength ? fileName.substring(0, maxLength) + '...' : fileName;
            }
    </script>
</x-app-layout>
{{-- @endsection --}}