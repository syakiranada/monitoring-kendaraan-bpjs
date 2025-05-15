<x-app-layout>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Peminjaman</title>
</head>
<body>
    <div class="relative p-4">
        <div class="relative overflow-x-auto sm:rounded-lg">
            <h1 class="text-2xl font-bold text-black mb-4">Daftar Peminjaman Kendaraan</h1>
           
            <!-- Wrapper untuk tombol dan search -->
            
            <div class="flex justify-between items-center mb-4 gap-4"> 
                <!-- Tambah -->
                 <div class="w-full sm:w-auto">
                    <a href="{{ route('peminjaman.showForm')}}" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 focus:outline-none">
                        + Tambah
                    </a>
                </div>
                
                <form action="{{ route('peminjaman') }}" method="GET" class="flex justify-end  w-full sm:w-auto justify-end ">
                    <div class="relative w-full">
                        <input 
                            type="text" 
                            name="search"
                            class="block w-full p-2 pl-10 text-sm text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500" 
                            placeholder="Cari"
                            value="{{ request('search') }}" 
                        >
                        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                            <svg class="w-4 h-4 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none"
                                viewBox="0 0 20 20">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z" />
                            </svg>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Tabel -->
            <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
                <table class="w-full text-sm text-left rtl:text-right text-gray-500">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 sm:px-6 sm:py-3 text-xs sm:text-xs ">Merek dan Tipe</th>
                            <th scope="col" class="px-6 py-3 sm:px-6 sm:py-3 text-xs sm:text-xs">Plat</th>
                            <th scope="col" class="px-6 py-3 sm:px-6 sm:py-3 text-xs sm:text-xs">Tanggal Mulai</th>
                            <th scope="col" class="px-6 py-3 sm:px-6 sm:py-3 text-xs sm:text-xs">Tanggal Selesai</th>
                            <th scope="col" class="px-6 py-3 sm:px-6 sm:py-3 text-xs sm:text-xs">Status</th>
                            <th scope="col" class="px-6 py-3 sm:px-6 sm:py-3 text-xs sm:text-xs">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($daftarPeminjaman as $peminjaman)
                            <tr class="bg-white border-b hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">{{ $peminjaman->kendaraan->merk }} {{ $peminjaman->kendaraan->tipe }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">{{ $peminjaman->kendaraan->plat_nomor }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">{{ \Carbon\Carbon::parse($peminjaman->tgl_mulai)->format('d-m-Y') }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">{{ \Carbon\Carbon::parse($peminjaman->tgl_selesai)->format('d-m-Y') }}</td>
                                <td class="px-6 py-4 whitespace-nowrap uppercase">
                                    <span class="
                                         @if($peminjaman->status_pinjam == 'Menunggu Persetujuan' || $peminjaman->status_pinjam == 'MENUNGGU PERSETUJUAN') bg-blue-100 text-blue-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded-sm  border border-blue-400
                            
                                        @elseif($peminjaman->status_pinjam == 'Disetujui' || $peminjaman->status_pinjam == 'DISETUJUI') bg-green-100 text-green-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded-sm border border-green-400
                                        
                                        @elseif($peminjaman->status_pinjam == 'Dibatalkan' || $peminjaman->status_pinjam == 'DIBATALKAN') bg-gray-100 text-gray-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded-sm border border-gray-500
                                        
                                        @elseif($peminjaman->status_pinjam == 'Ditolak' || $peminjaman->status_pinjam == 'DITOLAK') bg-red-100 text-red-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded-sm  border border-red-400

                                        @elseif($peminjaman->status_pinjam == 'Diperpanjang' || $peminjaman->status_pinjam == 'DIPERPANJANG') bg-yellow-100 text-yellow-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded-sm  border border-yellow-300

                                        @elseif($peminjaman->status_pinjam == 'Telah Dikembalikan' || $peminjaman->status_pinjam == 'TELAH DIKEMBALIKAN') bg-gray-100 text-gray-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded-sm border border-gray-500
            
                                        @endif">
                                        {{$peminjaman->status_pinjam}}
                                    </span>
                                </td>
                                

                                <td class="px-6 py-5">
                                    <!-- Tombol Detail (selalu tampil) -->
                                    <a href="{{ route('peminjaman.detail', ['id' => $peminjaman->id_peminjaman, 'page' => request('page'), 'search' => request('search')]) }}" class="font-medium text-blue-600 hover:underline">Detail</a>
                                    
                                    <!-- Jika status Menunggu Persetujuan -->
                                    <!-- <button type="button" onclick="window.location.href='{{ route('admin.cek-fisik.index', ['page' => request('page'), 'search' => request('search')]) }} -->
                                    @if($peminjaman->status_pinjam == 'Menunggu Persetujuan')
                                        <a href="javascript:void(0);"onclick="confirmBatal({{ $peminjaman->id_peminjaman }})" class="font-medium text-red-600  hover:underline">Batal</a>
                                    @endif

                                    <!-- Jika status Disetujui -->
                                    @if($peminjaman->status_pinjam == 'Disetujui')
                                        <a href="javascript:void(0);" onclick="confirmBatal({{ $peminjaman->id_peminjaman }})" class="font-medium text-red-600  hover:underline">Batal</a>
                                        
                                        <a href="{{ route('peminjaman.showFormPerpanjangan', ['id' => $peminjaman->id_peminjaman, 'page' => request('page'), 'search' => request('search')]) }}" class="font-medium text-yellow-500  hover:underline">Perpanjang</a>

                                        <a href="{{ route('peminjaman.showFormPengembalian', ['id' => $peminjaman->id_peminjaman, 'page' => request('page'), 'search' => request('search')]) }}" class="font-medium text-green-500  hover:underline">Selesai</a>
                                    @endif

                                    <!-- Jika status Ditolak -->
                                    @if($peminjaman->status_pinjam == 'Ditolak')
                                        <!-- Hanya tombol Detail -->
                                    @endif

                                    <!-- Jika status Diperpanjang -->
                                    @if($peminjaman->status_pinjam == 'Diperpanjang')
                                        <!-- Hanya tombol Detail -->
                                    @endif

                                    <!-- Jika status Dibatalkan -->
                                    @if($peminjaman->status_pinjam == 'Dibatalkan')
                                        <!-- Hanya tombol Detail -->
                                    @endif

                                    <!-- Jika status Telah Dikembalikan -->
                                    @if($peminjaman->status_pinjam == 'Telah Dikembalikan')
                                        <!-- Hanya tombol Detail -->
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <!-- <td colspan="8" class="text-center px-6 py-4">Data tidak tersedia.</td> -->
                                <td colspan="6" class="text-sm text-gray-500 text-center py-4 bg-white">Data tidak ditemukan</td>
                            </tr>                        
                        @endforelse
                    </tbody>
                </table>
            </div>
             <!-- Pagination -->
             @if ($daftarPeminjaman->hasPages())
                <div class="mt-4">
                    {{ $daftarPeminjaman->appends(request()->query())->links('vendor.pagination.tailwind') }}
                </div>
            @endif
        </div>
    </div>
</body>
</html>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
    function confirmBatal(id) {
        // Dapatkan parameter page dan search dari URL saat ini
        const urlParams = new URLSearchParams(window.location.search);
        const page = urlParams.get('page') || 1;
        const search = urlParams.get('search') || '';

        Swal.fire({
            title: 'Apakah Anda yakin?',
            text: "Anda tidak akan dapat mengembalikan perubahan ini!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Ya, Batalkan!',
            cancelButtonText: 'Batal',
            reverseButtons: true,
            customClass: {
                confirmButton: "text-white bg-red-600 hover:bg-red-800 font-medium rounded-m text-sm p-3"
            }
        }).then((result) => {
            if (result.isConfirmed) {
                // Kirim request AJAX untuk membatalkan peminjaman
                $.ajax({
    url: `/peminjaman/${id}/batal`,
    type: 'POST',
    data: {
        page: page,
        search: search
    },
    headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
    
    },
    success: function(response) {
        // Menampilkan pop-up sukses setelah berhasil
        Swal.fire({
            title: 'Berhasil!',
            text: response.message,
            icon: 'success',
            confirmButtonText: 'OK'
            
        }).then(() => {
            // Setelah pop-up, bisa redirect atau melakukan tindakan lain
            window.location.href = `/peminjaman?page=${page}&search=${search}`;
        });
    },
    error: function(xhr, status, error) {
        // Jika terjadi error dalam request AJAX
        Swal.fire({
            title: 'Error!',
            text: 'Terjadi kesalahan saat membatalkan peminjaman.',
            icon: 'error',
            confirmButtonColor: '#3085d6',
            confirmButtonText: 'OK'
        });
    }
});

            }
        });
    }
</script>

</x-app-layout>
