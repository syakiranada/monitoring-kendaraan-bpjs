{{-- <x-app-layout> --}}
    @extends('layouts.sidebar')

    @section('content')
    
    <div class="flex justify-center items-center min-h-screen">
        <div class="block max-w-sm p-6 bg-white border border-gray-200 rounded-lg shadow-sm
                    dark:bg-gray-800 dark:border-gray-700 dark:hover:bg-gray-700">
                    @php 
                    $currentPage = request()->query('page');
                    @endphp 
                    <input type="hidden" name="current_page" value="{{ $currentPage }}">        
            <div class="space-y-3">
                <div class="flex justify-between items-center">
                    <h5 class="text-xl font-bold text-gray-900 dark:text-white">
                        {{ $pajak->kendaraan->merk }} {{ $pajak->kendaraan->tipe }}
                    </h5>
                </div>
                
                <div class="space-y-2">
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">Plat Nomor</span>
                        <span class="text-gray-900">{{ $pajak->kendaraan->plat_nomor }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600 dark:text-gray-400">Tanggal Jatuh Tempo Pembayaran Pajak</span>
                        <span class="text-gray-900 dark:text-white">{{ \Carbon\Carbon::parse($pajak->tgl_jatuh_tempo)->format('d/m/Y') }}</span>
                    </div>
                    
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600 dark:text-gray-400">Tanggal Pembayaran Pajak</span>
                        <span class="text-gray-900 dark:text-white">{{ \Carbon\Carbon::parse($pajak->tgl_bayar)->format('d/m/Y') }}</span>
                    </div>
                    
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600 dark:text-gray-400">Tanggal Pembayaran Pajak Selanjutnya</span>
                        <span class="text-gray-900 dark:text-white">{{ \Carbon\Carbon::parse($pajak->tgl_jatuh_tempo_tahun_depan)->format('d/m/Y') }}</span>
                    </div>
                    
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600 dark:text-gray-400">Nominal Tagihan</span>
                        <span class="text-gray-900 dark:text-white">Rp {{ number_format($pajak->nominal, 0, ',', '.') }}</span>
                    </div>
                    
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600 dark:text-gray-400">Biaya Lain-lain</span>
                        <span class="text-gray-900 dark:text-white">
                            {{ $pajak->biaya_pajak_lain ? 'Rp ' . number_format($pajak->biaya_pajak_lain, 0, ',', '.') : '-' }}
                        </span>
                    </div>
                    
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600 dark:text-gray-400">Bukti Pembayaran</span>
                    
                        @if($pajak->bukti_bayar_pajak)
                            <button id="lihatBukti" class="text-blue-600 hover:underline dark:text-blue-400">
                                Lihat bukti
                            </button>
                        @else
                            <span class="text-gray-400">Tidak ada bukti</span>
                        @endif
                    </div>
                    <button type="button" onclick="window.location.href='{{ route('admin.riwayat.pajak', ['page' => $currentPage]) }}'" class="bg-purple-600 text-white px-6 py-2.5 rounded-lg font-semibold hover:bg-purple-700 transition">
                        Kembali
                    </button>  
                </div>
            </div>
        </div>
    </div>

    {{-- Modal untuk bukti pembayaran --}}
    @if($pajak->bukti_bayar_pajak)
    <div id="imageModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
        <div class="relative bg-white p-4 rounded-lg max-w-2xl mx-4">
            <button id="closeModal" class="absolute top-2 right-2 text-gray-500 hover:text-gray-700">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
            <img src="{{ asset('storage/' . trim($pajak->bukti_bayar_pajak)) }}" 
                 alt="Bukti Pembayaran" 
                 class="max-h-[80vh] max-w-full object-contain rounded-lg">
        </div>
    </div>
    @endif

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const lihatBukti = document.getElementById("lihatBukti");
            const imageModal = document.getElementById("imageModal");
            const closeModal = document.getElementById("closeModal");

            if (lihatBukti && imageModal) {
                // Buka modal
                lihatBukti.addEventListener("click", function(e) {
                    e.preventDefault();
                    imageModal.classList.remove('hidden');
                    imageModal.classList.add('flex');
                    document.body.style.overflow = 'hidden';
                });

                // Tutup modal dengan tombol close
                closeModal?.addEventListener("click", function() {
                    imageModal.classList.add('hidden');
                    imageModal.classList.remove('flex');
                    document.body.style.overflow = '';
                });

                // Tutup modal dengan klik di luar gambar
                imageModal.addEventListener("click", function(e) {
                    if (e.target === imageModal) {
                        imageModal.classList.add('hidden');
                        imageModal.classList.remove('flex');
                        document.body.style.overflow = '';
                    }
                });

                // Tutup modal dengan tombol ESC
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
{{-- </x-app-layout> --}}
@endsection