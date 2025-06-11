<x-app-layout>
    <div class="p-6">
        <!-- Tabel Daftar Servis Insidental (All Vehicles) -->
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-semibold text-gray-800 mb-4">Daftar Servis Insidental Kendaraan</h2>
                <form action="{{ route('admin.servisInsidental') }}" method="GET" class="relative">
                    <input type="text" name="search" class="block p-2 ps-10 text-sm text-gray-900 border border-gray-300 rounded-lg w-60 bg-gray-50 focus:ring-blue-500 focus:border-blue-500" placeholder="Cari" value="{{ request('search') }}">
                    <i class="fas fa-search absolute left-3 top-3 text-gray-500"></i>
                </form>
            </div>
            <div class="bg-white shadow-md rounded-lg overflow-hidden">
                <div class="table-wrapper relative overflow-x-auto shadow-md sm:rounded-lg">
                <table class="w-full text-sm text-left rtl:text-right text-gray-500">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                        <tr>
                            <th class="py-3 px-4 text-left">MERK DAN TIPE</th>
                            <th class="py-3 px-4 text-left">PLAT</th>
                            <th class="py-3 px-4 text-left">TANGGAL SERVIS INSIDENTAL</th>
                            <th class="py-3 px-4 text-left">STATUS KETERSEDIAAN</th>
                            <th class="py-3 px-4 text-left">AKSI</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($servisInsidentals as $servis)
                        <tr class="kendaraan-row cursor-pointer" data-id="{{ $servis->id_kendaraan }}">
                            <td class="py-3 px-4 border-b">
                                <div>{{ $servis->merk ?? 'Tidak Diketahui' }} {{ $servis->tipe ?? '' }}</div>
                            </td> 
                            <td class="py-3 px-4 border-b">{{ $servis->plat_nomor ?? '-' }}</td>
                            <td class="py-3 px-4 border-b">{{ $servis->tgl_servis ? \Carbon\Carbon::parse($servis->tgl_servis)->locale('id')->format('d-m-Y') : '-' }}</td>
                            <td class="py-3 px-4 border-b">
                                <span class="bg-green-100 text-green-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded-sm border border-green-400">
                                    TERSEDIA
                                </span>
                            </td> 
                            <td class="py-3 px-4 border-b text-center">
                                <div class="flex space-x-4">
                                    <a href="{{ route('admin.servisInsidental.create', [
                                        'id_kendaraan' => $servis->id_kendaraan,
                                        'merk' => $servis->merk ?? 'Tidak Diketahui',
                                        'tipe' => $servis->tipe ?? '',
                                        'plat' => $servis->plat_nomor ?? '-'
                                    ]) }}" class="font-medium text-blue-500 hover:underline">
                                        Input
                                    </a>
                                    @if ($servis->id_servis_insidental)
                                        <a href="{{ route('admin.servisInsidental.detail', $servis->id_servis_insidental) }}" class="font-medium text-gray-500 hover:underline">Detail</a>
                                        <a href="{{ route('admin.servisInsidental.edit', ['id' => $servis->id_servis_insidental]) }}" class="font-medium text-yellow-500 hover:underline">Edit</a>
                            
                                        <form action="{{ route('admin.servisInsidental.destroy', $servis->id_servis_insidental) }}" method="POST" class="inline form-delete">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" class="font-medium text-red-600 hover:underline delete-button"
                                                data-url="{{ route('admin.servisInsidental.destroy', $servis->id_servis_insidental) }}">
                                                Hapus
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-6 text-center text-gray-500">Tidak ada data servis insidental ditemukan</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            </div>
            <!-- Pagination -->
            <div class="mt-4">
                {{ $servisInsidentals->appends(request()->query())->links() }}
            </div>
        </div>
    </div>
    <style>
        .swal2-cancel-gray {
            background-color: #6c757d !important;
            color: white !important;
            border: none !important;
        }
        
        .swal2-confirm-blue {
            background-color: #3085d6 !important;
            color: white !important;
            border: none !important;
        }
    </style> 
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
                            reverseButtons: true,
                            confirmButtonColor: "#d33",
                            cancelButtonColor: "#3085d6",
                            confirmButtonText: "Ya, Hapus!",
                            cancelButtonText: "Batal",
                            customClass: {
                                confirmButton: "swal2-confirm-red",
                                cancelButton: "swal2-cancel-gray"
                            }
                        }).then((result) => {
                            if (result.isConfirmed) {
    
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
</x-app-layout>