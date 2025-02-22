<x-app-layout>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <title>Detail Kendaraan</title>
</head>
<body class="bg-gray-50 dark:bg-gray-900">
    <div class="container mx-auto p-6">
        <!-- Judul Halaman -->
        <!-- <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">Detail Kendaraan</h1> -->
        
        <!-- Button Back -->
        <a href="{{ route('kendaraan') }}" class="flex items-center text-blue-600 font-semibold hover:underline mb-5">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"></path>
            </svg>
            Back
        </a>


        <!-- Grid untuk Kartu Detail -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Kartu Detail Kendaraan -->
            <div class="block p-6 bg-white border border-gray-200 rounded-lg shadow-sm dark:bg-gray-800 dark:border-gray-700">
                <h5 class="mb-4 text-2xl font-bold tracking-tight text-gray-900 dark:text-white">
                    {{ $kendaraan->merk }} {{ $kendaraan->tipe }}
                </h5>

                <!-- Grid untuk Detail Kendaraan -->
                <div class="space-y-3">
                    <!-- Plat Nomor -->
                    <div class="flex justify-between">
                        <p class="font-normal text-gray-700 dark:text-gray-400">Plat Nomor</p>
                        <p class="font-normal text-gray-900 dark:text-white text-right">{{ $kendaraan->plat_nomor }}</p>
                    </div>

                    <!-- Warna -->
                    <div class="flex justify-between">
                        <p class="font-normal text-gray-700 dark:text-gray-400">Warna</p>
                        <p class="font-normal text-gray-900 dark:text-white text-right">{{ $kendaraan->warna }}</p>
                    </div>

                    <!-- Jenis Kendaraan -->
                    <div class="flex justify-between">
                        <p class="font-normal text-gray-700 dark:text-gray-400">Jenis Kendaraan</p>
                        <p class="font-normal text-gray-900 dark:text-white text-right">{{ $kendaraan->jenis }}</p>
                    </div>

                    <!-- Kapasitas -->
                    <div class="flex justify-between">
                        <p class="font-normal text-gray-700 dark:text-gray-400">Kapasitas</p>
                        <p class="font-normal text-gray-900 dark:text-white text-right">{{ $kendaraan->kapasitas }}</p>
                    </div>

                    <!-- Bahan Bakar -->
                    <div class="flex justify-between">
                        <p class="font-normal text-gray-700 dark:text-gray-400">Bahan Bakar</p>
                        <p class="font-normal text-gray-900 dark:text-white text-right">{{ $kendaraan->bahan_bakar }}</p>
                    </div>

                    <!-- Nomor Rangka -->
                    <div class="flex justify-between">
                        <p class="font-normal text-gray-700 dark:text-gray-400">Nomor Rangka</p>
                        <p class="font-normal text-gray-900 dark:text-white text-right">{{ $kendaraan->no_rangka }}</p>
                    </div>

                    <!-- Nomor Mesin -->
                    <div class="flex justify-between">
                        <p class="font-normal text-gray-700 dark:text-gray-400">Nomor Mesin</p>
                        <p class="font-normal text-gray-900 dark:text-white text-right">{{ $kendaraan->no_mesin }}</p>
                    </div>

                    <!-- Nilai Perolehan -->
                    <div class="flex justify-between">
                        <p class="font-normal text-gray-700 dark:text-gray-400">Nilai Perolehan</p>
                        <p class="font-normal text-gray-900 dark:text-white text-right">Rp.{{ number_format($kendaraan->nilai_perolehan, 0, ',', '.') }}</p>
                    </div>

                    <!-- Tanggal Pembelian -->
                    <div class="flex justify-between">
                        <p class="font-normal text-gray-700 dark:text-gray-400">Tanggal Pembelian</p>
                        <p class="font-normal text-gray-900 dark:text-white text-right">{{ \Carbon\Carbon::parse($kendaraan->tgl_pembelian)->format('d-m-Y') }}</p>
                    </div>

                    <!-- Status Aset -->
                    <div class="flex justify-between">
                        <p class="font-normal text-gray-700 dark:text-gray-400">Status Aset</p>
                        <p class="font-normal text-gray-900 dark:text-white text-right">{{ $kendaraan->aset }}</p>
                    </div>

                    <!-- Kondisi Fisik -->
                    <div class="flex justify-between">
                        <p class="font-normal text-gray-700 dark:text-gray-400">Kondisi Fisik</p>
                        <p class="font-normal text-gray-900 dark:text-white text-right">{{ $cekFisikTerbaru?->kondisi_keseluruhan ?? 'Data tidak tersedia' }}</p>
                    </div>

                    <!-- Nilai Buku -->
                    <div class="flex justify-between">
                        <p class="font-normal text-gray-700 dark:text-gray-400">Nilai Buku</p>
                        <p class="font-normal text-gray-900 dark:text-white text-right">Rp.{{ number_format($kendaraan->nilai_buku, 0, ',', '.') }}</p>
                    </div>

                    <!-- Status Pajak -->
                    <div class="flex justify-between">
                        <p class="font-normal text-gray-700 dark:text-gray-400">Status Pajak</p>
                        <p class="font-normal text-right 
                            @if($statusPajak == 'SUDAH DIBAYAR')
                                bg-green-100 text-green-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded-sm dark:bg-gray-700 dark:text-green-400 border border-green-400
                            @elseif($statusPajak == 'JATUH TEMPO')
                                bg-red-100 text-red-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded-sm dark:bg-gray-700 dark:text-red-400 border border-red-400
                            @elseif($statusPajak == 'MENDEKATI JATUH TEMPO')
                                bg-yellow-100 text-yellow-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded-sm dark:bg-gray-700 dark:text-yellow-300 border border-yellow-300
                            @else
                                bg-gray-100 text-gray-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded-sm dark:bg-gray-700 dark:text-gray-400 border border-gray-500 
                            @endif
                        ">
                            {{ $statusPajak }}
                        </p>
                    </div>

                    <!-- Status Asuransi -->
                    <div class="flex justify-between">
                    <p class="font-normal text-gray-700 dark:text-gray-400">Status Asuransi</p>
                    <p class="font-normal text-right
                        @if($statusAsuransi == 'SUDAH DIBAYAR')
                            bg-green-100 text-green-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded-sm dark:bg-gray-700 dark:text-green-400 border border-green-400
                        @elseif($statusAsuransi == 'JATUH TEMPO')
                            bg-red-100 text-red-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded-sm dark:bg-gray-700 dark:text-red-400 border border-red-400
                        @elseif($statusAsuransi == 'MENDEKATI JATUH TEMPO')
                            bg-yellow-100 text-yellow-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded-sm dark:bg-gray-700 dark:text-yellow-300 border border-yellow-300
                        @else
                            bg-gray-100 text-gray-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded-sm dark:bg-gray-700 dark:text-gray-400 border border-gray-500
                        @endif
                    ">
                        {{ $statusAsuransi }}
                    </p>
                </div>

                    <!-- Status Servis Rutin -->
                    <div class="flex justify-between sm:flex-row justify-between mb-">
                        <p class="font-normal text-gray-700 dark:text-gray-400">Status Servis Rutin</p>
                        <p class="font-normal text-right sm:mt-0 sm:-ml-5
                            @if($statusServisRutin == 'SUDAH SERVIS')
                                bg-green-100 text-green-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded-sm dark:bg-gray-700 dark:text-green-400 border border-green-400
                            @elseif($statusServisRutin == 'WAKTUNYA SERVIS')
                                bg-red-100 text-red-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded-sm dark:bg-gray-700 dark:text-red-400 border border-red-400
                            @elseif($statusServisRutin == 'MENDEKATI JADWAL SERVIS')
                                bg-yellow-100 text-yellow-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded-sm dark:bg-gray-700 dark:text-yellow-300 border border-yellow-300
                            @else
                                bg-gray-100 text-gray-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded-sm dark:bg-gray-700 dark:text-gray-400 border border-gray-500
                            @endif
                        ">
                            {{ $statusServisRutin }}
                        </p>
                    </div>

                    <!-- Pengisian BBM Terakhir -->
                    <div class="flex justify-between">
                        <p class="font-normal text-gray-700 dark:text-gray-400">Pengisian BBM Terakhir</p>
                        <p class="font-normal text-gray-900 dark:text-white mt-2 sm:mt-0 sm:ml-4 text-left sm:text-right">
                            {{ optional($bbm)->tgl_isi 
                                ? \Carbon\Carbon::parse(optional($bbm)->tgl_isi)->format('d-m-Y') 
                                : '-' 
                            }}
                            {{ optional($bbm)->nominal 
                                ? ' Rp.' . number_format(optional($bbm)->nominal, 0, ',', '.') 
                                : '' 
                            }}
                        </p>
                    </div>

                    <!-- Status Peminjaman -->
                    <div class="flex justify-between">
                        <p class="font-normal text-gray-700 dark:text-gray-400">Status Peminjaman</p>
                        <p class="font-normal text-gray-900 dark:text-white text-right uppercase">
                            <span class="
                                @if($kendaraan->status_ketersediaan == 'Tersedia') bg-green-100 text-green-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded-sm dark:bg-gray-700 dark:text-green-400 border border-green-400
                                @elseif($kendaraan->status_ketersediaan == 'Tidak Tersedia') bg-red-100 text-red-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded-sm dark:bg-gray-700 dark:text-red-400 border border-red-400
                                @else bg-green-100 text-green-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded-sm dark:bg-gray-700 dark:text-green-400 border border-green-400

                                @endif">
                                {{ $kendaraan->status_ketersediaan }}
                            </span>
                        </p>
                    </div>
                </div>
            </div>

            <!-- Kartu Cek Fisik -->
            <div class="block p-6 bg-white border border-gray-200 rounded-lg shadow-sm  dark:bg-gray-800 dark:border-gray-700">
                <h5 class="mb-2 text-2xl font-bold tracking-tight text-gray-900 dark:text-white">Cek Fisik</h5>
                <div class="flex justify-between">
                        <p class="font-normal text-gray-700 dark:text-gray-400">Mesin</p>
                        <p class="font-normal text-gray-900 dark:text-white text-right">{{ $cekFisikTerbaru?->mesin ?? 'Data tidak tersedia' }}</p>
                    </div>
                <div class="flex justify-between">
                    <p class="font-normal text-gray-700 dark:text-gray-400">Accu</p>
                    <p class="font-normal text-gray-900 dark:text-white text-right">{{  $cekFisikTerbaru?->accu ?? 'Data tidak tersedia' }}</p>
                </div>
                <div class="flex justify-between">
                    <p class="font-normal text-gray-700 dark:text-gray-400">Air Radiator</p>
                    <p class="font-normal text-gray-900 dark:text-white text-right">{{  $cekFisikTerbaru?->air_radiator ?? 'Data tidak tersedia' }}</p>
                </div>
                <div class="flex justify-between">
                    <p class="font-normal text-gray-700 dark:text-gray-400">Air Wiper
                    <p class="font-normal text-gray-900 dark:text-white text-right">{{  $cekFisikTerbaru?->air_wiper ?? 'Data tidak tersedia' }}</p>
                </div>
                <div class="flex justify-between">
                    <p class="font-normal text-gray-700 dark:text-gray-400">Body
                    <p class="font-normal text-gray-900 dark:text-white text-right">{{  $cekFisikTerbaru?->body ?? 'Data tidak tersedia' }}</p>
                </div>
                <div class="flex justify-between">
                    <p class="font-normal text-gray-700 dark:text-gray-400">Ban
                    <p class="font-normal text-gray-900 dark:text-white text-right">{{  $cekFisikTerbaru?->ban ?? 'Data tidak tersedia' }}</p>
                </div>
                <div class="flex justify-between">
                    <p class="font-normal text-gray-700 dark:text-gray-400">Pengharum
                    <p class="font-normal text-gray-900 dark:text-white text-right">{{  $cekFisikTerbaru?->pengharum ?? 'Data tidak tersedia' }}</p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
</x-app-layout>