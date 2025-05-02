{{-- @extends('layouts.sidebar')

@section('content') --}}

<x-app-layout>
    <!-- Button Back -->
    <a href="{{  route('admin.cek-fisik.index', ['page' => request('page'), 'search' => request('search')])  }}" class="flex items-center text-blue-600 font-semibold hover:underline px-4 mt-6 mb-5">
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"></path>
        </svg>
        Kembali
    </a>
    <div class="min-h-screen flex items-center justify-center py-4 px-4">
        <div class="max-w-2xl w-full bg-white p-6 rounded-lg shadow-lg">
            <h2 class="text-2xl font-bold mb-6 text-center">Edit Cek Fisik Kendaraan</h2>
            <form id = "save-form" action="{{ route('admin.cek-fisik.update', $cekFisik->id_cek_fisik) }}" method="POST">
                @csrf
                @method('PUT')

                <input type="hidden" name="page" value="{{ request()->query('page', 1) }}">
                <input type="hidden" name="search" value="{{ request()->query('search') }}">
                <input type="hidden" name="id_cek_fisik" value="{{ $cekFisik->id_cek_fisik }}">

                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Detail Kendaraan </label>
                        <input type="text" 
                               value="{{ $kendaraan->merk }} {{ $kendaraan->tipe }}"
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

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Cek Fisik</label>
                    <input type="date"
                           id="tgl_cek_fisik"
                           name="tgl_cek_fisik" 
                           value="{{ $cekFisik->tgl_cek_fisik }}"
                           class="w-full p-2.5 border rounded-lg">
                    <p id="warning-tgl-cek" class="text-red-500 text-sm mt-1 hidden">Tanggal cek fisik harus diisi dan tidak boleh melebihi hari ini!</p>
                </div>

                {{-- <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Cek Fisik</label>
                    <input type="date"
                           name="tgl_cek_fisik" 
                           value="{{ $cekFisik->tgl_cek_fisik }}"
                           class="w-full p-2.5 border rounded-lg bg-gray-100" 
                           readonly>
                </div> --}}

                @php
                    $dropdownOptions = [
                        'mesin' => ['Baik', 'Usang', 'Rusak'],
                        'accu' => ['Penuh', 'Kurang Penuh'],
                        'air_radiator' => ['Penuh', 'Kurang Penuh'],
                        'air_wiper' => ['Penuh', 'Kurang Penuh'],
                        'body' => ['Baik', 'Terdapat Kerusakan'],
                        'ban' => ['Baik', 'Kurang Angin'],
                        'pengharum' => ['Ada', 'Tidak Ada'],
                        'kondisi_keseluruhan' => ['Baik', 'Usang', 'Rusak']
                    ];
                @endphp

                @foreach($dropdownOptions as $field => $options)
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ ucfirst(str_replace('_', ' ', $field)) }}</label>
                        <select id="{{ $field }}" name="{{ $field }}" class="w-full p-2.5 border rounded-lg">
                            <option value="">- Pilih Kondisi {{ ucfirst(str_replace('_', ' ', $field)) }} -</option>
                            @foreach($options as $option)
                                <option value="{{ $option }}" @if($cekFisik->$field == $option) selected @endif>{{ $option }}</option>
                            @endforeach
                        </select>
                        <p id="warning-{{ $field }}" class="text-red-500 text-sm mt-1 hidden">Kolom ini harus dipilih!</p>
                    </div>
                @endforeach
                
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Catatan</label>
                    <textarea name="catatan" class="w-full p-2.5 border rounded-lg">{{ $cekFisik->catatan }}</textarea>
                </div>
                
                <div class="flex justify-end space-x-4 mb-2">
                    {{-- <button type="button" onclick="window.location.href='{{ route('admin.cek-fisik.index', ['page' => $currentPage]) }}'" class="bg-red-600 text-white px-6 py-2.5 rounded-lg font-semibold hover:bg-red-700 transition">
                        Batal
                    </button> --}}
                    <button type="submit" class="bg-blue-600 text-white px-6 py-2.5 rounded-lg font-semibold hover:bg-blue-700 transition">Simpan</button>
                </div>
            </form>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $(document).ready(function () {
            // Set maksimum tanggal cek fisik hari ini
            let today = new Date();
            let year = today.getFullYear();
            let month = (today.getMonth() + 1).toString().padStart(2, '0');
            let day = today.getDate().toString().padStart(2, '0');
            let todayStr = `${year}-${month}-${day}`;
            $('#tgl_cek_fisik').attr('max', todayStr);
    
            function showWarning(input, warningElement, condition) {
                if (condition) {
                    warningElement.removeClass("hidden");
                    input.addClass("border-red-500");
                } else {
                    warningElement.addClass("hidden");
                    input.removeClass("border-red-500");
                }
            }
    
            $('#tgl_cek_fisik').on('change', function () {
                let input = $(this);
                let warning = $('#warning-tgl-cek');
                let value = input.val();
                if (!value || value > todayStr) {
                    showWarning(input, warning, true);
                } else {
                    showWarning(input, warning, false);
                }
            });

            // Cek perubahan di semua select
            $('select').each(function () {
                let select = $(this);
                let field = select.attr('name'); // contoh: mesin, accu, dll
                select.on('change', function () {
                    let warning = $('#warning-' + field);
                    if (!select.val()) {
                        showWarning(select, warning, true);
                    } else {
                        showWarning(select, warning, false);
                    }
                });
            });
    
            $('#save-form').on('submit', function (event) {
                event.preventDefault();
                let valid = true;
    
                // Cek tanggal cek fisik
                let tglCek = $('#tgl_cek_fisik').val();
                if (!tglCek || tglCek > today) {
                    showWarning($('#tgl_cek_fisik'), $('#warning-tgl-cek'), true);
                    valid = false;
                } else {
                    showWarning($('#tgl_cek_fisik'), $('#warning-tgl-cek'), false);
                }
    
                // Cek semua select yang wajib diisi
                $(this).find('select').each(function () {
                    let select = $(this);
                    let field = select.attr('name');
                    let warning = $('#warning-' + field);
                    if (!select.val()) {
                        showWarning(select, warning, true);
                        valid = false;
                    } else {
                        showWarning(select, warning, false);
                    }
                });

    
                if (!valid) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: 'Mohon isi semua kolom yang wajib sebelum menyimpan!'
                    });
                    return;
                }
    
                // Konfirmasi sebelum kirim form
                Swal.fire({
                    title: "Konfirmasi",
                    text: "Apakah Anda yakin ingin menyimpan data cek fisik ini?",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#3085d6",
                    // cancelButtonColor: "#d33",
                    confirmButtonText: "Ya, simpan",
                    cancelButtonText: "Batal",
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        this.submit();
                    }
                });
            });
        });
    </script>
</x-app-layout>
{{-- @endsection --}}