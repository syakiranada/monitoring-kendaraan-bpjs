<x-app-layout>
    <div class="flex-1 p-10">
        <h1 class="text-2xl font-bold mb-6">Detail Servis Rutin Kendaraan</h1>
        <div class="bg-white p-6 rounded-lg shadow-md w-full md:w-2/3 lg:w-1/2">
            <h2 class="text-lg font-semibold mb-4">{{ $servis->kendaraan->merk }} {{ $servis->kendaraan->tipe }} - {{ $servis->kendaraan->plat_nomor }}</h2>
            <div class="space-y-3">
                <div class="grid" style="grid-template-columns: 30% 70%;">
                    <span class="font-semibold text-gray-700">Diinput Oleh</span>
                    <span class="text-left">{{ $servis->user->name }}</span>
                </div>
                {{--  <div class="grid" style="grid-template-columns: 30% 70%;">
                    <span class="font-semibold text-gray-700">Plat Nomor</span>
                    <span class="text-left">{{ $servis->kendaraan->plat_nomor }}</span>
                </div>  --}}
                <div class="grid" style="grid-template-columns: 30% 70%;">
                    <span class="font-semibold text-gray-700">Jadwal Servis</span>
                    <span class="text-left">{{ \Carbon\Carbon::parse($servis->tgl_servis_real)->format('d-m-Y') }}</span>
                </div>
                <div class="grid" style="grid-template-columns: 30% 70%;">
                    <span class="font-semibold text-gray-700">Tgl Servis Selanjutnya</span>
                    <span class="text-left">{{ \Carbon\Carbon::parse($servis->tgl_servis_selanjutnya)->format('d-m-Y') }}</span>
                </div>
                <div class="grid" style="grid-template-columns: 30% 70%;">
                    <span class="font-semibold text-gray-700">Kilometer Penggunaan</span>
                    <span class="text-left">{{ number_format($servis->kilometer, 0, ',', '.') }} km</span>
                </div>
                <div class="grid" style="grid-template-columns: 30% 70%;">
                    <span class="font-semibold text-gray-700">Jumlah Pembayaran</span>
                    <span class="text-left">Rp{{ number_format($servis->harga, 0, ',', '.') }}</span>
                </div>
                <div class="grid" style="grid-template-columns: 30% 70%;">
                    <span class="font-semibold text-gray-700">Lokasi Servis</span>
                    <span class="text-left break-words overflow-hidden">{{ $servis->lokasi }}</span>
                </div>
                <div class="grid" style="grid-template-columns: 30% 70%;">
                    <span class="font-semibold text-gray-700">Bukti Pembayaran</span>
                    @if($servis->bukti_bayar)
                        <a href="{{ asset('storage/' . $servis->bukti_bayar) }}" target="_blank" class="text-left text-blue-500">Lihat bukti</a>
                    @else
                        <span class="text-left text-gray-500">Tidak ada bukti</span>
                    @endif
                </div>
            </div>
            <div class="mt-6 flex justify-between">
                <a href="{{ url()->previous() }}" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-700 shadow-md" style="background-color: #3b82f6;">
                    Kembali
                </a>
            </div>  
        </div>
    </div>
    </x-app-layout>
    