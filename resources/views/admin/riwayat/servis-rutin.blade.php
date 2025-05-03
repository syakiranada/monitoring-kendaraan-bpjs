{{-- @extends('layouts.sidebar')

@section('content') --}}
<x-app-layout>
    <div class="p-6">
        <!-- Button Back -->
        <a href="{{ route('admin.riwayat.index') }}" class="flex items-center text-blue-600 font-semibold hover:underline mb-5">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"></path>
            </svg>
            Kembali
        </a> 
        <!-- Container dengan Flexbox -->
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between mb-4">
            <h2 class="text-2xl font-semibold text-gray-800 mb-4">Riwayat Servis Rutin Kendaraan</h2>
            <!-- Search Form -->
            <form action="{{ route('admin.riwayat.servis-rutin') }}" method="GET" class="flex justify-end pb-4">
                <div class="relative">
                    <input 
                        type="text" 
                        name="search"
                        class="block p-2 ps-10 text-sm text-gray-900 border border-gray-300 rounded-lg w-60 bg-gray-50 focus:ring-blue-500 focus:border-blue-500" 
                        placeholder="Cari"
                        value="{{ request('search') }}"
                    >
                    <div class="absolute inset-y-0 start-0 flex items-center ps-3">
                        <svg class="w-4 h-4 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z"/>
                        </svg>
                    </div>
                </div>
            </form>
        </div>

        <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
            <table class="w-full text-sm text-left rtl:text-right text-gray-500">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3">Merek & Tipe</th>
                        <th scope="col" class="px-6 py-3">Plat Nomor</th>
                        <th scope="col" class="px-6 py-3">Tanggal Servis</th>
                        <th scope="col" class="px-6 py-3">Kilometer</th>
                        <th scope="col" class="px-6 py-3">Biaya</th>
                        <th scope="col" class="px-6 py-3">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($riwayatServis as $item)
                        <tr class="bg-white border-b hover:bg-gray-50">
                            <td class="px-6 py-4">{{ $item->kendaraan->merk }} {{ $item->kendaraan->tipe }}</td>
                            <td class="px-6 py-4">{{ $item->kendaraan->plat_nomor }}</td>
                            <td class="px-6 py-4">{{ $item->tgl_servis_real ? \Carbon\Carbon::parse($item->tgl_servis_real)->format('d-m-Y') : '-' }}</td>
                            <td class="px-6 py-4">{{ number_format($item->kilometer, 0, ',', '.') }}</td>
                            <td class="px-6 py-4">Rp{{ number_format($item->harga, 0, ',', '.') }}</td>
                            <td class="px-6 py-4">
                                <a href="{{ route('admin.riwayat.detail-servis-rutin', ['id' => $item->id_servis_rutin, 'page' => request()->query('page', 1), 'search' => request()->query('search')]) }}" class="font-medium text-blue-600 hover:underline">
                                    Detail
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center px-6 py-4">Tidak ada riwayat servis rutin.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="mt-4">
            {{ $riwayatServis->appends(request()->query())->links() }}
        </div>
    </div>
</x-app-layout>
{{-- @endsection --}}
