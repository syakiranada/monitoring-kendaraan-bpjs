<x-app-layout>
{{-- @extends('layouts.sidebar')
@section('content') --}}
    <div class="min-h-screen flex items-center justify-center py-16 px-8">
        <div class="max-w-4xl w-full bg-white p-12 rounded-lg shadow-lg">
            <h2 class="text-2xl font-bold mb-6 text-center">Form Edit Kendaraan</h2>
            <form id="save-form" action="{{ route('kendaraan.update', ['id' => $kendaraan->id_kendaraan]) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
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
                               value="{{ $kendaraan->merk }}"
                               class="w-full p-2.5 border rounded-lg">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tipe</label>
                        <input type="text" 
                               name="tipe" 
                               value="{{ $kendaraan->tipe }}"
                               class="w-full p-2.5 border rounded-lg">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Plat Nomor</label>
                        <input type="text" 
                               name="plat_nomor" 
                               value="{{ $kendaraan->plat_nomor }}"
                               class="w-full p-2.5 border rounded-lg">
                    </div>
                </div>

                <div class="grid grid-cols-3 gap-4 mb-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Warna</label>
                        <input type="text" 
                               name="warna" 
                               value="{{ $kendaraan->warna }}"
                               class="w-full p-2.5 border rounded-lg">
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
                </div>

                <div class="grid grid-cols-3 gap-4 mb-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Beli</label>
                        <input type="date" 
                            name="tanggal_beli" 
                            value="{{ \Carbon\Carbon::parse($kendaraan->tgl_pembelian)->format('Y-m-d') }}"
                            class="w-full p-2.5 border rounded-lg">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nilai Perolehan</label>
                        <div class="relative">
                            <span class="absolute left-2 top-1/2 transform -translate-y-1/2 text-gray-500">Rp</span>
                            <input type="text" 
                                   id="nilai_perolehan"
                                   name="nilai_perolehan" 
                                   value="{{ number_format($kendaraan->nilai_perolehan, 0, ',', '.') }}"
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
                                   value="{{ number_format($kendaraan->nilai_buku, 0, ',', '.') }}"
                                   class="w-full pl-8 p-2.5 border rounded-lg" 
                                   oninput="formatRupiah(this)">
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-3 gap-4 mb-4">
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
                               name="nomor_mesin" 
                               value="{{ $kendaraan->no_mesin }}"
                               class="w-full p-2.5 border rounded-lg">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nomor Rangka</label>
                        <input type="text" 
                               name="nomor_rangka" 
                               value="{{ $kendaraan->no_rangka }}"
                               class="w-full p-2.5 border rounded-lg">
                    </div>
                </div>

                <div class="grid grid-cols-3 gap-4 mb-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Bayar Asuransi Terakhir</label>
                        <input type="date" 
                            name="tanggal_asuransi" 
                            value="{{ $asuransi->tgl_bayar ?? ''}}"
                            class="w-full p-2.5 border rounded-lg">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Masa Perlindungan Awal</label>
                        <input type="date" 
                            name="tanggal_perlindungan_awal" 
                            value="{{ $asuransi->tgl_perlindungan_awal ?? ''}}"
                            class="w-full p-2.5 border rounded-lg">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Masa Perlindungan Akhir</label>
                        <input type="date" 
                            name="tanggal_perlindungan_akhir" 
                            value="{{ $asuransi->tgl_perlindungan_akhir ?? '' }}"
                            class="w-full p-2.5 border rounded-lg">
                    </div>
                </div>

                <div class="grid grid-cols-3 gap-4 mb-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Bayar Pajak Terakhir</label>
                        <input type="date" 
                            name="tanggal_bayar_pajak" 
                            value="{{ $pajak->tgl_bayar }}"
                            class="w-full p-2.5 border rounded-lg">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Jatuh Tempo Pajak Terakhir</label>
                        <input type="date" 
                            name="tanggal_jatuh_tempo_pajak" 
                            value="{{ $pajak->tgl_jatuh_tempo }}"
                            class="w-full p-2.5 border rounded-lg">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Cek Fisik Terakhir</label>
                        <input type="date" 
                            name="tanggal_cek_fisik" 
                            value="{{ $cekFisik->tgl_cek_fisik }}"
                            class="w-full p-2.5 border rounded-lg">
                    </div>
                </div>

                <div class="grid grid-cols-3 gap-4 mb-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Frekuensi Servis (Bulan)</label>
                        <input type="number" 
                               name="frekuensi" 
                               value="{{ $kendaraan->frekuensi_servis }}"
                               class="w-full p-2.5 border rounded-lg"
                               min="1"  
                               step="1">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Kapasitas Penumpang</label>
                        <input type="number" 
                               name="kapasitas" 
                               value="{{ $kendaraan->kapasitas }}"
                               class="w-full p-2.5 border rounded-lg"
                               min="1"  
                               step="1">
                    </div>
                    <div>
                        <label for="status_pinjam" class="block mb-2 text-sm font-medium text-gray-900">Status Pinjam</label>
                        <select id="status_pinjam" name="status_pinjam" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5">
                            <option {{ $kendaraan->status_ketersediaan == 'TERSEDIA' ? 'selected' : '' }}>TERSEDIA</option>
                            <option {{ $kendaraan->status_ketersediaan == 'TIDAK TERSEDIA' ? 'selected' : '' }}>TIDAK TERSEDIA</option>
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
        let alertDiv = document.getElementById('alertMessage');
        alertDiv.classList.remove('hidden');
        setTimeout(() => alertDiv.classList.add('hidden'), 10000);
        return;
    }

    let platNomor = document.querySelector('input[name="plat_nomor"]').value.trim();

    // 🔍 Cek apakah plat nomor sudah ada di database dengan AJAX (pakai route di web.php)
    fetch('/admin/kendaraan/check-plat?plat_nomor=' + encodeURIComponent(platNomor))
        .then(response => response.json())
        .then(data => {
            if (data.exists) {
                let alertDiv = document.getElementById('alertMessage');
                alertDiv.innerHTML = '<span class="font-medium">Peringatan!</span> Plat nomor sudah digunakan oleh kendaraan lain.';
                alertDiv.classList.remove('hidden');
                alertDiv.scrollIntoView({ behavior: 'smooth', block: 'center' });
                setTimeout(() => alertDiv.classList.add('hidden'), 5000);
                return;
            }

            // ✅ Validasi tanggal perlindungan
            let tanggalAwalPerlindungan = document.querySelector('input[name="tanggal_perlindungan_awal"]').value;
            let tanggalAkhirPerlindungan = document.querySelector('input[name="tanggal_perlindungan_akhir"]').value;

            if (new Date(tanggalAwalPerlindungan) > new Date(tanggalAkhirPerlindungan)) {
                let alertDiv = document.getElementById('alertMessage');
                alertDiv.innerHTML = '<span class="font-medium">Peringatan!</span> Tanggal perlindungan awal tidak boleh lebih besar dari tanggal perlindungan akhir.';
                alertDiv.classList.remove('hidden');
                alertDiv.scrollIntoView({ behavior: 'smooth', block: 'center' });
                setTimeout(() => alertDiv.classList.add('hidden'), 5000);
                return;
            }

            // ✅ Format angka untuk nilai perolehan & nilai buku
            let nominalInput = document.querySelector('input[name="nilai_perolehan"]');
            let biayaLainInput = document.querySelector('input[name="nilai_buku"]');
            nominalInput.value = nominalInput.value.replace(/[^\d]/g, '');
            biayaLainInput.value = biayaLainInput.value.replace(/[^\d]/g, '');

            // ✅ Konfirmasi sebelum submit
            Swal.fire({
                title: "Konfirmasi",
                text: "Apakah Anda yakin ingin menyimpan perubahan data kendaraan ini?",
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
                            text: "Perubahan data kendaraan berhasil disimpan.",
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
        });
});

    </script>
</x-app-layout>
{{-- @endsection --}}