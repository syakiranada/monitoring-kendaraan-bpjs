<x-app-layout>
    @php
    $currentPage = request()->query('page', 1);
    @endphp 
    <a href="{{ route('kendaraan.daftar_kendaraan',  ['page' => $currentPage, 'search' => request()->query('search')]) }}"
        class="flex items-center text-blue-600 font-semibold hover:underline mb-5">
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"
            xmlns="http://www.w3.org/2000/svg">
            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"></path>
        </svg>
        Kembali
    </a>  
    <div class="min-h-screen flex items-start justify-center pt-4 sm:pt-3 px-4 sm:px-8 pb-8">
        <div class="max-w-4xl w-full">
            
            <div class="max-w-4xl w-full bg-white p-4 sm:p-8 md:p-12 rounded-lg shadow-lg">
                
                <h2 class="text-xl sm:text-2xl font-bold mb-4 sm:mb-6 text-center">Form Edit Kendaraan</h2>
                <form id="save-form" action="{{ route('kendaraan.update', ['id' => $kendaraan->id_kendaraan]) }}" method="POST" enctype="multipart/form-data" novalidate>

                    @csrf
                    @method('PUT')
                    <input type="hidden" name="id_kendaraan" value="{{ $kendaraan->id_kendaraan }}">
                    <input type="hidden" name="current_page" value="{{ $currentPage }}">   
                    <input type="hidden" name="user_id" value="{{ auth()->id() }}">
                    <input type="hidden" name="search" value="{{ request()->query('search', '') }}">

                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-2 lg:grid-cols-3 gap-4 mb-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Merk</label>
                            <input type="text" 
                                id="merk"
                                name="Merk" 
                                value="{{ $kendaraan->merk }}"
                                class="w-full p-2.5 border rounded-lg">
                            <p id="warning-merk" class="text-red-500 text-sm mt-1 hidden">Merk kendaraan harus diisi!</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tipe</label>
                            <input type="text" 
                                id="tipe"
                                name="Tipe" 
                                value="{{ $kendaraan->tipe }}"
                                class="w-full p-2.5 border rounded-lg">
                            <p id="warning-tipe" class="text-red-500 text-sm mt-1 hidden">Tipe kendaraan harus diisi!</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Plat Nomor (Tuliskan dengan spasi)</label>
                            <input type="text" 
                                id="plat_nomor"
                                name="Plat Nomor" 
                                value="{{ $kendaraan->plat_nomor }}"
                                class="w-full p-2.5 border rounded-lg">
                            <p id="warning-plat-nomor" class="text-red-500 text-sm mt-1 hidden">Plat nomor kendaraan harus diisi!</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Warna</label>
                            <input type="text" 
                                id="warna"
                                name="Warna" 
                                value="{{ $kendaraan->warna }}"
                                class="w-full p-2.5 border rounded-lg">
                            <p id="warning-warna" class="text-red-500 text-sm mt-1 hidden">Warna kendaraan harus diisi!</p>
                        </div>
                        <div>
                            <label for="jenis_kendaraan" class="block mb-2 text-sm font-medium text-gray-900">Jenis Kendaraan</label>
                            <select id="jenis_kendaraan" name="jenis_kendaraan" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                                <option {{ $kendaraan->jenis == 'Sedan' ? 'selected' : '' }}>Sedan</option>
                                <option {{ $kendaraan->jenis == 'Non Sedan' ? 'selected' : '' }}>Non Sedan</option>
                                <option {{ $kendaraan->jenis == 'Motor' ? 'selected' : '' }}>Motor</option>
                            </select>
                        </div>
                        <div>
                            <label for="aset_guna" class="block mb-2 text-sm font-medium text-gray-900">Aset Guna</label>
                            <select id="aset_guna" name="aset_guna" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                                <option value="Guna" {{ $kendaraan->aset == 'Guna' ? 'selected' : '' }}>Guna</option>
                                <option value="Tidak Guna" {{ $kendaraan->aset == 'Tidak Guna' ? 'selected' : '' }}>Tidak Guna</option>
                                <option value="Jual" {{ $kendaraan->aset == 'Jual' ? 'selected' : '' }}>Jual</option>
                                <option value="Lelang" {{ $kendaraan->aset == 'Lelang' ? 'selected' : '' }}>Lelang</option>
                            </select>
                        </div>                    
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Beli</label>
                            <input type="date" 
                                id="tanggal_beli"
                                name="Tanggal Beli" 
                                value="{{ \Carbon\Carbon::parse($kendaraan->tgl_pembelian)->format('Y-m-d') }}"
                                max="{{ \Carbon\Carbon::now()->format('Y-m-d') }}"
                                class="w-full p-2.5 border rounded-lg">
                            <p id="warning-tanggal-beli" class="text-red-500 text-sm mt-1 hidden">Tanggal beli harus diisi!</p>
                        </div>                    
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nilai Perolehan</label>
                            <div class="relative">
                                <span class="absolute left-2 top-1/2 transform -translate-y-1/2 text-gray-500">Rp</span>
                                <input type="text" 
                                    id="nilai_perolehan"
                                    name="Nilai Perolehan" 
                                    value="{{ number_format($kendaraan->nilai_perolehan, 0, ',', '.') }}"
                                    class="w-full pl-8 p-2.5 border rounded-lg" 
                                    oninput="formatRupiah(this)">
                            </div>
                            <p id="warning-nilai-perolehan" class="text-red-500 text-sm mt-1 hidden">Nilai perolehan harus diisi dan tidak boleh 0!</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nilai Buku</label>
                            <div class="relative">
                                <span class="absolute left-2 top-1/2 transform -translate-y-1/2 text-gray-500">Rp</span>
                                <input type="text" 
                                    id="nilai_buku"
                                    name="Nilai Buku" 
                                    value="{{ number_format($kendaraan->nilai_buku, 0, ',', '.') }}"
                                    class="w-full pl-8 p-2.5 border rounded-lg" 
                                    oninput="formatRupiah(this)">
                            </div>
                            <p id="warning-nilai-buku" class="text-red-500 text-sm mt-1 hidden">Nilai buku harus diisi dan tidak boleh 0!</p>
                        </div>
                        <div>
                            <label for="bahan_bakar" class="block mb-2 text-sm font-medium text-gray-900">Bahan Bakar</label>
                            <select id="bahan_bakar" name="bahan_bakar" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                                @foreach(['Pertalite', 'Pertamax', 'Pertamax Turbo', 'Dexlite', 'Pertamina Dex', 'Solar', 'BioSolar', 'Pertalite/Pertamax', 'Pertamax/Pertamax Turbo', 'Solar/Dexlite/Pertamina Dex', 'BioSolar/Solar/Dexlite'] as $fuel)
                                    <option {{ $kendaraan->bahan_bakar == $fuel ? 'selected' : '' }}>{{ $fuel }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nomor Mesin</label>
                            <input type="text" 
                                id="nomor_mesin"  
                                name="Nomor Mesin" 
                                value="{{ $kendaraan->no_mesin }}"
                                class="w-full p-2.5 border rounded-lg">
                            <p id="warning-nomor-mesin" class="text-red-500 text-sm mt-1 hidden">Nomor mesin harus diisi!</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Nomor Rangka</label>
                            <input type="text" 
                                id="nomor_rangka"
                                name="Nomor Rangka" 
                                value="{{ $kendaraan->no_rangka }}"
                                class="w-full p-2.5 border rounded-lg">
                            <p id="warning-nomor-rangka" class="text-red-500 text-sm mt-1 hidden">Nomor rangka harus diisi!</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Bayar Asuransi Terakhir</label>
                            <input type="date" 
                                   name="tanggal_asuransi" 
                                   value="{{ $asuransi->tgl_bayar ?? ''}}"
                                   max="{{ \Carbon\Carbon::now()->format('Y-m-d') }}"
                                   class="w-full p-2.5 border rounded-lg">
                        
                            <div id="warning-tanggal-asuransi" class="hidden text-red-500 text-sm"></div>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Masa Perlindungan Awal</label>
                            <input type="date" 
                                id="tanggal_perlindungan_awal"
                                name="tanggal_perlindungan_awal" 
                                value="{{ $asuransi->tgl_perlindungan_awal ?? ''}}"
                                class="w-full p-2.5 border rounded-lg">
                                <p id="warning-perlindungan-awal" class="text-red-500 text-sm mt-1 hidden"></p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Masa Perlindungan Akhir</label>
                            <input type="date" 
                                id="tanggal_perlindungan_akhir"
                                name="tanggal_perlindungan_akhir" 
                                value="{{ $asuransi->tgl_perlindungan_akhir ?? '' }}"
                                class="w-full p-2.5 border rounded-lg">
                            <p id="warning-perlindungan" class="text-red-500 text-sm mt-1 hidden">Tanggal perlindungan akhir harus lebih besar dari tanggal perlindungan awal!</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Bayar Pajak Terakhir</label>
                            <input type="date" 
                                name="Tanggal Bayar Pajak" 
                                value="{{ $pajak->tgl_bayar ?? ''}}"
                                max="{{ \Carbon\Carbon::now()->format('Y-m-d') }}"
                                class="w-full p-2.5 border rounded-lg">
                            <p class="text-red-500 text-sm mt-1 hidden" id="warning-tgl-bayar">Tanggal bayar pajak harus diisi!</p>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Jatuh Tempo Pajak Terakhir</label>
                            <input type="date" 
                                id="tanggal_jatuh_tempo_pajak"
                                name="Tanggal Jatuh Tempo Pajak" 
                                value="{{ $pajak->tgl_jatuh_tempo ?? ''}}"
                                class="w-full p-2.5 border rounded-lg">
                            <p class="text-red-500 text-sm mt-1 hidden" id="warning-jatuh-tempo">Tanggal jatuh tempo pajak harus diisi!</p>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Cek Fisik Terakhir</label>
                            <input type="date" 
                                name="Tanggal Cek Fisik" 
                                value="{{ $cekFisik->tgl_cek_fisik ?? ''}}"
                                max="{{ \Carbon\Carbon::now()->format('Y-m-d') }}"
                                class="w-full p-2.5 border rounded-lg">
                            <p class="text-red-500 text-sm mt-1 hidden" id="warning-cek-fisik">Tanggal cek fisik terakhir harus diisi!</p>
                        </div>                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Frekuensi Servis (Bulan)</label>
                            <input type="number" 
                                id="frekuensi"
                                name="Frekuensi" 
                                value="{{ $kendaraan->frekuensi_servis }}"
                                class="w-full p-2.5 border rounded-lg"
                                min="1"  
                                step="1"
                                oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                            <p id="warning-frekuensi" class="text-red-500 text-sm mt-1 hidden">Frekuensi servis harus diisi dan minimal 1!</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Kapasitas Penumpang</label>
                            <input type="number" 
                                id="kapasitas"
                                name="Kapasitas" 
                                value="{{ $kendaraan->kapasitas }}"
                                class="w-full p-2.5 border rounded-lg"
                                min="1"  
                                step="1"
                                oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                            <p id="warning-kapasitas" class="text-red-500 text-sm mt-1 hidden">Kapasitas penumpang harus diisi dan minimal 1!</p>
                        </div>
                        <div>
                            <label for="status_pinjam" class="block mb-2 text-sm font-medium text-gray-900">Status Pinjam</label>
                            <select id="status_pinjam" name="status_pinjam" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                                <option {{ $kendaraan->status_ketersediaan == 'TERSEDIA' ? 'selected' : '' }}>TERSEDIA</option>
                                <option {{ $kendaraan->status_ketersediaan == 'TIDAK TERSEDIA' ? 'selected' : '' }}>TIDAK TERSEDIA</option>
                            </select>
                        </div>
                    </div>

                    <div class="flex flex-col sm:flex-row justify-end sm:space-x-4 space-y-2 sm:space-y-0 mt-4">
                        <button type="button" id="saveButton" 
                            class="bg-blue-600 text-white px-6 py-2.5 rounded-lg font-semibold hover:bg-blue-700 transition w-fit min-w-[100px]">           
                            Simpan
                        </button>
                    </div>
                    
                    <div id="alertMessage" class="hidden p-4 mb-4 text-sm text-red-800 rounded-lg bg-red-50" role="alert">
                        <span class="font-medium">Peringatan!</span> Mohon isi semua kolom yang wajib sebelum menyimpan.
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
 document.getElementById('frekuensi').addEventListener('input', function(e) {
            this.value = this.value.replace(/[^0-9]/g, '');  
        });

        document.getElementById('kapasitas').addEventListener('input', function(e) {
            this.value = this.value.replace(/[^0-9]/g, '');  
        });

  document.addEventListener('DOMContentLoaded', function () {
    
    var today = new Date();
    var year = today.getFullYear();
    var month = String(today.getMonth() + 1).padStart(2, '0');
    var day = String(today.getDate()).padStart(2, '0');
    var todayStr = year + '-' + month + '-' + day;

    var selectorList = [
      '[name="Tanggal Beli"]',
      '[name="tanggal_asuransi"]',
      '[name="Tanggal Bayar Pajak"]',
      '[name="Tanggal Cek Fisik"]'
    ];

    selectorList.forEach(function (selector) {
      var input = document.querySelector(selector);
      if (input) {
        input.setAttribute('max', todayStr);
      }
    });
  });

   document.addEventListener('DOMContentLoaded', function() {
    function formatRupiah(input) {
        let value = input.value.replace(/[^\d]/g, '');
        input.dataset.numericValue = value;
        if (value.length > 0) {
            value = BigInt(value).toLocaleString('id-ID');
        }
        input.value = value;
    }

    window.formatRupiah = formatRupiah;

    function prepareFormForSubmission() {
        const rupiahInputs = document.querySelectorAll('input[oninput="formatRupiah(this)"]');
        rupiahInputs.forEach(input => {
            input.value = input.dataset.numericValue || input.value.replace(/[^\d]/g, '');
        });
        
        return true;
    }
    
function setupDateValidation() {
    const dateFields = [
        { name: 'Tanggal Bayar Pajak', label: 'Tanggal Bayar Pajak', warningId: 'warning-tgl-bayar' },
        { name: 'Tanggal Cek Fisik', label: 'Tanggal Cek Fisik', warningId: 'warning-cek-fisik' },
        { name: 'Tanggal Beli', label: 'Tanggal Beli', warningId: 'warning-tanggal-beli' },
        { name: 'tanggal_asuransi', label: 'Tanggal Asuransi Terakhir', warningId: 'warning-tanggal-asuransi' }
    ];
    
    dateFields.forEach(field => {
        const input = document.querySelector(`[name="${field.name}"]`);
        if (!input) {
            return;
        }
        
        const warningElement = field.warningId ? document.getElementById(field.warningId) : null;
        
        const today = new Date().toISOString().split('T')[0];
        input.setAttribute('max', today);
       
        function validateDate() {
            
            if (!this.value) {
                showWarning(this, warningElement, `${field.label} harus diisi!`);
                return;
            }
            
            const inputDate = new Date(this.value);
            
            if (isNaN(inputDate.getTime())) {
                showWarning(this, warningElement, `Format ${field.label} tidak valid.`);
                return;
            }
            
            inputDate.setHours(0, 0, 0, 0);
            
            const todayDate = new Date();
            todayDate.setHours(0, 0, 0, 0);
            
            if (inputDate > todayDate) {
                showWarning(this, warningElement, `${field.label} tidak boleh lebih dari hari ini.`);
            } else {
                hideWarning(this, warningElement);
            }
        }
        
        if (input.value) {
            
            const inputDate = new Date(input.value);
            inputDate.setHours(0, 0, 0, 0);
            
            const todayDate = new Date();
            todayDate.setHours(0, 0, 0, 0);
            
            if (isNaN(inputDate.getTime())) {
                showWarning(input, warningElement, `Format ${field.label} tidak valid.`);
            } 
            else if (inputDate > todayDate) {
                showWarning(input, warningElement, `${field.label} tidak boleh lebih dari hari ini.`);
            }
        }
        
        input.removeEventListener('input', validateDate);
        input.removeEventListener('change', validateDate);
        input.removeEventListener('blur', validateDate);
        input.addEventListener('input', validateDate);
        input.addEventListener('change', validateDate);
        input.addEventListener('blur', validateDate);
    });
    
    const tanggalJatuhTempo = document.querySelector('[name="Tanggal Jatuh Tempo Pajak"]');
    const warningJatuhTempo = document.getElementById('warning-jatuh-tempo');
    
    if (tanggalJatuhTempo) {
        
        function validateJatuhTempo() {
            
            if (!this.value) {
                showWarning(this, warningJatuhTempo, 'Tanggal Jatuh Tempo Pajak harus diisi!');
                return;
            }
            
            const inputDate = new Date(this.value);
            
            if (isNaN(inputDate.getTime())) {
                showWarning(this, warningJatuhTempo, 'Format Tanggal Jatuh Tempo Pajak tidak valid.');
                return;
            }
            
            hideWarning(this, warningJatuhTempo);
        }
        
        if (tanggalJatuhTempo.value) {
            const inputDate = new Date(tanggalJatuhTempo.value);
            
            if (isNaN(inputDate.getTime())) {
                showWarning(tanggalJatuhTempo, warningJatuhTempo, 'Format Tanggal Jatuh Tempo Pajak tidak valid.');
            } else {
                hideWarning(tanggalJatuhTempo, warningJatuhTempo);
            }
        }
        
        tanggalJatuhTempo.removeEventListener('input', validateJatuhTempo);
        tanggalJatuhTempo.removeEventListener('change', validateJatuhTempo);
        tanggalJatuhTempo.removeEventListener('blur', validateJatuhTempo);
        tanggalJatuhTempo.addEventListener('input', validateJatuhTempo);
        tanggalJatuhTempo.addEventListener('change', validateJatuhTempo);
        tanggalJatuhTempo.addEventListener('blur', validateJatuhTempo);
    }
    
    const tanggalAsuransi = document.querySelector('[name="tanggal_asuransi"]');
    const tanggalPerlindunganAwal = document.querySelector('[name="tanggal_perlindungan_awal"]');
    const tanggalPerlindunganAkhir = document.querySelector('[name="tanggal_perlindungan_akhir"]');
    const warningAsuransi = document.getElementById('warning-tanggal-asuransi');
    const warningPerlindunganAwal = document.getElementById('warning-perlindungan-awal');
    const warningPerlindunganAkhir = document.getElementById('warning-perlindungan');
    
    if (tanggalAsuransi && tanggalPerlindunganAwal && tanggalPerlindunganAkhir) {
        function validateTanggalAsuransi() {
            const asuransiIsFilled = tanggalAsuransi.value.trim() !== '';
            const awalIsFilled = tanggalPerlindunganAwal.value.trim() !== '';
            const akhirIsFilled = tanggalPerlindunganAkhir.value.trim() !== '';
            if (!asuransiIsFilled && !awalIsFilled && !akhirIsFilled) {
                hideWarning(tanggalAsuransi, warningAsuransi);
                return;
            }
            
            if ((awalIsFilled || akhirIsFilled) && !asuransiIsFilled) {
                showWarning(tanggalAsuransi, warningAsuransi, 'Tanggal Asuransi harus diisi karena Tanggal Perlindungan sudah diisi.');
                return;
            }
            
            if (asuransiIsFilled) {
                const asuransiDate = new Date(tanggalAsuransi.value);
                const todayDate = new Date();
                
                if (isNaN(asuransiDate.getTime())) {
                    showWarning(tanggalAsuransi, warningAsuransi, 'Format Tanggal Asuransi tidak valid.');
                    return;
                }
                
                if (asuransiDate > todayDate) {
                    showWarning(tanggalAsuransi, warningAsuransi, 'Tanggal Asuransi tidak boleh lebih dari hari ini.');
                    return;
                }
            }
            
            hideWarning(tanggalAsuransi, warningAsuransi);
        }
        
        function validateTanggalPerlindunganAwal() {
            const asuransiIsFilled = tanggalAsuransi.value.trim() !== '';
            const awalIsFilled = tanggalPerlindunganAwal.value.trim() !== '';
            const akhirIsFilled = tanggalPerlindunganAkhir.value.trim() !== '';
            if (!asuransiIsFilled && !awalIsFilled && !akhirIsFilled) {
                hideWarning(tanggalPerlindunganAwal, warningPerlindunganAwal);
                return;
            }
            
            if ((asuransiIsFilled || akhirIsFilled) && !awalIsFilled) {
                showWarning(tanggalPerlindunganAwal, warningPerlindunganAwal, 'Tanggal Perlindungan Awal harus diisi karena data terkait asuransi sudah diisi.');
                return;
            }
            
            if (awalIsFilled) {
                const awalDate = new Date(tanggalPerlindunganAwal.value);
                
                if (isNaN(awalDate.getTime())) {
                    showWarning(tanggalPerlindunganAwal, warningPerlindunganAwal, 'Format Tanggal Perlindungan Awal tidak valid.');
                    return;
                }
            }
            
            hideWarning(tanggalPerlindunganAwal, warningPerlindunganAwal);
        }
        function validateTanggalPerlindunganAkhir() {
            const asuransiIsFilled = tanggalAsuransi.value.trim() !== '';
            const awalIsFilled = tanggalPerlindunganAwal.value.trim() !== '';
            const akhirIsFilled = tanggalPerlindunganAkhir.value.trim() !== '';
            if (!asuransiIsFilled && !awalIsFilled && !akhirIsFilled) {
                hideWarning(tanggalPerlindunganAkhir, warningPerlindunganAkhir);
                return;
            }
            
            if ((asuransiIsFilled || awalIsFilled) && !akhirIsFilled) {
                showWarning(tanggalPerlindunganAkhir, warningPerlindunganAkhir, 'Tanggal Perlindungan Akhir harus diisi karena data terkait asuransi sudah diisi.');
                return;
            }
            
            if (akhirIsFilled) {
                const akhirDate = new Date(tanggalPerlindunganAkhir.value);
                
                if (isNaN(akhirDate.getTime())) {
                    showWarning(tanggalPerlindunganAkhir, warningPerlindunganAkhir, 'Format Tanggal Perlindungan Akhir tidak valid.');
                    return;
                }

                if (awalIsFilled) {
                    const awalDate = new Date(tanggalPerlindunganAwal.value);
                    
                    if (!isNaN(awalDate.getTime()) && akhirDate <= awalDate) {
                        showWarning(tanggalPerlindunganAkhir, warningPerlindunganAkhir, 'Tanggal Perlindungan Akhir harus lebih besar dari Tanggal Perlindungan Awal.');
                        return;
                    }
                }
            }
            hideWarning(tanggalPerlindunganAkhir, warningPerlindunganAkhir);
        }
        
        function validateAllInsuranceFields() {
            validateTanggalAsuransi();
            validateTanggalPerlindunganAwal();
            validateTanggalPerlindunganAkhir();
        }
        
        validateAllInsuranceFields();
        tanggalAsuransi.removeEventListener('input', validateTanggalAsuransi);
        tanggalAsuransi.removeEventListener('change', validateTanggalAsuransi);
        tanggalAsuransi.removeEventListener('blur', validateTanggalAsuransi);
        tanggalPerlindunganAwal.removeEventListener('input', validateTanggalPerlindunganAwal);
        tanggalPerlindunganAwal.removeEventListener('change', validateTanggalPerlindunganAwal);
        tanggalPerlindunganAwal.removeEventListener('blur', validateTanggalPerlindunganAwal);
        tanggalPerlindunganAkhir.removeEventListener('input', validateTanggalPerlindunganAkhir);
        tanggalPerlindunganAkhir.removeEventListener('change', validateTanggalPerlindunganAkhir);
        tanggalPerlindunganAkhir.removeEventListener('blur', validateTanggalPerlindunganAkhir);

        tanggalAsuransi.addEventListener('input', function() {
            validateAllInsuranceFields();
        });
        tanggalAsuransi.addEventListener('change', function() {
            validateAllInsuranceFields();
        });
        tanggalAsuransi.addEventListener('blur', function() {
            validateAllInsuranceFields();
        });
        
        tanggalPerlindunganAwal.addEventListener('input', function() {
            validateAllInsuranceFields();
        });
        tanggalPerlindunganAwal.addEventListener('change', function() {
            validateAllInsuranceFields();
        });
        tanggalPerlindunganAwal.addEventListener('blur', function() {
            validateAllInsuranceFields();
        });
        
        tanggalPerlindunganAkhir.addEventListener('input', function() {
            validateAllInsuranceFields();
        });
        tanggalPerlindunganAkhir.addEventListener('change', function() {
            validateAllInsuranceFields();
        });
        tanggalPerlindunganAkhir.addEventListener('blur', function() {
            validateAllInsuranceFields();
        });
    }
}

function addValidationStyles() {
    const existingStyle = document.getElementById('validation-fix-styles');
    if (existingStyle) {
        existingStyle.remove();
    }
    
    const style = document.createElement('style');
    style.id = 'validation-fix-styles';
    style.textContent = `
        /* Pastikan error selalu prioritas tertinggi */
        .input-error, input.input-error, select.input-error, textarea.input-error {
            border-color: red !important;
            box-shadow: none !important;
        }
        
        /* Pastikan focus tidak mengganggu error */
        .input-error:focus, input.input-error:focus {
            border-color: red !important;
            box-shadow: none !important;
        }
    `;
    
    document.head.appendChild(style);
}

function showWarning(input, warningElement, message) {
   
    if (warningElement) {
        warningElement.textContent = message;
        warningElement.style.display = 'block';
        warningElement.style.color = 'red';
        warningElement.classList.remove('hidden');
    }

    if (input) {
        input.classList.remove(
            'input-success',
            'input-focused',
            'border-green-500'
        );
        
        input.classList.add('input-error', 'border-red-500');
    }
}
function hideWarning(inputElement, warningElement) {
    if (inputElement) {
        inputElement.classList.remove('input-error', 'border-red-500');
    }

    if (warningElement) {
        warningElement.classList.add('hidden');
        warningElement.textContent = '';
    }
}

function patchValidationFunctions() {
    addValidationStyles();
    window.showWarning = showWarning;
    window.hideWarning = hideWarning;
    
    const vitalFields = [
        { name: 'Nomor Mesin', label: 'Nomor Mesin', warningId: 'warning-nomor-mesin', maxLength: 17 },
        { name: 'Nomor Rangka', label: 'Nomor Rangka', warningId: 'warning-nomor-rangka', maxLength: 20 }
    ];

    vitalFields.forEach(field => {
        const input = document.querySelector(`[name="${field.name}"]`);
        if (input) {
            const newInput = input.cloneNode(true);
            if (input.parentNode) {
                input.parentNode.replaceChild(newInput, input);
            }

            const warningElement = document.getElementById(field.warningId);

            newInput.addEventListener('input', function() {
                if (!this.value.trim()) {
                    showWarning(this, warningElement, `${field.label} harus diisi!`);
                } else if (this.value.length > field.maxLength) {
                    showWarning(this, warningElement, `${field.label} maksimal ${field.maxLength} karakter!`);
                } else {
                    hideWarning(this, warningElement);
                }
            });

            newInput.addEventListener('blur', function() {
                if (!this.value.trim()) {
                    showWarning(this, warningElement, `${field.label} harus diisi!`);
                }
            });

            if (!newInput.value.trim()) {
                showWarning(newInput, warningElement, `${field.label} harus diisi!`);
            }
        }
    });
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', patchValidationFunctions);
} else {
    patchValidationFunctions();
}

function setupRealTimeValidation() {
    const fieldLimits = {
        'Merk': { maxLength: 50, warningId: 'warning-merk', required: true },
        'Tipe': { maxLength: 40, warningId: 'warning-tipe', required: true },
        'Plat Nomor': { maxLength: 20, warningId: 'warning-plat-nomor', required: true },
        'Warna': { maxLength: 20, warningId: 'warning-warna', required: true },
        'Nomor Mesin': { maxLength: 17, warningId: 'warning-nomor-mesin', required: true },
        'Nomor Rangka': { maxLength: 20, warningId: 'warning-nomor-rangka', required: true }
    };
    
    Object.keys(fieldLimits).forEach(fieldName => {
        const input = document.querySelector(`[name="${fieldName}"]`);
        const config = fieldLimits[fieldName];
        
        if (input) {
            const newInput = input.cloneNode(true);
            input.parentNode.replaceChild(newInput, input);
            newInput.addEventListener('input', function() {
                const warningElement = document.getElementById(config.warningId);
                
                if (this.value.length > config.maxLength) {
                    const message = `${fieldName} maksimal ${config.maxLength} karakter!`;
                    showWarning(this, warningElement, message);
                } else if (config.required && !this.value.trim()) {
                    const message = `${fieldName} harus diisi!`;
                    showWarning(this, warningElement, message);
                } else {
                    hideWarning(this, warningElement);
                }
            });
            
            newInput.addEventListener('blur', function() {
                const warningElement = document.getElementById(config.warningId);
                
                if (config.required && !this.value.trim()) {
                    const message = `${fieldName} harus diisi!`;
                    showWarning(this, warningElement, message);
                }
            });
            
            if (config.required && !newInput.value.trim()) {
                const warningElement = document.getElementById(config.warningId);
                const message = `${fieldName} harus diisi!`;
                showWarning(newInput, warningElement, message);
            }
        }
    });
    
    const platNomorInput = document.querySelector('[name="Plat Nomor"]');
    if (platNomorInput) {
        let plateCheckTimeout;
        
        platNomorInput.addEventListener('input', function() {
            const warningElement = document.getElementById('warning-plat-nomor');
            const platNomor = this.value.trim();
            if (platNomor.length > 20) {
                showWarning(this, warningElement, 'Plat Nomor maksimal 20 karakter!');
            } else if (!platNomor) {
                showWarning(this, warningElement, 'Plat Nomor harus diisi!');
            } else {
                hideWarning(this, warningElement);
                clearTimeout(plateCheckTimeout);
                
                plateCheckTimeout = setTimeout(() => {
                    if (platNomor.length >= 2) { 
                        const currentVehicleId = document.querySelector('input[name="id_kendaraan"]')?.value || '0';
                        
                        showWarning(this, warningElement, 'Memeriksa ketersediaan plat nomor...');
                        
                        fetch('/admin/kendaraan/check-plat?plat_nomor=' + encodeURIComponent(platNomor) + '&exclude_id=' + currentVehicleId)
                            .then(response => response.json())
                            .then(data => {
                                if (data.exists) {
                                    showWarning(this, warningElement, 'Plat nomor sudah digunakan oleh kendaraan lain.');
                                } else {
                                    hideWarning(this, warningElement);
                                }
                            })
                            .catch(error => {
                                hideWarning(this, warningElement);
                            });
                    }
                }, 500); 
            }
        });
    }
    
    const integerFields = [
        { name: 'Frekuensi', warningId: 'warning-frekuensi', maxValue: 2147483647 }, 
        { name: 'Kapasitas', warningId: 'warning-kapasitas', maxValue: 2147483647 }  
    ];
    
    integerFields.forEach(field => {
        const input = document.querySelector(`[name="${field.name}"]`);
        if (input) {
            const newInput = input.cloneNode(true);
            input.parentNode.replaceChild(newInput, input);
            
            newInput.addEventListener('input', function() {
                const warningElement = document.getElementById(field.warningId);
                
                if (this.value.match(/[^0-9]/g)) {
                    this.value = this.value.replace(/[^0-9]/g, '');
                }
                
                const value = parseInt(this.value) || 0;
                
                if (value === 0) {
                    const message = `${field.name} harus diisi dan minimal 1!`;
                    showWarning(this, warningElement, message);
                } else if (value > field.maxValue) {
                    const message = `${field.name} tidak boleh lebih dari ${field.maxValue}!`;
                    showWarning(this, warningElement, message);
                } else {
                    hideWarning(this, warningElement);
                }
            });
            
            newInput.addEventListener('blur', function() {
                const warningElement = document.getElementById(field.warningId);
                const value = parseInt(this.value) || 0;
                
                if (value === 0) {
                    const message = `${field.name} harus diisi dan minimal 1!`;
                    showWarning(this, warningElement, message);
                }
            });
        
            const value = parseInt(newInput.value) || 0;
            if (value === 0) {
                const warningElement = document.getElementById(field.warningId);
                const message = `${field.name} harus diisi dan minimal 1!`;
                showWarning(newInput, warningElement, message);
            }
        }
    });
    
    const requiredFields = [
        { name: 'jenis_kendaraan', label: 'Jenis Kendaraan' },
        { name: 'aset_guna', label: 'Aset Guna' },
        { name: 'bahan_bakar', label: 'Bahan Bakar' },
        { name: 'status_pinjam', label: 'Status Pinjam' }
    ];
    
    requiredFields.forEach(field => {
        const input = document.querySelector(`[name="${field.name}"]`);
        if (input) {
            const newInput = input.cloneNode(true);
            input.parentNode.replaceChild(newInput, input);
            
            const warningElement = field.warningId ? document.getElementById(field.warningId) : null;
            newInput.addEventListener('input', function() {
                if (!this.value.trim()) {
                    const message = `${field.label} harus diisi!`;
                    showWarning(this, warningElement, message);
                } else {
                    hideWarning(this, warningElement);
                }
            });
            
            newInput.addEventListener('blur', function() {
                if (!this.value.trim()) {
                    const message = `${field.label} harus diisi!`;
                    showWarning(this, warningElement, message);
                } else {
                    hideWarning(this, warningElement);
                }
            });
            
            if (!newInput.value.trim()) {
                const message = `${field.label} harus diisi!`;
                showWarning(newInput, warningElement, message);
            }
        }
    });

    
    const vitalFields = [
    { name: 'Nomor Mesin', label: 'Nomor Mesin', warningId: 'warning-nomor-mesin', maxLength: 17 },
    { name: 'Nomor Rangka', label: 'Nomor Rangka', warningId: 'warning-nomor-rangka', maxLength: 20 }
];

vitalFields.forEach(field => {
    const input = document.querySelector(`[name="${field.name}"]`);
    if (input) {
        const newInput = input.cloneNode(true);
        input.parentNode.replaceChild(newInput, input);

        const warningElement = document.getElementById(field.warningId);

        newInput.addEventListener('input', function () {
            if (!this.value.trim()) {
                const message = `${field.label} harus diisi!`;
                showWarning(this, warningElement, message);
            } else if (this.value.length > field.maxLength) {
                const message = `${field.label} maksimal ${field.maxLength} karakter!`;
                showWarning(this, warningElement, message);
            } else {
                hideWarning(this, warningElement);
            }
        });

        newInput.addEventListener('blur', function () {
            if (!this.value.trim()) {
                const message = `${field.label} harus diisi!`;
                showWarning(this, warningElement, message);
            }
        });

        if (!newInput.value.trim()) {
            const message = `${field.label} harus diisi!`;
            showWarning(newInput, warningElement, message);
        }
    } else {
    }
});

    setupDateValidation();
    setupCurrencyValidation();
}

function setupCurrencyValidation() {
    const MAX_VALUE = BigInt("9223372036854775807");
    
    const currencyFields = [
        { name: 'Nilai Perolehan', warningId: 'warning-nilai-perolehan' },
        { name: 'Nilai Buku', warningId: 'warning-nilai-buku' }
    ];
    
    currencyFields.forEach(field => {
        const input = document.querySelector(`[name="${field.name}"]`);
        if (input) {
            const newInput = input.cloneNode(true);
            input.parentNode.replaceChild(newInput, input);
            
            const warningElement = document.getElementById(field.warningId);
            newInput.addEventListener('input', function() {
                const numericValue = this.value.replace(/[^\d]/g, '');
                
                if (!numericValue || BigInt(numericValue) === 0) {
                    const message = `${field.name} harus diisi dan tidak boleh 0!`;
                    showWarning(this, warningElement, message);
                } else {
                    try {
                        const bigIntValue = BigInt(numericValue);
                        if (bigIntValue > MAX_VALUE) {
                            const message = `${field.name} tidak boleh lebih dari 9.223.372.036.854.775.807!`;
                            showWarning(this, warningElement, message);
                        } else {
                            hideWarning(this, warningElement);
                        }
                    } catch (e) {
                        const message = `Format ${field.name} tidak valid!`;
                        showWarning(this, warningElement, message);
                    }
                }
                
                this.dataset.numericValue = numericValue;
            });
            
            newInput.addEventListener('blur', function() {
                const numericValue = this.value.replace(/[^\d]/g, '');
                
                if (!numericValue || BigInt(numericValue) === 0) {
                    const message = `${field.name} harus diisi dan tidak boleh 0!`;
                    showWarning(this, warningElement, message);
                } else {
                    try {
                        const bigIntValue = BigInt(numericValue);
                        if (bigIntValue > MAX_VALUE) {
                            const message = `${field.name} tidak boleh lebih dari 9.223.372.036.854.775.807!`;
                            showWarning(this, warningElement, message);
                        }
                    } catch (e) {
                        const message = `Format ${field.name} tidak valid!`;
                        showWarning(this, warningElement, message);
                    }
                }
            });
            
            const numericValue = newInput.value.replace(/[^\d]/g, '');
            if (!numericValue || BigInt(numericValue) === 0) {
                const message = `${field.name} harus diisi dan tidak boleh 0!`;
                showWarning(newInput, warningElement, message);
            } else {
                try {
                    const bigIntValue = BigInt(numericValue);
                    if (bigIntValue > MAX_VALUE) {
                        const message = `${field.name} tidak boleh lebih dari 9.223.372.036.854.775.807!`;
                        showWarning(newInput, warningElement, message);
                    }
                } catch (e) {
                }
            }
        }
    });
}

window.validateInputs = function() {
    let errors = [];
    let isValid = true;
    const fieldsMaxLength = [
        { name: 'Merk', label: 'Merk', maxLength: 50, warningId: 'warning-merk', message: 'Merk maksimal 50 karakter!' },
        { name: 'Tipe', label: 'Tipe', maxLength: 40, warningId: 'warning-tipe', message: 'Tipe maksimal 40 karakter!' },
        { name: 'Plat Nomor', label: 'Plat Nomor', maxLength: 20, warningId: 'warning-plat-nomor', message: 'Plat Nomor maksimal 20 karakter!' },
        { name: 'Warna', label: 'Warna', maxLength: 20, warningId: 'warning-warna', message: 'Warna maksimal 20 karakter!' },
        { name: 'Nomor Mesin', label: 'Nomor Mesin', maxLength: 17, warningId: 'warning-nomor-mesin', message: 'Nomor Mesin maksimal 17 karakter!' },
        { name: 'Nomor Rangka', label: 'Nomor Rangka', maxLength: 20, warningId: 'warning-nomor-rangka', message: 'Nomor Rangka maksimal 20 karakter!' },
    ];
    
    fieldsMaxLength.forEach(field => {
        const input = document.querySelector(`[name="${field.name}"]`);
        const warningElement = document.getElementById(field.warningId);
        
        if (input && input.value.length > field.maxLength) {
            showWarning(input, warningElement, field.message);
            errors.push(field.message);
            isValid = false;
        }
    });
    
    let fields = [
        { name: 'Merk', label: 'Merk', warningId: 'warning-merk' },
        { name: 'Tipe', label: 'Tipe', warningId: 'warning-tipe' },
        { name: 'Plat Nomor', label: 'Plat Nomor', warningId: 'warning-plat-nomor' },
        { name: 'Warna', label: 'Warna', warningId: 'warning-warna' },
        { name: 'jenis_kendaraan', label: 'Jenis Kendaraan' },
        { name: 'aset_guna', label: 'Aset Guna' },
        { name: 'Tanggal Beli', label: 'Tanggal Beli', warningId: 'warning-tanggal-beli' },
        { name: 'bahan_bakar', label: 'Bahan Bakar' },
        { name: 'Nomor Mesin', label: 'Nomor Mesin', warningId: 'warning-nomor-mesin' },
        { name: 'Nomor Rangka', label: 'Nomor Rangka', warningId: 'warning-nomor-rangka' },
        { name: 'Tanggal Bayar Pajak', label: 'Tanggal Bayar Pajak', warningId: 'warning-tgl-bayar' },
        { name: 'Tanggal Jatuh Tempo Pajak', label: 'Tanggal Jatuh Tempo Pajak', warningId: 'warning-jatuh-tempo' },
        { name: 'Tanggal Cek Fisik', label: 'Tanggal Cek Fisik', warningId: 'warning-cek-fisik' },
        { name: 'status_pinjam', label: 'Status Pinjam' }
    ];
    
    fields.forEach(field => {
        const input = document.querySelector(`[name="${field.name}"]`);
        const warningElement = field.warningId ? document.getElementById(field.warningId) : null;
        
        if (!input || !input.value.trim()) {
            errors.push(`${field.label} harus diisi!`);
            showWarning(input, warningElement, `${field.label} harus diisi!`);
            isValid = false;
        }
    });

    const platNomorInput = document.querySelector('[name="Plat Nomor"]');
    const warningPlatNomor = document.getElementById('warning-plat-nomor');
    
    if (platNomorInput && warningPlatNomor && !warningPlatNomor.classList.contains('hidden') && 
        warningPlatNomor.textContent.includes('sudah digunakan')) {
        errors.push('Plat nomor sudah digunakan oleh kendaraan lain.');
        isValid = false;
    }
    
    const today = new Date();
    today.setHours(0, 0, 0, 0);
    
    let dateFields = [
        { name: 'Tanggal Bayar Pajak', label: 'Tanggal Bayar Pajak', warningId: 'warning-tgl-bayar' },
        { name: 'Tanggal Beli', label: 'Tanggal Beli', warningId: 'warning-tanggal-beli' },
        { name: 'tanggal_asuransi', label: 'Tanggal Asuransi Terakhir', warningId: 'warning-tanggal-asuransi' },
        { name: 'Tanggal Cek Fisik', label: 'Tanggal Cek Fisik', warningId: 'warning-cek-fisik' }
    ];
    
    for (let field of dateFields) {
        let input = document.querySelector(`[name="${field.name}"]`);
        const warningElement = field.warningId ? document.getElementById(field.warningId) : null;
        
        if (input && input.value.trim()) {
            const inputDate = new Date(input.value);
            inputDate.setHours(0, 0, 0, 0);
            
            if (inputDate > today) {
                errors.push(`${field.label} tidak boleh lebih dari hari ini.`);
                showWarning(input, warningElement, `${field.label} tidak boleh lebih dari hari ini.`);
                isValid = false;
            }
        }
    }
    
    const tanggalAsuransi = document.querySelector('[name="tanggal_asuransi"]');
    const tanggalPerlindunganAwal = document.querySelector('[name="tanggal_perlindungan_awal"]');
    const tanggalPerlindunganAkhir = document.querySelector('[name="tanggal_perlindungan_akhir"]');
    const warningAsuransi = document.getElementById('warning-tanggal-asuransi');
    const warningPerlindunganAwal = document.getElementById('warning-perlindungan-awal');
    const warningPerlindunganAkhir = document.getElementById('warning-perlindungan');
    
    if (tanggalAsuransi && tanggalPerlindunganAwal && tanggalPerlindunganAkhir) {
        
        const asuransiValue = tanggalAsuransi.value.trim();
        const awalValue = tanggalPerlindunganAwal.value.trim();
        const akhirValue = tanggalPerlindunganAkhir.value.trim();
       
        const asuransiIsFilled = asuransiValue !== '';
        const awalIsFilled = awalValue !== '';
        const akhirIsFilled = akhirValue !== '';
       
        if (asuransiIsFilled || awalIsFilled || akhirIsFilled) {
           
            if (!asuransiIsFilled) {
                errors.push('Tanggal Asuransi harus diisi karena data terkait asuransi sudah diisi.');
                showWarning(tanggalAsuransi, warningAsuransi, 'Tanggal Asuransi harus diisi karena data terkait asuransi sudah diisi.');
                isValid = false;
            } else {
               
                const asuransiDate = new Date(asuransiValue);
                if (isNaN(asuransiDate.getTime())) {
                    errors.push('Format Tanggal Asuransi tidak valid.');
                    showWarning(tanggalAsuransi, warningAsuransi, 'Format Tanggal Asuransi tidak valid.');
                    isValid = false;
                } else if (asuransiDate > today) {
                    errors.push('Tanggal Asuransi tidak boleh lebih dari hari ini.');
                    showWarning(tanggalAsuransi, warningAsuransi, 'Tanggal Asuransi tidak boleh lebih dari hari ini.');
                    isValid = false;
                }
            }
            
            if (!awalIsFilled) {
                errors.push('Tanggal Perlindungan Awal harus diisi karena data terkait asuransi sudah diisi.');
                showWarning(tanggalPerlindunganAwal, warningPerlindunganAwal, 'Tanggal Perlindungan Awal harus diisi karena data terkait asuransi sudah diisi.');
                isValid = false;
            } else {
                const awalDate = new Date(awalValue);
                if (isNaN(awalDate.getTime())) {
                    errors.push('Format Tanggal Perlindungan Awal tidak valid.');
                    showWarning(tanggalPerlindunganAwal, warningPerlindunganAwal, 'Format Tanggal Perlindungan Awal tidak valid.');
                    isValid = false;
                }
            }
            
            if (!akhirIsFilled) {
                errors.push('Tanggal Perlindungan Akhir harus diisi karena data terkait asuransi sudah diisi.');
                showWarning(tanggalPerlindunganAkhir, warningPerlindunganAkhir, 'Tanggal Perlindungan Akhir harus diisi karena data terkait asuransi sudah diisi.');
                isValid = false;
            } else {
                const akhirDate = new Date(akhirValue);
                if (isNaN(akhirDate.getTime())) {
                    errors.push('Format Tanggal Perlindungan Akhir tidak valid.');
                    showWarning(tanggalPerlindunganAkhir, warningPerlindunganAkhir, 'Format Tanggal Perlindungan Akhir tidak valid.');
                    isValid = false;
                } else if (awalIsFilled) {
                    const awalDate = new Date(awalValue);
                    if (!isNaN(awalDate.getTime()) && akhirDate <= awalDate) {
                        errors.push('Tanggal Perlindungan Akhir harus lebih besar dari Tanggal Perlindungan Awal.');
                        showWarning(tanggalPerlindunganAkhir, warningPerlindunganAkhir, 'Tanggal Perlindungan Akhir harus lebih besar dari Tanggal Perlindungan Awal.');
                        isValid = false;
                    }
                }
            }
        }
    }
   
    const numericInputs = document.querySelectorAll('input[type="number"]');
    numericInputs.forEach(function(input) {
        if (input.name) {
            const value = parseInt(input.value) || 0;
            const fieldName = input.name;
            let warningId = '';
            let maxValue = 2147483647; 
            
            if (fieldName === 'Frekuensi') {
                warningId = 'warning-frekuensi';
            } else if (fieldName === 'Kapasitas') {
                warningId = 'warning-kapasitas';
            }
            
            const warningElement = document.getElementById(warningId);
            
            if (value <= 0) {
                const warningMsg = `${input.getAttribute('placeholder') || input.name} harus diisi dan minimal 1!`;
                errors.push(warningMsg);
                showWarning(input, warningElement, warningMsg);
                isValid = false;
            } else if (value > maxValue) {
                const warningMsg = `${input.getAttribute('placeholder') || input.name} maksimal ${maxValue}!`;
                errors.push(warningMsg);
                showWarning(input, warningElement, warningMsg);
                isValid = false;
            }
        }
    });
    
const MAX_VALUE = BigInt("9223372036854775807");

const currencyInputs = document.querySelectorAll('input[oninput="formatRupiah(this)"]');
currencyInputs.forEach(function(input) {
    const numericValue = input.dataset.numericValue || input.value.replace(/[^\d]/g, '');
    const fieldName = input.name;
    let warningId = '';
    
    if (fieldName === 'Nilai Perolehan') {
        warningId = 'warning-nilai-perolehan';
    } else if (fieldName === 'Nilai Buku') {
        warningId = 'warning-nilai-buku';
    }
    
    const warningElement = document.getElementById(warningId);
    
    if (!numericValue || BigInt(numericValue) === 0) {
        const warningMsg = `${input.getAttribute('placeholder') || input.name} harus diisi dan tidak boleh 0!`;
        errors.push(warningMsg);
        showWarning(input, warningElement, warningMsg);
        isValid = false;
    } else {
       
        try {
            const bigIntValue = BigInt(numericValue);
            if (bigIntValue > MAX_VALUE) {
                const warningMsg = `${input.getAttribute('placeholder') || input.name} tidak boleh lebih dari 9.223.372.036.854.775.807!`;
                errors.push(warningMsg);
                showWarning(input, warningElement, warningMsg);
                isValid = false;
            }
        } catch (e) {
           
            const warningMsg = `Format ${input.getAttribute('placeholder') || input.name} tidak valid!`;
            errors.push(warningMsg);
            showWarning(input, warningElement, warningMsg);
            isValid = false;
        }
    }
});
    
    return { isValid, errors };
};
document.addEventListener('DOMContentLoaded', function() {
    setupRealTimeValidation();
});

if (document.readyState === 'complete' || document.readyState === 'interactive') {
    setupRealTimeValidation();
}

(function() {
   
    const existingStyle = document.getElementById('validation-styles');
    if (existingStyle) {
        existingStyle.remove();
    }
    
    const style = document.createElement('style');
    style.id = 'validation-styles';
    style.textContent = `
        /* Reset default focus styles that might interfere */
        input:focus {
            outline: none !important;
        }
        
        /* Force input-error to take precedence */
        input.input-error {
            transition: border-color 0.3s ease !important;
            border-color: red !important;
            border-width: 1px !important;
            box-shadow: 0 0 0 1px rgba(255, 0, 0, 0.2) !important;
        }
        
        /* Ensure focus style doesn't override error style */
        input.input-error:focus {
            border-color: red !important;
            box-shadow: 0 0 0 1px rgba(255, 0, 0, 0.2) !important;
        }
        
        /* Normal focus state when no error */
        input:not(.input-error):focus {
            transition: border-color 0.3s ease !important;
            border-color: #3b82f6 !important; /* blue-500 */
        }
    `;
    document.head.appendChild(style);
})();
    
   
    window.addEventListener('DOMContentLoaded', function() {
    setupDateValidation();
});

    function markFieldAsError(element, message) {
        const fieldName = element.name;
        let warningId = null;
        
        if (fieldName === 'Merk') warningId = 'warning-merk';
        else if (fieldName === 'tipe') warningId = 'warning-tipe';
        else if (fieldName === 'plat_nomor') warningId = 'warning-plat-nomor';
        else if (fieldName === 'warna') warningId = 'warning-warna';
        else if (fieldName === 'nilai_perolehan') warningId = 'warning-nilai-perolehan';
        else if (fieldName === 'nilai_buku') warningId = 'warning-nilai-buku';
        else if (fieldName === 'nomor_mesin') warningId = 'warning-nomor-mesin';
        else if (fieldName === 'nomor_rangka') warningId = 'warning-nomor-rangka';
        else if (fieldName === 'Tanggal Beli') warningId = 'warning-tanggal-beli';
        else if (fieldName === 'Tanggal Bayar Pajak') warningId = 'warning-tgl-bayar';
        else if (fieldName === 'Tanggal Jatuh Tempo Pajak') warningId = 'warning-jatuh-tempo';
        else if (fieldName === 'Tanggal Cek Fisik') warningId = 'warning-cek-fisik';
        else if (fieldName === 'tanggal_perlindungan_awal' || fieldName === 'tanggal_perlindungan_akhir') warningId = 'warning-perlindungan';
        else if (fieldName === 'frekuensi') warningId = 'warning-frekuensi';
        else if (fieldName === 'kapasitas') warningId = 'warning-kapasitas';
        
        if (warningId) {
            const warningElement = document.getElementById(warningId);
            if (warningElement) {
                warningElement.textContent = message;
                warningElement.classList.remove('hidden');
                element.classList.add('border-red-500');
                return;
            }
        }
        
    }

    function removeFieldError(element) {
        const fieldName = element.name;
        let warningId = null;
        
        if (fieldName === 'merk') warningId = 'warning-merk';
        else if (fieldName === 'tipe') warningId = 'warning-tipe';
        else if (fieldName === 'plat_nomor') warningId = 'warning-plat-nomor';
        else if (fieldName === 'warna') warningId = 'warning-warna';
        else if (fieldName === 'nilai_perolehan') warningId = 'warning-nilai-perolehan';
        else if (fieldName === 'nilai_buku') warningId = 'warning-nilai-buku';
        else if (fieldName === 'nomor_mesin') warningId = 'warning-nomor-mesin';
        else if (fieldName === 'nomor_rangka') warningId = 'warning-nomor-rangka';
        else if (fieldName === 'tanggal_beli') warningId = 'warning-tanggal-beli';
        else if (fieldName === 'tanggal_bayar_pajak') warningId = 'warning-tgl-bayar';
        else if (fieldName === 'tanggal_jatuh_tempo_pajak') warningId = 'warning-jatuh-tempo';
        else if (fieldName === 'tanggal_cek_fisik') warningId = 'warning-cek-fisik';
        else if (fieldName === 'tanggal_perlindungan_awal' || fieldName === 'tanggal_perlindungan_akhir') warningId = 'warning-perlindungan';
        else if (fieldName === 'frekuensi') warningId = 'warning-frekuensi';
        else if (fieldName === 'kapasitas') warningId = 'warning-kapasitas';
        
        if (warningId) {
            const warningElement = document.getElementById(warningId);
            if (warningElement) {
                warningElement.classList.add('hidden');
                element.classList.remove('border-red-500');
                return;
            }
        }
    }

    window.validateInputs = validateInputs;
    window.prepareFormForSubmission = prepareFormForSubmission; 
    window.markFieldAsError = markFieldAsError;
    window.removeFieldError = removeFieldError;
    window.formatRupiah = formatRupiah;

    const rupiahInputs = document.querySelectorAll('input[oninput="formatRupiah(this)"]');
    rupiahInputs.forEach(input => {
        if (input.value) {
            const numericValue = input.value.replace(/[^\d]/g, '');
            input.dataset.numericValue = numericValue;
            
            if (numericValue.length > 0) {
                input.value = BigInt(numericValue).toLocaleString('id-ID');
            }
        }
        
        input.addEventListener('input', function() {
            const numericValue = this.value.replace(/[^\d]/g, '');
            if (!numericValue || BigInt(numericValue) === 0) {
                this.classList.add('border-red-500');
                markFieldAsError(this, `${this.getAttribute('placeholder') || this.name} tidak boleh 0!`);
            } else {
                this.classList.remove('border-red-500');
                removeFieldError(this);
            }
        });
    });

    const saveButton = document.getElementById('saveButton');
    if (saveButton) {
        saveButton.addEventListener('click', function(event) {
            event.preventDefault();
            
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
            
            let currentVehicleId = document.querySelector('input[name="id_kendaraan"]')?.value || '0';
            let platNomor = document.querySelector('input[name="Plat Nomor"]').value.trim();
            
            fetch('/admin/kendaraan/check-plat?plat_nomor=' + encodeURIComponent(platNomor) + '&exclude_id=' + currentVehicleId)
                .then(response => response.json())
                .then(data => {
                    if (data.exists) {
                        Swal.fire({
                            title: "Peringatan!",
                            text: "Plat nomor sudah digunakan oleh kendaraan lain.",
                            icon: "warning",
                            confirmButtonColor: "#3085d6",
                            confirmButtonText: "OK"
                        });
                        return;
                    }

                    Swal.fire({
                        title: "Konfirmasi",
                        text: "Apakah Anda yakin ingin menyimpan perubahan data kendaraan ini?",
                        icon: "warning",
                        showCancelButton: true,
                        confirmButtonColor: "#3085d6",
                        confirmButtonText: "Ya, simpan!",
                        cancelButtonText: "Batal", 
                        reverseButtons: true 
                    }).then((result) => {
                        if (result.isConfirmed) {
                            prepareFormForSubmission();

                            Swal.fire({
                                title: "Sukses!",
                                text: "Perubahan data kendaraan berhasil disimpan.",
                                icon: "success",
                                confirmButtonColor: "#3085d6",
                                confirmButtonText: "OK"
                            }).then(() => {
                                document.getElementById('save-form').submit();
                            });
                        }
                    });
                })
                .catch(error => {
                    Swal.fire({
                        title: "Error!",
                        text: "Terjadi kesalahan saat memeriksa plat nomor.",
                        icon: "error",
                        confirmButtonColor: "#3085d6",
                        confirmButtonText: "OK"
                    });
                });
        });
    }

    document.addEventListener('invalid', function(e) {
        e.preventDefault();
        return false;
    }, true);
});

</script>
</x-app-layout>