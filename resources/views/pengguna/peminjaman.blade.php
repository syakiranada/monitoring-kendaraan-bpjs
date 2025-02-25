<x-app-layout>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <title>Peminjaman</title>
</head>
<body>
    <div class="relative p-6">
        <div class="relative overflow-x-auto sm:rounded-lg">
            <h1 class="text-2xl font-bold text-black dark:text-white mb-4">Daftar Peminjaman Kendaraan</h1>
           
            <!-- Wrapper untuk tombol dan search -->
            
            <div class="flex justify-between items-center mb-4"> 
                <!-- Tambah -->
                <div>
                    <a href="{{ route('peminjaman.showForm')}}" class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800">
                        + Tambah
                    </a>
                </div>
                

                <!-- Search -->
                <!-- <label for="input-group-search" class="sr-only">Search</label>
                <div class="relative mb-4">
                    <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-auto">
                        <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z"/>
                        </svg>
                    </div>
                    <input type="text" id="input-group-search" class="block w-full p-2 pl-10 text-sm text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="Cari">
                </div> -->
                <form action="{{ route('peminjaman') }}" method="GET" class="flex justify-end pb-4">
                    <div class="relative">
                        <input 
                            type="text" 
                            name="search"
                            class="block p-2 ps-10 text-sm text-gray-900 border border-gray-300 rounded-lg w-60 bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" 
                            placeholder="Cari peminjam, kendaraan, ..."
                            value="{{ request('search') }}"
                        >
                        <div class="absolute inset-y-0 start-0 flex items-center ps-3">
                            <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z"/>
                            </svg>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Tabel -->
           
            <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                    <tr>
                        <th scope="col" class="px-6 py-3 whitespace-nowrap">Merek dan Tipe</th>
                        <th scope="col" class="px-6 py-3 whitespace-nowrap">Plat</th>
                        <th scope="col" class="px-6 py-3 whitespace-nowrap">Tanggal Mulai</th>
                        <th scope="col" class="px-6 py-3 whitespace-nowrap">Tanggal Selesai</th>
                        <th scope="col" class="px-6 py-3 whitespace-nowrap">Status</th>
                        <th scope="col" class="px-6 py-3 whitespace-nowrap">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($daftarPeminjaman as $peminjaman)
                    <tr class="bg-white border-b hover:bg-gray-50 dark:hover:bg-gray-600">
                        <td class="px-6 py-4 whitespace-nowrap">{{ $peminjaman->kendaraan->merk }} {{ $peminjaman->kendaraan->tipe }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">{{ $peminjaman->kendaraan->plat_nomor }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">{{ \Carbon\Carbon::parse($peminjaman->tgl_mulai)->format('d-m-Y') }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">{{ \Carbon\Carbon::parse($peminjaman->tgl_selesai)->format('d-m-Y') }}</td>
                        <td class="px-6 py-4 whitespace-nowrap uppercase">
                            <span class="
                                @if($peminjaman->status_pinjam == 'Menunggu Persetujuan') bg-blue-100 text-blue-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded-sm dark:bg-gray-700 dark:text-blue-400 border border-blue-400
                            
                                @elseif($peminjaman->status_pinjam == 'Disetujui') bg-green-100 text-green-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded-sm dark:bg-gray-700 dark:text-green-400 border border-green-400
                                
                                @elseif($peminjaman->status_pinjam == 'Dibatalkan') bg-red-100 text-red-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded-sm dark:bg-gray-700 dark:text-red-400 border border-red-400

                                @elseif($peminjaman->status_pinjam == 'Diperpanjang') bg-yellow-100 text-yellow-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded-sm dark:bg-gray-700 dark:text-yellow-300 border border-yellow-300

                                @elseif($peminjaman->status_pinjam == 'Telah Dikembalikan') bg-gray-100 text-gray-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded-sm dark:bg-gray-700 dark:text-gray-400 border border-gray-500
    
                                @endif">
                                {{$peminjaman->status_pinjam}}
                            </span>
                            
                        </td>
                        

                        <td class="px-6 py-5">
                            <!-- Tombol Detail (selalu tampil) -->
                            <a href="{{ route('peminjaman.detail', ['id' => $peminjaman->id_peminjaman]) }}" class="font-medium text-blue-600 dark:text-blue-500 hover:underline">Detail</a>
                            
                            <!-- Jika status Menunggu Persetujuan -->
                            @if($peminjaman->status_pinjam == 'Menunggu Persetujuan')
                                <a href="javascript:void(0);"onclick="confirmBatal({{ $peminjaman->id_peminjaman }})" class="font-medium text-red-600 dark:text-red-500 hover:underline">Batal</a>
                            @endif

                            <!-- Jika status Disetujui -->
                            @if($peminjaman->status_pinjam == 'Disetujui')
                                <a href="javascript:void(0);" onclick="confirmBatal({{ $peminjaman->id_peminjaman }})" class="font-medium text-red-600 dark:text-red-500 hover:underline">Batal</a>
                                
                                <a href="{{ route('peminjaman.showFormPerpanjangan', $peminjaman->id_peminjaman) }}" class="font-medium text-yellow-500 dark:text-yellow-600 hover:underline">Perpanjang</a>

                                <a href="{{ route('peminjaman.showFormPengembalian', $peminjaman->id_peminjaman) }}" class="font-medium text-green-500 dark:text-green-600 hover:underline">Selesai</a>
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
                    @endforeach
                </tbody>
            </table>
             <!-- Pagination -->
             @if ($daftarPeminjaman->hasPages())
                <div class="mt-4 flex justify-end">
                    {{ $daftarPeminjaman->appends(request()->query())->links('vendor.pagination.tailwind') }}
                </div>
            @endif
        </div>
    </div>
</body>
</html>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    function confirmBatal(id) {
        Swal.fire({
            title: 'Apakah Anda yakin?',
            text: "Anda tidak akan dapat mengembalikan perubahan ini!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya, Batalkan!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                // Redirect ke URL pembatalan
                window.location.href = `/peminjaman/${id}/batal`;
            }
        });
    }
    @if(session('success'))
    <script>
        Swal.fire({
            title: 'Berhasil!',
            text: "{{ session('success') }}",
            icon: 'success',
            confirmButtonText: 'OK'
        });
    </script>
    @endif

    @if(session('error'))
        <script>
            Swal.fire({
                title: 'Gagal!',
                text: "{{ session('error') }}",
                icon: 'error',
                confirmButtonText: 'OK'
            });
        </script>
    @endif
    
</script>
<!-- <script>
  document.addEventListener("DOMContentLoaded", function () {
    const searchInput = document.getElementById("input-group-search");
    const tableRows = document.querySelectorAll("tbody tr");

    searchInput.addEventListener("input", function () {
      const searchValue = searchInput.value.toLowerCase();

      tableRows.forEach(row => {
        // Dapatkan semua <td> dalam baris, kecuali kolom aksi (asumsikan kolom terakhir)
        const cells = row.querySelectorAll("td");
        let rowText = "";
        // Ambil semua cell kecuali cell terakhir
        for (let i = 0; i < cells.length - 1; i++) {
          rowText += cells[i].textContent.toLowerCase() + " ";
        }
        // Tampilkan atau sembunyikan baris berdasarkan apakah teks cocok
        if (rowText.includes(searchValue)) {
          row.style.display = "";
        } else {
          row.style.display = "none";
        }
      });
    });
  });
</script> -->
</x-app-layout>
