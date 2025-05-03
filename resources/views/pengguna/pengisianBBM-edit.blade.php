<x-app-layout>
    <a href="{{  url()->previous()  }}" class="flex items-center text-blue-600 font-semibold hover:underline mb-5">
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"></path>
        </svg>
        Kembali
    </a>
        <div class="flex justify-center">
            <div class="w-full max-w-lg bg-white p-8 rounded shadow-md">
                <h1 class="text-3xl font-bold mb-6 text-center">Edit Pengisian BBM Kendaraan</h1>
                <form id="bbmForm" action="{{ route('pengisianBBM.update', $bbm->id_bbm) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="mb-4">
                        <label class="block text-gray-700">Merek dan Tipe Kendaraan</label>
                        <input type="text" class="w-full p-2 border border-gray-300 rounded bg-gray-100" readonly value="{{ $bbm->kendaraan->merk . ' ' . $bbm->kendaraan->tipe }}">
                    </div>
                    <div class="mb-4">
                        <label class="block text-gray-700">Nomor Plat</label>
                        <input type="text" class="w-full p-2 border border-gray-300 rounded bg-gray-100" readonly value="{{ $bbm->kendaraan->plat_nomor }}">
                    </div>
                    <div class="mb-4">
                        <label class="block text-gray-700">Jenis BBM</label>
                        <select name="jenis_bbm" class="w-full p-2 border border-gray-300 rounded" required>
                            <option value="Pertalite" {{ $bbm->jenis_bbm == 'Pertalite' ? 'selected' : '' }}>Pertalite</option>
                            <option value="Pertamax" {{ $bbm->jenis_bbm == 'Pertamax' ? 'selected' : '' }}>Pertamax</option>
                            <option value="Pertamax Turbo" {{ $bbm->jenis_bbm == 'Pertamax Turbo' ? 'selected' : '' }}>Pertamax Turbo</option>
                            <option value="Dexlite" {{ $bbm->jenis_bbm == 'Dexlite' ? 'selected' : '' }}>Dexlite</option>
                            <option value="Pertamina Dex" {{ $bbm->jenis_bbm == 'Pertamina Dex' ? 'selected' : '' }}>Pertamina Dex</option>
                            <option value="Solar" {{ $bbm->jenis_bbm == 'Solar' ? 'selected' : '' }}>Solar</option>
                            <option value="Bio Solar" {{ $bbm->jenis_bbm == 'Bio Solar' ? 'selected' : '' }}>Bio Solar</option>
                        </select>
                    </div>
                    <div class="gap-4 mb-4">
                        @php
                        $tglMulai = optional($bbm->peminjaman)->tgl_mulai;
                        $tglSelesai = optional($bbm->peminjaman)->tgl_selesai;
                    @endphp
                        <div>
                        <label class="block text-gray-700">Tanggal Pengisian BBM</label>

                        <input 
                            type="date" 
                            name="tgl_isi" 
                            class="w-full p-2 border border-gray-300 rounded" 
                            value="{{ $bbm->tgl_isi }}"
                            required
                            @if($tglMulai) min="{{ \Carbon\Carbon::parse($tglMulai)->format('Y-m-d') }}" @endif
                            @if($tglSelesai) max="{{ \Carbon\Carbon::parse($tglSelesai)->format('Y-m-d') }}" @endif
                        >
                        <small class="text-gray-500 text-sm">
                            Pilih tanggal antara {{ \Carbon\Carbon::parse($tglMulai)->format('d M Y') }} dan {{ \Carbon\Carbon::parse($tglSelesai)->format('d M Y') }}.
                        </small>
                        </div>
                    </div>
                    <div class="mb-4">
                        <label class="block text-gray-700">Nominal</label>
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500">Rp</span>
                            <input type="text" 
                                   id="nominalInput" 
                                   name="nominal" 
                                   class="w-full pl-10 p-2 border border-gray-300 rounded" 
                                   value="{{ number_format($bbm->nominal, 0, '', '.') }}"
                                   required>
                        </div>
                    </div>   
                    <div class="flex justify-end">
                        {{--  <a href="{{ route('pengisianBBM') }}" class="bg-gray-500 text-white px-4 py-2 rounded">Kembali</a>  --}}
                        <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>

        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const form = document.getElementById('bbmForm');
                if (form) {
                    form.addEventListener('submit', function(event) {
                        event.preventDefault(); // Mencegah form dikirim langsung
            
                        // Validasi ukuran file
                        function validateFileSize() {
                            const fileInput = form.querySelector('input[type="file"]');
                            if (fileInput && fileInput.files.length > 0) {
                                const maxSize = 2 * 1024 * 1024;
                                if (fileInput.files[0].size > maxSize) {
                                    return false;
                                }
                            }
                            return true;
                        }
            
                        if (!validateFileSize()) {
                            Swal.fire({
                                title: "Gagal!",
                                text: "Ukuran file tidak boleh melebihi 2MB",
                                icon: "error",
                                confirmButtonColor: "#d33",
                                confirmButtonText: "OK"
                            });
                            return;
                        }
            
                        // Tampilkan konfirmasi
                        Swal.fire({
                            title: "Konfirmasi",
                            text: "Apakah Anda yakin ingin mengubah data pengisian BBM ini?",
                            icon: "warning",
                            showCancelButton: true,
                            confirmButtonColor: "#3085d6",
                            cancelButtonColor: "#d33",
                            confirmButtonText: "Ya, Ubah!",
                            cancelButtonText: "Batal"
                        }).then((result) => {
                            if (result.isConfirmed) {
                                // Tampilkan loading
                                Swal.fire({
                                    title: "Memproses...",
                                    text: "Mohon tunggu sebentar",
                                    allowOutsideClick: false,
                                    didOpen: () => {
                                        Swal.showLoading();
                                    }
                                });
            
                                const formData = new FormData(form);
                                formData.append('_method', 'PUT');
            
                                // Ambil CSRF token
                                const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                                formData.append('_token', csrfToken);
            
                                fetch(form.action, {
                                    method: 'POST', // Laravel mengenali _method: 'PUT'
                                    body: formData,
                                    headers: {
                                        'X-Requested-With': 'XMLHttpRequest'
                                    }
                                })
                                .then(response => response.json())
                                .then(data => {
                                    if (data.errors) {
                                        Swal.fire({
                                            title: "Gagal!",
                                            text: Object.values(data.errors).flat().join('\n'),
                                            icon: "error",
                                            confirmButtonColor: "#d33",
                                            confirmButtonText: "OK"
                                        });
                                    } else {
                                        Swal.fire({
                                            title: "Berhasil!",
                                            text: "Data pengisian BBM berhasil diperbarui",
                                            icon: "success",
                                            confirmButtonColor: "#3085d6",
                                            confirmButtonText: "OK"
                                        }).then(() => {
                                            window.location.href = '/pengisianBBM';
                                        });
                                    }
                                })
                                .catch(error => {
                                    Swal.fire({
                                        title: "Gagal!",
                                        text: "Terjadi kesalahan saat memperbarui data",
                                        icon: "error",
                                        confirmButtonColor: "#d33",
                                        confirmButtonText: "OK"
                                    });
                                    console.error('Error:', error);
                                });
                            }
                        });
                    });
                }
            });            
        </script>

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                @if(session('success'))
                    Swal.fire({
                        title: "Berhasil!",
                        text: "{{ session('success') }}",
                        icon: "success",
                        confirmButtonColor: "#3085d6",
                        confirmButtonText: "OK"
                    });
                @endif
            
                @if(session('error'))
                    Swal.fire({
                        title: "Gagal!",
                        text: "{{ session('error') }}",
                        icon: "error",
                        confirmButtonColor: "#d33",
                        confirmButtonText: "OK"
                    });
                @endif
            });

            document.getElementById('nominalInput').addEventListener('input', function (e) {
                let value = e.target.value.replace(/\D/g, '');
                e.target.value = value.replace(/\B(?=(\d{3})+(?!\d))/g, ".");
            });
        </script>
</x-app-layout>