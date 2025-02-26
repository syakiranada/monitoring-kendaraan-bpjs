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
        <h2 class="text-2xl font-semibold text-gray-800 dark:text-white mb-4">Riwayat Pengisian BBM</h2>
        
        <!-- Filter Form -->
        <form action="{{ route('admin.riwayat.pengisian-bbm') }}" method="GET" class="space-y-4">
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Kendaraan</label>
                    <select name="kendaraan" class="block w-full p-2 border rounded-lg dark:bg-gray-700 dark:text-white">
                        <option value="">Semua Kendaraan</option>
                        @foreach ($kendaraan as $k)
                            <option value="{{ $k->plat_nomor }}" {{ request('kendaraan') == $k->plat_nomor ? 'selected' : '' }}>
                                {{ $k->merk }} {{ $k->tipe }} - {{ $k->plat_nomor }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Pengguna</label>
                    <select name="pengguna" class="block w-full p-2 border rounded-lg dark:bg-gray-700 dark:text-white">
                        <option value="">Semua Pengguna</option>
                        @foreach ($penggunas as $pengguna)
                            <option value="{{ $pengguna->id }}" {{ request('pengguna') == $pengguna->id ? 'selected' : '' }}>
                                {{ $pengguna->name }}
                            </option>
                        @endforeach
                    </select>                    
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Tanggal Awal</label>
                    <input type="date" name="tgl_awal" class="block w-full p-2 border rounded-lg dark:bg-gray-700 dark:text-white" value="{{ request('tgl_awal') }}">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Tanggal Akhir</label>
                    <input type="date" name="tgl_akhir" class="block w-full p-2 border rounded-lg dark:bg-gray-700 dark:text-white" value="{{ request('tgl_akhir') }}">
                </div>
            </div>

            <div class="mt-4">
                <button type="submit" class="text-white bg-blue-700 hover:bg-blue-800 focus:outline-none focus:ring-4 focus:ring-blue-300 font-medium rounded-full text-sm px-5 py-2.5 text-center me-2 mb-2">Filter</button>
            </div>
        </form>


        <!-- Total Transaksi -->
        <div class="mt-4">
            <h3 class="text-xl font-semibold text-gray-800 dark:text-white">Total Transaksi: Rp{{ number_format($totalTransaksi, 0, ',', '.') }}</h3>
        </div>

        <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
            <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                    <tr>
                        <th scope="col" class="px-6 py-3">Merek & Tipe</th>
                        <th scope="col" class="px-6 py-3">Plat Nomor</th>
                        <th scope="col" class="px-6 py-3">Tanggal Pengisian</th>
                        <th scope="col" class="px-6 py-3">Biaya</th>
                        <th scope="col" class="px-6 py-3">User Input</th>
                        <th scope="col" class="px-6 py-3">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($riwayatBBM as $item)
                        <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                            <td class="px-6 py-4">{{ $item->kendaraan->merk }} {{ $item->kendaraan->tipe }}</td>
                            <td class="px-6 py-4">{{ $item->kendaraan->plat_nomor }}</td>
                            <td class="px-6 py-4">{{ $item->tgl_isi ? \Carbon\Carbon::parse($item->tgl_isi)->format('d-m-Y') : '-' }}</td>
                            <td class="px-6 py-4">Rp{{ number_format($item->nominal, 0, ',', '.') }}</td>
                            <td class="px-6 py-4">{{ $item->user->name }}</td>
                            <td class="px-6 py-4">
                                {{-- <a href="{{ route('admin.riwayat.detail-pengisian-bbm', ['id' => $item->id_bbm, 'page' => request()->query('page', 1), 'search' => request()->query('search')]) }}" class="text-blue-600 dark:text-blue-500 hover:underline">
                                    Detail
                                </a> --}}
                                <a href="{{ route('admin.riwayat.detail-pengisian-bbm', [
                                    'id' => $item->id_bbm,
                                    'kendaraan' => request('kendaraan'),
                                    'pengguna' => request('pengguna'),
                                    'tgl_awal' => request('tgl_awal'),
                                    'tgl_akhir' => request('tgl_akhir'),
                                    'page' => request()->query('page', 1)
                                ]) }}" class="text-blue-600 dark:text-blue-500 hover:underline">
                                    Detail
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center px-6 py-4">Tidak ada riwayat pengisian BBM.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="mt-4">
            {{ $riwayatBBM->appends(request()->query())->links() }}
        </div>
    </div>
</x-app-layout>
{{-- @endsection --}}
