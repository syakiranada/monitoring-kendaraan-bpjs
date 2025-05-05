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
        <a href="{{ route('admin.riwayat.pengisian-bbm', ['page' => request('page'), 'search' => request('search')]) }}" class="flex items-center text-blue-600 font-semibold hover:underline mb-5">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"></path>
            </svg>
            Kembali
        </a>

        <h1 class="text-2xl font-bold mb-6">Detail Pengisian BBM Kendaraan</h1>
        <div class="w-full max-w-md sm:max-w-lg md:max-w-xl lg:max-w-2xl xl:max-w-3xl p-4 sm:p-6 bg-white border border-gray-200 rounded-lg shadow-sm mx-auto detail-container">
            <input type="hidden" name="page" value="{{ request()->query('page', 1) }}">
            <input type="hidden" name="search" value="{{ request()->query('search') }}">

            <div class="flex justify-between items-center mb-4">
                <h2 class="text-lg font-semibold text-gray-900">
                    {{ $bbm->kendaraan->merk }} {{ $bbm->kendaraan->tipe }} - {{ $bbm->kendaraan->plat_nomor }}
                </h2>
            </div>

            <div class="space-y-3">
                @php
                    $fields = [
                        'Diinput Oleh' => $bbm->user->name ?? '-',
                        'Tanggal Pengisian BBM' => $bbm->tgl_isi ? \Carbon\Carbon::parse($bbm->tgl_isi)->format('d-m-Y') : '-',
                        'Jenis BBM' => $bbm->jenis_bbm ?? '-',
                        'Jumlah Pembayaran' => 'Rp ' . number_format($bbm->nominal, 0, ',', '.'),
                    ];
                @endphp

                @foreach ($fields as $label => $value)
                    <div class="flex flex-col sm:flex-row items-start text-sm detail-item w-full">
                        <span class="text-gray-600 sm:w-56 detail-label font-semibold">{{ $label }}</span>
                        <span class="text-gray-900 break-words sm:max-w-[calc(100%-14rem)]">{{ $value }}</span>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</x-app-layout>
