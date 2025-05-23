{{-- @extends('layouts.sidebar')

@section('content') --}}

<x-app-layout>
    <div class="p-6">
        <!-- Container dengan Flexbox -->
        <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between mb-4">
            <h2 class="text-2xl font-semibold text-gray-800 mb-4">Daftar Pengajuan Peminjaman Kendaraan</h2>
            <!-- Search Form -->
            <form action="{{ route('admin.pengajuan-peminjaman.index') }}" method="GET" class="flex justify-end pb-4">
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

        {{-- @if (session('success'))
            <div class="p-4 mb-4 text-sm text-green-800 rounded-lg bg-green-50" role="alert">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="p-4 mb-4 text-sm text-red-800 rounded-lg bg-red-50" role="alert">
                {{ session('error') }}
            </div>
        @endif --}}

        <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
            <table class="w-full text-sm text-left rtl:text-right text-gray-500">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3">Nama Peminjam</th>
                        <th scope="col" class="px-6 py-3">Merek dan Tipe</th>
                        <th scope="col" class="px-6 py-3">Plat</th>
                        <th scope="col" class="px-6 py-3">Tanggal Mulai</th>
                        <th scope="col" class="px-6 py-3">Tanggal Pengembalian</th>
                        <th scope="col" class="px-6 py-3">Tujuan</th>
                        <th scope="col" class="px-6 py-3">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($peminjaman as $item)
                        <tr class="bg-white border-b hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                {{ $item->user->name }}
                            </td>
                            <td class="px-6 py-4">{{ $item->kendaraan->merk }} {{ $item->kendaraan->tipe }}</td>
                            <td class="px-6 py-4">{{ $item->kendaraan->plat_nomor }}</td>
                            <td class="px-6 py-4">{{ $item->tgl_mulai ? \Carbon\Carbon::parse($item->tgl_mulai)->format('d-m-Y') : '-' }}</td>
                            <td class="px-6 py-4">{{ $item->tgl_selesai ? \Carbon\Carbon::parse($item->tgl_selesai)->format('d-m-Y') : '-' }}</td>
                            <td class="px-6 py-4">{{ $item->tujuan }}</td>
                            <td class="px-6 py-4">
                                <div class="flex items-center justify-center space-x-2">
                                    <a href="{{ route('admin.pengajuan-peminjaman.detail', ['id' => $item->id_peminjaman, 'page' => request()->query('page'), 'search' => request()->query('search')]) }}" class="font-medium text-gray-600 hover:underline">
                                        Detail
                                    </a>
                                    <!-- Form Setujui -->
                                    <form action="{{ route('admin.pengajuan-peminjaman.setujui', $item->id_peminjaman) }}" method="POST" id="setujui-form-{{ $item->id_peminjaman }}">
                                        @csrf
                                        <button type="button" onclick="confirmAction('setujui', {{ $item->id_peminjaman }})" class="font-medium text-green-600 hover:underline">
                                            Setujui
                                        </button>
                                    </form>
    
                                    <!-- Form Tolak -->
                                    <form action="{{ route('admin.pengajuan-peminjaman.tolak', $item->id_peminjaman) }}" method="POST" id="tolak-form-{{ $item->id_peminjaman }}">
                                        @csrf
                                        <button type="button" onclick="confirmAction('tolak', {{ $item->id_peminjaman }})" class="font-medium text-red-600 hover:underline">
                                            Tolak
                                        </button>
                                    </form>
                                </div>
                                
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center px-6 py-4">Tidak ada pengajuan peminjaman yang menunggu persetujuan.</td>
                        </tr>
                    @endforelse

                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="mt-4">
            {{ $peminjaman->appends(request()->query())->links() }}
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.14.5/dist/sweetalert2.all.min.js"></script>
    <script>
        function confirmAction(action, id) {
            const actionText = action === 'setujui' ? 'menyetujui' : 'menolak';
            const buttonText = action === 'setujui' ? 'Ya, setujui' : 'Ya, tolak';
            const confirmButtonColor = action === 'setujui' ? '#3085d6' : '#d33'; // Biru untuk setujui, Merah untuk tolak

            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: `Anda akan ${actionText} peminjaman ini!`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: confirmButtonColor,
                confirmButtonText: buttonText,
                cancelButtonText: 'Batal',
                reverseButtons: true,
            }).then((result) => {
                if (result.isConfirmed) {
                    // Jika ya, submit form yang sesuai
                    const form = document.getElementById(`${action}-form-${id}`);
                    form.submit();
                }
            });
        }

        document.addEventListener('DOMContentLoaded', function() {
        // Cek jika ada flash message success
        @if(session('success'))
            Swal.fire({
                title: "Berhasil!",
                text: "{{ session('success') }}",
                icon: "success",
                confirmButtonColor: "#3085d6",
            });
        @endif

        // Cek jika ada flash message error
        @if(session('error'))
            Swal.fire({
                title: "Gagal!",
                text: "{{ session('error') }}",
                icon: "error",
                confirmButtonColor: "#d33",
            });
        @endif
    });
    </script>
</x-app-layout>
{{-- @endsection --}}