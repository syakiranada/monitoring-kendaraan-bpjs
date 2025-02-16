{{-- resources/views/admin/dashboard.blade.php --}}
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Beranda Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="max-w-6xl mx-auto p-6">
        <h1 class="text-3xl font-bold text-gray-800 mb-6">Selamat Datang, {{ $user->name }}!</h1>

        {{-- 🚨 Alert Kendaraan Terlambat --}}
@if(count($peminjamanTerlambat) > 0)
<div class="p-4 mb-6 text-red-800 border border-red-300 rounded-lg bg-red-50" role="alert">
    <div class="flex items-center">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="size-5">
            <path d="M4.214 3.227a.75.75 0 0 0-1.156-.955 8.97 8.97 0 0 0-1.856 3.825.75.75 0 0 0 1.466.316 7.47 7.47 0 0 1 1.546-3.186ZM16.942 2.272a.75.75 0 0 0-1.157.955 7.47 7.47 0 0 1 1.547 3.186.75.75 0 0 0 1.466-.316 8.971 8.971 0 0 0-1.856-3.825Z" />
            <path fill-rule="evenodd" d="M10 2a6 6 0 0 0-6 6c0 1.887-.454 3.665-1.257 5.234a.75.75 0 0 0 .515 1.076 32.91 32.91 0 0 0 3.256.508 3.5 3.5 0 0 0 6.972 0 32.903 32.903 0 0 0 3.256-.508.75.75 0 0 0 .515-1.076A11.448 11.448 0 0 1 16 8a6 6 0 0 0-6-6Zm0 14.5a2 2 0 0 1-1.95-1.557 33.54 33.54 0 0 0 3.9 0A2 2 0 0 1 10 16.5Z" clip-rule="evenodd" />
          </svg>
                   
        <h3 class="text-lg font-semibold"> Pengembalian Kendaraan Dinas Terlambat!</h3>
    </div>
    <ul class="list-disc pl-6 mt-2">
        @foreach($peminjamanTerlambat as $pinjam)
        <li>
            <span class="font-semibold">{{ $pinjam->user->name }}</span> telah melewati batas waktu pengembalian kendaraan dinas 
            <span class="font-semibold">{{ $pinjam->kendaraan->merk }} {{ $pinjam->kendaraan->tipe }}</span>
            ({{ $pinjam->kendaraan->plat_nomor }})
        </li>
        @endforeach
    </ul>
</div>
@endif

@if(count($butuhCekFisik) > 0) 
<div class="p-4 mb-6 text-yellow-800 border border-yellow-300 rounded-lg bg-yellow-50" role="alert">
    <div class="flex items-center">
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="size-5">
            <path fill-rule="evenodd" d="M8.485 2.495c.673-1.167 2.357-1.167 3.03 0l6.28 10.875c.673 1.167-.17 2.625-1.516 2.625H3.72c-1.347 0-2.189-1.458-1.515-2.625L8.485 2.495ZM10 5a.75.75 0 0 1 .75.75v3.5a.75.75 0 0 1-1.5 0v-3.5A.75.75 0 0 1 10 5Zm0 9a1 1 0 1 0 0-2 1 1 0 0 0 0 2Z" clip-rule="evenodd" />
        </svg>
        <h3 class="text-lg font-semibold ml-2">Segera Lakukan Cek Fisik Kendaraan Dinas!</h3>
    </div>
    <ul class="list-disc pl-6 mt-3">
        @foreach($butuhCekFisik as $cek)
        <li>
            <span class="font-semibold">{{ $cek->kendaraan->merk ?? 'Merk Tidak Ada' }} 
            {{ $cek->kendaraan->tipe ?? 'Tipe Tidak Ada' }}</span>
            ({{ $cek->kendaraan->plat_nomor ?? 'Plat Tidak Ada' }})
        </li>
        @endforeach
    </ul>
</div>
@endif



