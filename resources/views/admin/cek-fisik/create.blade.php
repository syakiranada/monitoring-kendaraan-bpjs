@extends('layouts.sidebar')

@section('content')

{{-- <x-app-layout> --}}
    <div class="min-h-screen flex items-center justify-center py-12 px-4">
        <div class="max-w-2xl w-full bg-white p-6 rounded-lg shadow-lg">
            <h2 class="text-2xl font-bold mb-6 text-center">Form Cek Fisik Kendaraan</h2>
            <form id = "save-form" action="{{ route('admin.cek-fisik.store') }}" method="POST">
                @csrf
                <input type="hidden" name="id_kendaraan" value="{{ $kendaraan->id_kendaraan }}">

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
                           name="tgl_cek_fisik" 
                           class="w-full p-2.5 border rounded-lg" 
                           required>
                </div>

                @php
                    $options = ['Baik', 'Usang', 'Rusak'];
                @endphp
                
                @foreach(['mesin', 'accu', 'air_radiator', 'air_wiper', 'body', 'ban', 'pengharum', 'kondisi_keseluruhan'] as $field)
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">{{ ucfirst(str_replace('_', ' ', $field)) }}</label>
                        <select name="{{ $field }}" class="w-full p-2.5 border rounded-lg">
                            @foreach($options as $option)
                                <option value="{{ $option }}">{{ $option }}</option>
                            @endforeach
                        </select>
                    </div>
                @endforeach

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Catatan</label>
                    <textarea name="catatan" class="w-full p-2.5 border rounded-lg"></textarea>
                </div>
                
                <div class="flex justify-end space-x-4 mb-2">
                    <button type="button" onclick="window.location.href='{{ route('admin.cek-fisik.index') }}'" class="bg-red-600 text-white px-6 py-2.5 rounded-lg font-semibold hover:bg-red-700 transition">Batal</button>
                    <button type="submit" class="bg-blue-600 text-white px-6 py-2.5 rounded-lg font-semibold hover:bg-blue-700 transition">Simpan</button>
                </div>
                <div id="alertMessage" class="hidden p-4 mb-4 text-sm text-red-800 rounded-lg bg-red-50" role="alert">
                    <span class="font-medium">Peringatan!</span> Mohon isi semua kolom yang wajib sebelum menyimpan.
                </div>
            </form>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>    
        document.getElementById('save-form').addEventListener('submit', function (event) {
             event.preventDefault();
             let form = event.target;
             let inputs = form.querySelectorAll('input[required], select[required]');
             let valid = true;
             
             inputs.forEach(input => {
                 if (!input.value) {
                     valid = false;
                 }
             });
             
             if (!valid) {
                 let alertDiv = document.getElementById('alertMessage');
                 alertDiv.classList.remove('hidden');
                 setTimeout(() => alertDiv.classList.add('hidden'), 3000);
                 return;
             }
             
             Swal.fire({
                 title: "Konfirmasi",
                 text: "Apakah Anda yakin ingin menyimpan data cek fisik ini?",
                 icon: "warning", 
                 showCancelButton: true,
                 confirmButtonColor: "#3085d6",
                 cancelButtonColor: "#d33",
                 confirmButtonText: "Ya",
                 cancelButtonText: "Tidak"
             }).then((result) => {
                 if (result.isConfirmed) {
                    form.submit();
                 }
             });
         });
     </script>
{{-- </x-app-layout> --}}
@endsection