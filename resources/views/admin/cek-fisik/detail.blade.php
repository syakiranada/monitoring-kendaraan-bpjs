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
        <a href="{{  route('admin.cek-fisik.index', ['page' => request('page'), 'search' => request('search')])  }}" class="flex items-center text-blue-600 font-semibold hover:underline mb-5">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"></path>
            </svg>
            Kembali
        </a>
        <h1 class="text-2xl font-bold mb-6">Detail Cek Fisik Kendaraan</h1>
        <div class="w-full max-w-md sm:max-w-lg md:max-w-xl lg:max-w-2xl xl:max-w-3xl p-4 sm:p-6 bg-white border border-gray-200 rounded-lg shadow-sm mx-auto detail-container">
            <input type="hidden" name="page" value="{{ request()->query('page', 1) }}">
            <input type="hidden" name="search" value="{{ request()->query('search') }}">

            <div class="flex justify-between items-center mb-4">
                <h2 class="text-lg font-semibold text-gray-900">
                    {{ $cekFisik->kendaraan->merk }} {{ $cekFisik->kendaraan->tipe }} - {{ $cekFisik->kendaraan->plat_nomor }}
                </h2>
            </div>

            <div class="space-y-3">
                @php
                    $fields = [
                        'Diinput Oleh' => $cekFisik->user->name ?? '-',
                        'Tanggal Cek Fisik' => $cekFisik->tgl_cek_fisik ? \Carbon\Carbon::parse($cekFisik->tgl_cek_fisik)->format('d-m-Y') : '-',
                        'Mesin' => $cekFisik->mesin,
                        'Accu' => $cekFisik->accu,
                        'Air Radiator' => $cekFisik->air_radiator,
                        'Air Wiper' => $cekFisik->air_wiper,
                        'Body' => $cekFisik->body,
                        'Ban' => $cekFisik->ban,
                        'Pengharum' => $cekFisik->pengharum,
                        'Kondisi Keseluruhan' => $cekFisik->kondisi_keseluruhan,
                        'Catatan' => $cekFisik->catatan
                    ];
                @endphp

                {{-- @foreach ($fields as $label => $value)
                    <div class="flex flex-col sm:flex-row items-start text-sm detail-item">
                        <span class="text-gray-600 sm:w-56 detail-label">{{ $label }}</span>
                        <span class="text-gray-900">{{ $value }}</span>
                    </div>
                @endforeach --}}

                @foreach ($fields as $label => $value)
                    <div class="flex flex-col sm:flex-row items-start text-sm detail-item w-full">
                        <span class="text-gray-600 sm:w-56 detail-label">{{ $label }}</span>
                        <span class="text-gray-900 break-words sm:max-w-[calc(100%-14rem)]">{{ $value ?: '-' }}</span>
                    </div>
                @endforeach

                {{-- <div class="mt-6 pt-2">
                    <button type="button" onclick="window.location.href='{{ route('admin.cek-fisik.index', ['page' => request('page'), 'search' => request('search')]) }}'" 
                        class="bg-purple-600 text-white px-4 sm:px-6 py-2 sm:py-2.5 rounded-lg font-semibold hover:bg-purple-700 transition sm:self-start w-fit sm:w-auto">
                        Kembali
                    </button>
                </div> --}}
            </div>
        </div>
    </div>
</x-app-layout>
