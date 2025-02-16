@extends('layouts.sidebar')

@section('content')

{{-- <x-app-layout> --}}
    <div class="p-6">
        <h2 class="text-2xl font-semibold text-gray-800 dark:text-white mb-4">Daftar Pengajuan Peminjaman Kendaraan</h2>
        <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
            <table class="w-full text-sm text-left rtl:text-right text-gray-500 dark:text-gray-400">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                    <tr>
                        {{-- <th scope="col" class="p-4">
                            <div class="flex items-center">
                                <input id="checkbox-all" type="checkbox" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded-sm focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 dark:focus:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                            </div>
                        </th> --}}
                        <th scope="col" class="px-6 py-3">Nama Peminjam</th>
                        <th scope="col" class="px-6 py-3">Merek & Tipe</th>
                        <th scope="col" class="px-6 py-3">Plat Nomor</th>
                        <th scope="col" class="px-6 py-3">Tanggal Mulai</th>
                        <th scope="col" class="px-6 py-3">Tanggal Pengembalian</th>
                        <th scope="col" class="px-6 py-3">Tujuan</th>
                        <th scope="col" class="px-6 py-3">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($peminjaman as $item)
                        <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                            {{-- <td class="w-4 p-4">
                                <div class="flex items-center">
                                    <input type="checkbox" class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded-sm focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 dark:focus:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                                </div>
                            </td> --}}
                            <td class="px-6 py-4 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                {{ $item->user->name }}
                            </td>
                            <td class="px-6 py-4">{{ $item->kendaraan->merk }} {{ $item->kendaraan->tipe }}</td>
                            <td class="px-6 py-4">{{ $item->kendaraan->plat_nomor }}</td>
                            <td class="px-6 py-4">{{ $item->tgl_mulai }}</td>
                            <td class="px-6 py-4">{{ $item->tgl_selesai }}</td>
                            <td class="px-6 py-4">{{ $item->tujuan }}</td>
                            <td class="flex space-x-2 px-6 py-4">
                                <a href="{{ route('admin.pengajuan-peminjaman.detail', $item->id_peminjaman) }}" class="text-blue-600 dark:text-blue-500 hover:underline">
                                    Detail
                                </a>
                                <!-- Form Setujui -->
                                <form action="{{ route('admin.pengajuan-peminjaman.setujui', $item->id_peminjaman) }}" method="POST" id="setujui-form-{{ $item->id_peminjaman }}">
                                    @csrf
                                    <button type="button" onclick="confirmAction('setujui', {{ $item->id_peminjaman }})" class="text-green-600 dark:text-green-500 hover:underline">
                                        Setujui
                                    </button>
                                </form>

                                <!-- Form Tolak -->
                                <form action="{{ route('admin.pengajuan-peminjaman.tolak', $item->id_peminjaman) }}" method="POST" id="tolak-form-{{ $item->id_peminjaman }}">
                                    @csrf
                                    <button type="button" onclick="confirmAction('tolak', {{ $item->id_peminjaman }})" class="text-red-600 dark:text-red-500 hover:underline">
                                        Tolak
                                    </button>
                                </form>
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
    </div>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.14.5/dist/sweetalert2.all.min.js"></script>
    <script>
        function confirmAction(action, id) {
            const actionText = action === 'setujui' ? 'menyetujui' : 'menolak';
            const buttonText = action === 'setujui' ? 'Ya, setujui!' : 'Ya, tolak';
            const confirmButtonColor = action === 'setujui' ? '#28a745' : '#d33'; // Hijau untuk setujui, Merah untuk tolak

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

                    // Tampilkan pesan sukses setelah form dikirim
                    Swal.fire({
                        title: "Berhasil!",
                        text: `Peminjaman telah ${action === 'setujui' ? 'disetujui' : 'ditolak'}`,
                        icon: "success",
                        confirmButtonColor: "#3085d6",
                    });
                }
            });
        }
    </script>
{{-- </x-app-layout> --}}
@endsection