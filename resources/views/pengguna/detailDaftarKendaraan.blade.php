<x-app-layout>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Detail Kendaraan</title>
</head>
<body class="bg-gray-50">
    <div class="container mx-auto p-6">
        <!-- Judul Halaman -->
        <!-- <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">Detail Kendaraan</h1> -->
        
        <!-- Button Back--> 
        <a href="{{  route('kendaraan', ['page' => request('page'), 'search' => request('search')])  }}" class="flex items-center text-blue-600 font-semibold hover:underline mb-5">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"></path>
            </svg>
            Kembali
        </a>

        <!-- Grid untuk Kartu Detail -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Kartu Detail Kendaraan -->
            <div class="block p-6 bg-white border border-gray-200 rounded-lg shadow-sm ">
                <h5 class="mb-4 text-2xl font-bold tracking-tight text-gray-900 ">
                    {{ $kendaraan->merk }} {{ $kendaraan->tipe }}
                </h5>

                <!-- Grid untuk Detail Kendaraan -->
                <div class="space-y-3">
                    <!-- Plat Nomor -->
                    <div class="flex flex-col sm:flex-row items-start detail-item w-full">
                        <p class="text-gray-700 sm:w-56 detail-label font-bold">Plat Nomor</p>
                        <p class="text-gray-900 break-words sm:max-w-[calc(100%-14rem)]">{{ $kendaraan->plat_nomor }}</p>
                    </div>

                    <!-- Warna -->
                    <div class="flex flex-col sm:flex-row items-start detail-item w-full">
                        <p class="text-gray-700 sm:w-56 detail-label font-bold">Warna</p>
                        <p class="text-gray-900 break-words sm:max-w-[calc(100%-14rem)]">{{ $kendaraan->warna }}</p>
                    </div>

                    <!-- Jenis Kendaraan -->
                    <div class="flex flex-col sm:flex-row items-start detail-item w-full">
                        <p class="text-gray-700 sm:w-56 detail-label font-bold">Jenis Kendaraan</p>
                        <p class="text-gray-900 break-words sm:max-w-[calc(100%-14rem)]">{{ $kendaraan->jenis }}</p>
                    </div>

                    <!-- Kapasitas -->
                    <div class="flex flex-col sm:flex-row items-start detail-item w-full">
                        <p class="text-gray-700 sm:w-56 detail-label font-bold">Kapasitas</p>
                        <p class="text-gray-900 break-words sm:max-w-[calc(100%-14rem)]">{{ $kendaraan->kapasitas }}</p>
                    </div>

                    <!-- Bahan Bakar -->
                    <div class="flex flex-col sm:flex-row items-start detail-item w-full">
                        <p class="text-gray-700 sm:w-56 detail-label font-bold">Bahan Bakar</p>
                        <p class="text-gray-900 break-words sm:max-w-[calc(100%-14rem)]">{{ $kendaraan->bahan_bakar }}</p>
                    </div>

                    <!-- Nomor Rangka -->
                    <div class="flex flex-col sm:flex-row items-start detail-item w-full">
                        <p class="text-gray-700 sm:w-56 detail-label font-bold">Nomor Rangka</p>
                        <p class="text-gray-900 break-words sm:max-w-[calc(100%-14rem)]">{{ $kendaraan->no_rangka }}</p>
                    </div>

                    <!-- Nomor Mesin -->
                    <div class="flex flex-col sm:flex-row items-start detail-item w-full">
                        <p class="text-gray-700 sm:w-56 detail-label font-bold">Nomor Mesin</p>
                        <p class="text-gray-900 break-words sm:max-w-[calc(100%-14rem)]">{{ $kendaraan->no_mesin }}</p>
                    </div>

                    <!-- Nilai Perolehan -->
                    <div class="flex flex-col sm:flex-row items-start detail-item w-full">
                        <p class="text-gray-700 sm:w-56 detail-label font-bold">Nilai Perolehan</p>
                        <p class="text-gray-900 break-words sm:max-w-[calc(100%-14rem)]">Rp.{{ number_format($kendaraan->nilai_perolehan, 0, ',', '.') }}</p>
                    </div>

                    <!-- Tanggal Pembelian -->
                    <div class="flex flex-col sm:flex-row items-start detail-item w-full">
                        <p class="text-gray-700 sm:w-56 detail-label font-bold">Tanggal Pembelian</p>
                        <p class="text-gray-900 break-words sm:max-w-[calc(100%-14rem)]">{{ \Carbon\Carbon::parse($kendaraan->tgl_pembelian)->format('d-m-Y') }}</p>
                    </div>

                    <!-- Status Aset -->
                    <div class="flex flex-col sm:flex-row items-start detail-item w-full">
                        <p class="text-gray-700 sm:w-56 detail-label font-bold">Status Aset</p>
                        <p class="text-gray-900 break-words sm:max-w-[calc(100%-14rem)]">{{ $kendaraan->aset }}</p>
                    </div>

                    <!-- Kondisi Fisik -->
                    <div class="flex flex-col sm:flex-row items-start detail-item w-full">
                        <p class="text-gray-700 sm:w-56 detail-label font-bold">Kondisi Fisik</p>
                        <p class="text-gray-900 break-words sm:max-w-[calc(100%-14rem)]">{{ $cekFisikTerbaru?->kondisi_keseluruhan ?? 'Data tidak tersedia' }}</p>
                    </div>

                    <!-- Nilai Buku -->
                    <div class="flex flex-col sm:flex-row items-start detail-item w-full">
                        <p class="text-gray-700 sm:w-56 detail-label font-bold">Nilai Buku</p>
                        <p class="text-gray-900 break-words sm:max-w-[calc(100%-14rem)]">Rp.{{ number_format($kendaraan->nilai_buku, 0, ',', '.') }}</p>
                    </div>

                    <!-- Status Pajak -->
                    <div class="flex flex-col sm:flex-row items-start detail-item w-full">
                        <p class="text-gray-700 sm:w-56 detail-label font-bold">Status Pajak</p>
                        <p class="font-normal sm:text-center
                            @if($statusPajak == 'SUDAH DIBAYAR')
                                 bg-green-100 text-green-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded-sm  border border-green-400
                            @elseif($statusPajak == 'JATUH TEMPO')
                                bg-red-100 text-red-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded-sm border border-red-400
                            @elseif($statusPajak == 'MENDEKATI JATUH TEMPO')
                                bg-yellow-100 text-yellow-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded-sm border border-yellow-300
                            @else
                                bg-gray-100 text-gray-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded-sm border border-gray-500 
                            @endif
                        ">
                            {{ $statusPajak }}
                        </p>
                    </div>

                    <!-- Status Asuransi -->
                    <div class="flex flex-col sm:flex-row items-start detail-item w-full">
                    <p class="text-gray-700 sm:w-56 detail-label font-bold">Status Asuransi</p>
                    <p class="font-normal sm:text-center
                        @if($statusAsuransi == 'SUDAH DIBAYAR')
                            bg-green-100 text-green-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded-sm  border border-green-400
                        @elseif($statusAsuransi == 'JATUH TEMPO')
                            bg-red-100 text-red-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded-sm  border border-red-400
                        @elseif($statusAsuransi == 'MENDEKATI JATUH TEMPO')
                            bg-yellow-100 text-yellow-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded-sm  border border-yellow-300
                        @else
                            bg-gray-100 text-gray-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded-sm  border border-gray-500
                        @endif
                    ">
                        {{ $statusAsuransi }}
                    </p>
                </div>

                    <!-- Status Servis Rutin -->
                    <div class="flex flex-col sm:flex-row items-start detail-item w-full">
                        <p class="text-gray-700 sm:w-56 detail-label font-bold">Status Servis Rutin</p>
                        <p class="font-normal sm:text-center
                            @if($statusServisRutin == 'SUDAH SERVIS')
                                bg-green-100 text-green-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded-sm  border border-green-400
                            @elseif($statusServisRutin == 'JATUH TEMPO')
                                bg-red-100 text-red-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded-sm  border border-red-400
                            @elseif($statusServisRutin == 'MENDEKATI JATUH TEMPO')
                                bg-yellow-100 text-yellow-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded-sm  border border-yellow-300
                            @else
                                bg-gray-100 text-gray-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded-sm  border border-gray-500
                            @endif
                        text-left sm:text-center">
                            {{ $statusServisRutin }}
                        </p>
                    </div>

                    <!-- Pengisian BBM Terakhir -->
                    <div class="flex flex-col sm:flex-row items-start detail-item w-full">
                        <p class="text-gray-700 sm:w-56 detail-label font-bold">Pengisian BBM Terakhir</p>
                        <p class="text-gray-900 break-words sm:max-w-[calc(100%-14rem)]">
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
                    <div class="flex flex-col sm:flex-row items-start detail-item w-full">
                        <p class="text-gray-700 sm:w-56 detail-label font-bold">Status Peminjaman</p>
                        <p class="font-normal text-gray-900  text-right uppercase">
                            <span class="
                                @if($kendaraan->status_ketersediaan == 'Tersedia' || $kendaraan->status_ketersediaan == 'TERSEDIA'  ) bg-green-100 text-green-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded-sm  border border-green-400
                                @elseif($kendaraan->status_ketersediaan == 'Tidak Tersedia' ||  $kendaraan->status_ketersediaan == ' TIDAK TERSEDIA') bg-red-100 text-red-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded-sm  border-red-400
                                @else bg-green-100 text-green-800 text-xs font-medium me-2 px-2.5 py-0.5 rounded-sm  border border-green-400

                                @endif">
                                {{ $kendaraan->status_ketersediaan }}
                            </span>
                        </p>
                    </div>
                </div>
            </div>

            <!-- Kartu Cek Fisik -->
            <div class="block p-6 bg-white border border-gray-200 rounded-lg shadow-sm ">
                <h5 class="mb-2 text-2xl font-bold tracking-tight text-gray-900 font-bold">Cek Fisik</h5>
                <div class="space-y-3">
                    <div class="flex flex-col sm:flex-row items-start detail-item w-full">
                        <p class="text-gray-700 sm:w-56 detail-label font-bold">Mesin</p>
                        <p class="text-gray-900 break-words sm:max-w-[calc(100%-14rem)]">{{ $cekFisikTerbaru?->mesin ?? '-' }}</p> <!-- Diubah ke text-left -->
                    </div>
                    <div class="flex flex-col sm:flex-row items-start detail-item w-full">
                        <p class="text-gray-700 sm:w-56 detail-label font-bold">Accu</p>
                        <p class="text-gray-900 break-words sm:max-w-[calc(100%-14rem)]">{{ $cekFisikTerbaru?->accu ?? '-' }}</p> <!-- Diubah ke text-left -->
                    </div>
                    <div class="flex flex-col sm:flex-row items-start detail-item w-full">
                        <p class="text-gray-700 sm:w-56 detail-label font-bold">Air Radiator</p>
                        <p class="text-gray-900 break-words sm:max-w-[calc(100%-14rem)]">{{ $cekFisikTerbaru?->air_radiator ?? '-' }}</p> <!-- Diubah ke text-left -->
                    </div>
                    <div class="flex flex-col sm:flex-row items-start detail-item w-full">
                        <p class="text-gray-700 sm:w-56 detail-label font-bold">Air Wiper</p>
                        <p class="text-gray-900 break-words sm:max-w-[calc(100%-14rem)]">{{ $cekFisikTerbaru?->air_wiper ?? '-' }}</p> <!-- Diubah ke text-left -->
                    </div>
                    <div class="flex flex-col sm:flex-row items-start detail-item w-full">
                        <p class="text-gray-700 sm:w-56 detail-label font-bold">Body</p>
                        <p class="text-gray-900 break-words sm:max-w-[calc(100%-14rem)]">{{ $cekFisikTerbaru?->body ?? '-' }}</p> <!-- Diubah ke text-left -->
                    </div>
                    <div class="flex flex-col sm:flex-row items-start detail-item w-full">
                        <p class="text-gray-700 sm:w-56 detail-label font-bold">Ban</p>
                        <p class="text-gray-900 break-words sm:max-w-[calc(100%-14rem)]">{{ $cekFisikTerbaru?->ban ?? '-' }}</p> <!-- Diubah ke text-left -->
                    </div>
                    <div class="flex flex-col sm:flex-row items-start detail-item w-full">
                        <p class="text-gray-700 sm:w-56 detail-label font-bold">Pengharum</p>
                        <p class="text-gray-900 break-words sm:max-w-[calc(100%-14rem)]">{{ $cekFisikTerbaru?->pengharum ?? '-' }}</p> <!-- Diubah ke text-left -->
                    </div>
                </div>
            </div>

        </div>
    </div>
</body>
</html>
</x-app-layout>