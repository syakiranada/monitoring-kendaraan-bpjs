<x-app-layout>
<div class="flex-1 p-10">
    <h1 class="text-2xl font-bold mb-6">Detail Servis Insidental Kendaraan</h1>
    <div class="bg-white p-6 rounded-lg shadow-md w-full md:w-2/3 lg:w-1/3">
        <h2 class="text-lg font-semibold mb-4">{{ $servis->kendaraan->merk }} {{ $servis->kendaraan->tipe }}</h2>
        <div class="space-y-4">
            <div class="flex justify-between">
                <span>Plat Nomor</span>
                <span>{{ $servis->kendaraan->plat_nomor }}</span>
            </div>
            <div class="flex justify-between">
                <span class="font-medium text-gray-600">Tanggal Servis</span>
                <span>{{ $servis->tgl_servis ? \Carbon\Carbon::parse($servis->tgl_servis)->format('d-m-Y') : '-' }}</span>
            </div>
            <div class="flex justify-between">
                <span class="font-medium text-gray-600">Lokasi Servis</span>
                <span>{{ $servis->lokasi ?? '-' }}</span>
            </div>
            <div class="flex justify-between">
                <span class="font-medium text-gray-600">Deskripsi Servis</span>
                <span>{{ $servis->deskripsi ?? 'Tidak ada deskripsi' }}</span>
            </div>
            <div class="flex justify-between">
                <span class="font-medium text-gray-600">Jumlah Pembayaran</span>
                <span>Rp {{ number_format($servis->harga, 0, ',', '.') }}</span>
            </div>
            <div class="flex justify-between">
                <span class="font-medium text-gray-600">Kondisi Kendaraan</span>
                @if($servis->bukti_fisik)
                    <a href="{{ asset('storage/' . $servis->bukti_fisik) }}" target="_blank" class="text-blue-500 hover:underline">Lihat kondisi</a>
                @else
                    <span class="text-gray-500">Tidak ada bukti</span>
                @endif
            </div>
            <div class="flex justify-between">
                <span class="font-medium text-gray-600">Bukti Pembayaran</span>
                @if($servis->bukti_bayar)
                    <a href="{{ asset('storage/' . $servis->bukti_bayar) }}" target="_blank" class="text-blue-500 hover:underline">Lihat bukti</a>
                @else
                    <span class="text-gray-500">Tidak ada bukti</span>
                @endif
            </div>
        </div>
    </div>
</div>
</x-app-layout>
