<x-app-layout>
{{-- @extends('layouts.sidebar')
@section('content') --}}
        <h2 class="custom-text font-extrabold mb-6 ml-16 pt-6">Daftar Pajak Kendaraan</h2>
        <style>
            .custom-text {
                font-size: 2rem; 
            }
        </style>

        <form action="{{ route('pajak.daftar_kendaraan_pajak') }}" method="GET" class="flex justify-end pb-4">
            <div class="relative me-14">
                <input 
                    type="text" 
                    name="search"
                    class="block p-2 ps-10 text-sm text-gray-900 border border-gray-300 rounded-lg w-48 bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" 
                    placeholder="Search for items"
                    value="{{ request('search') }}"
                >
                <div class="absolute inset-y-0 start-0 flex items-center ps-3">
                    <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z"/>
                    </svg>
                </div>
            </div>
        </form>
        
        <table class="pb-8 w-11/12 mx-auto text-sm text-left text-gray-500 dark:text-gray-400">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
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
                    <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
                        <td class="px-6 py-3">{{ $item->merk ?? '-' }} {{ $item->tipe ?? '-' }}</td>
                        <td class="px-6 py-3">{{ $item->plat_nomor ?? '-' }}</td>
                        <td class="px-6 py-3">
                            {{ $item->tgl_jatuh_tempo_seharusnya ? \Carbon\Carbon::parse($item->tgl_jatuh_tempo_seharusnya)->format('d-m-Y') : '-' }}
                        </td>
                        <td class="px-6 py-3">
                            {{ $item->tgl_bayar ? \Carbon\Carbon::parse($item->tgl_bayar)->format('d-m-Y') : '-' }}
                        </td>
                        <td class="px-6 py-3 whitespace-nowrap">
                            @if ($item->status === 'JATUH TEMPO')
                                <span class="text-red-500">{{ $item->status }}</span>
                            @elseif ($item->status === 'MENDEKATI JATUH TEMPO')
                                <span class="text-orange-500">{{ $item->status }}</span>
                            @elseif ($item->status === 'SUDAH DIBAYAR')
                                <span class="text-green-500">{{ $item->status }}</span>
                            @else
                                <span>-</span>
                            @endif
                        </td>
                        
                        <td class="px-6 py-3 whitespace-nowrap">
                            @if (!empty($item->id_pajak))
                                @if ($item->status === 'JATUH TEMPO' || $item->status === 'MENDEKATI JATUH TEMPO')
                                    <a href="{{ route('pajak.kelola', ['id_kendaraan' => $item->id_kendaraan, 'page' => request()->query('page', 1)]) }}" class="font-medium text-gray-600 dark:text-gray-500 hover:underline">Kelola</a>
                                @elseif ($item->status === 'SUDAH DIBAYAR')
                                    <a href="{{ route('pajak.detail', ['id_pajak' => $item->id_pajak, 'page' => request()->query('page', 1)]) }}" class="font-medium text-blue-600 dark:text-blue-500 hover:underline mr-1">Detail</a>
                                    <a href="{{ route('pajak.edit', ['id_pajak' => $item->id_pajak, 'page' => request()->query('page', 1)]) }}" class="font-medium text-yellow-600 dark:text-yellow-500 hover:underline mr-1">Edit</a>
                                    <button class="font-medium text-red-600 dark:text-red-500 hover:underline" onclick="event.preventDefault(); confirmDelete({{ $item->id_pajak }})">Hapus</button>
                                    <form id="delete-form-{{ $item->id_pajak }}" action="{{ route('pajak.hapus', $item->id_pajak) }}" method="POST" class="hidden">
                                        @csrf
                                        @method('DELETE')
                                    </form>
                                @else
                                    <span>-</span>
                                @endif
                            @else
                                <a href="{{ route('pajak.kelola', ['id_kendaraan' => $item->id_kendaraan, 'page' => request()->query('page', 1)]) }}" class="font-medium text-gray-600 dark:text-gray-500 hover:underline">Kelola</a>
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
        <nav class="pb-4 flex items-center justify-end pt-4 px-12" aria-label="Table navigation">
            <div class="w-full md:w-auto flex justify-end">
                {{ $dataKendaraan->onEachSide(1)->links() }}
            </div>
        </nav>        
        
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
      function confirmDelete(id_pajak) {
        console.log("Fungsi confirmDelete dipanggil dengan id_pajak:", id_pajak);

        Swal.fire({
            title: "Konfirmasi",
            text: "Apakah Anda yakin ingin menghapus data pembayaran pajak ini?",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Ya, hapus!",
            cancelButtonText: "Batal"
        }).then((result) => {
            console.log("Result dari Swal:", result);

            if (result.isConfirmed) {
                console.log("User mengonfirmasi penghapusan, mencari form dengan ID:", 'delete-form-' + id_pajak);
                
                let form = document.getElementById('delete-form-' + id_pajak);
                if (!form) {
                    console.error("Form tidak ditemukan! Pastikan ID form benar.");
                    return;
                }
                let currentPage = new URLSearchParams(window.location.search).get('page') || 1;
                let actionUrl = form.getAttribute('action') + "?page=" + currentPage;
                form.setAttribute('action', actionUrl);

                Swal.fire({
                    title: "Berhasil!",
                    text: "Data pembayaran pajak berhasil dihapus.",
                    icon: "success"
                }).then(() => {
                    console.log("Mengirim form untuk menghapus pajak.");
                    form.submit();
                });
            }
        });
    }
</script>
{{-- @endsection --}}
</x-app-layout>

{{-- <div class="flex justify-center py-4">
    <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400"> --}}
