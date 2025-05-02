<x-app-layout>
    <style>
        .detail-label {
            font-weight: 600 !important; 
        }
        
        @media (max-width: 640px) {
            .detail-container {
                padding: 1rem;
            }
            
            .detail-item {
                margin-bottom: 0.75rem;
            }
            
            .detail-label {
                margin-bottom: 0.25rem;
            }
        }
    </style>

    @php 
        $currentPage = request()->query('page');
    @endphp
    <input type="hidden" name="current_page" value="{{ $currentPage }}">
    <a href="{{ route('kendaraan.daftar_kendaraan', ['page' => $currentPage, 'search' => request()->query('search')]) }}" class="flex items-center text-blue-600 font-semibold hover:underline mb-5">
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"></path>
        </svg>
        Kembali
    </a>
    <h2 class="text-2xl font-semibold text-gray-800 ml-4 dark:text-white sm:mb-0 mb-4">Detail Kendaraan</h2>
    <div class="grid grid-cols-1 md:grid-cols-2 ml-4 mt-3 gap-4 md:gap-6 px-2 md:px-0">
        <div class="space-y-3 bg-white border border-gray-200 p-3 md:p-4 rounded-lg shadow-sm">
            <h2 class="text-lg md:text-xl font-semibold text-gray-900">Informasi Kendaraan</h2>
                <div class="space-y-2 md:space-y-3">
                    <div class="flex flex-col sm:flex-row sm:items-start detail-item">
                        <span class="text-sm text-gray-700 w-full sm:w-48 font-semibold detail-label">Merk</span>
                        <span class="text-sm text-gray-700 flex-1">{{ $kendaraan->merk ?? '-' }}</span>
                    </div>
                    <div class="flex flex-col sm:flex-row sm:items-start detail-item">
                        <span class="text-sm text-gray-700 w-full sm:w-48 font-semibold detail-label">Tipe</span>
                        <span class="text-sm text-gray-700 flex-1">{{ $kendaraan->tipe ?? '-' }}</span>
                    </div>
                    <div class="flex flex-col sm:flex-row sm:items-start detail-item">
                        <span class="text-sm text-gray-700 w-full sm:w-48 font-semibold detail-label">Plat Nomor</span>
                        <span class="text-sm text-gray-700 flex-1">{{ $kendaraan->plat_nomor ?? '-' }}</span>
                    </div>
                    <div class="flex flex-col sm:flex-row sm:items-start detail-item">
                        <span class="text-sm text-gray-700 w-full sm:w-48 font-semibold detail-label">Warna</span>
                        <span class="text-sm text-gray-700 flex-1">{{ $kendaraan->warna ?? '-' }}</span>
                    </div>
                    <div class="flex flex-col sm:flex-row sm:items-start detail-item">
                        <span class="text-sm text-gray-700 w-full sm:w-48 font-semibold detail-label">Jenis Kendaraan</span>
                        <span class="text-sm text-gray-700 flex-1">{{ $kendaraan->jenis ?? '-' }}</span>
                    </div>
                    <div class="flex flex-col sm:flex-row sm:items-start detail-item">
                        <span class="text-sm text-gray-700 w-full sm:w-48 font-semibold detail-label">Status Aset</span>
                        <span class="text-sm text-gray-700 flex-1">{{ $kendaraan->aset ?? '-' }}</span>
                    </div>
                    <div class="flex flex-col sm:flex-row sm:items-start detail-item">
                        <span class="text-sm text-gray-700 w-full sm:w-48 font-semibold detail-label">Kapasitas Penumpang</span>
                        <span class="text-sm text-gray-700 flex-1">{{ $kendaraan->kapasitas ?? '-' }}</span>
                    </div>
                    <div class="flex flex-col sm:flex-row sm:items-start detail-item">
                        <span class="text-sm text-gray-700 w-full sm:w-48 font-semibold detail-label">Tanggal Pembelian</span>
                        <span class="text-sm text-gray-700 flex-1">{{ $kendaraan->tgl_pembelian ? \Carbon\Carbon::parse($kendaraan->tgl_pembelian)->format('d-m-Y') : '-' }}</span>
                    </div>
                    <div class="flex flex-col sm:flex-row sm:items-start detail-item">
                        <span class="text-sm text-gray-700 w-full sm:w-48 font-semibold detail-label">Nilai Perolehan</span>
                        <span class="text-sm text-gray-700 flex-1">{{ $kendaraan->nilai_perolehan ? 'Rp ' . number_format($kendaraan->nilai_perolehan, 0, ',', '.') : '-' }}</span>
                    </div>
                    <div class="flex flex-col sm:flex-row sm:items-start detail-item">
                        <span class="text-sm text-gray-700 w-full sm:w-48 font-semibold detail-label">Nilai Buku</span>
                        <span class="text-sm text-gray-700 flex-1">{{ $kendaraan->nilai_buku ? 'Rp ' . number_format($kendaraan->nilai_buku, 0, ',', '.') : '-' }}</span>
                    </div>
                    <div class="flex flex-col sm:flex-row sm:items-start detail-item">
                        <span class="text-sm text-gray-700 w-full sm:w-48 font-semibold detail-label">Bahan Bakar</span>
                        <span class="text-sm text-gray-700 flex-1">{{ $kendaraan->bahan_bakar ?? '-' }}</span>
                    </div>
                    <div class="flex flex-col sm:flex-row sm:items-start detail-item">
                        <span class="text-sm text-gray-700 w-full sm:w-48 font-semibold detail-label">Nomor Mesin</span>
                        <span class="text-sm text-gray-700 flex-1">{{ $kendaraan->no_mesin ?? '-' }}</span>
                    </div>
                    <div class="flex flex-col sm:flex-row sm:items-start detail-item">
                        <span class="text-sm text-gray-700 w-full sm:w-48 font-semibold detail-label">Nomor Rangka</span>
                        <span class="text-sm text-gray-700 flex-1">{{ $kendaraan->no_rangka ?? '-' }}</span>
                    </div>
                    <div class="flex flex-col sm:flex-row sm:items-start detail-item">
                        <span class="text-sm text-gray-700 w-full sm:w-48 font-semibold detail-label">Frekuensi Servis</span>
                        <span class="text-sm text-gray-700 flex-1">{{ $kendaraan->frekuensi_servis ? $kendaraan->frekuensi_servis . ' Bulan' : '-' }}</span>
                    </div>
                    <div class="flex flex-col sm:flex-row sm:items-start detail-item">
                        <span class="text-sm text-gray-700 w-full sm:w-48 font-semibold detail-label">Tanggal Servis Rutin Terakhir</span>
                        <span class="text-sm text-gray-700 flex-1">
                            {{ isset($servisRutin) && $servisRutin->tgl_servis_real ? \Carbon\Carbon::parse($servisRutin->tgl_servis_real)->format('d-m-Y') : '-' }}
                        </span>
                    </div>
                    <div class="flex flex-col sm:flex-row sm:items-start detail-item">
                        <span class="text-sm text-gray-700 w-full sm:w-48 font-semibold detail-label">Tanggal Bayar Asuransi Terakhir</span>
                        <span class="text-sm text-gray-700 flex-1">
                            {{ isset($asuransi) && $asuransi->tgl_bayar ? \Carbon\Carbon::parse($asuransi->tgl_bayar)->format('d-m-Y') : '-' }}
                        </span>
                    </div>
                    <div class="flex flex-col sm:flex-row sm:items-start detail-item">
                        <span class="text-sm text-gray-700 w-full sm:w-48 font-semibold detail-label">Masa Perlindungan Awal</span>
                        <span class="text-sm text-gray-700 flex-1">
                            {{ isset($asuransi) && $asuransi->tgl_perlindungan_awal ? \Carbon\Carbon::parse($asuransi->tgl_perlindungan_awal)->format('d-m-Y') : '-' }}
                        </span>
                    </div>
                    <div class="flex flex-col sm:flex-row sm:items-start detail-item">
                        <span class="text-sm text-gray-700 w-full sm:w-48 font-semibold detail-label">Masa Perlindungan Akhir</span>
                        <span class="text-sm text-gray-700 flex-1">
                            {{ isset($asuransi) && $asuransi->tgl_perlindungan_akhir ? \Carbon\Carbon::parse($asuransi->tgl_perlindungan_akhir)->format('d-m-Y') : '-' }}
                        </span>
                    </div>
                    <div class="flex flex-col sm:flex-row sm:items-start detail-item">
                        <span class="text-sm text-gray-700 w-full sm:w-48 font-semibold detail-label">Tanggal Bayar Pajak Terakhir</span>
                        <span class="text-sm text-gray-700 flex-1">
                            {{ isset($pajak) && $pajak->tgl_bayar ? \Carbon\Carbon::parse($pajak->tgl_bayar)->format('d-m-Y') : '-' }}
                        </span>
                    </div>
                    <div class="flex flex-col sm:flex-row sm:items-start detail-item">
                        <span class="text-sm text-gray-700 w-full sm:w-48 font-semibold detail-label">Tanggal Jatuh Tempo Pajak Terakhir</span>
                        <span class="text-sm text-gray-700 flex-1">
                            {{ isset($pajak) && $pajak->tgl_jatuh_tempo ? \Carbon\Carbon::parse($pajak->tgl_jatuh_tempo)->format('d-m-Y') : '-' }}
                        </span>
                    </div>
                    <div class="flex flex-col sm:flex-row sm:items-start detail-item">
                        <span class="text-sm text-gray-700 w-full sm:w-48 font-semibold detail-label">Tanggal Pengisian BBM Terakhir</span>
                        <span class="text-sm text-gray-700 flex-1">
                            {{ isset($bbm) && $bbm->tgl_isi ? \Carbon\Carbon::parse($bbm->tgl_isi)->format('d-m-Y') : '-' }}
                        </span>
                    </div>
                    <div class="flex flex-col sm:flex-row sm:items-start detail-item">
                        <span class="text-sm text-gray-700 w-full sm:w-48 font-semibold detail-label">Tanggal Cek Fisik Terakhir</span>
                        <span class="text-sm text-gray-700 flex-1">
                            {{ isset($cekFisik) && $cekFisik->tgl_cek_fisik ? \Carbon\Carbon::parse($cekFisik->tgl_cek_fisik)->format('d-m-Y') : '-' }}
                        </span>
                    </div>
                    <div class="flex flex-col sm:flex-row sm:items-start detail-item">
                        <span class="text-sm text-gray-700 w-full sm:w-48 font-semibold detail-label">Ketersediaan</span>
                        <span class="text-sm flex-1 
                            {{ isset($kendaraan) && strtoupper($kendaraan->status_ketersediaan) == 'TERSEDIA' ? 'text-green-600' : 'text-red-600' }}">
                            {{ isset($kendaraan) && $kendaraan->status_ketersediaan ? ucwords(strtolower($kendaraan->status_ketersediaan)) : '-' }}
                        </span>
                    </div>
                                                
                </div>
            </div>

            <div class="bg-white border border-gray-200 p-3 md:p-4 rounded-lg shadow-sm detail-container">
                <h2 class="text-lg md:text-xl font-semibold text-gray-900">Informasi Kondisi Fisik</h2>
                <div class="space-y-2 md:space-y-3 mt-3">
                    <div class="flex flex-col sm:flex-row sm:items-start detail-item">
                        <span class="text-sm text-gray-700 w-full sm:w-48 font-semibold detail-label">Mesin</span>
                        <span class="text-sm text-gray-700 flex-1">{{ isset($cekFisik) && isset($cekFisik->mesin) ? $cekFisik->mesin : '-' }}</span>
                    </div>
                    
                    <div class="flex flex-col sm:flex-row sm:items-start detail-item">
                        <span class="text-sm text-gray-700 w-full sm:w-48 font-semibold detail-label">Accu</span>
                        <span class="text-sm text-gray-700 flex-1">{{ isset($cekFisik) && isset($cekFisik->accu) ? $cekFisik->accu : '-' }}</span>
                    </div>
                    
                    <div class="flex flex-col sm:flex-row sm:items-start detail-item">
                        <span class="text-sm text-gray-700 w-full sm:w-48 font-semibold detail-label">Air Radiator</span>
                        <span class="text-sm text-gray-700 flex-1">{{ isset($cekFisik) && isset($cekFisik->air_radiator) ? $cekFisik->air_radiator : '-' }}</span>
                    </div>
                    
                    <div class="flex flex-col sm:flex-row sm:items-start detail-item">
                        <span class="text-sm text-gray-700 w-full sm:w-48 font-semibold detail-label">Air Wiper</span>
                        <span class="text-sm text-gray-700 flex-1">{{ isset($cekFisik) && isset($cekFisik->air_wiper) ? $cekFisik->air_wiper : '-' }}</span>
                    </div>
                    
                    <div class="flex flex-col sm:flex-row sm:items-start detail-item">
                        <span class="text-sm text-gray-700 w-full sm:w-48 font-semibold detail-label">Body</span>
                        <span class="text-sm text-gray-700 flex-1">{{ isset($cekFisik) && isset($cekFisik->body) ? $cekFisik->body : '-' }}</span>
                    </div>
                    
                    <div class="flex flex-col sm:flex-row sm:items-start detail-item">
                        <span class="text-sm text-gray-700 w-full sm:w-48 font-semibold detail-label">Ban</span>
                        <span class="text-sm text-gray-700 flex-1">{{ isset($cekFisik) && isset($cekFisik->ban) ? $cekFisik->ban : '-' }}</span>
                    </div>
                    
                    <div class="flex flex-col sm:flex-row sm:items-start detail-item">
                        <span class="text-sm text-gray-700 w-full sm:w-48 font-semibold detail-label">Pengharum</span>
                        <span class="text-sm text-gray-700 flex-1">{{ isset($cekFisik) && isset($cekFisik->pengharum) ? $cekFisik->pengharum : '-' }}</span>
                    </div>
                        
                    <div class="flex flex-col sm:flex-row sm:items-start detail-item">
                        <span class="text-sm text-gray-700 w-full sm:w-48 font-semibold detail-label">Kondisi Keseluruhan</span>
                        <span class="text-sm text-gray-700 flex-1">{{ isset($cekFisik) && isset($cekFisik->kondisi_keseluruhan) ? $cekFisik->kondisi_keseluruhan : '-' }}</span>
                    </div>   
                        
                    <div class="flex flex-col sm:flex-row sm:items-start detail-item">
                        <span class="text-sm text-gray-700 w-full sm:w-48 font-semibold detail-label">Catatan</span>
                        <span class="text-sm text-gray-700 flex-1">{{ isset($cekFisik) && isset($cekFisik->catatan) ? $cekFisik->catatan : '-' }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
