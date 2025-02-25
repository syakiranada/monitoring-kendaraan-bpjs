<x-app-layout>
<div class="flex-1 p-10">
    <h1 class="text-2xl font-bold mb-6">Detail Servis Rutin Kendaraan</h1>
    <div class="bg-white p-6 rounded-lg shadow-md w-1/3">
        <h2 class="text-lg font-semibold mb-4">{{ $servis->kendaraan->merk }} {{ $servis->kendaraan->tipe }}</h2>
        <div class="space-y-2">
            <div class="flex justify-between">
                <span>Plat Nomor</span>
                <span>{{ $servis->kendaraan->plat_nomor }}</span>
            </div>
            <div class="flex justify-between">
                <span>Jadwal Servis</span>
                <span>{{ \Carbon\Carbon::parse($servis->tgl_servis_real)->format('d-m-Y') }}</span>
            </div>
            <div class="flex justify-between">
                <span>Tgl Servis Selanjutnya</span>
                <span>{{ \Carbon\Carbon::parse($servis->tgl_servis_selanjutnya)->format('d-m-Y') }}</span>
            </div>
            <div class="flex justify-between">
                <span>Kilometer Penggunaan</span>
                <span>{{ number_format($servis->kilometer, 0, ',', '.') }} km</span>
            </div>
            <div class="flex justify-between">
                <span>Jumlah Pembayaran</span>
                <span>Rp {{ number_format($servis->harga, 0, ',', '.') }}</span>
            </div>
            <div class="flex justify-between">
                <span>Lokasi Servis</span>
                <span>{{ $servis->lokasi }}</span>
            </div>
            <div class="flex justify-between">
                <span>Bukti Pembayaran</span>
                @if($servis->bukti_bayar)
                    <a href="{{ asset('storage/' . $servis->bukti_bayar) }}" target="_blank" class="text-blue-500">Lihat bukti</a>
                @else
                    <span class="text-gray-500">Tidak ada bukti</span>
                @endif
            </div>
        </div>
        <div class="mt-6">
            <a href="{{ url()->previous() }}" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-700 shadow-md" style="background-color: #3b82f6;">
                Kembali
            </a>
        </div>  
    </div>
</div>
</x-app-layout>
