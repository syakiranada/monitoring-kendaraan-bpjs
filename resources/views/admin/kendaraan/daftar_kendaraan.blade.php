<x-app-layout>
    <div id="main-content" class="transition-all duration-300 w-full">
        <h2 class="custom-text font-extrabold mb-6 ml-6 pt-6">Daftar Kendaraan </h2>
        <style>
            .custom-text {
                font-size: 2rem; 
            }
            
            /* Main layout adjustments */
            #main-content {
                width: 100%;
                transition: padding-left 0.3s ease;
            }
            
            /* When sidebar is closed */
            body:not(.sidebar-open) #main-content {
                padding-left: 0;
            }
            
            /* When sidebar is open */
            body.sidebar-open #main-content {
                padding-left: 250px; /* Adjust based on your sidebar width */
            }
            
            /* Ensure table is always properly aligned */
            .table-wrapper {
                width: 95%;
                max-width: 1400px;
                margin: 0 auto;
                overflow-x: auto;
            }
            
            /* Form alignment */
            .form-wrapper {
                width: 95%;
                max-width: 1400px;
                margin: 0 auto;
            }
        </style>
        
        @if(!empty($alerts))
            <div class="form-wrapper">
                <div class="flex items-center w-full p-4 mb-4 text-sm text-yellow-800 rounded-lg bg-yellow-50" role="alert">
                    <svg class="shrink-0 inline w-4 h-4 me-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z"/>
                    </svg>
                    <span class="sr-only">Warning</span>
                    <div>
                        <span class="font-medium">Peringatan!</span><br>
                        <p>Tolong lengkapi form untuk kendaraan berikut:</p>
                        <ul class="list-disc pl-5">
                            @foreach($alerts as $alert)
                                <li>
                                    <strong>{{ $alert['vehicle'] }}</strong>: {{ implode(', ', $alert['incomplete']) }}
                                </li>
                            @endforeach
                        </ul>
                    </div>
                    <button type="button" class="close ml-auto" aria-label="Close" onclick="this.parentElement.style.display='none';">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            </div>
        @endif
        
        <div class="form-wrapper">
            <form action="{{ route('kendaraan.daftar_kendaraan') }}" method="GET" class="flex justify-end pb-4">
                <div class="relative me-1">
                    <input 
                        type="text" 
                        name="search"
                        class="block p-2 ps-10 text-sm text-gray-900 border border-gray-300 rounded-lg w-48 bg-gray-50 focus:ring-blue-500 focus:border-blue-500" 
                        placeholder="Cari Kendaraan"
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
        
        <div class="form-wrapper flex justify-between items-center pb-4">
            <div class="relative"> 
                <a href="{{ route('kendaraan.tambah', ['page' => request()->query('page', 1), 'search' => request()->query('search')]) }}" 
                    class="text-white bg-blue-700 hover:bg-blue-800 focus:outline-none focus:ring-4 focus:ring-blue-300 font-medium rounded-full text-sm px-5 py-2.5 text-center me-2 mb-2">
                     + Tambah
                </a>                 
            </div>
        </div>
        
        <div class="table-wrapper">
            <table class="w-full mx-auto text-sm text-left text-gray-500">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                    <tr>
                        <th class="px-6 py-3">Merk dan Tipe</th>
                        <th class="px-6 py-3">Warna</th>
                        <th class="px-6 py-3">Plat</th>
                        <th class="px-6 py-3">Status Aset</th>
                        <th class="px-6 py-3">Status Ketersediaan</th>
                        <th class="px-6 py-3">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($dataKendaraan as $item)
                        <tr class="bg-white border-b hover:bg-gray-50">
                            <td class="px-6 py-3">{{ $item->merk ?? '-' }} {{ $item->tipe ?? '-' }}</td>
                            <td class="px-6 py-3">{{ $item->warna ?? '-' }}</td>
                            <td class="px-6 py-3">{{ $item->plat_nomor ?? '-' }}</td>
                            <td class="px-6 py-3">{{ $item->aset ?? '-' }}</td>
                            <td class="px-6 py-3">
                                @if ($item->status_ketersediaan === 'Tersedia' || $item->status_ketersediaan === 'TERSEDIA')
                                    <span class="text-green-500 font-semibold">TERSEDIA</span>
                                @elseif ($item->status_ketersediaan === 'Tidak Tersedia' || $item->status_ketersediaan === 'TIDAK TERSEDIA')
                                    <span class="text-red-500 font-semibold">TIDAK TERSEDIA</span>
                                @else
                                    <span>-</span>
                                @endif
                            </td>
                            <td class="px-6 py-3">
                                <a href="{{ route('kendaraan.detail', ['id_kendaraan' => $item->id_kendaraan, 'page' => request()->query('page', 1), 'search' => request()->query('search')]) }}" 
                                   class="font-medium text-blue-600 hover:underline">
                                    Detail
                                </a>
                            
                                @if ($item->aset !== 'Lelang' && $item->aset !== 'LELANG')
                                    <a href="{{ route('kendaraan.edit', ['id_kendaraan' => $item->id_kendaraan, 'page' => request()->query('page', 1), 'search' => request()->query('search')]) }}" 
                                       class="font-medium text-yellow-600 hover:underline ml-2">
                                        Edit
                                    </a> 
                            
                                    <form id="delete-form-{{ $item->id_kendaraan }}" 
                                        action="{{ route('kendaraan.hapus', ['id_kendaraan' => $item->id_kendaraan, 'page' => request()->query('page', 1), 'search' => rawurldecode(request()->query('search'))]) }}" 
                                        method="POST" 
                                        style="display: none;">
                                      @csrf
                                      @method('DELETE') 
                                    </form>
                                  
                                    <button class="font-medium text-red-600 hover:underline ml-2" 
                                            onclick="confirmDelete({{ $item->id_kendaraan }})">
                                        Hapus
                                    </button>
                                @endif
                            </td>                                                             
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-4">Data tidak ditemukan</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="form-wrapper">
            <nav class="pb-4 flex items-center justify-end pt-4" aria-label="Table navigation">
                <div class="w-full md:w-auto flex justify-end">
                    {{ $dataKendaraan->onEachSide(1)->links() }}
                </div>
            </nav>  
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // Detect sidebar state on page load and window resize
        document.addEventListener('DOMContentLoaded', function() {
            adjustLayout();
            window.addEventListener('resize', adjustLayout);
            
            // Check if a sidebar toggle button exists and attach a listener
            const sidebarToggle = document.querySelector('.sidebar-toggle');
            if (sidebarToggle) {
                sidebarToggle.addEventListener('click', function() {
                    document.body.classList.toggle('sidebar-open');
                    adjustLayout();
                });
            }
        });
        
        function adjustLayout() {
            // This function can be extended based on your sidebar implementation
            // If you have a specific class or ID for the sidebar, you can check its visibility
            const sidebar = document.querySelector('.sidebar'); // Change to your sidebar selector
            const mainContent = document.getElementById('main-content');
            
            if (sidebar && mainContent) {
                // Check if sidebar is visible
                const sidebarVisible = window.getComputedStyle(sidebar).display !== 'none';
                
                if (sidebarVisible) {
                    document.body.classList.add('sidebar-open');
                } else {
                    document.body.classList.remove('sidebar-open');
                }
            }
        }
       
        function confirmDelete(id_kendaraan) {
            Swal.fire({
                title: "Konfirmasi",
                text: "Apakah Anda yakin ingin menghapus kendaraan ini? Tindakan ini tidak dapat dibatalkan!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#d33",
                cancelButtonColor: "#3085d6",
                confirmButtonText: "Ya, hapus!",
                cancelButtonText: "Batal"
            }).then((result) => {
                console.log("Result dari Swal:", result);

                if (result.isConfirmed) {
                    console.log("User mengonfirmasi penghapusan, mencari form dengan ID:", 'delete-form-' + id_kendaraan);

                    let form = document.getElementById('delete-form-' + id_kendaraan);
                    if (!form) {
                        console.error("Form tidak ditemukan! Pastikan ID form benar.");
                        return;
                    }

                    let currentPage = new URLSearchParams(window.location.search).get('page') || 1;
                    let actionUrl = form.getAttribute('action') + "?page=" + currentPage;
                    form.setAttribute('action', actionUrl);

                    Swal.fire({
                        title: "Berhasil!",
                        text: "Kendaraan berhasil dihapus.",
                        icon: "success"
                    }).then(() => {
                        console.log("Mengirim form untuk menghapus kendaraan.");
                        form.submit();
                    });
                }
            });
        }
    </script>
</x-app-layout>