<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="size-5">
    <path d="M5.25 12a.75.75 0 0 1 .75-.75h.01a.75.75 0 0 1 .75.75v.01a.75.75 0 0 1-.75.75H6a.75.75 0 0 1-.75-.75V12ZM6 13.25a.75.75 0 0 0-.75.75v.01c0 .414.336.75.75.75h.01a.75.75 0 0 0 .75-.75V14a.75.75 0 0 0-.75-.75H6ZM7.25 12a.75.75 0 0 1 .75-.75h.01a.75.75 0 0 1 .75.75v.01a.75.75 0 0 1-.75.75H8a.75.75 0 0 1-.75-.75V12ZM8 13.25a.75.75 0 0 0-.75.75v.01c0 .414.336.75.75.75h.01a.75.75 0 0 0 .75-.75V14a.75.75 0 0 0-.75-.75H8ZM9.25 10a.75.75 0 0 1 .75-.75h.01a.75.75 0 0 1 .75.75v.01a.75.75 0 0 1-.75.75H10a.75.75 0 0 1-.75-.75V10ZM10 11.25a.75.75 0 0 0-.75.75v.01c0 .414.336.75.75.75h.01a.75.75 0 0 0 .75-.75V12a.75.75 0 0 0-.75-.75H10ZM9.25 14a.75.75 0 0 1 .75-.75h.01a.75.75 0 0 1 .75.75v.01a.75.75 0 0 1-.75.75H10a.75.75 0 0 1-.75-.75V14ZM12 9.25a.75.75 0 0 0-.75.75v.01c0 .414.336.75.75.75h.01a.75.75 0 0 0 .75-.75V10a.75.75 0 0 0-.75-.75H12ZM11.25 12a.75.75 0 0 1 .75-.75h.01a.75.75 0 0 1 .75.75v.01a.75.75 0 0 1-.75.75H12a.75.75 0 0 1-.75-.75V12ZM12 13.25a.75.75 0 0 0-.75.75v.01c0 .414.336.75.75.75h.01a.75.75 0 0 0 .75-.75V14a.75.75 0 0 0-.75-.75H12ZM13.25 10a.75.75 0 0 1 .75-.75h.01a.75.75 0 0 1 .75.75v.01a.75.75 0 0 1-.75.75H14a.75.75 0 0 1-.75-.75V10ZM14 11.25a.75.75 0 0 0-.75.75v.01c0 .414.336.75.75.75h.01a.75.75 0 0 0 .75-.75V12a.75.75 0 0 0-.75-.75H14Z" />
    <path fill-rule="evenodd" d="M5.75 2a.75.75 0 0 1 .75.75V4h7V2.75a.75.75 0 0 1 1.5 0V4h.25A2.75 2.75 0 0 1 18 6.75v8.5A2.75 2.75 0 0 1 15.25 18H4.75A2.75 2.75 0 0 1 2 15.25v-8.5A2.75 2.75 0 0 1 4.75 4H5V2.75A.75.75 0 0 1 5.75 2Zm-1 5.5c-.69 0-1.25.56-1.25 1.25v6.5c0 .69.56 1.25 1.25 1.25h10.5c.69 0 1.25-.56 1.25-1.25v-6.5c0-.69-.56-1.25-1.25-1.25H4.75Z" clip-rule="evenodd" />
  </svg> <h2 class="text-xl font-semibold mb-4"> Jatuh Tempo (1 Bulan Kedepan)</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
            {{-- Pajak Card --}}
            <div class="p-6 bg-white border border-gray-200 rounded-lg shadow-sm hover:bg-gray-50">
                <div class="flex items-center mb-3">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="size-5">
                        <path fill-rule="evenodd" d="M1 4a1 1 0 0 1 1-1h16a1 1 0 0 1 1 1v8a1 1 0 0 1-1 1H2a1 1 0 0 1-1-1V4Zm12 4a3 3 0 1 1-6 0 3 3 0 0 1 6 0ZM4 9a1 1 0 1 0 0-2 1 1 0 0 0 0 2Zm13-1a1 1 0 1 1-2 0 1 1 0 0 1 2 0ZM1.75 14.5a.75.75 0 0 0 0 1.5c4.417 0 8.693.603 12.749 1.73 1.111.309 2.251-.512 2.251-1.696v-.784a.75.75 0 0 0-1.5 0v.784a.272.272 0 0 1-.35.25A49.043 49.043 0 0 0 1.75 14.5Z" clip-rule="evenodd" />
                      </svg> 
                    <h3 class="text-xl font-bold text-gray-900">Pajak Kendaraan</h3>
                </div>
                <div class="space-y-2">
                    @forelse($batasWaktu->where('tipe', 'Pajak') as $deadline)
                    <p class="text-gray-700">
                        <span class="font-semibold">{{ $deadline['kendaraan']->merk }} {{ $deadline['kendaraan']->tipe }}</span>
                        <span class="text-sm">{{ $deadline['kendaraan']->plat_nomor }} - {{ \Carbon\Carbon::parse($deadline['batas_waktu'])->format('d-m-Y') }}</span>
                    </p>
                    @empty
                    <p class="text-gray-500">Tidak ada pajak yang akan jatuh tempo</p>
                    @endforelse
                </div>
            </div>

            {{-- Asuransi Card --}}
            <div class="p-6 bg-white border border-gray-200 rounded-lg shadow-sm hover:bg-gray-50">
                <div class="flex items-center mb-3">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="size-5">
                        <path fill-rule="evenodd" d="M9.661 2.237a.531.531 0 0 1 .678 0 11.947 11.947 0 0 0 7.078 2.749.5.5 0 0 1 .479.425c.069.52.104 1.05.104 1.59 0 5.162-3.26 9.563-7.834 11.256a.48.48 0 0 1-.332 0C5.26 16.564 2 12.163 2 7c0-.538.035-1.069.104-1.589a.5.5 0 0 1 .48-.425 11.947 11.947 0 0 0 7.077-2.75Zm4.196 5.954a.75.75 0 0 0-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 1 0-1.06 1.061l2.5 2.5a.75.75 0 0 0 1.137-.089l4-5.5Z" clip-rule="evenodd" />
                      </svg>
                    <h3 class="text-xl font-bold text-gray-900">Asuransi</h3>
                </div>
                <div class="space-y-2">
                    @forelse($batasWaktu->where('tipe', 'Asuransi') as $deadline)
                    <p class="text-gray-700">
                        <span class="font-semibold">{{ $deadline['kendaraan']->merk }} {{ $deadline['kendaraan']->tipe }}</span>
                        <br>
                        <span class="text-sm">{{ $deadline['kendaraan']->plat_nomor }} - {{ \Carbon\Carbon::parse($deadline['batas_waktu'])->format('d-m-Y') }}</span>
                    </p>
                    @empty
                    <p class="text-gray-500">Tidak ada asuransi yang akan jatuh tempo</p>
                    @endforelse
                </div>
            </div>

            {{-- Servis Rutin Card --}}
            <div class="p-6 bg-white border border-gray-200 rounded-lg shadow-sm hover:bg-gray-50">
                <div class="flex items-center mb-3">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="size-5">
                        <path fill-rule="evenodd" d="M14.5 10a4.5 4.5 0 0 0 4.284-5.882c-.105-.324-.51-.391-.752-.15L15.34 6.66a.454.454 0 0 1-.493.11 3.01 3.01 0 0 1-1.618-1.616.455.455 0 0 1 .11-.494l2.694-2.692c.24-.241.174-.647-.15-.752a4.5 4.5 0 0 0-5.873 4.575c.055.873-.128 1.808-.8 2.368l-7.23 6.024a2.724 2.724 0 1 0 3.837 3.837l6.024-7.23c.56-.672 1.495-.855 2.368-.8.096.007.193.01.291.01ZM5 16a1 1 0 1 1-2 0 1 1 0 0 1 2 0Z" clip-rule="evenodd" />
                        <path d="M14.5 11.5c.173 0 .345-.007.514-.022l3.754 3.754a2.5 2.5 0 0 1-3.536 3.536l-4.41-4.41 2.172-2.607c.052-.063.147-.138.342-.196.202-.06.469-.087.777-.067.128.008.257.012.387.012ZM6 4.586l2.33 2.33a.452.452 0 0 1-.08.09L6.8 8.214 4.586 6H3.309a.5.5 0 0 1-.447-.276l-1.7-3.402a.5.5 0 0 1 .093-.577l.49-.49a.5.5 0 0 1 .577-.094l3.402 1.7A.5.5 0 0 1 6 3.31v1.277Z" />
                      </svg>
                      
                    <h3 class="text-xl font-bold text-gray-900">Servis Rutin</h3>
                </div>
                <div class="space-y-2">
                    @forelse($batasWaktu->where('tipe', 'Servis') as $deadline)
                    <p class="text-gray-700">
                        <span class="font-semibold">{{ $deadline['kendaraan']->merk }} {{ $deadline['kendaraan']->tipe }}</span>
                        <br>
                        <span class="text-sm">{{ $deadline['kendaraan']->plat_nomor }} - {{ \Carbon\Carbon::parse($deadline['batas_waktu'])->format('d-m-Y') }}</span>
                    </p>
                    @empty
                    <p class="text-gray-500">Tidak ada jadwal servis dalam waktu dekat</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</body>
</html>