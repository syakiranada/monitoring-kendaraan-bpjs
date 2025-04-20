{{-- @extends('layouts.sidebar')

@section('content') --}}

<x-app-layout>
    <div class="min-h-screen flex items-center justify-center py-12 px-4">
        <div class="max-w-2xl w-full bg-white p-6 rounded-lg shadow-lg">
            <h2 class="text-2xl font-bold mb-6 text-center">Edit Cek Fisik Kendaraan</h2>
            <form action="{{ route('admin.cek-fisik.update', $cekFisik->id_cek_fisik) }}" method="POST">
                @csrf
                @method('PUT')

                {{-- @php 
                $currentPage = request()->query('page', 1);
                @endphp 
                <input type="hidden" name="current_page" value="{{ $currentPage }}"> --}}
                <input type="hidden" name="page" value="{{ request()->query('page', 1) }}">
                <input type="hidden" name="search" value="{{ request()->query('search') }}">
                <input type="hidden" name="id_cek_fisik" value="{{ $cekFisik->id_cek_fisik }}">
                
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal Cek Fisik</label>
                    <input type="date" name="tgl_cek_fisik" value="{{ $cekFisik->tgl_cek_fisik }}" class="w-full p-2.5 border rounded-lg" required>
                </div>

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
                        <select name="{{ $field }}" class="w-full p-2.5 border rounded-lg">
                            @foreach($options as $option)
                                <option value="{{ $option }}" @if($cekFisik->$field == $option) selected @endif>{{ $option }}</option>
                            @endforeach
                        </select>
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
                    <button type="button" onclick="window.location.href='{{ route('admin.cek-fisik.index', ['page' => request('page'), 'search' => request('search')]) }}'" class="bg-red-600 text-white px-6 py-2.5 rounded-lg font-semibold hover:bg-red-700 transition">
                        Batal
                    </button>
                    <button type="submit" class="bg-blue-600 text-white px-6 py-2.5 rounded-lg font-semibold hover:bg-blue-700 transition">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
{{-- @endsection --}}