<x-app-layout>
    <style>
        #pajakModal {
            display: none;
        }
        #pajakModal.flex {
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
        <h1 class="text-2xl font-bold mb-6">Detail Pajak</h1>
        <div class="w-full max-w-md sm:max-w-lg md:max-w-xl lg:max-w-2xl xl:max-w-3xl p-4 sm:p-6 bg-white border border-gray-200 rounded-lg shadow-sm mx-auto detail-container">
                @php 
                $currentPage = request()->query('page');
                @endphp 
                <input type="hidden" name="current_page" value="{{ $currentPage }}">        
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-lg font-semibold text-gray-900">
                    {{ $pajak->kendaraan->merk }} {{ $pajak->kendaraan->tipe }}
                </h2>
            </div>
            
            <div class="space-y-3">
                @php
                    $fields = [
                        'Admin Input' => $pajak->user->name,
                        'Plat Nomor' => $pajak->kendaraan->plat_nomor,
                        'Tanggal Jatuh Tempo Pembayaran Pajak' => \Carbon\Carbon::parse($pajak->tgl_jatuh_tempo)->format('d-m-Y'),
                        'Tanggal Pembayaran Pajak' => \Carbon\Carbon::parse($pajak->tgl_bayar)->format('d-m-Y'),
                        'Tanggal Pembayaran Pajak Selanjutnya' => \Carbon\Carbon::parse($pajak->tgl_jatuh_tempo_tahun_depan)->format('d-m-Y'),
                        'Nominal Tagihan' => 'Rp ' . number_format($pajak->nominal, 0, ',', '.'),
                        'Biaya Lain-lain' => $pajak->biaya_pajak_lain ? 'Rp ' . number_format($pajak->biaya_pajak_lain, 0, ',', '.') : '-'
                    ];
                @endphp
                
                @foreach ($fields as $label => $value)
                    <div class="flex flex-col sm:flex-row items-start text-sm detail-item">
                        <span class="text-gray-600 sm:w-72 detail-label">{{ $label }}</span>
                        <span class="text-gray-900">{{ $value }}</span>
                    </div>
                @endforeach
            
                @php
                $pembayaranPath = $pajak->bukti_bayar_pajak ?? ''; 
                $pembayaranExists = !empty($pembayaranPath) && 
                    (Storage::exists($pembayaranPath) || 
                     Storage::exists('public/' . $pembayaranPath) || 
                     file_exists(storage_path('app/public/' . $pembayaranPath)));
                @endphp
            
                <div class="flex flex-col sm:flex-row items-start text-sm detail-item">
                    <span class="text-gray-600 sm:w-72 detail-label">Bukti Pembayaran</span>
                
                    @if($pembayaranExists)
                        <a href="#" class="text-blue-600 hover:underline"
                           onclick="showModal('pajakModal', '{{ asset('storage/' . $pembayaranPath) }}')">Lihat bukti</a>
                    @else
                        <span class="text-gray-400">Tidak ada bukti</span>
                    @endif
                </div>
              
                <div class="mt-6 pt-2">
                    <button type="button" onclick="window.location.href='{{ route('admin.riwayat.pajak', ['page' => $currentPage, 'search' => request()->query('search', '')]) }}'" 
                        class="bg-purple-600 text-white px-4 sm:px-6 py-2 sm:py-2.5 rounded-lg font-semibold hover:bg-purple-700 transition sm:self-start w-fit sm:w-auto">
                        Kembali
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div id="pajakModal" class="fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center hidden z-50">
        <div class="relative bg-white p-2 sm:p-4 rounded-lg shadow-lg w-full max-w-xs sm:max-w-lg md:max-w-2xl lg:max-w-3xl mx-4">
            <button onclick="hideModal('pajakModal')" class="absolute top-1.5 right-3 bg-white text-black text-sm w-7 h-7 flex items-center justify-center rounded-full shadow-md hover:bg-gray-300 border border-black z-10">
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
                filePreviewContainer.innerHTML = `<iframe src="${filePath}" class="w-full h-[70vh] sm:h-[80vh]" frameborder="0"></iframe>`;
            } else {
                filePreviewContainer.innerHTML = `<img src="${filePath}" alt="File Pajak" class="max-h-[70vh] sm:max-h-[80vh] max-w-full object-contain rounded-lg">`;
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
            hideModal('pajakModal');
        });
    </script>
</x-app-layout>