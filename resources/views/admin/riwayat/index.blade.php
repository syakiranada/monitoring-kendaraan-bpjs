{{-- @extends('layouts.sidebar')

@section('content') --}}
<x-app-layout>
<div class="container mx-auto p-6">
    <h2 class="text-2xl font-bold mb-6">Daftar Riwayat</h2>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <a href="{{ route('admin.riwayat.peminjaman') }}" class="bg-green-500 hover:bg-green-700 text-white p-4 rounded-lg text-center font-semibold transition">
            Riwayat Peminjaman
        </a>
        <a href="{{ route('admin.riwayat.pajak') }}" class="bg-green-500 text-white p-4 rounded-lg text-center font-semibold hover:bg-green-700 transition">
            Riwayat Pembayaran Pajak
        </a>
        <a href="{{ route('admin.riwayat.asuransi') }}" class="bg-green-500 text-white p-4 rounded-lg text-center font-semibold hover:bg-green-700 transition">
            Riwayat Pembayaran Asuransi
        </a>
        <a href="{{ route('admin.riwayat.servis-rutin') }}" class="bg-green-500 text-white p-4 rounded-lg text-center font-semibold hover:bg-green-700 transition">
            Riwayat Servis Rutin
        </a>
        <a href="{{ route('admin.riwayat.servis-insidental') }}" class="bg-green-500 text-white p-4 rounded-lg text-center font-semibold hover:bg-green-700 transition">
            Riwayat Servis Insidental
        </a>
        <a href="{{ route('admin.riwayat.pengisian-bbm') }}" class="bg-green-500 text-white p-4 rounded-lg text-center font-semibold hover:bg-green-700 transition">
            Riwayat Pengisian BBM
        </a>
        <a href="{{ route('admin.riwayat.cek-fisik') }}" class="bg-green-500 text-white p-4 rounded-lg text-center font-semibold hover:bg-green-700 transition">
            Riwayat Cek Fisik
        </a>
    </div>
</div>
</x-app-layout>
{{-- @endsection --}}
