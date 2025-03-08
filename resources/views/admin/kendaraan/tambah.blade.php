<x-app-layout>
    <div class="min-h-screen flex items-center justify-center py-16 px-8">
        <div class="max-w-4xl w-full bg-white p-12 rounded-lg shadow-lg">
            <h2 class="text-2xl font-bold mb-6 text-center">Form Tambah Kendaraan</h2>
            <form id="save-form" action="{{ route('kendaraan.store') }}" method="POST" enctype="multipart/form-data">
                @csrf    
                @php 
                $currentPage = request()->query('page', 1);
                @endphp 
                <input type="hidden" name="current_page" value="{{ $currentPage }}">   
                <input type="hidden" name="user_id" value="{{ auth()->id() }}">    
                <input type="hidden" name="search" value="{{ request()->query('search', '') }}"> 

                <div class="grid grid-cols-3 gap-4 mb-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Merk</label>
                        <input type="text" 
                               name="merk" 
                               class="w-full p-2.5 border rounded-lg">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tipe</label>
                        <input type="text" 
                               name="tipe" 
                               class="w-full p-2.5 border rounded-lg">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Plat Nomor</label>
                        <input type="text" 
                               name="plat_nomor" 
                               class="w-full p-2.5 border rounded-lg">
                    </div>
                </div>

                <div class="grid grid-cols-3 gap-4 mb-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Warna</label>
                        <input type="text" 
                               name="warna" 
                               class="w-full p-2.5 border rounded-lg">
                    </div>
                    <div>
                        <label for="jenis_kendaraan" class="block mb-2 text-sm font-medium text-gray-900">Jenis Kendaraan</label>
                            <select id="jenis_kendaraan" name="jenis_kendaraan" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                                <option>Sedan</option>
                                <option>Non Sedan</option>
                                <option>Motor</option>
                            </select>
                    </div>
                    <div>
                        <label for="aset_guna" class="block mb-2 text-sm font-medium text-gray-900">Aset Guna</label>
                            <select id="aset_guna" name="aset_guna" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                                <option>Guna</option>
                                <option>Tidak Guna</option>
                                <option>Jual</option>
                                <option>Lelang</option>
                            </select>
                    </div>
                </div>

                <div class="grid grid-cols-3 gap-4 mb-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Beli</label>
                        <input type="date" 
                               name="tanggal_beli" 
                               max="{{ \Carbon\Carbon::now()->format('Y-m-d') }}"
                               class="w-full p-2.5 border rounded-lg">
                    </div>                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nilai Perolehan</label>
                        <div class="relative">
                            <span class="absolute left-2 top-1/2 transform -translate-y-1/2 text-gray-500">Rp</span>
                            <input type="text" 
                                   id="nilai_perolehan"
                                   name="nilai_perolehan" 
                                   class="w-full pl-8 p-2.5 border rounded-lg" 
                                   oninput="formatRupiah(this)">
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nilai Buku</label>
                        <div class="relative">
                            <span class="absolute left-2 top-1/2 transform -translate-y-1/2 text-gray-500">Rp</span>
                            <input type="text" 
                                   id="nilai_buku"
                                   name="nilai_buku" 
                                   class="w-full pl-8 p-2.5 border rounded-lg" 
                                   oninput="formatRupiah(this)">
                        </div>
                    </div>
                </div>
 
                <div class="grid grid-cols-3 gap-4 mb-4">
                    <div>
                        <label for="bahan_bakar" class="block mb-2 text-sm font-medium text-gray-900">Bahan Bakar</label>
                            <select id="bahan_bakar" name="bahan_bakar" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                                <option>Pertalite</option>
                                <option>Pertamax</option>
                                <option>Pertamax Turbo</option>
                                <option>Dexlite</option>
                                <option>Pertamina Dex</option>
                                <option>Solar</option>
                                <option>BioSolar</option>
                                <option>Pertalite/Pertamax</option>
                                <option>Pertamax/Pertamax Turbo</option>
                                <option>Solar/Dexlite/Pertamina Dex</option>
                                <option>BioSolar/Solar/Dexlite</option>
                            </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nomor Mesin</label>
                        <input type="text" 
                               name="nomor_mesin" 
                               class="w-full p-2.5 border rounded-lg">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nomor Rangka</label>
                        <input type="text" 
                               name="nomor_rangka" 
                               class="w-full p-2.5 border rounded-lg">
                    </div>
                </div>

                <div class="grid grid-cols-3 gap-4 mb-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Bayar Asuransi Terakhir</label>
                        <input type="date" 
                               name="tanggal_asuransi" 
                               max="{{ \Carbon\Carbon::now()->format('Y-m-d') }}"
                               class="w-full p-2.5 border rounded-lg">
                    </div>                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Masa Perlindungan Awal</label>
                        <input type="date" 
                            name="tanggal_perlindungan_awal" 
                            class="w-full p-2.5 border rounded-lg">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Masa Perlindungan Akhir</label>
                        <input type="date" 
                            name="tanggal_perlindungan_akhir" 
                            class="w-full p-2.5 border rounded-lg">
                    </div>
                </div>

                <div class="grid grid-cols-3 gap-4 mb-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Bayar Pajak Terakhir</label>
                        <input type="date" 
                               name="tanggal_bayar_pajak" 
                               max="{{ \Carbon\Carbon::now()->format('Y-m-d') }}"
                               class="w-full p-2.5 border rounded-lg">
                    </div>                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Jatuh Tempo Pajak Terakhir</label>
                        <input type="date" 
                            name="tanggal_jatuh_tempo_pajak" 
                            class="w-full p-2.5 border rounded-lg">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Cek Fisik Terakhir</label>
                        <input type="date" 
                               name="tanggal_cek_fisik"  
                               max="{{ \Carbon\Carbon::now()->format('Y-m-d') }}"
                               class="w-full p-2.5 border rounded-lg">
                    </div>
                </div>
                <div class="grid grid-cols-3 gap-4 mb-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Frekuensi Servis (Bulan)</label>
                        <input type="number" 
                               name="frekuensi" 
                               class="w-full p-2.5 border rounded-lg"
                               min="1"  
                               step="1">
                    </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Kapasitas Penumpang</label>
                    <input type="number" 
                           name="kapasitas" 
                           class="w-full p-2.5 border rounded-lg"
                           min="1"  
                           step="1">
                </div>
                <div>
                    <label for="status_pinjam" class="block mb-2 text-sm font-medium text-gray-900">Status Pinjam</label>
                        <select id="status_pinjam" name="status_pinjam" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                            <option>TERSEDIA</option>
                            <option>TIDAK TERSEDIA</option>
                        </select>
                </div>
            </div>

                <div class="flex justify-end space-x-4 mb-2 mt-4">
                    <button type="button" onclick="window.location.href='{{ route('kendaraan.daftar_kendaraan',  ['page' => $currentPage, 'search' => request()->query('search')]) }}'" class="bg-red-600 text-white px-6 py-2.5 rounded-lg font-semibold hover:bg-red-700 transition">
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
            
            let alertDiv = document.getElementById('alertMessage');
            let today = new Date().toISOString().split('T')[0]; 
           
            let fields = [
                'merk', 'tipe', 'plat_nomor', 'warna', 'jenis_kendaraan', 'aset_guna',
                'kapasitas', 'tanggal_beli', 'nilai_perolehan', 'nilai_buku', 
                'bahan_bakar', 'nomor_mesin', 'nomor_rangka',
                'tanggal_bayar_pajak', 'tanggal_jatuh_tempo_pajak', 'tanggal_cek_fisik', 'frekuensi', 'status_pinjam'
            ];

            let missingFields = [];
            fields.forEach(function(field) {
                let input = document.querySelector('[name="' + field + '"]');
                if (!input || !input.value.trim()) {
                    missingFields.push(field);
                }
            });

            if (missingFields.length > 0) {
                alertDiv.innerHTML = '<span class="font-medium">Peringatan!</span> Mohon isi semua kolom yang wajib sebelum menyimpan.';
                alertDiv.classList.remove('hidden');
                alertDiv.scrollIntoView({ behavior: 'smooth', block: 'center' });
                setTimeout(() => alertDiv.classList.add('hidden'), 10000);
                return;
            }
            
            let dateFields = [
                { name: 'tanggal_bayar_pajak', label: 'Tanggal Bayar Pajak' },
                { name: 'tanggal_beli', label: 'Tanggal Beli' },
                { name: 'tanggal_asuransi', label: 'Tanggal Asuransi' },
                { name: 'tanggal_cek_fisik', label: 'Tanggal Cek Fisik' }
            ];
            
            for (let field of dateFields) {
                let input = document.querySelector('[name="' + field.name + '"]');
                if (input && input.value.trim()) {
                    if (input.value > today) {
                        alertDiv.innerHTML = `<span class="font-medium">Peringatan!</span> ${field.label} tidak boleh lebih dari hari ini.`;
                        alertDiv.classList.remove('hidden');
                        alertDiv.scrollIntoView({ behavior: 'smooth', block: 'center' });
                        setTimeout(() => alertDiv.classList.add('hidden'), 10000);
                        return;
                    }
                }
            }

            let tanggalAsuransi = document.querySelector('[name="tanggal_asuransi"]').value.trim();
            let tanggalPerlindunganAwal = document.querySelector('[name="tanggal_perlindungan_awal"]').value.trim();
            let tanggalPerlindunganAkhir = document.querySelector('[name="tanggal_perlindungan_akhir"]').value.trim();
            
            let tanggalErrorMessage = "";
            
            if (tanggalAsuransi || tanggalPerlindunganAwal || tanggalPerlindunganAkhir) {
                if (!tanggalAsuransi) {
                    tanggalErrorMessage = "Tanggal Asuransi harus diisi.";
                } else if (!tanggalPerlindunganAwal) {
                    tanggalErrorMessage = "Tanggal Perlindungan Awal harus diisi.";
                } else if (!tanggalPerlindunganAkhir) {
                    tanggalErrorMessage = "Tanggal Perlindungan Akhir harus diisi.";
                } else if (new Date(tanggalPerlindunganAkhir) <= new Date(tanggalPerlindunganAwal)) {
                    tanggalErrorMessage = "Tanggal Perlindungan Akhir harus setelah Tanggal Perlindungan Awal.";
                } else if (new Date(tanggalAsuransi) > new Date(today)) {
                    tanggalErrorMessage = "Tanggal Asuransi tidak boleh lebih dari hari ini.";
                }
            }
            
            if (tanggalErrorMessage) {
                alertDiv.innerHTML = `<span class="font-medium">Peringatan!</span> ${tanggalErrorMessage}`;
                alertDiv.classList.remove('hidden');
                alertDiv.scrollIntoView({ behavior: 'smooth', block: 'center' });
                setTimeout(() => alertDiv.classList.add('hidden'), 5000);
                return;
            }
           
            let numericInputs = document.querySelectorAll('input[type="number"], input.currency');
            numericInputs.forEach(function(input) {
                if (input.value) {
                    input.value = input.value.replace(/[^\d]/g, '');
                }
            });
         
            let platNomor = document.querySelector('input[name="plat_nomor"]').value.trim();
            
            fetch('/admin/kendaraan/check-plat?plat_nomor=' + encodeURIComponent(platNomor))
                .then(response => response.json())
                .then(data => {
                    if (data.exists) {
                        alertDiv.innerHTML = '<span class="font-medium">Peringatan!</span> Plat nomor sudah digunakan oleh kendaraan lain.';
                        alertDiv.classList.remove('hidden');
                        alertDiv.scrollIntoView({ behavior: 'smooth', block: 'center' });
                        setTimeout(() => alertDiv.classList.add('hidden'), 5000);
                        return;
                    }

                    Swal.fire({
                        title: "Konfirmasi",
                        text: "Apakah Anda yakin ingin menyimpan data kendaraan ini?",
                        icon: "warning",
                        showCancelButton: true,
                        confirmButtonColor: "#3085d6",
                        cancelButtonColor: "#d33",
                        confirmButtonText: "Ya",
                        cancelButtonText: "Tidak"
                    }).then((result) => {
                        if (result.isConfirmed) {
                            if (typeof prepareFormForSubmission === 'function') {
                                prepareFormForSubmission();
                            }

                            setTimeout(() => {
                                Swal.fire({
                                    title: "Sukses!",
                                    text: "Data kendaraan berhasil disimpan.",
                                    icon: "success",
                                    confirmButtonColor: "#3085d6",
                                    confirmButtonText: "OK"
                                }).then(() => {
                                    document.getElementById('save-form').submit();
                                });
                            }, 500);
                        }
                    });
                })
                .catch(error => {
                    console.error('Error:', error);
                    alertDiv.innerHTML = '<span class="font-medium">Error!</span> Terjadi kesalahan saat memeriksa plat nomor.';
                    alertDiv.classList.remove('hidden');
                    alertDiv.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    setTimeout(() => alertDiv.classList.add('hidden'), 5000);
                });
        });

    document.addEventListener('DOMContentLoaded', function () {
        document.getElementById('save-form').setAttribute('novalidate', true);
        
        const today = new Date().toISOString().split('T')[0]; 
        const dateFields = [
            { name: "tanggal_bayar_pajak", label: "Tanggal Bayar Pajak" },
            { name: "tanggal_beli", label: "Tanggal Beli" },
            { name: "tanggal_asuransi", label: "Tanggal Asuransi" },
            { name: "tanggal_cek_fisik", label: "Tanggal Cek Fisik" }
        ];

        dateFields.forEach(field => {
            const input = document.querySelector(`input[name="${field.name}"]`);
            if (input) {
                input.setAttribute('max', today);
                
                input.addEventListener('change', function () {
                    let alertDiv = document.getElementById('alertMessage');
                    
                    if (this.value > today) {
                        alertDiv.innerHTML = `<span class="font-medium">Peringatan!</span> ${field.label} tidak boleh lebih dari hari ini.`;
                        alertDiv.classList.remove('hidden');
                        alertDiv.scrollIntoView({ behavior: 'smooth', block: 'center' });
                        setTimeout(() => alertDiv.classList.add('hidden'), 5000);
                    }
                });
                
                input.addEventListener('input', function() {
                    if (this.value > today) {
                        let alertDiv = document.getElementById('alertMessage');
                        alertDiv.innerHTML = `<span class="font-medium">Peringatan!</span> ${field.label} tidak boleh lebih dari hari ini.`;
                        alertDiv.classList.remove('hidden');
                        alertDiv.scrollIntoView({ behavior: 'smooth', block: 'center' });
                        setTimeout(() => alertDiv.classList.add('hidden'), 5000);
                    }
                });
            }
        });
        
        const insuranceDateFields = [
            { name: "tanggal_asuransi", label: "Tanggal Asuransi", needsMax: true },
            { name: "tanggal_perlindungan_awal", label: "Tanggal Perlindungan Awal", needsMax: false },
            { name: "tanggal_perlindungan_akhir", label: "Tanggal Perlindungan Akhir", needsMax: false }
        ];
        
        insuranceDateFields.forEach(field => {
            const input = document.querySelector(`input[name="${field.name}"]`);
            if (input) {
                if (field.needsMax) {
                    input.setAttribute('max', today);
                }
                
                input.addEventListener('change', function() {
                    let alertDiv = document.getElementById('alertMessage');
                    if (field.name === "tanggal_asuransi" && this.value > today) {
                        alertDiv.innerHTML = `<span class="font-medium">Peringatan!</span> ${field.label} tidak boleh lebih dari hari ini.`;
                        alertDiv.classList.remove('hidden');
                        alertDiv.scrollIntoView({ behavior: 'smooth', block: 'center' });
                        setTimeout(() => alertDiv.classList.add('hidden'), 5000);
                    }
                    
                    if (field.name === "tanggal_perlindungan_akhir") {
                        const startDate = document.querySelector('input[name="tanggal_perlindungan_awal"]').value;
                        if (startDate && this.value && this.value <= startDate) {
                            alertDiv.innerHTML = '<span class="font-medium">Peringatan!</span> Tanggal Perlindungan Akhir harus setelah Tanggal Perlindungan Awal.';
                            alertDiv.classList.remove('hidden');
                            alertDiv.scrollIntoView({ behavior: 'smooth', block: 'center' });
                            setTimeout(() => alertDiv.classList.add('hidden'), 5000);
                        }
                    }
                });
            }
        });
        
        document.addEventListener('invalid', function(e) {
            e.preventDefault();
            return false;
        }, true);
    });
</script>
</x-app-layout>