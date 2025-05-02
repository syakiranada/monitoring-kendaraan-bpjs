<x-app-layout>
    <div id="main-content" class="transition-all duration-300 w-full">
        <div class="p-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <h2 class="text-2xl font-semibold text-gray-800 dark:text-white sm:mb-0 mb-4">
                Daftar Pajak Kendaraan
            </h2>
            <form action="{{ route('pajak.daftar_kendaraan_pajak') }}" method="GET" class="flex items-center gap-2">
                <div class="relative">
                    <input 
                        type="text" 
                        name="search"
                        class="search-field block p-2 ps-10 text-sm text-gray-900 border border-gray-300 rounded-lg w-full sm:w-48 bg-gray-50 focus:ring-blue-500 focus:border-blue-500" 
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
        overflow-x: auto;
    }
    
    .form-wrapper {
        width: 100%;
        max-width: none;
        margin: 0 auto;
    }
    
    @media (min-width: 768px) {
    table th, table td {
        white-space: nowrap;
    }

    table th:nth-child(1),
    table td:nth-child(1) {
        max-width: 150px;
        white-space: normal !important;
        word-wrap: break-word;
    }

    table th:nth-child(2), table td:nth-child(2) {
        width: 10%;
    }

    table th:nth-child(3),
    table td:nth-child(3) {
        max-width: 180px;  
        width: auto;     
        white-space: normal !important;
        word-wrap: break-word;
    }

    table th:nth-child(4),
    table td:nth-child(4) {
        max-width: 160px;
        white-space: normal !important;
        word-wrap: break-word;
    }

    table th:nth-child(5), table td:nth-child(5) {
        width: 12%;
    }
    table th:nth-child(6), table td:nth-child(6) {
        width: 12%;
    }

    table th {
        white-space: normal;
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
        table th:nth-child(3), table td:nth-child(3) { min-width: 140px; }
        table th:nth-child(4), table td:nth-child(4) { min-width: 140px; }
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
    
    .status-badge-red {
        background-color: rgba(239, 68, 68, 0.1);
        color: rgb(239, 68, 68);
    }
    
    .status-badge-orange {
        background-color: rgba(249, 115, 22, 0.1);
        color: rgb(249, 115, 22);
    }
    
    .status-badge-green {
        background-color: rgba(34, 197, 94, 0.1);
        color: rgb(34, 197, 94);
    }
</style>

<div class ="mt-6"></div>
<div class="table-wrapper relative overflow-x-auto shadow-md sm:rounded-lg">
    <table class="w-full text-sm text-left rtl:text-right text-gray-500">
        <thead class="text-xs text-gray-700 uppercase bg-gray-50">
            <tr>
                <th class="px-6 py-3">Merk dan Tipe</th>
                <th class="px-6 py-3">Plat</th>
                <th class="px-6 py-3">Tanggal Jatuh Tempo</th>
                <th class="px-6 py-3">Tanggal Terakhir Bayar</th>
                <th class="px-6 py-3">Status Pajak</th>
                <th class="px-6 py-3">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($dataKendaraan as $item)
                <tr class="bg-white border-b hover:bg-gray-50">
                    <td class="px-6 py-3">{{ $item->merk ?? '-' }} {{ $item->tipe ?? '-' }}</td>                
                    <td class="px-6 py-3">{{ $item->plat_nomor ?? '-' }}</td>
                    <td class="px-6 py-3">
                        {{ $item->tgl_jatuh_tempo_seharusnya ? \Carbon\Carbon::parse($item->tgl_jatuh_tempo_seharusnya)->format('d-m-Y') : '-' }}
                    </td>
                    <td class="px-6 py-3">
                        {{ $item->tgl_bayar ? \Carbon\Carbon::parse($item->tgl_bayar)->format('d-m-Y') : '-' }}
                    </td>
                    <td class="px-6 py-3">
                        @if ($item->status === 'JATUH TEMPO')
                        <span class="bg-red-100 text-red-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded-sm border border-red-400">
                            {{ $item->status }}
                          </span>
                        @elseif ($item->status === 'MENDEKATI JATUH TEMPO')
                        <span class="bg-yellow-100 text-yellow-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded-sm border border-yellow-300">
                            {{ $item->status }}
                          </span>
                        @elseif ($item->status === 'SUDAH DIBAYAR')
                        <span class="bg-green-100 text-green-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded-sm border border-green-400">
                            {{ $item->status }}
                        </span>
                        @else
                            <span>-</span>
                        @endif
                    </td>
                    <td class="px-6 py-3 whitespace-nowrap">
                        @if (!empty($item->id_pajak))
                            @if ($item->status === 'JATUH TEMPO' || $item->status === 'MENDEKATI JATUH TEMPO')
                                <a href="{{ route('pajak.kelola', ['id_kendaraan' => $item->id_kendaraan, 'page' => request()->query('page', 1), 'search' => request()->query('search', '')]) }}" 
                                   class="font-medium text-blue-600 hover:underline">
                                    Input
                                </a>
                            @elseif ($item->status === 'SUDAH DIBAYAR')
                            <div class="flex flex-col sm:flex-row gap-1 items-start">
                                <a href="{{ route('pajak.detail', ['id_pajak' => $item->id_pajak, 'page' => request()->query('page', 1), 'search' => request()->query('search', '')]) }}" 
                                   class="font-medium text-gray-600 hover:underline mr-1">
                                    Detail
                                </a>
                                <a href="{{ route('pajak.edit', ['id_pajak' => $item->id_pajak, 'page' => request()->query('page', 1), 'search' => request()->query('search', '')]) }}" 
                                   class="font-medium text-yellow-600 hover:underline mr-1">
                                    Edit
                                </a>
                                <button class="font-medium text-red-600 hover:underline" 
                                        onclick="confirmDelete({{ $item->id_pajak }})">
                                    Hapus
                                </button>
                            </div>
                                
                                <form id="delete-form-{{ $item->id_pajak }}" 
                                      action="{{ route('pajak.hapus', ['id_pajak' => $item->id_pajak, 'page' => request()->query('page', 1), 'search' => request()->query('search', '')]) }}" 
                                      method="POST" 
                                      style="display: none;">
                                    @csrf
                                    @method('DELETE')
                                </form>
                            @else
                                <span>-</span>
                            @endif
                        @else
                            <a href="{{ route('pajak.kelola', ['id_kendaraan' => $item->id_kendaraan, 'page' => request()->query('page', 1), 'search' => request()->query('search', '')]) }}" 
                                class="font-medium text-blue-600 hover:underline">
                                    Input
                            </a>
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
<div class="mt-4">
    {{  $dataKendaraan->appends(request()->query())->links() }}
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
    
    function confirmDelete(id_pajak) {
        const isMobile = window.innerWidth < 768;
        
        Swal.fire({
            title: "Konfirmasi",
            text: "Apakah Anda yakin ingin menghapus data pembayaran pajak ini?",
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
                console.log("User mengonfirmasi penghapusan, mencari form dengan ID:", 'delete-form-' + id_pajak);
                
                let form = document.getElementById('delete-form-' + id_pajak);
                if (!form) {
                    console.error("Form tidak ditemukan! Pastikan ID form benar.");
                    return;
                }
                Swal.fire({
                    title: "Berhasil!",
                    text: "Data pembayaran pajak berhasil dihapus.",
                    icon: "success",
                    confirmButtonColor: "#3085d6",
                    width: isMobile ? '90%' : '32em'
                }).then(() => {
                    console.log("Mengirim form untuk menghapus pajak.");
                    form.submit();
                });
            }
        });
    }
</script>
</x-app-layout>