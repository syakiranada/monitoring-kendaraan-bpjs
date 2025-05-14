<x-app-layout>
    <a href="{{ route('admin.pengisianBBM') }}" class="flex items-center text-blue-600 font-semibold hover:underline mb-5">
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"></path>
        </svg>
        Kembali
    </a>
    <div class="flex-1 px-4">
        <h1 class="text-2xl font-bold mb-6">Detail Pengisian BBM Kendaraan</h1>
        <div class="bg-white p-6 rounded-lg shadow-md w-full md:w-2/3 lg:w-1/3">
            <h2 class="text-lg font-semibold mb-4">{{ $bbm->kendaraan->merk }} {{ $bbm->kendaraan->tipe }} - {{ $bbm->kendaraan->plat_nomor }}</h2>
            <div class="space-y-3">
            <div class="grid text-sm" style="grid-template-columns: 60% 40%;">
                    <span class="font-semibold text-gray-700">Diinput Oleh</span>
                    <span>{{ $bbm->user->name }}</span>
                </div>
            <div class="grid text-sm" style="grid-template-columns: 60% 40%;">
                    <span class="font-semibold text-gray-700">Tanggal Pengisian BBM</span>
                    <span>{{ $bbm->tgl_isi ? \Carbon\Carbon::parse($bbm->tgl_isi)->format('d-m-Y') : '-' }}</span>
                </div>
            <div class="grid text-sm" style="grid-template-columns: 60% 40%;">
                    <span class="font-semibold text-gray-700">Jenis BBM</span>
                    <span>{{ $bbm->jenis_bbm }}</span>
                </div>
            <div class="grid text-sm" style="grid-template-columns: 60% 40%;">
                    <span class="font-semibold text-gray-700">Jumlah Pembayaran</span>
                    <span>Rp{{ number_format($bbm->nominal, 0, ',', '.') }}</span>
                </div>
            </div>
            <div class="mt-6 flex justify-between">
            </div>        
        </div>
    </div>
</x-app-layout>