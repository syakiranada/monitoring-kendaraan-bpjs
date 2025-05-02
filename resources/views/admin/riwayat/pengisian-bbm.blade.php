{{-- @extends('layouts.sidebar')

@section('content') --}}
<x-app-layout>
    <div class="p-6">
        <!-- Button Back -->
        <a href="{{ route('admin.riwayat.index') }}" class="flex items-center text-blue-600 font-semibold hover:underline mb-5">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"></path>
            </svg>
            Kembali
        </a> 
        <h2 class="text-2xl font-semibold text-gray-800 mb-4">Riwayat Pengisian BBM Kendaraan</h2>
        
        <!-- Filter Form -->
        <form action="{{ route('admin.riwayat.pengisian-bbm') }}" method="GET" class="space-y-4">
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">Kendaraan</label>
                    <select name="kendaraan" class="block w-full p-2 border rounded-lg">
                        <option value="">Semua Kendaraan</option>
                        @foreach ($kendaraan as $k)
                            <option value="{{ $k->plat_nomor }}" {{ request('kendaraan') == $k->plat_nomor ? 'selected' : '' }}>
                                {{ $k->merk }} {{ $k->tipe }} - {{ $k->plat_nomor }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Pengguna</label>
                    <select name="pengguna" class="block w-full p-2 border rounded-lg">
                        <option value="">Semua Pengguna</option>
                        @foreach ($penggunas as $pengguna)
                            <option value="{{ $pengguna->id }}" {{ request('pengguna') == $pengguna->id ? 'selected' : '' }}>
                                {{ $pengguna->name }}
                            </option>
                        @endforeach
                    </select>                    
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700">Tanggal Awal</label>
                    <input type="date" id="tgl_awal" name="tgl_awal" class="block w-full p-2 border rounded-lg" value="{{ request('tgl_awal') }}">
                    <p id="warning-tgl-awal" class="text-red-500 text-sm mt-1 hidden">Tanggal Awal harus diisi!</p>
                </div>
                
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700">Tanggal Akhir</label>
                    <input type="date" id="tgl_akhir" name="tgl_akhir" class="block w-full p-2 border rounded-lg" value="{{ request('tgl_akhir') }}">
                    <p id="warning-tgl-akhir" class="text-red-500 text-sm mt-1 hidden">Tanggal Akhir harus diisi setelah Tanggal Awal!</p>
                </div>
            </div>

            <div class="mt-4">
                <button id="submit" class="text-white bg-blue-700 hover:bg-blue-800 focus:outline-none focus:ring-4 focus:ring-blue-300 font-medium rounded-full text-sm px-5 py-2.5 text-center me-2 mb-2">Filter</button>
            </div>
        </form>


        <!-- Total Transaksi -->
        <div class="mt-4">
            <h3 class="text-xl font-semibold text-gray-800">Total Transaksi: Rp{{ number_format($totalTransaksi, 0, ',', '.') }}</h3>
        </div>

        <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
            <table class="w-full text-sm text-left rtl:text-right text-gray-500">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3">Merek & Tipe</th>
                        <th scope="col" class="px-6 py-3">Plat Nomor</th>
                        <th scope="col" class="px-6 py-3">Tanggal Pengisian</th>
                        <th scope="col" class="px-6 py-3">Biaya</th>
                        {{-- <th scope="col" class="px-6 py-3">User Input</th> --}}
                        <th scope="col" class="px-6 py-3">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($riwayatBBM as $item)
                        <tr class="bg-white border-b hover:bg-gray-50">
                            <td class="px-6 py-4">{{ $item->kendaraan->merk }} {{ $item->kendaraan->tipe }}</td>
                            <td class="px-6 py-4">{{ $item->kendaraan->plat_nomor }}</td>
                            <td class="px-6 py-4">{{ $item->tgl_isi ? \Carbon\Carbon::parse($item->tgl_isi)->format('d-m-Y') : '-' }}</td>
                            <td class="px-6 py-4">Rp{{ number_format($item->nominal, 0, ',', '.') }}</td>
                            {{-- <td class="px-6 py-4">{{ $item->user->name }}</td> --}}
                            <td class="px-6 py-4">
                                {{-- <a href="{{ route('admin.riwayat.detail-pengisian-bbm', ['id' => $item->id_bbm, 'page' => request()->query('page', 1), 'search' => request()->query('search')]) }}" class="text-blue-600 hover:underline">
                                    Detail
                                </a> --}}
                                <a href="{{ route('admin.riwayat.detail-pengisian-bbm', [
                                    'id' => $item->id_bbm,
                                    'kendaraan' => request('kendaraan'),
                                    'pengguna' => request('pengguna'),
                                    'tgl_awal' => request('tgl_awal'),
                                    'tgl_akhir' => request('tgl_akhir'),
                                    'page' => request()->query('page', 1)
                                ]) }}" class="font-medium text-blue-600 hover:underline">
                                    Detail
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center px-6 py-4">Tidak ada riwayat pengisian BBM.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="mt-4">
            {{ $riwayatBBM->appends(request()->query())->links() }}
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $(document).ready(function () {
            const tglAwal = $('#tgl_awal');
            const tglAkhir = $('#tgl_akhir');
            const warningAwal = $('#warning-tgl-awal');
            const warningAkhir = $('#warning-tgl-akhir');

            function showWarning(element, warningElement, show) {
                if (show) {
                    warningElement.removeClass('hidden');
                    element.addClass('border-red-500');
                } else {
                    warningElement.addClass('hidden');
                    element.removeClass('border-red-500');
                }
            }

            function validateDateInputs() {
                let valid = true;
                const awal = tglAwal.val();
                const akhir = tglAkhir.val();

                // Cek tanggal awal
                if (!awal) {
                    showWarning(tglAwal, warningAwal, true);
                    valid = false;
                } else {
                    showWarning(tglAwal, warningAwal, false);
                }

                // Cek tanggal akhir
                if (!akhir || (awal && akhir < awal)) {
                    showWarning(tglAkhir, warningAkhir, true);
                    valid = false;
                } else {
                    showWarning(tglAkhir, warningAkhir, false);
                }

                return valid;
            }

            tglAwal.on('change', function () {
                // Set minimum tanggal akhir
                tglAkhir.attr('min', tglAwal.val());

                // Kalau akhir sudah dipilih tapi lebih kecil, kosongkan
                if (tglAkhir.val() && tglAkhir.val() < tglAwal.val()) {
                    tglAkhir.val('');
                }

                validateDateInputs();
            });

            tglAkhir.on('change', function () {
                validateDateInputs();
            });

            // $('#filter-btn').on('click', function (e) {
            //     e.preventDefault();

            //     if (!validateDateInputs()) {
            //         return;
            //     }

            //     // Kalau valid, kirim AJAX
            //     $.ajax({
            //         url: '{{ route('admin.riwayat.pengisian-bbm') }}',
            //         method: 'GET',
            //         data: {
            //             tgl_awal: tglAwal.val(),
            //             tgl_akhir: tglAkhir.val()
            //         },
            //         success: function (response) {
            //             // misal update tabel/filter hasil disini
            //             console.log(response);
            //             // $('#your-table').html(response.html); 
            //         },
            //         error: function () {
            //             Swal.fire({
            //                 icon: 'error',
            //                 title: 'Error',
            //                 text: 'Gagal memfilter data. Coba lagi.'
            //             });
            //         }
            //     });
            // });
        });

        // const tglAwal = document.getElementById('tgl_awal');
        // const tglAkhir = document.getElementById('tgl_akhir');

        // tglAwal.addEventListener('change', () => {
        //     tglAkhir.min = tglAwal.value;
        //     if (tglAkhir.value && tglAkhir.value < tglAwal.value) {
        //         tglAkhir.value = '';
        //     }
        // });

        // function validateDateRange() {
        //     if (tglAwal.value && tglAkhir.value && tglAkhir.value < tglAwal.value) {
        //         Swal.fire({
        //             icon: 'warning',
        //             title: 'Tanggal Tidak Valid',
        //             text: 'Tanggal Akhir tidak boleh lebih awal dari Tanggal Awal!',
        //             confirmButtonText: 'OK'
        //         });
        //         return false;
        //     }
        //     return true;
        // }
    </script>
</x-app-layout>
{{-- @endsection --}}
