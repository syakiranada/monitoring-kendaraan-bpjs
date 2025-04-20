<x-app-layout>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Daftar Servis Rutin Kendaraan</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
</head>
<body class="bg-gray-100 font-sans leading-normal tracking-normal">
    <div class="flex">
        <!-- Main content -->
        <div class="flex-1 p-6">
            <div class="flex justify-between items-center mb-6">
                <h1 class="text-3xl font-bold">Daftar Servis Rutin Kendaraan</h1>
                <form action="{{ route('admin.servisRutin') }}" method="GET" class="relative">
                    <input type="text" name="search" class="border rounded-lg py-2 px-4 pl-10 w-64" placeholder="Search" value="{{ request('search') }}">
                    <i class="fas fa-search absolute left-3 top-3 text-gray-500"></i>
                </form>
            </div>
            <div class="bg-white shadow-md rounded-lg overflow-hidden">
                <table class="min-w-full bg-white">
                    <thead class="bg-gray-100 text-gray-600">
                        <tr>
                            <th class="py-3 px-4 text-left">Merk dan Tipe</th>
                            <th class="py-3 px-4 text-left">Plat</th>
                            <th class="py-3 px-4 text-left">Tanggal Servis Rutin</th>
                            <th class="py-3 px-4 text-left">Status</th>
                            <th class="py-3 px-4 text-left">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($servisRutins as $servis)
                            @if(isset($servis->kendaraan) && in_array($servis->kendaraan->aset, ['Guna', 'Tidak Guna']))
                            <tr class="kendaraan-row cursor-pointer" data-id="{{ $servis->kendaraan->id_kendaraan ?? '' }}">
                                <td class="py-3 px-4 border-b">
                                    <div>{{ ($servis->kendaraan->merk ?? 'Tidak Diketahui') . ' ' . ($servis->kendaraan->tipe ?? '') }}</div>
                                </td> 
                                <td class="py-3 px-4 border-b">{{ $servis->kendaraan->plat_nomor ?? '-' }}</td>
                                <td class="py-3 px-4 border-b">{{ \Carbon\Carbon::parse($servis->tgl_servis_real)->locale('id')->format('d-m-Y') }}</td>
                                <td class="py-3 px-4 border-b">
                                    @php
                                        $tglServis = \Carbon\Carbon::parse($servis->tgl_servis_selanjutnya);
                                        $hariIni = \Carbon\Carbon::now();
                                        $selisihHari = $hariIni->diffInDays($tglServis, false);
                                
                                        if ($selisihHari <= 30 && $selisihHari > 0) {
                                            $status = 'Mendekati Jatuh Tempo';
                                            $color = 'yellow';
                                        } elseif ($selisihHari <= 0) {
                                            $status = 'Jatuh Tempo';
                                            $color = 'red';
                                        } else {
                                            $status = 'Sudah Dibayar';
                                            $color = 'green';
                                        }
                                    @endphp
                                
                                    <span class="text-xs font-medium px-2.5 py-0.5 rounded text-{{ $color }}-500 bg-{{ $color }}-100">
                                        {{ strtoupper($status) }}
                                    </span>
                                </td>
                                
                                <td class="py-3 px-4 border-b">
                                    <a href="{{ route('admin.servisRutin.detail', $servis->id_servis_rutin) }}" 
                                        class="text-blue-500 hover:underline">Detail</a>
                                    <a href="{{ route('admin.servisRutin.create', [
                                        'id_kendaraan' => $servis->kendaraan->id_kendaraan ?? '',
                                        'merk' => $servis->kendaraan->merk ?? 'Tidak Diketahui',
                                        'tipe' => $servis->kendaraan->tipe ?? '',
                                        'plat' => $servis->kendaraan->plat_nomor ?? '-',
                                        'jadwal_servis' => $servis->tgl_servis_selanjutnya ?? '-'
                                    ]) }}" class="text-gray-500 hover:underline">Input</a>
                                    {{--  <a href="{{ route('admin.servisRutin.edit', [
                                        'servis' => $servis,
                                        'id_kendaraan' => $servis->kendaraan->id_kendaraan ?? '',
                                        'merk' => $servis->kendaraan->merk ?? 'Tidak Diketahui',
                                        'tipe' => $servis->kendaraan->tipe ?? '',
                                        'plat' => $servis->kendaraan->plat_nomor ?? '-',
                                        'jadwal_servis' => $servis->tgl_servis_selanjutnya ?? '-'
                                    ]) }}" class="text-blue-500 hover:underline">Edit</a>  --}}
                                    <a href="{{ route('admin.servisRutin.edit', ['id' => $servis->id_servis_rutin]) }}" class="text-blue-500 hover:underline">
                                        Edit
                                    </a>
                                    
                                    <form action="{{ route('admin.servisRutin.destroy', $servis->id_servis_rutin) }}" method="POST" class="delete-form inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" class="text-red-500 hover:underline delete-button" data-url="{{ route('admin.servisRutin.destroy', $servis->id_servis_rutin) }}">
                                            Hapus
                                        </button>
                                    </form>                                
                                </td>
                            </tr>
                            @endif {{-- ✅ Tutup if di sini, sebelum @empty --}}
                        @empty
                            <tr>
                                <td colspan="6" class="py-6 text-center text-gray-500">
                                    Tidak ada data servis rutin ditemukan
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                    
                </table>
            </div>
            <!-- Pagination -->
            <div class="flex justify-center items-center py-4">
                <div class="bg-white rounded-lg shadow-md p-2">
                    {{ $servisRutins->appends(request()->query())->links('pagination::tailwind') }}
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const deleteButtons = document.querySelectorAll('.delete-button');
    
            deleteButtons.forEach(button => {
                button.addEventListener('click', function (event) {
                    event.preventDefault();
                    console.log("Tombol hapus diklik:", button);
    
                    const deleteUrl = button.getAttribute('data-url');
    
                    Swal.fire({
                        title: "Konfirmasi",
                        text: "Apakah Anda yakin ingin menghapus data ini? Tindakan ini tidak dapat dibatalkan!",
                        icon: "warning",
                        showCancelButton: true,
                        confirmButtonColor: "#d33",
                        cancelButtonColor: "#3085d6",
                        confirmButtonText: "Ya, Hapus!",
                        cancelButtonText: "Batal"
                    }).then((result) => {
                        if (result.isConfirmed) {
                            Swal.fire({
                                title: "Menghapus...",
                                text: "Mohon tunggu sebentar",
                                allowOutsideClick: false,
                                didOpen: () => {
                                    Swal.showLoading();
                                }
                            });
    
                            fetch(deleteUrl, {
                                method: 'POST', // Tetap POST karena Laravel butuh method spoofing
                                headers: {
                                    'X-Requested-With': 'XMLHttpRequest',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                                    'Content-Type': 'application/x-www-form-urlencoded'
                                },
                                body: new URLSearchParams({ _method: 'DELETE' }) // Kirim method spoofing DELETE
                            })
                            
                            .then(response => {
                                if (response.ok) {
                                    return response.text(); // Bisa response.json() atau response.text()
                                } else {
                                    return response.json().then(err => { throw new Error(err.message || 'Terjadi kesalahan'); });
                                }
                            })
                            .then(() => {
                                Swal.fire({
                                    title: "Berhasil!",
                                    text: "Data berhasil dihapus",
                                    icon: "success",
                                    confirmButtonColor: "#3085d6",
                                    confirmButtonText: "OK",
                                    allowOutsideClick: false,
                                }).then(() => {
                                    window.location.reload(); // Refresh halaman setelah sukses
                                });
                            })
                            
                            .then(data => {
                                Swal.fire({
                                    title: "Berhasil!",
                                    text: "Data berhasil dihapus",
                                    icon: "success",
                                    confirmButtonColor: "#3085d6",
                                    confirmButtonText: "OK"
                                }).then(() => {
                                    window.location.reload(); // Reload halaman
                                });
                            })
                            .catch(error => {
                                console.error('Error:', error);
                                Swal.fire({
                                    title: "Gagal!",
                                    text: "Terjadi kesalahan saat menghapus data",
                                    icon: "error",
                                    confirmButtonColor: "#d33",
                                    confirmButtonText: "OK"
                                });
                            });
                        }
                    });
                });
            });
        });
    </script>
    
</body>
</html>

</x-app-layout>
