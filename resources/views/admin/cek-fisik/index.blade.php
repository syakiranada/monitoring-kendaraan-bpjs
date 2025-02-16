@extends('layouts.sidebar')

@section('content')

{{-- <x-app-layout> --}}
    <div class="p-6">
        <h2 class="text-2xl font-semibold text-gray-800 dark:text-white mb-4">Daftar Cek Fisik Kendaraan</h2>
        <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
            <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                    <tr>
                        {{-- <th scope="col" class="p-4">
                            <div class="flex items-center">
                                <input id="checkbox-all" type="checkbox" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded-sm focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 dark:focus:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
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
                        <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                            {{-- <td class="w-4 p-4">
                                <div class="flex items-center">
                                    <input type="checkbox" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded-sm focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 dark:focus:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                                </div>
                            </td> --}}
                            <td class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                {{ $item->merk }} {{ $item->tipe }}
                            </td>
                            <td class="px-6 py-4">{{ $item->plat_nomor }}</td>
                            <td class="px-6 py-4">{{ $item->tgl_cek_fisik_terakhir ? date('d-m-Y', strtotime($item->tgl_cek_fisik_terakhir)) : '-' }}</td>
                            <td class="flex space-x-2 px-6 py-4">
                                <a href="{{ route('admin.cek-fisik.create', $item->id_kendaraan) }}" class="text-blue-600 dark:text-blue-500 hover:underline">
                                    Catat
                                </a>
                                <a href="{{ route('admin.cek-fisik.detail', $item->id_kendaraan) }}" class="text-gray-600 dark:text-gray-500 hover:underline">
                                    Detail
                                </a>
                                <a href="{{ route('admin.cek-fisik.edit', $item->id_kendaraan) }}" class="text-yellow-600 dark:text-yellow-500 hover:underline">
                                    Edit
                                </a>
                                <form action="{{ route('admin.cek-fisik.destroy', $item->id_kendaraan) }}" method="POST" onsubmit="return confirmDelete(event, this);">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 dark:text-red-500 hover:underline">
                                        Hapus
                                    </button>
                                </form>
                                {{-- <button class="text-red-600 dark:text-red-500 hover:underline">
                                    Hapus
                                </button> --}}
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
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        }
    </script>    
{{-- </x-app-layout> --}}
@endsection