<x-app-layout>
    <div class="flex-1 p-10">
        <h1 class="text-2xl font-bold mb-6">Detail Servis Rutin Kendaraan</h1>
        <div class="bg-white p-6 rounded-lg shadow-md w-1/3">
            <input type="hidden" name="page" value="{{ request()->query('page', 1) }}">
            <input type="hidden" name="search" value="{{ request()->query('search') }}">
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
                <button type="button" onclick="window.location.href='{{ route('admin.riwayat.servis-rutin', ['page' => request('page'), 'search' => request('search')]) }}'" class="bg-purple-600 text-white px-6 py-2.5 rounded-lg font-semibold hover:bg-purple-700 transition">
                    Kembali
                </button>   
            </div>
        </div>
    </div>
</x-app-layout>
    