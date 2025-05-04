<x-app-layout>
        <div class="p-6 space-y-12">
            <!-- Tabel Daftar Kendaraan Dipinjam -->
            <div>
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-2xl font-semibold text-gray-800 mb-4">Daftar Kendaraan Dipinjam</h2>
                    <form action="{{ route('servisInsidental') }}" method="GET" class="relative">
                        <input type="text" name="search_daftar" class="block p-2 ps-10 text-sm text-gray-900 border border-gray-300 rounded-lg w-60 bg-gray-50 focus:ring-blue-500 focus:border-blue-500" placeholder="Cari" value="{{ request('search_daftar') }}">
                        <i class="fas fa-search absolute left-3 top-3 text-gray-500"></i>
                        @if(request('search_riwayat'))
                            <input type="hidden" name="search_riwayat" value="{{ request('search_riwayat') }}">
                        @endif
                    </form>
                </div>
                <div class="bg-white shadow-md rounded-lg overflow-hidden">
                    <table class="w-full text-sm text-left rtl:text-right text-gray-500">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                            <tr>
                                <th class="py-3 px-4 text-left">MEREK DAN TIPE</th>
                                <th class="py-3 px-4 text-left">PLAT</th>
                                <th class="py-3 px-4 text-left">STATUS PEMINJAMAN</th>
                                <th class="py-3 px-4 text-center">AKSI</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($peminjamans as $peminjaman)
                                @if($peminjaman->status_pinjam == 'Disetujui' && $peminjaman->user_id == auth()->id())
                                    <tr class="kendaraan-row cursor-pointer" data-id="{{ $peminjaman->kendaraan->id_kendaraan ?? '' }}">
                                        <td class="py-3 px-4 border-b">
                                            <div>{{ ($peminjaman->kendaraan->merk ?? 'Tidak Diketahui') . ' ' . ($peminjaman->kendaraan->tipe ?? '') }}</div>
                                        </td>      
                                        <td class="py-3 px-4 border-b">{{ $peminjaman->kendaraan->plat_nomor ?? '-' }}</td>
                                        <td class="py-3 px-4 border-b">
                                            <span class="text-xs font-medium px-2.5 py-0.5 rounded text-blue-500 bg-blue-100">
                                                {{ strtoupper($peminjaman->status_pinjam ?? 'TIDAK DIKETAHUI') }}
                                            </span>
                                        </td>
                                        <td class="py-3 px-4 border-b text-center">
                                            <a href="{{ route('servisInsidental.create', [
                                                'id_peminjaman' => $peminjaman->id_peminjaman,
                                                'id_kendaraan'   => $peminjaman->kendaraan->id_kendaraan ?? '',
                                                'merk'           => $peminjaman->kendaraan->merk ?? 'Tidak Diketahui',
                                                'tipe'           => $peminjaman->kendaraan->tipe ?? '',
                                                'plat'           => $peminjaman->kendaraan->plat_nomor ?? '-'
                                            ]) }}" class="font-medium text-blue-500 hover:underline">
                                                Input Servis
                                            </a>
                                        </td>
                                    </tr>
                                @endif
                            @empty
                                <tr>
                                    <td colspan="5" class="py-6 text-center text-gray-500">Tidak ada data peminjaman ditemukan</td>
                                </tr>
                            @endforelse

                            @if(count($peminjamans) > 0 && !$peminjamans->contains('status_pinjam', 'Disetujui'))
                                <tr>
                                    <td colspan="5" class="py-6 text-center text-gray-500">Tidak ada data peminjaman dengan status disetujui</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
                <!-- Pagination -->
                <div class="mt-4">
                    {{ $peminjamans->appends(request()->except('peminjamans_page'))->links() }}
                </div>
            </div>
    
            <!-- Tabel Riwayat Servis Insidental -->
            <div>
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-2xl font-semibold text-gray-800 mb-4">Riwayat Servis Insidental Kendaraan</h2>
                    <form action="{{ route('servisInsidental') }}" method="GET" class="relative">
                        <input type="text" name="search_riwayat" class="block p-2 ps-10 text-sm text-gray-900 border border-gray-300 rounded-lg w-60 bg-gray-50 focus:ring-blue-500 focus:border-blue-500" placeholder="Cari" value="{{ request('search_riwayat') }}">
                        <i class="fas fa-search absolute left-3 top-3 text-gray-500"></i>

                        @if(request('search_daftar'))
                            <input type="hidden" name="search_daftar" value="{{ request('search_daftar') }}">
                        @endif
                    </form>
                </div>
                <div class="bg-white shadow-md rounded-lg overflow-hidden">
                    <table class="w-full text-sm text-left rtl:text-right text-gray-500">
                        <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                            <tr>
                                <th class="py-3 px-4 text-left">MEREK DAN TIPE</th>
                                <th class="py-3 px-4 text-left">PLAT</th>
                                <th class="py-3 px-4 text-left">TANGGAL SERVIS INSIDENTAL</th>
                                <th class="py-3 px-4 text-left">STATUS PEMINJAMAN</th>
                                <th class="py-3 px-4 text-center">AKSI</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($servisInsidentals as $servis)
                            <tr class="kendaraan-row cursor-pointer" data-id="{{ $servis->kendaraan->id_kendaraan ?? '' }}">
                                <td class="py-3 px-4 border-b">
                                    <div>{{ ($servis->kendaraan->merk ?? 'Tidak Diketahui') . ' ' . ($servis->kendaraan->tipe ?? '') }}</div>
                                </td>  
                                <td class="py-3 px-4 border-b">{{ $servis->kendaraan->plat_nomor ?? '-' }}</td>
                                <td class="py-3 px-4 border-b">{{ \Carbon\Carbon::parse($servis->tgl_servis)->locale('id')->format('d-m-Y') }}</td>
                                <td class="py-3 px-4 border-b">
                                    @if($servis->id_peminjaman && $servis->peminjaman)
                                        <span class="text-xs font-medium px-2.5 py-0.5 rounded text-{{ 
                                            $servis->peminjaman->status_pinjam == 'Telah Dikembalikan' ? 'green' : 
                                            ($servis->peminjaman->status_pinjam == 'Dibatalkan' ? 'red' : 
                                            ($servis->peminjaman->status_pinjam == 'Ditolak' ? 'red' : 
                                            ($servis->peminjaman->status_pinjam == 'Diperpanjang' ? 'yellow' : 
                                            ($servis->peminjaman->status_pinjam == 'Disetujui' ? 'blue' : 'gray')))) }}-500 bg-{{ 
                                            $servis->peminjaman->status_pinjam == 'Telah Dikembalikan' ? 'green' : 
                                            ($servis->peminjaman->status_pinjam == 'Dibatalkan' ? 'red' : 
                                            ($servis->peminjaman->status_pinjam == 'Ditolak' ? 'red' : 
                                            ($servis->peminjaman->status_pinjam == 'Diperpanjang' ? 'yellow' : 
                                            ($servis->peminjaman->status_pinjam == 'Disetujui' ? 'blue' : 'gray')))) }}-100">
                                            {{ strtoupper($servis->peminjaman->status_pinjam) }}
                                        </span>
                                    @else
                                        <span class="text-xs font-medium px-2.5 py-0.5 rounded text-gray-500 bg-gray-100">
                                            TIDAK TERKAIT PEMINJAMAN
                                        </span>
                                    @endif
                                </td>                                
                                
                                <td class="py-3 px-4 border-b text-center">
                                    <a href="{{ route('servisInsidental.detail', ['id' => $servis->id_servis_insidental, 'page' => request()->query('page', 1), 'search' => request()->query('search')]) }}" 
                                        class="font-medium text-gray-500 hover:underline mr-2">Detail</a>
                                    
                                    @if($servis->peminjaman->status_pinjam == 'Disetujui')
                                        <a href="{{ route('servisInsidental.edit', $servis->id_servis_insidental) }}" 
                                            class="font-medium text-yellow-500 hover:underline mr-2">Edit</a>
                                        
                                        <form action="{{ route('servisInsidental.destroy', $servis->id_servis_insidental) }}" 
                                              method="POST" 
                                              class="inline-block form-delete">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                    class="font-medium text-red-600 hover:underline delete-button">
                                                Hapus
                                            </button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="py-6 text-center text-gray-500">Tidak ada data servis insidental ditemukan</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <!-- Pagination -->
                <div class="mt-4">
                    {{ $servisInsidentals->appends(request()->except('servisInsidentals_page'))->links() }}
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
                                reverseButtons: true,
                                confirmButtonColor: "#d33",
                                cancelButtonColor: "#3085d6",
                                confirmButtonText: "Ya, Hapus!",
                                cancelButtonText: "Batal"
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    // Tampilkan loading
                                    {{--  Swal.fire({
                                        title: "Menghapus...",
                                        text: "Mohon tunggu sebentar",
                                        allowOutsideClick: false,
                                        didOpen: () => {
                                            Swal.showLoading();
                                        }
                                    });  --}}
        
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