<x-app-layout>
    <h1 class="text-2xl font-bold mb-6">Detail Cek Fisik Kendaraan</h1>
    <div class="block max-w-md p-6 bg-white border border-gray-200 rounded-lg shadow-sm hover:bg-gray-100 
                dark:bg-gray-800 dark:border-gray-700 dark:hover:bg-gray-700">
        {{-- @php 
            $currentPage = request()->query('page');
        @endphp 
        <input type="hidden" name="current_page" value="{{ $currentPage }}">   --}}
        <input type="hidden" name="page" value="{{ request()->query('page', 1) }}">
        <input type="hidden" name="search" value="{{ request()->query('search') }}">

        <div class="space-y-3">
            <div class="flex justify-between items-center">
                <h5 class="text-xl font-bold text-gray-900 dark:text-white">
                    {{ $cekFisik->kendaraan->merk }} {{ $cekFisik->kendaraan->tipe }} - {{ $cekFisik->kendaraan->plat_nomor }}
                </h5>
            </div>

            <div class="space-y-2">
                <div class="flex justify-between text-sm">
                    <span class="text-gray-600">Tanggal Cek Fisik</span>
                    <span class="text-gray-900">{{ $cekFisik->tgl_cek_fisik }}</span>
                </div>

                @foreach(['mesin', 'accu', 'air_radiator', 'air_wiper', 'body', 'ban', 'pengharum', 'kondisi_keseluruhan'] as $field)
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600 dark:text-gray-400">{{ ucfirst(str_replace('_', ' ', $field)) }}</span>
                        <span class="text-gray-900 dark:text-white">{{ $cekFisik->$field }}</span>
                    </div>
                @endforeach

                <div class="flex justify-between text-sm">
                    <span class="text-gray-600 dark:text-gray-400">Catatan</span>
                    <span class="text-gray-900 dark:text-white">{{ $cekFisik->catatan }}</span>
                </div>
            </div>

            {{-- <button type="button" onclick="window.location.href='{{ route('admin.cek-fisik.index', ['page' => request('page'), 'search' => request('search')]) }}'" 
                    class="bg-purple-600 text-white px-6 py-2.5 rounded-lg font-semibold hover:bg-purple-700 transition">
                Kembali
            </button> --}} 
            <button type="button" onclick="window.location.href='{{ route('admin.riwayat.cek-fisik', ['page' => request('page'), 'search' => request('search')]) }}'" class="bg-purple-600 text-white px-6 py-2.5 rounded-lg font-semibold hover:bg-purple-700 transition">
                Kembali
            </button>  
        </div>
    </div>
</x-app-layout>
