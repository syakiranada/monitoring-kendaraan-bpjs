<x-app-layout>
{{-- @extends('layouts.sidebar')
@section('content') --}}
    <style>
        #polisModal,
        #pembayaranModal {
            display: none;
        }
        #polisModal.flex,
        #pembayaranModal.flex {
            display: flex !important;
        }
    </style>
     <h1 class="text-2xl font-bold mb-6">Detail Asuransi</h1>
        <div class="w-full max-w-md p-6 bg-white border border-gray-200 rounded-lg shadow-sm">
            @php 
                $currentPage = request()->query('page');
                @endphp 
                <input type="hidden" name="current_page" value="{{ $currentPage }}">  
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-lg font-semibold text-gray-900">
                    {{ $asuransi->kendaraan->merk }} {{ $asuransi->kendaraan->tipe }}
                </h2>
            </div>
            <div class="space-y-3">
                <div class="flex justify-between text-sm">
                    <span class="text-gray-600">Plat Nomor</span>
                    <span class="text-gray-900">{{ $asuransi->kendaraan->plat_nomor }}</span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-gray-600">Masa Perlindungan Awal</span>
                    <span class="text-gray-900">{{ \Carbon\Carbon::parse($asuransi->tgl_perlindungan_awal)->format('d-m-Y') }}</span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-gray-600">Masa Perlindungan akhir</span>
                    <span class="text-gray-900">{{ \Carbon\Carbon::parse($asuransi->tgl_perlindungan_akhir)->format('d-m-Y') }}</span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-gray-600">Jatuh Tempo Pembayaran Asuransi</span>
                    <span class="text-gray-900">{{ \Carbon\Carbon::parse($asuransi->tgl_jatuh_tempo)->format('d-m-Y') }}</span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-gray-600">Tanggal Pembayaran Asuransi</span>
                    <span class="text-gray-900">{{ \Carbon\Carbon::parse($asuransi->tgl_bayar)->format('d-m-Y') }}</span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-gray-600 dark:text-gray-400">Nominal Tagihan</span>
                    <span class="text-gray-900 dark:text-white">Rp {{ number_format($asuransi->nominal, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-gray-600 dark:text-gray-400">Biaya Lain-lain</span>
                    <span class="text-gray-900 dark:text-white">
                        {{ $asuransi->biaya_asuransik_lain ? 'Rp ' . number_format($asuransi->biaya_asuransi_lain, 0, ',', '.') : '-' }}
                    </span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-gray-600">Tanggal Pembayaran Asuransi Selanjutnya</span>
                    <span class="text-gray-900">{{ \Carbon\Carbon::parse($asuransi->tgl_perlindungan_akhir)->format('d-m-Y') }}</span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-gray-600">Bukti Polis Asuransi</span>
                    @if($asuransi->polis)
                        <a href="#" class="text-blue-600 hover:underline" 
                           onclick="showModal('polisModal', '{{ asset('storage/' . $asuransi->polis) }}')">Lihat bukti</a>
                    @else
                        <span class="text-gray-400">Tidak ada bukti</span>
                    @endif
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-gray-600">Bukti Pembayaran</span>
                    @if($asuransi->bukti_bayar_asuransi)
                        <a href="#" class="text-blue-600 hover:underline" 
                           onclick="showModal('pembayaranModal', '{{ asset('storage/' . $asuransi->bukti_bayar_asuransi) }}')">Lihat bukti</a>
                    @else
                        <span class="text-gray-400">Tidak ada bukti</span>
                    @endif
                </div>
                <button type="button" onclick="window.location.href='{{ route('asuransi.daftar_kendaraan_asuransi', ['page' => $currentPage]) }}'" class="bg-purple-600 text-white px-6 py-2.5 rounded-lg font-semibold hover:bg-purple-700 transition">
                    Kembali
                </button>   
            </div>
        </div>
    </div>
    <div id="polisModal" class="fixed inset-0 bg-black bg-opacity-50 justify-center items-center hidden z-40">
        <div class="relative bg-white p-4 rounded-lg shadow-lg w-auto max-w-3xl">
            <button onclick="hideModal('polisModal')" class="absolute top-1.5 right-3 bg-white text-black text-sm w-7 h-7 flex items-center justify-center rounded-full shadow-md hover:bg-gray-300 z-50 border border-black">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
            <div class="filePreviewContainer max-h-[80vh] overflow-auto"></div>
        </div>
    </div>
    <div id="pembayaranModal" class="fixed inset-0 bg-black bg-opacity-50 justify-center items-center hidden z-40">
        <div class="relative bg-white p-4 rounded-lg shadow-lg w-auto max-w-3xl">
            <button onclick="hideModal('pembayaranModal')" class="absolute top-1.5 right-3 bg-white text-black text-sm w-7 h-7 flex items-center justify-center rounded-full shadow-md hover:bg-gray-300 z-50 border border-black">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
            <div class="filePreviewContainer max-h-[80vh] overflow-auto"></div>
        </div>
    
    <script>
       function showModal(modalId, filePath) {
            const modal = document.getElementById(modalId);
            const filePreviewContainer = modal.querySelector('.filePreviewContainer'); 

            if (!modal || !filePreviewContainer) {
                console.error("Modal atau filePreviewContainer tidak ditemukan!");
                return;
            }

            if (!filePath) {
                console.error("File path tidak valid.");
                return;
            }

            const fileExtension = filePath.split('.').pop().toLowerCase();
            filePreviewContainer.innerHTML = ""; 

            if (fileExtension === "pdf") {
                filePreviewContainer.innerHTML = `
                    <iframe src="${filePath}" class="w-full h-[80vh]" frameborder="0"></iframe>
                `;
            } else {
                filePreviewContainer.innerHTML = `
                    <img src="${filePath}" alt="File Asuransi" class="max-h-[80vh] max-w-full object-contain rounded-lg">
                `;
            }

            modal.classList.remove('hidden');
            modal.classList.add('flex');
            document.body.style.overflow = 'hidden';
        }

        function hideModal(modalId) {
            const modal = document.getElementById(modalId);
            modal.classList.add('hidden');
            modal.classList.remove('flex');
            document.body.style.overflow = '';
        }

        document.querySelectorAll('.fixed').forEach(modal => {
            modal.addEventListener('click', (e) => {
                if (e.target.classList.contains('fixed')) {
                    hideModal(modal.id);
                }
            });
        });

        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                document.querySelectorAll('.fixed').forEach(modal => {
                    if (!modal.classList.contains('hidden')) {
                        hideModal(modal.id);
                    }
                });
            }
        });
    </script>
</x-app-layout>
{{-- @endsection --}}