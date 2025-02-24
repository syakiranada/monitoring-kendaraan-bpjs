<x-app-layout>
{{-- @extends('layouts.sidebar')
@section('content') --}}
    <style>
        #imageModal {
            z-index: 40;
        }
        #closeModal {
            z-index: 50;
        }
    </style>
    
    <h1 class="text-2xl font-bold mb-6">Detail Pajak</h1>
        <div class="block max-w-sm p-6 bg-white border border-gray-200 rounded-lg shadow-sm hover:bg-gray-100">
                    @php 
                    $currentPage = request()->query('page');
                    @endphp 
                    <input type="hidden" name="current_page" value="{{ $currentPage }}">        
            <div class="space-y-3">
                <div class="flex justify-between items-center">
                    <h5 class="text-xl font-bold text-gray-900">
                        {{ $pajak->kendaraan->merk }} {{ $pajak->kendaraan->tipe }}
                    </h5>
                </div>
                
                <div class="space-y-2">
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">Plat Nomor</span>
                        <span class="text-gray-900">{{ $pajak->kendaraan->plat_nomor }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">Tanggal Jatuh Tempo Pembayaran Pajak</span>
                        <span class="text-gray-900">{{ \Carbon\Carbon::parse($pajak->tgl_jatuh_tempo)->format('d-m-Y') }}</span>
                    </div>
                    
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">Tanggal Pembayaran Pajak</span>
                        <span class="text-gray-900">{{ \Carbon\Carbon::parse($pajak->tgl_bayar)->format('d-m-Y') }}</span>
                    </div>
                    
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">Tanggal Pembayaran Pajak Selanjutnya</span>
                        <span class="text-gray-900">{{ \Carbon\Carbon::parse($pajak->tgl_jatuh_tempo_tahun_depan)->format('d-m-Y') }}</span>
                    </div>
                    
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">Nominal Tagihan</span>
                        <span class="text-gray-900">Rp {{ number_format($pajak->nominal, 0, ',', '.') }}</span>
                    </div>
                    
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">Biaya Lain-lain</span>
                        <span class="text-gray-900">
                            {{ $pajak->biaya_pajak_lain ? 'Rp ' . number_format($pajak->biaya_pajak_lain, 0, ',', '.') : '-' }}
                        </span>
                    </div>
                    
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">Bukti Pembayaran</span>
                    
                        @if($pajak->bukti_bayar_pajak)
                            <button id="lihatBukti" class="text-blue-600 hover:underline">
                                Lihat bukti
                            </button>
                        @else
                            <span class="text-gray-400">Tidak ada bukti</span>
                        @endif
                    </div>
                    <div id="imageModal" class="fixed inset-0 bg-black bg-opacity-50 justify-center items-center hidden z-40">
                        <div class="relative bg-white p-4 rounded-lg shadow-lg w-auto max-w-3xl">
                            <button id="closeModal" class="absolute top-1.5 right-3 bg-white text-black text-sm w-7 h-7 flex items-center justify-center rounded-full shadow-md hover:bg-gray-300 z-50 border border-black">
                                <span style="color: black !important;"><svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg></span>
                            </button>
                            <div id="filePreviewContainer" class="max-h-[80vh] overflow-auto">
                            </div>
                        </div>
                    </div>
                    <button type="button" onclick="window.location.href='{{ route('pajak.daftar_kendaraan_pajak', ['page' => $currentPage, 'search' => request()->query('search')]) }}'" class="bg-purple-600 text-white px-6 py-2.5 rounded-lg font-semibold hover:bg-purple-700 transition">
                        Kembali
                    </button>  
                </div>
            </div>
        </div>
    @if($pajak->bukti_bayar_pajak)
    <div id="imageModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
        <div class="relative bg-white p-4 rounded-lg max-w-2xl mx-4">
            <button id="closeModal" class="absolute top-2 right-2 text-gray-500 hover:text-gray-700">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
            
            <div id="filePreviewContainer">
            </div>
        </div>
    </div>
@endif

    <script>
         document.addEventListener("DOMContentLoaded", function() {
        const lihatBukti = document.getElementById("lihatBukti");
        const imageModal = document.getElementById("imageModal");
        const closeModal = document.getElementById("closeModal");
        const filePreviewContainer = document.getElementById("filePreviewContainer");

        if (lihatBukti && imageModal) {
            lihatBukti.addEventListener("click", function(e) {
                e.preventDefault();
                let filePath = "{{ asset('storage/' . trim($pajak->bukti_bayar_pajak)) }}";
                let fileExtension = filePath.split('.').pop().toLowerCase();

                filePreviewContainer.innerHTML = ""; 

                if (fileExtension === "pdf") {
                    filePreviewContainer.innerHTML = `
                        <iframe src="${filePath}" class="w-full h-[80vh]" frameborder="0"></iframe>
                    `;
                } else {
                    filePreviewContainer.innerHTML = `
                        <img src="${filePath}" alt="Bukti Pembayaran" class="max-h-[80vh] max-w-full object-contain rounded-lg">
                    `;
                }

                imageModal.classList.remove('hidden');
                imageModal.classList.add('flex');
                document.body.style.overflow = 'hidden';
            });

            closeModal?.addEventListener("click", function() {
                imageModal.classList.add('hidden');
                imageModal.classList.remove('flex');
                document.body.style.overflow = '';
            });

            imageModal.addEventListener("click", function(e) {
                if (e.target === imageModal) {
                    imageModal.classList.add('hidden');
                    imageModal.classList.remove('flex');
                    document.body.style.overflow = '';
                }
            });

            document.addEventListener("keydown", function(e) {
                if (e.key === "Escape" && !imageModal.classList.contains('hidden')) {
                    imageModal.classList.add('hidden');
                    imageModal.classList.remove('flex');
                    document.body.style.overflow = '';
                }
            });
        }
    });
    </script>
</x-app-layout>
{{-- @endsection --}}