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
        <h1 class="text-xl font-semibold text-gray-900 dark:text-white mb-6">Detail Kendaraan</h1>

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
                        <p class="font-normal text-gray-900 dark:text-white">{{ $kendaraan->plat_nomor }}</p>
                    </div>

                    <!-- Warna -->
                    <div class="flex justify-between">
                        <p class="font-normal text-gray-700 dark:text-gray-400">Warna</p>
                        <p class="font-normal text-gray-900 dark:text-white">{{ $kendaraan->warna }}</p>
                    </div>

                    <!-- Jenis Kendaraan -->
                    <div class="flex justify-between">
                        <p class="font-normal text-gray-700 dark:text-gray-400">Jenis Kendaraan</p>
                        <p class="font-normal text-gray-900 dark:text-white">{{ $kendaraan->jenis }}</p>
                    </div>

                    <!-- Kapasitas -->
                    <div class="flex justify-between">
                        <p class="font-normal text-gray-700 dark:text-gray-400">Kapasitas</p>
                        <p class="font-normal text-gray-900 dark:text-white">{{ $kendaraan->kapasitas }}</p>
                    </div>

                    <!-- Bahan Bakar -->
                    <div class="flex justify-between">
                        <p class="font-normal text-gray-700 dark:text-gray-400">Bahan Bakar</p>
                        <p class="font-normal text-gray-900 dark:text-white">{{ $kendaraan->bahan_bakar }}</p>
                    </div>

                    <!-- Nomor Rangka -->
                    <div class="flex justify-between">
                        <p class="font-normal text-gray-700 dark:text-gray-400">Nomor Rangka</p>
                        <p class="font-normal text-gray-900 dark:text-white">{{ $kendaraan->no_rangka }}</p>
                    </div>

                    <!-- Nomor Mesin -->
                    <div class="flex justify-between">
                        <p class="font-normal text-gray-700 dark:text-gray-400">Nomor Mesin</p>
                        <p class="font-normal text-gray-900 dark:text-white">{{ $kendaraan->no_mesin }}</p>
                    </div>

                    <!-- Nilai Perolehan -->
                    <div class="flex justify-between">
                        <p class="font-normal text-gray-700 dark:text-gray-400">Nilai Perolehan</p>
                        <p class="font-normal text-gray-900 dark:text-white">Rp.{{ number_format($kendaraan->nilai_perolehan, 0, ',', '.') }}</p>
                    </div>

                    <!-- Tanggal Pembelian -->
                    <div class="flex justify-between">
                        <p class="font-normal text-gray-700 dark:text-gray-400">Tanggal Pembelian</p>
                        <p class="font-normal text-gray-900 dark:text-white">{{ $kendaraan->tgl_pembelian }}</p>
                    </div>

                    <!-- Status Aset -->
                    <div class="flex justify-between">
                        <p class="font-normal text-gray-700 dark:text-gray-400">Status Aset</p>
                        <p class="font-normal text-gray-900 dark:text-white">{{ $kendaraan->aset }}</p>
                    </div>

                    <!-- Kondisi Fisik -->
                    <div class="flex justify-between">
                        <p class="font-normal text-gray-700 dark:text-gray-400">Kondisi Fisik</p>
                        <p class="font-normal text-gray-900 dark:text-white">{{ $cekFisikTerbaru?->kondisi_keseluruhan ?? 'Data tidak tersedia' }}</p>
                    </div>

                    <!-- Nilai Buku -->
                    <div class="flex justify-between">
                        <p class="font-normal text-gray-700 dark:text-gray-400">Nilai Buku</p>
                        <p class="font-normal text-gray-900 dark:text-white">Rp.{{ number_format($kendaraan->nilai_buku, 0, ',', '.') }}</p>
                    </div>

                    <!-- Status Pajak -->
                    <div class="flex justify-between">
                        <p class="font-normal text-gray-700 dark:text-gray-400">Status Pajak</p>
                        <p class="font-normal 
                            @if($statusPajak == 'SUDAH DIBAYAR')
                                text-green-600 dark:text-green-400
                            @elseif($statusPajak == 'JATUH TEMPO')
                                text-red-600 dark:text-red-400
                            @elseif($statusPajak == 'MENDEKATI JATUH TEMPO')
                                text-yellow-600 dark:text-yellow-400
                            @else
                                text-gray-900 dark:text-white
                            @endif
                        ">
                            {{ $statusPajak }}
                        </p>
                    </div>

                    <!-- Status Asuransi -->
                    <div class="flex justify-between">
                    <p class="font-normal text-gray-700 dark:text-gray-400">Status Asuransi</p>
                    <p class="font-normal 
                        @if($statusAsuransi == 'SUDAH DIBAYAR')
                            text-green-600 dark:text-green-400
                        @elseif($statusAsuransi == 'JATUH TEMPO')
                            text-red-600 dark:text-red-400
                        @elseif($statusAsuransi == 'MENDEKATI JATUH TEMPO')
                            text-yellow-600 dark:text-yellow-400
                        @else
                            text-gray-900 dark:text-white
                        @endif
                    ">
                        {{ $statusAsuransi }}
                    </p>
                </div>

                    <!-- Status Servis Rutin -->
                    <div class="flex justify-between">
                        <p class="font-normal text-gray-700 dark:text-gray-400">Status Servis Rutin</p>
                        <p class="font-normal 
                            @if($statusServisRutin == 'SUDAH SERVIS')
                                text-green-600 dark:text-green-400
                            @elseif($statusServisRutin == 'WAKTUNYA SERVIS')
                                text-red-600 dark:text-red-400
                            @elseif($statusServisRutin == 'MENDEKATI JADWAL SERVIS')
                                text-yellow-600 dark:text-yellow-400
                            @else
                                text-gray-900 dark:text-white
                            @endif
                        ">
                            {{ $statusServisRutin }}
                        </p>
                    </div>

                    <!-- Pengisian BBM Terakhir -->
                    <div class="flex justify-between">
                        <p class="font-normal text-gray-700 dark:text-gray-400">Pengisian BBM Terakhir</p>
                        <p class="font-normal text-gray-900 dark:text-white">{{ $bbm->tgl_isi }} - Rp.{{ number_format($bbm->nominal, 0, ',', '.') }}</p>
                    </div>

                    <!-- Status Peminjaman -->
                    <div class="flex justify-between">
                        <p class="font-normal text-gray-700 dark:text-gray-400">Status Peminjaman</p>
                        <p class="font-normal text-gray-900 dark:text-white">{{ $kendaraan->status_ketersediaan }}</p>
                    </div>
                </div>
            </div>

            <!-- Kartu Cek Fisik -->
            <div class="block p-6 bg-white border border-gray-200 rounded-lg shadow-sm  dark:bg-gray-800 dark:border-gray-700">
                <h5 class="mb-2 text-2xl font-bold tracking-tight text-gray-900 dark:text-white">Cek Fisik</h5>
                <div class="flex justify-between">
                        <p class="font-normal text-gray-700 dark:text-gray-400">Mesin</p>
                        <p class="font-normal text-gray-900 dark:text-white">{{ $cekFisikTerbaru?->mesin ?? 'Data tidak tersedia' }}</p>
                    </div>
                <div class="flex justify-between">
                    <p class="font-normal text-gray-700 dark:text-gray-400">Accu</p>
                    <p class="font-normal text-gray-900 dark:text-white">{{  $cekFisikTerbaru?->accu ?? 'Data tidak tersedia' }}</p>
                </div>
                <div class="flex justify-between">
                    <p class="font-normal text-gray-700 dark:text-gray-400">Air Radiator</p>
                    <p class="font-normal text-gray-900 dark:text-white">{{  $cekFisikTerbaru?->air_radiator ?? 'Data tidak tersedia' }}</p>
                </div>
                <div class="flex justify-between">
                    <p class="font-normal text-gray-700 dark:text-gray-400">Air Wiper
                    <p class="font-normal text-gray-900 dark:text-white">{{  $cekFisikTerbaru?->air_wiper ?? 'Data tidak tersedia' }}</p>
                </div>
                <div class="flex justify-between">
                    <p class="font-normal text-gray-700 dark:text-gray-400">Body
                    <p class="font-normal text-gray-900 dark:text-white">{{  $cekFisikTerbaru?->body ?? 'Data tidak tersedia' }}</p>
                </div>
                <div class="flex justify-between">
                    <p class="font-normal text-gray-700 dark:text-gray-400">Ban
                    <p class="font-normal text-gray-900 dark:text-white">{{  $cekFisikTerbaru?->ban ?? 'Data tidak tersedia' }}</p>
                </div>
                <div class="flex justify-between">
                    <p class="font-normal text-gray-700 dark:text-gray-400">Pengharum
                    <p class="font-normal text-gray-900 dark:text-white">{{  $cekFisikTerbaru?->pengharum ?? 'Data tidak tersedia' }}</p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
</x-app-layout>