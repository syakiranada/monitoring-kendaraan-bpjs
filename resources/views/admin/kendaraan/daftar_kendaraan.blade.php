<x-app-layout>
    <div id="main-content" class="transition-all duration-300 w-full">
        <div class="p-6">
        <h2 class="text-2xl font-semibold text-gray-800 mb-4 sm:mb-0">Daftar Kendaraan </h2>
        <style>
            .custom-text {
                font-size: 2rem; 
            }
            
            #main-content {
                width: 100%;
                transition: padding-left 0.3s ease;
            }
            
            body:not(.sidebar-open) #main-content {
                padding-left: 0;
            }
            
            body.sidebar-open #main-content {
                padding-left: 250px; 
            }
            
            .table-wrapper {
                width: 100%;
                max-width: none;
                margin: 0 auto;
            }
            
            .form-wrapper {
                width: 100%;
                max-width: none;
                margin: 0 auto;
            }
            
            @media (min-width: 768px) {
    table th,
    table td {
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    table th:nth-child(1),
    table td:nth-child(1) {
        width: 17% !important;
        max-width: 200px !important;
        white-space: normal !important;
        word-wrap: break-word;
        overflow-wrap: break-word;
        text-overflow: initial !important;
    }

    table th:nth-child(2),
    table td:nth-child(2) {
        width: 10% !important;
    }

    table th:nth-child(3),
    table td:nth-child(3) {
        width: 10% !important;
    }

    table th:nth-child(4),
    table td:nth-child(4) {
        width: 15% !important;
    }

    table th:nth-child(5),
    table td:nth-child(5) {
        width: 18% !important;
    }

    table th:nth-child(6),
    table td:nth-child(6) {
        width: 15% !important;
    }

    table th {
        white-space: normal !important;
        vertical-align: middle !important;
        padding: 12px 6px;
        text-align: left;
        font-size: 0.75rem;
    }
}

            @media (max-width: 767px) {
                .table-wrapper {
                    width: 100%;
                    overflow-x: scroll;
                }
                
                table th, table td {
                    min-width: 120px;
                }
                
                table th:nth-child(1), table td:nth-child(1) { min-width: 150px; }
                table th:nth-child(3), table td:nth-child(3) { min-width: 100px; }
                table th:nth-child(4), table td:nth-child(4) { min-width: 120px; }
            }
            
            @media (max-width: 640px) {
                .custom-text {
                    font-size: 1.25rem;
                    text-align: center;
                    margin-left: 0;
                }
                
                .search-field {
                    width: 100%;
                }
                
                .form-wrapper form {
                    margin-bottom: 1rem;
                }
            }
            
            .status-badge {
                display: inline-block;
                padding: 0.25rem 0.5rem;
                border-radius: 9999px;
                font-size: 0.75rem;
                font-weight: 500;
                white-space: nowrap;
            }
        </style>
        
        <div class="w-full">
        @if(!empty($alerts))
            <div class="form-wrapper">
                <div class="flex items-center w-full mt-3 p-4 mb-4 text-sm text-yellow-800 rounded-lg bg-yellow-50" role="alert">
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
   
<div class = "mt-6"></div>
<div class="form-wrapper flex flex-col sm:flex-row justify-between items-center pb-4 gap-2">
    <div>
        <a href="{{ route('kendaraan.tambah', ['page' => request()->query('page', 1), 'search' => request()->query('search')]) }}" 
            class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 focus:outline-none">
            + Tambah
        </a>
    </div>

    <form action="{{ route('kendaraan.daftar_kendaraan') }}" method="GET" class="w-full sm:w-auto flex items-center">
        <div class="relative w-full sm:w-64">
            <input 
                type="text" 
                name="search"
                class="search-field block w-full p-2 ps-10 text-sm text-gray-900 border border-gray-300 rounded-lg bg-gray-50 focus:ring-blue-500 focus:border-blue-500" 
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

        <div class="table-wrapper relative overflow-x-auto shadow-md sm:rounded-lg">
            <table class="w-full text-sm text-left rtl:text-right text-gray-500">
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
                                <span class="bg-green-100 text-green-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded-sm border border-green-400">
                                    TERSEDIA
                                  </span>                                  
                                @elseif ($item->status_ketersediaan === 'Tidak Tersedia' || $item->status_ketersediaan === 'TIDAK TERSEDIA')
                                <span class="bg-red-100 text-red-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded-sm border border-red-400">
                                    TIDAK TERSEDIA
                                  </span>
                                @else
                                    <span>-</span>
                                @endif
                            </td>
                            <td class="px-6 py-3">
                                <div class="flex flex-col sm:flex-row gap-1 items-start">
                                    <a href="{{ route('kendaraan.detail', ['id_kendaraan' => $item->id_kendaraan, 'page' => request()->query('page', 1), 'search' => request()->query('search')]) }}" 
                                        class="font-medium text-gray-600 hover:underline mr-1">
                                        Detail
                                    </a>
                                
                                    @if ($item->aset !== 'Lelang' && $item->aset !== 'LELANG')
                                        <a href="{{ route('kendaraan.edit', ['id_kendaraan' => $item->id_kendaraan, 'page' => request()->query('page', 1), 'search' => request()->query('search')]) }}" 
                                           class="font-medium text-yellow-600 hover:underline sm:ml-2">
                                            Edit
                                        </a> 
                                
                                        <form id="delete-form-{{ $item->id_kendaraan }}" 
                                            action="{{ route('kendaraan.hapus', ['id_kendaraan' => $item->id_kendaraan, 'page' => request()->query('page', 1), 'search' => rawurldecode(request()->query('search'))]) }}" 
                                            method="POST" 
                                            style="display: none;">
                                          @csrf
                                          @method('DELETE') 
                                        </form>
                                      
                                        <button class="font-medium text-red-600 hover:underline sm:ml-2" 
                                                onclick="confirmDelete({{ $item->id_kendaraan }})">
                                            Hapus
                                        </button>
                                    @endif
                                </div>
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
        <div class="mt-4">
            {{ $dataKendaraan->appends(request()->query())->links() }}
        </div>
    </div>
</div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            adjustLayout();
            window.addEventListener('resize', adjustLayout);
            const sidebarToggle = document.querySelector('.sidebar-toggle');
            if (sidebarToggle) {
                sidebarToggle.addEventListener('click', function() {
                    document.body.classList.toggle('sidebar-open');
                    adjustLayout();
                });
            }
        });
        
        function adjustLayout() {
            const sidebar = document.querySelector('.sidebar'); 
            const mainContent = document.getElementById('main-content');
            
            if (sidebar && mainContent) {
                const sidebarVisible = window.getComputedStyle(sidebar).display !== 'none';
                if (sidebarVisible) {
                    document.body.classList.add('sidebar-open');
                } else {
                    document.body.classList.remove('sidebar-open');
                }
            }
            
            const isMobile = window.innerWidth < 768;
            if (isMobile) {
                document.body.classList.remove('sidebar-open');
                if (mainContent) {
                    mainContent.style.paddingLeft = '0';
                }
            }
        }
       
        function confirmDelete(id_kendaraan) {
            const isMobile = window.innerWidth < 768;
            
            Swal.fire({
                title: "Konfirmasi",
                text: "Apakah Anda yakin ingin menghapus kendaraan ini? Tindakan ini tidak dapat dibatalkan!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: '#d33',
                confirmButtonText: "Ya, hapus!",
                cancelButtonText: "Batal",
                width: isMobile ? '90%' : '32em',
                reverseButtons: true 
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
                        icon: "success",
                        confirmButtonColor: "#3085d6",
                        width: isMobile ? '90%' : '32em'
                    }).then(() => {
                        console.log("Mengirim form untuk menghapus kendaraan.");
                        form.submit();
                    });
                }
            });
        }
    </script>
</x-app-layout>