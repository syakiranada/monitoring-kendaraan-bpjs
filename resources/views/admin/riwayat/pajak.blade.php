{{-- @extends('layouts.sidebar')

@section('content') --}}
<x-app-layout>
    <div class="p-6">
        <div class="mb-4 flex space-x-2">
            <a href="{{ route('admin.riwayat.index') }}" class="flex items-center px-5 py-2.5 text-gray-900 bg-white border border-gray-300 rounded-full text-sm hover:bg-gray-100 dark:bg-gray-800 dark:text-white dark:border-gray-600 dark:hover:bg-gray-700">
                <svg class="w-4 h-4 text-gray-800 dark:text-white mr-2" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m15 19-7-7 7-7"/>
                </svg>              
                Kembali
            </a>
        </div> 
        <h2 class="text-2xl font-semibold text-gray-800 dark:text-white mb-4">Riwayat Pembayaran Pajak</h2>
        
        <!-- Search Form -->
        <form action="{{ route('admin.riwayat.pajak') }}" method="GET" class="flex justify-end pb-4">
            <div class="relative">
                <input 
                    type="text" 
                    name="search"
                    class="block p-2 ps-10 text-sm text-gray-900 border border-gray-300 rounded-lg w-60 bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" 
                    placeholder="Cari merek, tipe, plat nomor..."
                    value="{{ request('search') }}"
                >
                <div class="absolute inset-y-0 start-0 flex items-center ps-3">
                    <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z"/>
                    </svg>
                </div>
            </div>
        </form>

        <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
            <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                    <tr>
                        <th scope="col" class="px-6 py-3">Merek & Tipe</th>
                        <th scope="col" class="px-6 py-3">Plat Nomor</th>
                        <th scope="col" class="px-6 py-3">Tanggal Jatuh Tempo</th>
                        <th scope="col" class="px-6 py-3">Tanggal Pembayaran</th>
                        <th scope="col" class="px-6 py-3">Total Pembayaran</th>
                        <th scope="col" class="px-6 py-3">Admin Input</th>
                        <th scope="col" class="px-6 py-3">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($riwayatPajak as $item)
                        <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                            <td class="px-6 py-4">{{ $item->kendaraan->merk }} {{ $item->kendaraan->tipe }}</td>
                            <td class="px-6 py-4">{{ $item->kendaraan->plat_nomor }}</td>
                            <td class="px-6 py-4">{{ $item->tgl_jatuh_tempo }}</td>
                            <td class="px-6 py-4">{{ $item->tgl_bayar ?? '-' }}</td>
                            <td class="px-6 py-4">Rp{{ number_format($item->nominal + $item->biaya_pajak_lain, 0, ',', '.') }}</td>
                            <td class="px-6 py-4">
                                {{ $item->user->name }}
                            </td>
                            <td class="px-6 py-4">
                                <!-- Button for "Detail" -->
                                <a href="{{ route('admin.riwayat.detail-pajak', ['id' => $item->id_pajak, 'page' => request()->query('page', 1), 'search' => request()->query('search')]) }}" class="text-blue-600 dark:text-blue-500 hover:underline">
                                    Detail
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center px-6 py-4">Tidak ada riwayat pembayaran pajak.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="mt-4">
            {{ $riwayatPajak->appends(request()->query())->links() }}
        </div>
        {{-- <nav class="pb-4 flex items-center justify-end pt-4 px-12" aria-label="Table navigation">
            <div class="w-full md:w-auto flex justify-end">
                {{ $riwayatPajak->appends(request()->query())->onEachSide(1)->links() }}
            </div>
        </nav>  --}}
    </div>
</x-app-layout>
{{-- @endsection --}}
