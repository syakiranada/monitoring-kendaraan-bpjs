{{-- <x-app-layout> --}}
    @extends('layouts.sidebar')

@section('content')

    <div class="flex justify-center items-center min-h-screen bg-gray-50">
        <div class="w-full max-w-md p-6 bg-white border border-gray-200 rounded-lg shadow-sm">
            @php 
                $currentPage = request()->query('page');
                @endphp 
                <input type="hidden" name="current_page" value="{{ $currentPage }}">        
            <!-- Header -->
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-lg font-semibold text-gray-900">
                    {{ $asuransi->kendaraan->merk }} {{ $asuransi->kendaraan->tipe }}
                </h2>
            </div>

            <!-- Insurance Details -->
            <div class="space-y-3">
                <div class="flex justify-between text-sm">
                    <span class="text-gray-600">Plat Nomor</span>
                    <span class="text-gray-900">{{ $asuransi->kendaraan->plat_nomor }}</span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-gray-600">Masa Perlindungan Awal</span>
                    <span class="text-gray-900">{{ \Carbon\Carbon::parse($asuransi->tgl_perlindungan_awal)->format('d/m/Y') }}</span>
                </div>

                <div class="flex justify-between text-sm">
                    <span class="text-gray-600">Masa Perlindungan akhir</span>
                    <span class="text-gray-900">{{ \Carbon\Carbon::parse($asuransi->tgl_perlindungan_akhir)->format('d/m/Y') }}</span>
                </div>

                <div class="flex justify-between text-sm">
                    <span class="text-gray-600">Jatuh Tempo Pembayaran Asuransi</span>
                    <span class="text-gray-900">{{ \Carbon\Carbon::parse($asuransi->tgl_jatuh_tempo)->format('d/m/Y') }}</span>
                </div>

                <div class="flex justify-between text-sm">
                    <span class="text-gray-600">Tanggal Pembayaran Asuransi</span>
                    <span class="text-gray-900">{{ \Carbon\Carbon::parse($asuransi->tgl_bayar)->format('d/m/Y') }}</span>
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
                    <span class="text-gray-900">{{ \Carbon\Carbon::parse($asuransi->tgl_perlindungan_akhir)->format('d/m/Y') }}</span>
                </div>

                <div class="flex justify-between text-sm">
                    <span class="text-gray-600">Bukti Polis Asuransi</span>
                    @if($asuransi->polis)
                        <a href="#" class="text-blue-600 hover:underline" onclick="showModal('polisModal')">Lihat bukti</a>
                    @else
                        <span class="text-gray-400">Tidak ada bukti</span>
                    @endif
                </div>

                <div class="flex justify-between text-sm">
                    <span class="text-gray-600">Bukti Pembayaran</span>
                    @if($asuransi->bukti_bayar_asuransi)
                        <a href="#" class="text-blue-600 hover:underline" onclick="showModal('pembayaranModal')">Lihat bukti</a>
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

    <!-- Modal Bukti Polis -->
    <div id="polisModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
        <div class="relative bg-white p-4 rounded-lg max-w-2xl mx-4">
            <button onclick="hideModal('polisModal')" class="absolute top-2 right-2 text-gray-500 hover:text-gray-700">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
            <img src="{{ asset('storage/' . $asuransi->polis) }}" 
                 alt="Bukti Polis" 
                 class="max-h-[80vh] max-w-full object-contain rounded-lg">
        </div>
    </div>

    <!-- Modal Bukti Pembayaran -->
    <div id="pembayaranModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
        <div class="relative bg-white p-4 rounded-lg max-w-2xl mx-4">
            <button onclick="hideModal('pembayaranModal')" class="absolute top-2 right-2 text-gray-500 hover:text-gray-700">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
            <img src="{{ asset('storage/' . $asuransi->bukti_bayar_asuransi) }}" 
                 alt="Bukti Pembayaran" 
                 class="max-h-[80vh] max-w-full object-contain rounded-lg">
        </div>
    </div> 

    <script>
        function showModal(modalId) {
            const modal = document.getElementById(modalId);
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

        // Close modal when clicking outside
        document.querySelectorAll('.fixed').forEach(modal => {
            modal.addEventListener('click', (e) => {
                if (e.target === modal) {
                    hideModal(modal.id);
                }
            });
        });

        // Close modal with ESC key
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
{{-- </x-app-layout> --}}
@endsection