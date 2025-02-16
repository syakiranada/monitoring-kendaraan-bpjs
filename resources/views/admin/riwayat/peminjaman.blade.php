@extends('layouts.sidebar')

@section('content')
    <div class="p-6">
        <div class="mb-4 flex space-x-2">
            <a href="{{ route('admin.riwayat.index') }}" class="flex items-center px-5 py-2.5 text-gray-900 bg-white border border-gray-300 rounded-lg text-sm hover:bg-gray-100 dark:bg-gray-800 dark:text-white dark:border-gray-600 dark:hover:bg-gray-700">
                <svg class="w-4 h-4 text-gray-800 dark:text-white mr-2" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" viewBox="0 0 24 24">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m15 19-7-7 7-7"/>
                </svg>              
                Kembali
            </a>
        </div> 
        <h2 class="text-2xl font-semibold text-gray-800 dark:text-white mb-4">Riwayat Peminjaman Kendaraan</h2>
        <div class="mb-4 flex space-x-4">
            <form action="{{ route('admin.riwayat.peminjaman') }}" method="GET" class="flex items-center space-x-2">
                <!-- Filter by Kendaraan (Merk, Tipe, Plat Nomor) -->
                <div>
                    <label for="kendaraan" class="sr-only">Kendaraan</label>
                    <select name="kendaraan" id="kendaraan" class="py-2.5 px-5 text-sm font-medium text-gray-900 bg-white rounded-full border border-gray-200 
                        hover:bg-gray-100 hover:text-blue-700 focus:outline-none focus:ring-4 focus:ring-gray-100 
                        dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700 
                        dark:focus:ring-gray-700">
                        <option value="">Pilih Kendaraan</option>
                        @foreach ($kendaraan as $k)
                            <option value="{{ $k->plat_nomor }}">
                                {{ $k->merk }} {{ $k->tipe }} - {{ $k->plat_nomor }}
                            </option>
                        @endforeach
                    </select>
                </div>
        
                <!-- Filter by Peminjam -->
                <div>
                    <label for="peminjam" class="sr-only">Peminjam</label>
                    <input type="text" name="peminjam" id="peminjam" placeholder="Nama Peminjam" 
                        class="py-2.5 px-5 text-sm font-medium text-gray-900 bg-white rounded-full border border-gray-200 
                        hover:bg-gray-100 hover:text-blue-700 focus:outline-none focus:ring-4 focus:ring-gray-100 
                        dark:bg-gray-800 dark:text-gray-400 dark:border-gray-600 dark:hover:text-white dark:hover:bg-gray-700 
                        dark:focus:ring-gray-700">
                </div>
        
                <!-- Apply Filters Button -->
                <button type="submit" 
                    class="py-2.5 px-5 text-sm font-medium text-white bg-blue-600 rounded-full border border-blue-600 
                    hover:bg-blue-700 focus:outline-none focus:ring-4 focus:ring-blue-300 
                    dark:bg-blue-500 dark:border-blue-500 dark:hover:bg-blue-600 dark:focus:ring-blue-700">
                    Terapkan Filter
                </button>
            </form>
        </div>
        
        
        <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
            <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                    <tr>
                        <th scope="col" class="px-6 py-3">Nama Peminjam</th>
                        <th scope="col" class="px-6 py-3">Merek & Tipe</th>
                        <th scope="col" class="px-6 py-3">Plat Nomor</th>
                        <th scope="col" class="px-6 py-3">Tanggal Mulai</th>
                        <th scope="col" class="px-6 py-3">Tanggal Pengembalian</th>
                        <th scope="col" class="px-6 py-3">Tujuan</th>
                        <th scope="col" class="px-6 py-3">Status</th>
                        <th scope="col" class="px-6 py-3">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($riwayatPeminjaman as $item)
                        <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                            <td class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                {{ $item->user->name }}
                            </td>
                            <td class="px-6 py-4">{{ $item->kendaraan->merk }} {{ $item->kendaraan->tipe }}</td>
                            <td class="px-6 py-4">{{ $item->kendaraan->plat_nomor }}</td>
                            <td class="px-6 py-4">{{ $item->tgl_mulai }}</td>
                            <td class="px-6 py-4">{{ $item->tgl_selesai }}</td>
                            <td class="px-6 py-4">{{ $item->tujuan }}</td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-1 text-xs font-semibold 
                                    {{ $item->status_pinjam == 'disetujui' ? 'text-green-800 bg-green-200' : ($item->status == 'ditolak' ? 'text-red-800 bg-red-200' : 'text-gray-800 bg-gray-200') }} 
                                    rounded-lg">
                                    {{ ucfirst($item->status_pinjam) }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <!-- Button for "Detail" -->
                                <a href="{{ route('admin.riwayat.detail-peminjaman', $item->id_peminjaman) }}" class="text-blue-600 hover:text-blue-800">Detail</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center px-6 py-4">Tidak ada riwayat peminjaman.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
