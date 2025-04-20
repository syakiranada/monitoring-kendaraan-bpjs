<x-app-layout>
    <div class="flex-1 p-10">
        <h1 class="text-2xl font-bold mb-6">Detail Pengisian BBM Kendaraan</h1>
        <input type="hidden" name="page" value="{{ request()->query('page', 1) }}">
        <input type="hidden" name="search" value="{{ request()->query('search') }}">

        <div class="bg-white p-6 rounded-lg shadow-md w-full md:w-2/3 lg:w-1/3">
            <h2 class="text-lg font-semibold mb-4">{{ $bbm->kendaraan->merk }} {{ $bbm->kendaraan->tipe }}</h2>
            <div class="space-y-4">
                <div class="flex justify-between">
                    <span class="font-medium text-gray-600">User Input</span>
                    <span>{{ $bbm->user->name }}</span>
                </div>
                <div class="flex justify-between">
                    <span>Plat Nomor</span>
                    <span>{{ $bbm->kendaraan->plat_nomor }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="font-medium text-gray-600">Tanggal Pengisian BBM</span>
                    <span>{{ $bbm->tgl_isi ? \Carbon\Carbon::parse($bbm->tgl_isi)->format('d-m-Y') : '-' }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="font-medium text-gray-600">Jenis BBM</span>
                    <span>{{ $bbm->jenis_bbm }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="font-medium text-gray-600">Jumlah Pembayaran</span>
                    <span>Rp {{ number_format($bbm->nominal, 0, ',', '.') }}</span>
                </div>
            </div>
            
            {{-- <div class="mt-6 flex justify-between">
                <a href="{{ url()->previous() }}" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-700 shadow-md" style="background-color: #3b82f6;">
                    Kembali
                </a>
            </div>     --}}
            <button type="button" onclick="window.location.href='{{ route('admin.riwayat.pengisian-bbm', ['page' => request('page'), 'search' => request('search')]) }}'" class="bg-purple-600 text-white px-6 py-2.5 mt-6 rounded-lg font-semibold hover:bg-purple-700 transition">
                Kembali
            </button>       
        </div>
    </div>
</x-app-layout>
