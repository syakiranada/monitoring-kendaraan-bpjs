@extends('layouts.sidebar')

@section('content')

{{-- <x-app-layout> --}}
    <div class="min-h-screen flex items-center justify-center py-12 px-4">
        <div class="max-w-2xl w-full bg-white p-6 rounded-lg shadow-lg">
            <h2 class="text-2xl font-bold mb-6 text-center">Detail Cek Fisik Kendaraan</h2>
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Cek Fisik</label>
                <p class="w-full p-2.5 border rounded-lg bg-gray-100">{{ $cekFisik->tgl_cek_fisik }}</p>
            </div>
            
            @foreach(['mesin', 'accu', 'air_radiator', 'air_wiper', 'body', 'ban', 'pengharum', 'kondisi_keseluruhan'] as $field)
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">{{ ucfirst(str_replace('_', ' ', $field)) }}</label>
                    <p class="w-full p-2.5 border rounded-lg bg-gray-100">{{ $cekFisik->$field }}</p>
                </div>
            @endforeach
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Catatan</label>
                <p class="w-full p-2.5 border rounded-lg bg-gray-100">{{ $cekFisik->catatan }}</p>
            </div>
            
            <div class="flex justify-end space-x-4 mb-2">
                <button type="button" onclick="window.location.href='{{ route('admin.cek-fisik.index') }}'" class="bg-blue-600 text-white px-6 py-2.5 rounded-lg font-semibold hover:bg-blue-700 transition">Kembali</button>
            </div>
        </div>
    </div>
{{-- </x-app-layout> --}}
@endsection