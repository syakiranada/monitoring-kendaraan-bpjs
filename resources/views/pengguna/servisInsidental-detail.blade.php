<x-app-layout>
    <div class="flex-1 p-10">
        <h1 class="text-2xl font-bold mb-6">Detail Servis Insidental Kendaraan</h1>
        <div class="bg-white p-6 rounded-lg shadow-md w-full md:w-2/3 lg:w-1/2">
            <h2 class="text-lg font-semibold mb-4">{{ $servis->kendaraan->merk }} {{ $servis->kendaraan->tipe }} - {{ $servis->kendaraan->plat_nomor }}</h2>
            <div class="space-y-2">
                <div class="grid" style="grid-template-columns: 30% 70%;">
                    <span class="font-semibold text-gray-700">Diinput Oleh</span>
                    <span>{{ $servis->user->name }}</span>
                </div>
                {{--  <div class="grid" style="grid-template-columns: 30% 70%;">
                    <span class="font-semibold text-gray-700">Plat Nomor</span>
                    <span>{{ $servis->kendaraan->plat_nomor }}</span>
                </div>  --}}
                <div class="grid" style="grid-template-columns: 30% 70%;">
                    <span class="font-semibold text-gray-700">Tanggal Servis</span>
                    <span>{{ $servis->tgl_servis ? \Carbon\Carbon::parse($servis->tgl_servis)->format('d-m-Y') : '-' }}</span>
                </div>
                <div class="grid" style="grid-template-columns: 30% 70%;">
                    <span class="font-semibold text-gray-700">Lokasi Servis</span>
                    <span class="text-left break-words overflow-hidden">{{ $servis->lokasi ?? '-' }}</span>
                </div>
                <div class="grid" style="grid-template-columns: 30% 70%;">
                    <span class="font-semibold text-gray-700">Deskripsi Servis</span>
                    <span class="text-left break-words overflow-hidden">{{ $servis->deskripsi ?? 'Tidak ada deskripsi' }}</span>
                </div>
                <div class="grid" style="grid-template-columns: 30% 70%;">
                    <span class="font-semibold text-gray-700">Jumlah Pembayaran</span>
                    <span>Rp{{ number_format($servis->harga, 0, ',', '.') }}</span>
                </div>
                <div class="grid" style="grid-template-columns: 30% 70%;">
                    <span class="font-semibold text-gray-700">Kondisi Kendaraan</span>
                    @if($servis->bukti_fisik)
                        <a href="{{ asset('storage/' . $servis->bukti_fisik) }}" target="_blank" class="text-blue-500 hover:underline">Lihat kondisi</a>
                    @else
                        <span class="text-gray-500">Tidak ada bukti</span>
                    @endif
                </div>
                <div class="grid" style="grid-template-columns: 30% 70%;">
                    <span class="font-semibold text-gray-700">Bukti Pembayaran</span>
                    @if($servis->bukti_bayar)
                        <a href="{{ asset('storage/' . $servis->bukti_bayar) }}" target="_blank" class="text-blue-500 hover:underline">Lihat bukti</a>
                    @else
                        <span class="text-gray-500">Tidak ada bukti</span>
                    @endif
                </div>
        </div>
        <div class="mt-6 flex justify-between">
            <a href="{{ url()->previous() }}" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-700 shadow-md" style="background-color: #3b82f6;">
                Kembali
            </a>
            {{--  @if($servis->peminjaman->status_pinjam == 'Disetujui')
                <a href="{{ route('servisInsidental.edit', ['id' => $servis->id_servis_insidental]) }}" class="text-white px-4 py-2 rounded hover:bg-yellow-600 shadow-md" style="background-color: #f59e0b;">
                    Edit
                </a> 
            @endif  --}}
        </div>   
            
    </div>
</div>
</x-app-layout>
