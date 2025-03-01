<x-app-layout>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Pengisian BBM</title>
        <script src="https://cdn.tailwindcss.com"></script>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    </head>
    <body class="bg-gray-100 font-sans">
        <div class="p-6 space-y-12">
            <!-- Tabel Daftar Kendaraan (Only Available Vehicles) -->
            <div>
                <div class="flex justify-between items-center mb-6">
                    <h1 class="text-3xl font-bold">Daftar Kendaraan Tersedia</h1>
                    <div class="relative">
                        <input type="text" class="border rounded-lg py-2 px-4 pl-10 w-64" placeholder="Search">
                        <i class="fas fa-search absolute left-3 top-3 text-gray-500"></i>
                    </div>
                </div>
                <div class="bg-white shadow-md rounded-lg overflow-hidden">
                    <table class="min-w-full bg-white">
                        <thead class="bg-gray-100 text-gray-600">
                            <tr>
                                <th class="py-3 px-4 text-left">MEREK DAN TIPE</th>
                                <th class="py-3 px-4 text-left">PLAT</th>
                                <th class="py-3 px-4 text-left">STATUS KETERSEDIAAN</th>
                                <th class="py-3 px-4 text-left">AKSI</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($kendaraanTersedia as $kendaraan)
                            <tr class="kendaraan-row cursor-pointer" data-id="{{ $kendaraan->id_kendaraan }}">
                                <td class="py-3 px-4 border-b">
                                    <div>{{ ($kendaraan->merk ?? 'Tidak Diketahui') . ' ' . ($kendaraan->tipe ?? '') }}</div>
                                </td>
                                <td class="py-3 px-4 border-b">{{ $kendaraan->plat_nomor ?? '-' }}</td>
                                <td class="py-3 px-4 border-b">
                                    <span class="text-xs font-medium px-2.5 py-0.5 rounded text-{{
                                        $kendaraan->status_ketersediaan == 'Tersedia' ? 'green' : 
                                        ($kendaraan->status_ketersediaan == 'Tidak Tersedia' ? 'red' : 'gray') 
                                    }}-500 bg-{{
                                        $kendaraan->status_ketersediaan == 'Tersedia' ? 'green' : 
                                        ($kendaraan->status_ketersediaan == 'Tidak Tersedia' ? 'red' : 'gray') 
                                    }}-100">
                                        {{ strtoupper($kendaraan->status_ketersediaan) }}
                                    </span>
                                </td>

                                <td class="py-3 px-4 border-b">
                                    <a href="{{ route('admin.pengisianBBM.create', [
                                                'id_kendaraan'   => $kendaraan->id_kendaraan ?? '',
                                                'merk'           => $kendaraan->merk ?? 'Tidak Diketahui',
                                                'tipe'           => $kendaraan->tipe ?? '',
                                                'plat'           => $kendaraan->plat_nomor ?? '-'
                                            ]) }}" class="text-blue-500 hover:underline">
                                                Input
                                            </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
    
            <!-- Tabel Daftar Riwayat BBM (Semua)-->
            <div>
                <div class="flex justify-between items-center mb-6">
                    <h1 class="text-3xl font-bold">Riwayat Pengisian BBM</h1>
                    <div class="relative">
                        <input type="text" class="border rounded-lg py-2 px-4 pl-10 w-64" placeholder="Search">
                        <i class="fas fa-search absolute left-3 top-3 text-gray-500"></i>
                    </div>
                </div>
                <div class="bg-white shadow-md rounded-lg overflow-hidden">
                    <table class="min-w-full bg-white">
                        <thead class="bg-gray-100 text-gray-600">
                            <tr>
                                <th class="py-3 px-4 text-left">MEREK DAN TIPE</th>
                                <th class="py-3 px-4 text-left">PLAT</th>
                                <th class="py-3 px-4 text-left">TANGGAL PENGISIAN BBM TERAKHIR</th>
                                <th class="py-3 px-4 text-left">STATUS PEMINJAMAN</th>
                                <th class="py-3 px-4 text-center">AKSI</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($pengisianBBMs as $bbm)
                            <tr class="kendaraan-row cursor-pointer" data-id="{{ $bbm->kendaraan->id_kendaraan ?? '' }}">
                                <td class="py-3 px-4 border-b">
                                    <div>{{ ($bbm->kendaraan->merk ?? 'Tidak Diketahui') . ' ' . ($bbm->kendaraan->tipe ?? '') }}</div>
                                </td>  
                                <td class="py-3 px-4 border-b">{{ $bbm->kendaraan->plat_nomor ?? '-' }}</td>
                                <td class="py-3 px-4 border-b">{{ \Carbon\Carbon::parse($bbm->tgl_isi)->locale('id')->format('d-m-Y') }}</td>
                                <td class="py-3 px-4 border-b">
                                    <span class="text-xs font-medium px-2.5 py-0.5 rounded text-{{
                                        $bbm->kendaraan?->status_ketersediaan == 'Tersedia' ? 'green' : 
                                        ($bbm->kendaraan?->status_ketersediaan == 'Tidak Tersedia' ? 'red' : 'gray') 
                                    }}-500 bg-{{
                                        $bbm->kendaraan?->status_ketersediaan == 'Tersedia' ? 'green' : 
                                        ($bbm->kendaraan?->status_ketersediaan == 'Tidak Tersedia' ? 'red' : 'gray') 
                                    }}-100">
                                        {{ strtoupper($bbm->kendaraan?->status_ketersediaan) }}
                                    </span>
                                </td>                                
                                <td class="py-3 px-4 border-b text-center">
                                    <div class="flex justify-center space-x-4">
                                        <a href="{{ route('admin.pengisianBBM.detail', $bbm->id_bbm) }}" class="text-blue-500 hover:underline">Detail</a>
                                        <a href="{{ route('admin.pengisianBBM.edit', ['id' => $bbm->id_bbm]) }}" class="text-green-500 hover:underline">Edit</a>
                                        <form action="{{ route('admin.pengisianBBM.destroy', $bbm->id_bbm) }}" method="POST" class="inline form-delete">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" class="text-red-500 hover:underline delete-button" 
                                                    data-url="{{ route('admin.pengisianBBM.destroy', $bbm->id_bbm) }}">
                                                Hapus
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="py-6 text-center text-gray-500">Tidak ada data pengisian BBM ditemukan</td>
                            </tr>
                            @endforelse
                        </tbody>                                                
                    </table>
                </div>
                <!-- Pagination -->
                <div class="flex justify-center items-center py-4">
                    <div class="bg-white rounded-lg shadow-md p-2">
                        {{ $pengisianBBMs->appends(request()->query())->links('pagination::tailwind') }}
                    </div>
                </div>
            </div>
        </div>

        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const deleteForms = document.querySelectorAll('form.form-delete');
        
                deleteForms.forEach(form => {
                    const deleteButton = form.querySelector('.delete-button');
                    if (deleteButton) {
                        deleteButton.addEventListener('click', function (event) {
                            event.preventDefault();
                            
                            // Ambil URL delete dari form action atau data attribute
                            const deleteUrl = form.getAttribute('action') || form.dataset.deleteUrl;
                            
                            if (!deleteUrl) {
                                console.error('URL untuk delete tidak ditemukan. Tambahkan action pada form atau data-delete-url attribute');
                                return;
                            }
        
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
                                    // Tampilkan loading
                                    Swal.fire({
                                        title: "Menghapus...",
                                        text: "Mohon tunggu sebentar",
                                        allowOutsideClick: false,
                                        didOpen: () => {
                                            Swal.showLoading();
                                        }
                                    });
        
                                    // Cek apakah element csrf token ada
                                    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                                    if (!csrfToken) {
                                        console.error('CSRF token tidak ditemukan, tambahkan <meta name="csrf-token" content="{{ csrf_token() }}"> di header');
                                    }
        
                                    fetch(deleteUrl, {
                                        method: 'POST',
                                        headers: {
                                            'X-Requested-With': 'XMLHttpRequest',
                                            'X-CSRF-TOKEN': csrfToken || '',
                                            'Content-Type': 'application/x-www-form-urlencoded'
                                        },
                                        body: new URLSearchParams({ _method: 'DELETE' })
                                    })
                                    .then(response => {
                                        // Coba parse response sebagai JSON, jika gagal kembalikan sebagai text
                                        const contentType = response.headers.get('content-type');
                                        if (contentType && contentType.includes('application/json')) {
                                            return response.json().then(data => {
                                                if (!response.ok) {
                                                    throw new Error(data.message || 'Terjadi kesalahan saat menghapus data');
                                                }
                                                return data;
                                            });
                                        } else {
                                            return response.text().then(text => {
                                                if (!response.ok) {
                                                    throw new Error('Terjadi kesalahan saat menghapus data');
                                                }
                                                return { message: text || 'Data berhasil dihapus' };
                                            });
                                        }
                                    })
                                    .then(data => {
                                        // Notifikasi sukses
                                        Swal.fire({
                                            title: "Berhasil!",
                                            text: data.message || "Data berhasil dihapus",
                                            icon: "success",
                                            confirmButtonColor: "#3085d6",
                                            confirmButtonText: "OK"
                                        }).then(() => {
                                            // Reload halaman atau redirect jika diperlukan
                                            if (data.redirect) {
                                                window.location.href = data.redirect;
                                            } else {
                                                window.location.reload();
                                            }
                                        });
                                    })
                                    .catch(error => {
                                        console.error('Error:', error);
                                        // Notifikasi gagal
                                        Swal.fire({
                                            title: "Gagal!",
                                            text: error.message || "Terjadi kesalahan saat menghapus data",
                                            icon: "error",
                                            confirmButtonColor: "#d33",
                                            confirmButtonText: "OK"
                                        });
                                    });
                                }
                            });
                        });
                    }
                });
            });
        </script>
    </body>
    </html>
    </x-app-layout>