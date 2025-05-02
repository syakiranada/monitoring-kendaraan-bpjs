<x-app-layout>
    <style>
        #polisModal, #pembayaranModal {
            display: none;
        }
        #polisModal.flex, #pembayaranModal.flex {
            display: flex !important;
        }
        
        /* Responsive styling */
        @media (max-width: 640px) {
            .detail-container {
                padding: 1rem;
            }
            
            .detail-item {
                margin-bottom: 0.75rem;
            }
            
            .detail-label {
                margin-bottom: 0.25rem;
                font-weight: 500;
            }
        }
    </style>

<div class="container px-4 py-6 w-fit">
        @php 
            $currentPage = request()->query('page');
        @endphp 
        <!-- Button Back -->
        <a href="{{ route('admin.riwayat.asuransi', ['page' => $currentPage, 'search' => request()->query('search', '')]) }}" class="flex items-center text-blue-600 font-semibold hover:underline mb-5">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"></path>
            </svg>
            Kembali
        </a>

        <h1 class="text-2xl font-bold mb-6">Detail Asuransi</h1>
        
        <div class="w-full max-w-md sm:max-w-lg md:max-w-xl lg:max-w-2xl xl:max-w-3xl p-4 sm:p-6 bg-white border border-gray-200 rounded-lg shadow-sm mx-auto detail-container">
            <input type="hidden" name="current_page" value="{{ $currentPage }}">  

            <div class="flex justify-between items-center mb-4">
                <h2 class="text-lg font-semibold text-gray-900">
                    {{ $asuransi->kendaraan->merk }} {{ $asuransi->kendaraan->tipe }}
                </h2>
            </div>

            <div class="space-y-3">
                @php
                    $fields = [
                        'Diinput Oleh' => $asuransi->user->name,
                        'Plat Nomor' => $asuransi->kendaraan->plat_nomor,
                        'Masa Perlindungan Awal' => \Carbon\Carbon::parse($asuransi->tgl_perlindungan_awal)->format('d-m-Y'),
                        'Masa Perlindungan Akhir' => \Carbon\Carbon::parse($asuransi->tgl_perlindungan_akhir)->format('d-m-Y'),
                        'Jatuh Tempo Pembayaran Asuransi' => \Carbon\Carbon::parse($asuransi->tgl_jatuh_tempo)->format('d-m-Y'),
                        'Tanggal Pembayaran Asuransi' => \Carbon\Carbon::parse($asuransi->tgl_bayar)->format('d-m-Y'),
                        'Nominal Tagihan' => 'Rp ' . number_format($asuransi->nominal, 0, ',', '.'),
                        'Biaya Lain-lain' => $asuransi->biaya_asuransi_lain ? 'Rp ' . number_format($asuransi->biaya_asuransi_lain, 0, ',', '.') : '-',
                        'Tanggal Pembayaran Asuransi Selanjutnya' => \Carbon\Carbon::parse($asuransi->tgl_perlindungan_akhir)->format('d-m-Y')
                    ];
                @endphp
                
                @foreach ($fields as $label => $value)
                    <div class="flex flex-col sm:flex-row items-start text-sm detail-item">
                        <span class="text-gray-600 sm:w-72 detail-label">{{ $label }}</span>
                        <span class="text-gray-900">{{ $value }}</span>
                    </div>
                @endforeach

                @php
                    $polisPath = $asuransi->polis ?? ''; 
                    $polisExists = !empty($polisPath) && Storage::exists('public/' . $polisPath);
                @endphp

                <div class="flex flex-col sm:flex-row items-start text-sm detail-item">
                    <span class="text-gray-600 sm:w-72 detail-label">Bukti Polis Asuransi</span>
                    @if($polisExists)
                        <a href="#" class="text-blue-600 hover:underline" onclick="showModal('polisModal', '{{ asset('storage/' . $polisPath) }}')">Lihat bukti</a>
                    @else
                        <span class="text-gray-400">Tidak ada bukti</span>
                    @endif
                </div>

                @php
                    $pembayaranPath = $asuransi->bukti_bayar_asuransi ?? ''; 
                    $pembayaranExists = !empty($pembayaranPath) && Storage::exists('public/' . $pembayaranPath);
                @endphp

                <div class="flex flex-col sm:flex-row items-start text-sm detail-item">
                    <span class="text-gray-600 sm:w-72 detail-label">Bukti Pembayaran</span>
                    @if($pembayaranExists)
                        <a href="#" class="text-blue-600 hover:underline" onclick="showModal('pembayaranModal', '{{ asset('storage/' . $pembayaranPath) }}')">Lihat bukti</a>
                    @else
                        <span class="text-gray-400">Tidak ada bukti</span>
                    @endif
                </div>

                {{-- <div class="mt-6 pt-2">
                    <button type="button" onclick="window.location.href='{{ route('admin.riwayat.asuransi', ['page' => $currentPage, 'search' => request()->query('search', '')]) }}'" 
                        class="bg-purple-600 text-white px-4 sm:px-6 py-2 sm:py-2.5 rounded-lg font-semibold hover:bg-purple-700 transition sm:self-start w-fitt sm:w-auto">
                        Kembali
                    </button>
                </div> --}}
            </div>
        </div>
    </div>

    <!-- Modal for Polis Document -->
    <div id="polisModal" class="fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center hidden z-50">
        <div class="relative bg-white p-2 sm:p-4 rounded-lg shadow-lg w-full max-w-xs sm:max-w-lg md:max-w-2xl lg:max-w-3xl mx-4">
            <button onclick="hideModal('polisModal')" class="absolute top-1.5 right-3 bg-white text-black text-sm w-7 h-7 flex items-center justify-center rounded-full shadow-md hover:bg-gray-300 border border-black z-10">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
            <div class="filePreviewContainer max-h-[70vh] sm:max-h-[80vh] overflow-auto"></div>
        </div>
    </div>

    <!-- Modal for Pembayaran Document -->
    <div id="pembayaranModal" class="fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center hidden z-50">
        <div class="relative bg-white p-2 sm:p-4 rounded-lg shadow-lg w-full max-w-xs sm:max-w-lg md:max-w-2xl lg:max-w-3xl mx-4">
            <button onclick="hideModal('pembayaranModal')" class="absolute top-1.5 right-3 bg-white text-black text-sm w-7 h-7 flex items-center justify-center rounded-full shadow-md hover:bg-gray-300 border border-black z-10">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
            <div class="filePreviewContainer max-h-[70vh] sm:max-h-[80vh] overflow-auto"></div>
        </div>
    </div>

    <script>
        function showModal(modalId, filePath) {
            const modal = document.getElementById(modalId);
            const filePreviewContainer = modal.querySelector('.filePreviewContainer'); 

            if (!modal || !filePreviewContainer) return;

            const fileExtension = filePath.split('.').pop().toLowerCase();
            
            if (fileExtension === "pdf") {
                filePreviewContainer.innerHTML = `<iframe src="${filePath}" class="w-full h-[70vh] sm:h-[80vh]" frameborder="0"></iframe>`;
            } else {
                filePreviewContainer.innerHTML = `<img src="${filePath}" alt="File Asuransi" class="max-h-[70vh] sm:max-h-[80vh] max-w-full object-contain rounded-lg">`;
            }

            modal.classList.remove('hidden');
            modal.classList.add('flex');
            document.body.style.overflow = 'hidden';
        }

        function hideModal(modalId) {
            const modal = document.getElementById(modalId);
            if (modal) {
                modal.classList.add('hidden');
                modal.classList.remove('flex');
                document.body.style.overflow = '';
            }
        }

        // Close modal when clicking outside the content
        document.querySelectorAll('.fixed').forEach(modal => {
            modal.addEventListener('click', e => { 
                if (e.target === modal) hideModal(modal.id); 
            });
        });

        // Close modal with Escape key
        document.addEventListener('keydown', e => {
            if (e.key === 'Escape') {
                document.querySelectorAll('.fixed:not(.hidden)').forEach(modal => hideModal(modal.id));
            }
        });
        document.addEventListener("DOMContentLoaded", () => {
    hideModal('polisModal');
    hideModal('pembayaranModal');
});

    </script>
</x-app-layout>