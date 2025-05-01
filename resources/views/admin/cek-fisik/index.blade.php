{{-- @extends('layouts.sidebar')

@section('content') --}}

<x-app-layout>
    <div class="p-6">
        <!-- Container dengan Flexbox -->
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between mb-4">
            <h2 class="text-2xl font-semibold text-gray-800 mb-4">Daftar Cek Fisik Kendaraan</h2>

            <!-- Search Form -->
            <form action="{{ route('admin.cek-fisik.index') }}" method="GET" class="flex justify-end pb-4">
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
                        {{-- <th scope="col" class="p-4">
                            <div class="flex items-center">
                                <input id="checkbox-all" type="checkbox" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded-sm focus:ring-blue-500">
                            </div>
                        </th> --}}
                        <th scope="col" class="px-6 py-3">Merek & Tipe</th>
                        <th scope="col" class="px-6 py-3">Plat Nomor</th>
                        <th scope="col" class="px-6 py-3">Tanggal Cek Fisik Terakhir</th>
                        <th scope="col" class="px-6 py-3">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($kendaraan as $item)
                        <tr class="bg-white border-b">
                            <td class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap">
                                {{ $item->merk }} {{ $item->tipe }}
                            </td>
                            <td class="px-6 py-4">{{ $item->plat_nomor }}</td>
                            <td class="px-6 py-4">{{ $item->tgl_cek_fisik_terakhir ? \Carbon\Carbon::parse($item->tgl_cek_fisik_terakhir)->format('d-m-Y') : '-' }}</td>

                            @php
                                $cekFisikTerakhir = $item->tgl_cek_fisik_terakhir ? \Carbon\Carbon::parse($item->tgl_cek_fisik_terakhir) : null;
                                $sekarang = \Carbon\Carbon::now();

                                $mingguTerakhir = $cekFisikTerakhir ? $cekFisikTerakhir->weekOfYear : null;
                                $mingguSekarang = $sekarang->weekOfYear;
                            @endphp
                            <td class="flex space-x-2 px-6 py-4">
                                {{-- <a href="{{ route('admin.cek-fisik.create', ['id_kendaraan' => $item->id_kendaraan, 'page' => request()->query('page'), 'search' => request()->query('search')]) }}" 
                                    class="font-medium text-blue-600 hover:underline">
                                    Input
                                </a> --}}
                                @if (is_null($cekFisikTerakhir) || $mingguTerakhir != $mingguSekarang)
                                    {{-- muncul kalo udah beda minggu dari tgl cek fisik terakhir --}}
                                    <a href="{{ route('admin.cek-fisik.create', ['id_kendaraan' => $item->id_kendaraan, 'page' => request()->query('page'), 'search' => request()->query('search')]) }}" 
                                        class="font-medium text-blue-600 hover:underline">
                                        Input
                                    </a>
                                @endif
                                <a href="{{ route('admin.cek-fisik.detail', ['id_kendaraan' => $item->id_kendaraan, 'page' => request()->query('page'), 'search' => request()->query('search')]) }}" 
                                    class="font-medium text-gray-600 hover:underline">
                                    Detail
                                </a>
                                <a href="{{ route('admin.cek-fisik.edit', ['id_kendaraan' => $item->id_kendaraan, 'page' => request()->query('page'), 'search' => request()->query('search')]) }}"
                                    class="font-medium text-yellow-600 hover:underline">
                                    Edit
                                </a>
                                <form action="{{ route('admin.cek-fisik.destroy', ['id_kendaraan' => $item->id_kendaraan, 'page' => request()->query('page'), 'search' => request()->query('search')]) }}" method="POST" onsubmit="return confirmDelete(event, this);">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="font-medium text-red-600 hover:underline">
                                        Hapus
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center px-6 py-4">Data kendaraan tidak tersedia.</td>
                        </tr>
                    @endforelse

                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="mt-4">
            {{ $kendaraan->appends(request()->query())->links() }}
        </div>

    </div>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.14.5/dist/sweetalert2.all.min.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            @if (session('success'))
                Swal.fire({
                    title: 'Berhasil!',
                    text: '{{ session('success') }}',
                    icon: 'success',
                    confirmButtonColor: '#3085d6',
                    confirmButtonText: 'OK'
                });
            @endif
        });
    
        function confirmDelete(event, form) {
            event.preventDefault();
            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: "Data cek fisik terakhir akan dihapus!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                // cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, hapus',
                cancelButtonText: 'Batal',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        }
    </script>    
</x-app-layout>
{{-- @endsection --}}