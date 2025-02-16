{{-- <x-app-layout> --}}
    @extends('layouts.sidebar')

@section('content')

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
                    <button type="button" onclick="window.location.href='{{ route('asuransi.daftar_kendaraan_asuransi', ['page' => $currentPage]) }}'" class="bg-red-600 text-white px-6 py-2.5 rounded-lg font-semibold hover:bg-red-700 transition">
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
        // Modified formatRupiah function to store clean numbers
function formatRupiah(input) {
    // Remove any non-digit characters from the input
    let value = input.value.replace(/[^\d]/g, '');
    
    // Store the raw number value in a hidden input
    let hiddenInput = document.getElementById(input.id + '_hidden');
    if (!hiddenInput) {
        hiddenInput = document.createElement('input');
        hiddenInput.type = 'hidden';
        hiddenInput.name = input.name;
        hiddenInput.id = input.id + '_hidden';
        input.parentNode.appendChild(hiddenInput);
    }
    
    // Store raw value in hidden input
    hiddenInput.value = value;
    
    // Format the visible input with commas
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
    let fotoPolis = document.getElementById('fotoInputPolis').files[0];
    let fotoPembayaran = document.getElementById('fotoInputPembayaran').files[0];

    // Get alert div
    let alertDiv = document.getElementById('alertMessage');

    // Validation checks - file tidak wajib saat edit
    if (!tanggalBayar || !nominalTagihan || !tanggalAwalPerlindungan || !tanggalAkhirPerlindungan || !fotoPolis || !fotoPembayaran) {
        let alertDiv = document.getElementById('alertMessage');
            alertDiv.classList.remove('hidden');
            setTimeout(() => alertDiv.classList.add('hidden'), 10000);
            return;
    }

    // Date validation
    if (new Date(tanggalAwalPerlindungan) > new Date(tanggalAkhirPerlindungan)) {
        alertDiv.innerHTML = '<span class="font-medium">Peringatan!</span> Tanggal perlindungan awal tidak boleh lebih besar dari tanggal perlindungan akhir.';
        alertDiv.classList.remove('hidden');
        alertDiv.scrollIntoView({ behavior: 'smooth', block: 'center' });
        setTimeout(() => alertDiv.classList.add('hidden'), 5000);
        return false;
    }

    // Clean currency format before submitting
    let nominalInput = document.querySelector('input[name="nominal_tagihan"]');
    let biayaLainInput = document.querySelector('input[name="biaya_lain"]');
    
    nominalInput.value = nominalInput.value.replace(/[^\d]/g, '');
    biayaLainInput.value = biayaLainInput.value.replace(/[^\d]/g, '');

    // Show confirmation dialog
    Swal.fire({
        title: "Konfirmasi",
        text: "Apakah Anda yakin ingin menyimpan perubahan data pembayaran asuransi ini?",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Ya",
        cancelButtonText: "Tidak"
    }).then((result) => {
        if (result.isConfirmed) {
            // Simulate form submission
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
// Function to show alert message
function showAlert(message, duration = 5000) {
    let alertDiv = document.getElementById('alertMessage');
    alertDiv.innerHTML = `<span class="font-medium">Peringatan!</span> ${message}`;
    alertDiv.classList.remove('hidden');
    alertDiv.scrollIntoView({ behavior: 'smooth', block: 'center' });
    
    setTimeout(() => {
        alertDiv.classList.add('hidden');
    }, duration);
}
function shortenFileName(fileName, maxLength = 15) {
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

document.getElementById('removeFilePolis').addEventListener('click', function() {
    let fileInput = document.getElementById('fotoInputPolis');
    fileInput.value = ""; 
    document.getElementById('uploadTextPolis').textContent = "Upload File";
    this.classList.add('hidden');
});

document.getElementById('fotoInputPembayaran').addEventListener('change', function(event) {
    let fileName = event.target.files[0] ? event.target.files[0].name : "Upload File";
    let shortFileName = shortenFileName(fileName);
    document.getElementById('uploadTextPembayaran').textContent = shortFileName;
    document.getElementById('removeFilePembayaran').classList.remove('hidden');
});

document.getElementById('removeFilePembayaran').addEventListener('click', function() {
    let fileInput = document.getElementById('fotoInputPembayaran');
    fileInput.value = ""; 
    document.getElementById('uploadTextPembayaran').textContent = "Upload File";
    this.classList.add('hidden');
});

        
    </script>
{{-- </x-app-layout> --}}
@endsection