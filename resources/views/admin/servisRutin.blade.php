<x-app-layout>
    <div class="flex">
        <!-- Main content -->
        <div class="flex-1 p-6">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-semibold text-gray-800 mb-4">Daftar Servis Rutin Kendaraan</h2>
                <form action="{{ route('admin.servisRutin') }}" method="GET" class="relative">
                    <input type="text" name="search" class="block p-2 ps-10 text-sm text-gray-900 border border-gray-300 rounded-lg w-60 bg-gray-50 focus:ring-blue-500 focus:border-blue-500" placeholder="Cari" value="{{ request('search') }}">
                    <i class="fas fa-search absolute left-3 top-3 text-gray-500"></i>
                </form>
            </div>
            <div class="bg-white shadow-md rounded-lg overflow-hidden">
                <table class="w-full text-sm text-left rtl:text-right text-gray-500">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                        <tr>
                            <th class="py-3 px-4 text-left">Merk dan Tipe</th>
                            <th class="py-3 px-4 text-left">Plat</th>
                            <th class="py-3 px-4 text-left">Tanggal Servis Rutin</th>
                            <th class="py-3 px-4 text-left">Status Servis</th>
                            <th class="py-3 px-4 text-left">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($servisRutins as $servis)
                            @if(in_array(strtolower($servis->aset ?? ''), ['guna', 'tidak guna']))
                            <tr class="kendaraan-row cursor-pointer" data-id="{{ $servis->id_kendaraan ?? '' }}">
                                <td class="py-3 px-4 border-b">
                                    <div>{{ ($servis->merk ?? 'Tidak Diketahui') . ' ' . ($servis->tipe ?? '') }}</div>
                                </td> 
                                <td class="py-3 px-4 border-b">{{ $servis->plat_nomor ?? '-' }}</td>
                                <td class="py-3 px-4 border-b">
                                    {{ $servis->tgl_servis_real ? \Carbon\Carbon::parse($servis->tgl_servis_real)->locale('id')->format('d-m-Y') : '-' }}
                                </td>
                                <td class="py-3 px-4 border-b">
                                    @php
                                        $hariIni = \Carbon\Carbon::now();
                                    
                                        if (!$servis->tgl_servis_selanjutnya) {
                                            $status = 'Belum Pernah Servis';
                                            $color = 'gray';
                                        } else {
                                            $tglServis = \Carbon\Carbon::parse($servis->tgl_servis_selanjutnya);
                                            $selisihHari = $hariIni->diffInDays($tglServis, false);
                                    
                                            if ($selisihHari > 0 && $selisihHari <= 30) {
                                                $status = 'Mendekati Jatuh Tempo';
                                                $color = 'yellow';
                                            } elseif ($selisihHari <= 0) {
                                                $status = 'Jatuh Tempo';
                                                $color = 'red';
                                            } else {
                                                $status = 'Sudah Dibayar';
                                                $color = 'green';
                                            }
                                        }
                                        // Define full Tailwind color variants
                                        $textColor = "text-{$color}-800";
                                        $bgColor = "bg-{$color}-100";
                                        $borderColor = "border-{$color}-400";
                                    @endphp

                                    <span class="text-xs font-medium px-2.5 py-0.5 rounded-sm border {{ $textColor }} {{ $bgColor }} {{ $borderColor }}">
                                        {{ strtoupper($status) }}
                                    </span>
                                </td>
                                
                                <td class="py-3 px-4 border-b text-center">
                                    <div class="flex space-x-4">
                                        <a href="{{ route('admin.servisRutin.create', [
                                            'id_kendaraan' => $servis->id_kendaraan ?? '',
                                            'merk' => $servis->merk ?? 'Tidak Diketahui',
                                            'tipe' => $servis->tipe ?? '',
                                            'plat' => $servis->plat_nomor ?? '-',
                                            'jadwal_servis' => $servis->tgl_servis_selanjutnya ?? '-'
                                        ]) }}" class="font-medium text-blue-500 hover:underline">Input</a>                                
                                        @if ($servis->id_servis_rutin)
                                            @php
                                                $page = request('page');
                                                $search = request('search');
                                            @endphp

                                            <a href="{{ route('admin.servisRutin.detail', $servis->id_servis_rutin) }}?page={{ $page }}&search={{ $search }}"
                                            class="font-medium text-gray-500 hover:underline">Detail</a>

                                            <a href="{{ route('admin.servisRutin.edit', ['id' => $servis->id_servis_rutin]) }}?page={{ $page }}&search={{ $search }}"
                                            class="font-medium text-yellow-500 hover:underline">Edit</a>

                                            <form action="{{ route('admin.servisRutin.destroy', $servis->id_servis_rutin) }}?page={{ $page }}&search={{ $search }}"
                                                method="POST" class="inline form-delete">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button"
                                                        class="font-medium text-red-600 hover:underline delete-button"
                                                        data-url="{{ route('admin.servisRutin.destroy', $servis->id_servis_rutin) }}?page={{ $page }}&search={{ $search }}">
                                                    Hapus
                                                </button>
                                            </form>
                                        @endif
                                    </div>                              
                                </td>                                
                            </tr>
                            @endif
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
            <div class="mt-4">
                {{ $servisRutins->appends(request()->query())->links() }}
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
                        reverseButtons: true,
                        confirmButtonColor: "#d33",
                        cancelButtonColor: "#3085d6",
                        confirmButtonText: "Ya, Hapus!",
                        cancelButtonText: "Batal",
                        customClass: {
                            confirmButton: "swal2-confirm-blue",
                            cancelButton: "swal2-cancel-gray"
                        }
                    }).then((result) => {
                        if (result.isConfirmed) {
    
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
</x-app-layout>
