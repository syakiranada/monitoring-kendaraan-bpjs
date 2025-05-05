<x-app-layout>
    <style>
        @media (max-width: 640px) {
            .detail-container {
                padding: 1rem;
            }
            .detail-item {
                margin-bottom: 0.75rem;
            }
            .detail-label {
                margin-bottom: 0.25rem;
                font-weight: 500;
            }
        }
    </style>

    <div class="container px-4 py-6 w-fit">
        <!-- Button Back -->
        <a href="{{ route('admin.riwayat.servis-insidental', ['page' => request('page'), 'search' => request('search')]) }}" class="flex items-center text-blue-600 font-semibold hover:underline mb-5">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"></path>
            </svg>
            Kembali
        </a>

        <h1 class="text-2xl font-bold mb-6">Detail Servis Insidental Kendaraan</h1>

        <div class="w-full max-w-md sm:max-w-lg md:max-w-xl lg:max-w-2xl xl:max-w-3xl p-4 sm:p-6 bg-white border border-gray-200 rounded-lg shadow-sm mx-auto detail-container">
            <input type="hidden" name="page" value="{{ request()->query('page', 1) }}">
            <input type="hidden" name="search" value="{{ request()->query('search') }}">

            <div class="flex justify-between items-center mb-4">
                <h2 class="text-lg font-semibold text-gray-900">
                    {{ $servis->kendaraan->merk }} {{ $servis->kendaraan->tipe }} - {{ $servis->kendaraan->plat_nomor }}
                </h2>
            </div>

            <div class="space-y-3 text-sm">
                @php
                    $fields = [
                        'Diinput Oleh' => $servis->user->name,
                        'Tanggal Servis' => $servis->tgl_servis ? \Carbon\Carbon::parse($servis->tgl_servis)->format('d-m-Y') : '-',
                        'Lokasi Servis' => $servis->lokasi ?? '-',
                        'Deskripsi Servis' => $servis->deskripsi ?? 'Tidak ada deskripsi',
                        'Jumlah Pembayaran' => 'Rp ' . number_format($servis->harga, 0, ',', '.'),
                        'Kondisi Kendaraan' => $servis->bukti_fisik ? '<a href="' . asset('storage/' . $servis->bukti_fisik) . '" target="_blank" class="text-blue-500 hover:underline">Lihat kondisi</a>' : '<span class="text-gray-500">Tidak ada bukti</span>',
                        'Bukti Pembayaran' => $servis->bukti_bayar ? '<a href="' . asset('storage/' . $servis->bukti_bayar) . '" target="_blank" class="text-blue-500 hover:underline">Lihat bukti</a>' : '<span class="text-gray-500">Tidak ada bukti</span>',
                    ];
                @endphp

                @foreach ($fields as $label => $value)
                    <div class="flex flex-col sm:flex-row items-start detail-item w-full">
                        <span class="text-gray-700 sm:w-56 detail-label font-semibold">{{ $label }}</span>
                        <span class="text-gray-900 break-words sm:max-w-[calc(100%-14rem)]">{!! $value !!}</span>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</x-app-layout>
