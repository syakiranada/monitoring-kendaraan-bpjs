<x-app-layout>
    <a href="{{ route('admin.servisRutin') }}" class="flex items-center text-blue-600 font-semibold hover:underline mb-5">
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"></path>
        </svg>
        Kembali
    </a>
    <div class="flex-1 px-4">
        <h1 class="text-2xl font-bold mb-6">Detail Servis Rutin Kendaraan</h1>
        <div class="bg-white p-6 rounded-lg shadow-md w-full md:w-2/3 lg:w-1/2">
            <h2 class="text-lg font-semibold mb-4">{{ $servis->kendaraan->merk }} {{ $servis->kendaraan->tipe }} - {{ $servis->kendaraan->plat_nomor }}</h2>
            <div class="space-y-3">
                <div class="grid text-sm" style="grid-template-columns: 30% 70%;">
                    <span class="font-semibold text-gray-700">Diinput Oleh</span>
                    <span class="text-left">{{ $servis->user->name }}</span>
                </div>
                <div class="grid text-sm" style="grid-template-columns: 30% 70%;">
                    <span class="font-semibold text-gray-700">Jadwal Servis</span>
                    <span class="text-left">{{ \Carbon\Carbon::parse($servis->tgl_servis_real)->format('d-m-Y') }}</span>
                </div>
                <div class="grid text-sm" style="grid-template-columns: 30% 70%;">
                    <span class="font-semibold text-gray-700">Tgl Servis Selanjutnya</span>
                    <span class="text-left">{{ \Carbon\Carbon::parse($servis->tgl_servis_selanjutnya)->format('d-m-Y') }}</span>
                </div>
                <div class="grid text-sm" style="grid-template-columns: 30% 70%;">
                    <span class="font-semibold text-gray-700">Kilometer Penggunaan</span>
                    <span class="text-left">{{ number_format($servis->kilometer, 0, ',', '.') }} km</span>
                </div>
                <div class="grid text-sm" style="grid-template-columns: 30% 70%;">
                    <span class="font-semibold text-gray-700">Jumlah Pembayaran</span>
                    <span class="text-left">Rp{{ number_format($servis->harga, 0, ',', '.') }}</span>
                </div>
                <div class="grid text-sm" style="grid-template-columns: 30% 70%;">
                    <span class="font-semibold text-gray-700">Lokasi Servis</span>
                    <span class="text-left break-words overflow-hidden">{{ $servis->lokasi }}</span>
                </div>
                <div class="grid text-sm" style="grid-template-columns: 30% 70%;">
                    <span class="font-semibold text-gray-700">Bukti Pembayaran</span>
                    @if($servis->bukti_bayar)
                        <a href="{{ asset('storage/' . $servis->bukti_bayar) }}" target="_blank" class="text-left text-blue-500">Lihat bukti</a>
                    @else
                        <span class="text-left text-gray-500">Tidak ada bukti</span>
                    @endif
                </div>
            </div>
            </div>  
        </div>
    </div>
    </x-app-layout>
    