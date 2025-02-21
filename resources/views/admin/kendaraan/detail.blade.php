<x-app-layout>
{{-- @extends('layouts.sidebar')
@section('content') --}}
            @php 
                $currentPage = request()->query('page');
            @endphp
            <input type="hidden" name="current_page" value="{{ $currentPage }}">

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="space-y-3 bg-white border border-gray-200 p-4 rounded-lg shadow-sm">
            <h2 class="text-xl font-semibold text-gray-900">Detail Kendaraan</h2>
            <div class="space-y-3">
                <div class="flex items-start">
                    <span class="text-sm text-gray-600 w-48">Merk</span>
                    <span class="text-sm text-gray-900 flex-1">{{ $kendaraan->merk ?? '-' }}</span>
                </div>
                <div class="flex items-start">
                    <span class="text-sm text-gray-600 w-48">Tipe</span>
                    <span class="text-sm text-gray-900 flex-1">{{ $kendaraan->tipe ?? '-' }}</span>
                </div>
                <div class="flex items-start">
                    <span class="text-sm text-gray-600 w-48">Plat Nomor</span>
                    <span class="text-sm text-gray-900 flex-1">{{ $kendaraan->plat_nomor ?? '-' }}</span>
                </div>
                <div class="flex items-start">
                    <span class="text-sm text-gray-600 w-48">Warna</span>
                    <span class="text-sm text-gray-900 flex-1">{{ $kendaraan->warna ?? '-' }}</span>
                </div>
                <div class="flex items-start">
                    <span class="text-sm text-gray-600 w-48">Jenis Kendaraan</span>
                    <span class="text-sm text-gray-900 flex-1">{{ $kendaraan->jenis ?? '-' }}</span>
                </div>
                <div class="flex items-start">
                    <span class="text-sm text-gray-600 w-48">Status Aset</span>
                    <span class="text-sm text-gray-900 flex-1">{{ $kendaraan->aset ?? '-' }}</span>
                </div>
                <div class="flex items-start">
                    <span class="text-sm text-gray-600 w-48">Kapasitas Penumpang</span>
                    <span class="text-sm text-gray-900 flex-1">{{ $kendaraan->kapasitas ?? '-' }}</span>
                </div>
                <div class="flex items-start">
                    <span class="text-sm text-gray-600 w-48">Tanggal Pembelian</span>
                    <span class="text-sm text-gray-900 flex-1">{{ $kendaraan->tgl_pembelian ? \Carbon\Carbon::parse($kendaraan->tgl_pembelian)->format('d-m-Y') : '-' }}</span>
                </div>
                <div class="flex items-start">
                    <span class="text-sm text-gray-600 w-48">Nilai Perolehan</span>
                    <span class="text-sm text-gray-900 flex-1">{{ $kendaraan->nilai_perolehan ? 'Rp ' . number_format($kendaraan->nilai_perolehan, 0, ',', '.') : '-' }}</span>
                </div>
                <div class="flex items-start">
                    <span class="text-sm text-gray-600 w-48">Nilai Buku</span>
                    <span class="text-sm text-gray-900 flex-1">{{ $kendaraan->nilai_buku ? 'Rp ' . number_format($kendaraan->nilai_buku, 0, ',', '.') : '-' }}</span>
                </div>
                <div class="flex items-start">
                    <span class="text-sm text-gray-600 w-48">Bahan Bakar</span>
                    <span class="text-sm text-gray-900 flex-1">{{ $kendaraan->bahan_bakar ?? '-' }}</span>
                </div>
                <div class="flex items-start">
                    <span class="text-sm text-gray-600 w-48">Nomor Mesin</span>
                    <span class="text-sm text-gray-900 flex-1">{{ $kendaraan->no_mesin ?? '-' }}</span>
                </div>
                <div class="flex items-start">
                    <span class="text-sm text-gray-600 w-48">Nomor Rangka</span>
                    <span class="text-sm text-gray-900 flex-1">{{ $kendaraan->no_rangka ?? '-' }}</span>
                </div>
                <div class="flex items-start">
                    <span class="text-sm text-gray-600 w-48">Frekuensi Servis</span>
                    <span class="text-sm text-gray-900 flex-1">{{ $kendaraan->frekuensi_servis ? $kendaraan->frekuensi_servis . ' Bulan' : '-' }}</span>
                </div>
                <div class="flex items-start">
                    <span class="text-sm text-gray-600 w-48">Tanggal Servis Rutin Terakhir</span>
                    <span class="text-sm text-gray-900 flex-1">
                        {{ isset($servisRutin) && $servisRutin->tgl_servis_real ? \Carbon\Carbon::parse($servisRutin->tgl_servis_real)->format('d-m-Y') : '-' }}
                    </span>
                </div>
                <div class="flex items-start">
                    <span class="text-sm text-gray-600 w-48">Tanggal Bayar Asuransi Terakhir</span>
                    <span class="text-sm text-gray-900 flex-1">
                        {{ isset($asuransi) && $asuransi->tgl_bayar ? \Carbon\Carbon::parse($asuransi->tgl_bayar)->format('d-m-Y') : '-' }}
                    </span>
                </div>
                <div class="flex items-start">
                    <span class="text-sm text-gray-600 w-48">Masa Perlindungan Awal</span>
                    <span class="text-sm text-gray-900 flex-1">
                        {{ isset($asuransi) && $asuransi->tgl_perlindungan_awal ? \Carbon\Carbon::parse($asuransi->tgl_perlindungan_awal)->format('d-m-Y') : '-' }}
                    </span>
                </div>
                <div class="flex items-start">
                    <span class="text-sm text-gray-600 w-48">Masa Perlindungan Akhir</span>
                    <span class="text-sm text-gray-900 flex-1">
                        {{ isset($asuransi) && $asuransi->tgl_perlindungan_akhir ? \Carbon\Carbon::parse($asuransi->tgl_perlindungan_akhir)->format('d-m-Y') : '-' }}
                    </span>
                </div>
                <div class="flex items-start">
                    <span class="text-sm text-gray-600 w-48">Tanggal Bayar Pajak Terakhir</span>
                    <span class="text-sm text-gray-900 flex-1">
                        {{ isset($pajak) && $pajak->tgl_bayar ? \Carbon\Carbon::parse($pajak->tgl_bayar)->format('d-m-Y') : '-' }}
                    </span>
                </div>
                <div class="flex items-start">
                    <span class="text-sm text-gray-600 w-48">Tanggal Jatuh Tempo Pajak Terakhir</span>
                    <span class="text-sm text-gray-900 flex-1">
                        {{ isset($pajak) && $pajak->tgl_jatuh_tempo ? \Carbon\Carbon::parse($pajak->tgl_jatuh_tempo)->format('d-m-Y') : '-' }}
                    </span>
                </div>
                <div class="flex items-start">
                    <span class="text-sm text-gray-600 w-48">Tanggal Pengisian BBM Terakhir</span>
                    <span class="text-sm text-gray-900 flex-1">
                        {{ isset($bbm) && $bbm->tgl_isi ? \Carbon\Carbon::parse($bbm->tgl_isi)->format('d-m-Y') : '-' }}
                    </span>
                </div>
                <div class="flex items-start">
                    <span class="text-sm text-gray-600 w-48">Tanggal Cek Fisik Terakhir</span>
                    <span class="text-sm text-gray-900 flex-1">
                        {{ isset($cekFisik) && $cekFisik->tgl_cek_fisik ? \Carbon\Carbon::parse($cekFisik->tgl_cek_fisik)->format('d-m-Y') : '-' }}
                    </span>
                </div>
                <div class="flex items-start">
                    <span class="text-sm text-gray-600 w-48">Ketersediaan</span>
                    <span class="text-sm flex-1 
                        {{ isset($kendaraan) && $kendaraan->status_ketersediaan == 'TERSEDIA' ? 'text-green-600' : 'text-red-600' }}">
                        {{ isset($kendaraan) && $kendaraan->status_ketersediaan ? ucwords(strtolower($kendaraan->status_ketersediaan)) : '-' }}
                    </span>
                </div>                              
            </div>
        </div>

        <div class="bg-white border border-gray-200 p-4 rounded-lg shadow-sm">
            <h3 class="text-lg font-semibold mb-4">Detail Kondisi Fisik</h3>
            <div class="space-y-3">
                <div class="flex items-start">
                    <span class="text-sm text-gray-600 w-48">Mesin</span>
                    <span class="text-sm text-gray-900 flex-1">{{ isset($cekFisik) && isset($cekFisik->mesin) ? $cekFisik->mesin : '-' }}</span>
                </div>
                
                <div class="flex items-start">
                    <span class="text-sm text-gray-600 w-48">Accu</span>
                    <span class="text-sm text-gray-900 flex-1">{{ isset($cekFisik) && isset($cekFisik->accu) ? $cekFisik->accu : '-' }}</span>
                </div>
                
                <div class="flex items-start">
                    <span class="text-sm text-gray-600 w-48">Air Radiator</span>
                    <span class="text-sm text-gray-900 flex-1">{{ isset($cekFisik) && isset($cekFisik->air_radiator) ? $cekFisik->air_radiator : '-' }}</span>
                </div>
                
                <div class="flex items-start">
                    <span class="text-sm text-gray-600 w-48">Air Wiper</span>
                    <span class="text-sm text-gray-900 flex-1">{{ isset($cekFisik) && isset($cekFisik->air_wiper) ? $cekFisik->air_wiper : '-' }}</span>
                </div>
                
                <div class="flex items-start">
                    <span class="text-sm text-gray-600 w-48">Body</span>
                    <span class="text-sm text-gray-900 flex-1">{{ isset($cekFisik) && isset($cekFisik->body) ? $cekFisik->body : '-' }}</span>
                </div>
                
                <div class="flex items-start">
                    <span class="text-sm text-gray-600 w-48">Ban</span>
                    <span class="text-sm text-gray-900 flex-1">{{ isset($cekFisik) && isset($cekFisik->ban) ? $cekFisik->ban : '-' }}</span>
                </div>
                
                <div class="flex items-start">
                    <span class="text-sm text-gray-600 w-48">Pengharum</span>
                    <span class="text-sm text-gray-900 flex-1">{{ isset($cekFisik) && isset($cekFisik->pengharum) ? $cekFisik->pengharum : '-' }}</span>
                </div>
                
                <div class="flex items-start">
                    <span class="text-sm text-gray-600 w-48">Kondisi Keseluruhan</span>
                    <span class="text-sm text-gray-900 flex-1">{{ isset($cekFisik) && isset($cekFisik->kondisi_keseluruhan) ? $cekFisik->kondisi_keseluruhan : '-' }}</span>
                </div>
                
                <div class="flex items-start">
                    <span class="text-sm text-gray-600 w-48">Catatan</span>
                    <span class="text-sm text-gray-900 flex-1">{{ isset($cekFisik) && isset($cekFisik->catatan) ? $cekFisik->catatan : '-' }}</span>
                </div>
            </div>
        </div>
            <button type="button" onclick="window.location.href='{{ route('kendaraan.daftar_kendaraan', ['page' => $currentPage]) }}'" class="bg-purple-600 text-white px-6 py-2.5 rounded-lg font-semibold hover:bg-purple-700 transition mt-6">
                Kembali
            </button>
        
            
</x-app-layout>
{{-- @endsection --}}
