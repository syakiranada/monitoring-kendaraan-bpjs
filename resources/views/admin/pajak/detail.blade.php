<x-app-layout>
    <style>
        #pajakModal {
            display: none;
        }
        #pajakModal.flex {
            display: flex !important;
        }
        
        .detail-label {
            font-weight: 600 !important; 
        }
        
        @media (max-width: 640px) {
            .detail-container {
                padding: 1rem;
            }
            
            .detail-item {
                margin-bottom: 0.75rem;
            }
            
            .detail-label {
                margin-bottom: 0.25rem;
            }
        }
    </style>
    @php 
    $currentPage = request()->query('page');
@endphp 

<a href="{{ route('pajak.daftar_kendaraan_pajak', ['page' => $currentPage, 'search' => request()->query('search', '')]) }}"
    class="flex items-center text-blue-600 font-semibold hover:underline mb-5 ml-2"> 
     <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
         <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"></path>
     </svg>
     Kembali
 </a>
 
<div class="container px-4 w-fit"> 
<h2 class="text-2xl font-semibold text-gray-800 mb-2">Detail Pajak</h2>
        <div class="w-full mt-3 max-w-md sm:max-w-lg md:max-w-xl lg:max-w-2xl xl:max-w-3xl p-4 sm:p-6 bg-white border border-gray-200 rounded-lg shadow-sm mx-auto detail-container">
                <input type="hidden" name="current_page" value="{{ $currentPage }}">        
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-lg font-semibold text-gray-900">
                    {{ $pajak->kendaraan->merk }} {{ $pajak->kendaraan->tipe }} - {{ $pajak->kendaraan->plat_nomor }}
                </h2>
            </div>
            
            <div class="space-y-3">
                @php
                    $fields = [
                        'Diinput Oleh' => $pajak->user->name,
                        'Tanggal Jatuh Tempo Pembayaran Pajak' => \Carbon\Carbon::parse($pajak->tgl_jatuh_tempo)->format('d-m-Y'),
                        'Tanggal Pembayaran Pajak' => \Carbon\Carbon::parse($pajak->tgl_bayar)->format('d-m-Y'),
                        'Tanggal Pembayaran Pajak Selanjutnya' => \Carbon\Carbon::parse($pajak->tgl_jatuh_tempo_tahun_depan)->format('d-m-Y'),
                        'Nominal Tagihan' => 'Rp ' . number_format($pajak->nominal, 0, ',', '.'),
                        'Biaya Lain-lain' => $pajak->biaya_pajak_lain ? 'Rp ' . number_format($pajak->biaya_pajak_lain, 0, ',', '.') : '-'
                    ];
                @endphp
                
                @foreach ($fields as $label => $value)
                    <div class="flex flex-col sm:flex-row items-start text-sm detail-item">
                        <span class="font-semibold text-gray-700 sm:w-72 detail-label">{{ $label }}</span>
                        <span class="text-gray-700">{{ $value }}</span>
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
                    <span class="font-semibold text-gray-600 sm:w-72 detail-label">Bukti Pembayaran</span>
                
                    @if($pembayaranExists)
                        <a href="{{ asset('storage/' . $pembayaranPath) }}" target="_blank" class="text-blue-600 hover:underline">
                            Lihat bukti
                        </a>
                    @else
                        <span class="text-gray-400">Tidak ada bukti</span>
                    @endif
                </div>
              
            </div>
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
        
        document.querySelectorAll('.fixed').forEach(modal => {
            modal.addEventListener('click', e => { 
                if (e.target === modal) hideModal(modal.id); 
            });
        });
    
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