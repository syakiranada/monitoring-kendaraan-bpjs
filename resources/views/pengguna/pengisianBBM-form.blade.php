<x-app-layout>
    <a href="{{  url()->previous()  }}" class="flex items-center text-blue-600 font-semibold hover:underline mb-5">
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"></path>
        </svg>
        Kembali
    </a>
        <div class="flex justify-center">
            <div class="w-full max-w-lg bg-white p-8 rounded shadow-md">
                <h1 class="text-3xl font-bold mb-6 text-center">Form Pengisian BBM Kendaraan</h1>
                <form id="bbmForm" action="{{ route('pengisianBBM.store') }}" method="POST">
                    @csrf
                    <div class="mb-4">
                        <label class="block text-gray-700">Merek dan Tipe Kendaraan</label>
                        <input type="text" name="merk_tipe" class="w-full p-2 border border-gray-300 rounded bg-gray-100" readonly value="{{ request('merk') . ' ' . request('tipe') }}">
                        <input type="hidden" name="id_kendaraan" value="{{ request('id_kendaraan') }}">
                        <input type="hidden" id="id_peminjaman" name="id_peminjaman" value="{{ request('id_peminjaman') }}">
                    </div>
                    <div class="mb-4">
                        <label class="block text-gray-700">Nomor Plat</label>
                        <input type="text" class="w-full p-2 border border-gray-300 rounded bg-gray-100" readonly value="{{ request('plat') }}">
                    </div>
                    <div class="mb-4">
                        <label class="block text-gray-700">Jenis BBM</label>
                        <select name="jenis_bbm" class="w-full p-2 border border-gray-300 rounded" required>
                            <option value="" disabled selected>Pilih Jenis BBM</option>
                            <option value="Pertalite">Pertalite</option>
                            <option value="Pertamax">Pertamax</option>
                            <option value="Pertamax Turbo">Pertamax Turbo</option>
                            <option value="Dexlite">Dexlite</option>
                            <option value="Pertamina Dex">Pertamina Dex</option>
                            <option value="Solar">Solar</option>
                            <option value="Bio Solar">Bio Solar</option>
                        </select>
                    </div>
                    <div class="grid gap-4 mb-4">
                    @php
                        $tglMulai = optional($peminjaman)->tgl_mulai;
                        $tglSelesai = optional($peminjaman)->tgl_selesai;
                    @endphp

                        <div>
                            <label class="block text-gray-700">Tanggal Pengisian BBM</label>
                            <input 
                            type="date" 
                            name="tgl_isi" 
                            class="w-full p-2 border border-gray-300 rounded" 
                            required
                            @if($tglMulai) min="{{ \Carbon\Carbon::parse($tglMulai)->format('Y-m-d') }}" @endif
                            @if($tglSelesai) max="{{ \Carbon\Carbon::parse($tglSelesai)->format('Y-m-d') }}" @endif
                        >
                        <small class="text-gray-500 text-sm">
                            Pilih tanggal antara {{ \Carbon\Carbon::parse($tglMulai)->format('d M Y') }} dan {{ \Carbon\Carbon::parse($tglSelesai)->format('d M Y') }}.
                        </small>
                        </div>
{{--  
                        <div>
                            <label class="block text-gray-700">Jumlah Pembayaran</label>
                            <input type="text" id="hargaInput" name="harga" class="w-full p-2 border border-gray-300 rounded" required>
                        </div>  --}}
                    </div>
                    <div class="mb-4">
                        <label class="block text-gray-700">Nominal</label>
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500">Rp</span>
                            <input type="text" 
                                   id="nominalInput" 
                                   name="nominal" 
                                   class="w-full pl-10 p-2 border border-gray-300 rounded" 
                                   required>
                        </div>
                    </div>   
                    <div class="flex justify-end">
                        {{--  <a href="{{ url()->previous() }}" class="bg-gray-500 text-white px-4 py-2 rounded">Kembali</a>  --}}
                        <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('bbmForm');
            if (form) {
                // Menambahkan fungsi validasi ukuran file
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
        
                form.addEventListener('submit', function(event) {
                    event.preventDefault();
                    
                    // Validasi ukuran file
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
                    
                    // Konfirmasi simpan data
                    Swal.fire({
                        title: "Konfirmasi",
                        text: "Apakah Anda yakin ingin menyimpan data pengisian BBM ini?",
                        icon: "warning",
                        showCancelButton: true,
                        reverseButtons: true,
                        confirmButtonColor: "#3085d6",
                        cancelButtonColor: "#d33",
                        confirmButtonText: "Ya, Simpan!",
                        cancelButtonText: "Batal"
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // Tampilkan loading
                            {{--  Swal.fire({
                                title: "Memproses...",
                                text: "Mohon tunggu sebentar",
                                allowOutsideClick: false,
                                didOpen: () => {
                                    Swal.showLoading();
                                }
                            });  --}}
                            
                            // Menggunakan AJAX untuk submit form
                            const formData = new FormData(form);
                            
                            // Tambahkan parameter ajax=true untuk memudahkan deteksi di server
                            formData.append('ajax', 'true');
                            
                            fetch(form.action, {
                                method: form.method,
                                body: formData,
                                headers: {
                                    'X-Requested-With': 'XMLHttpRequest'
                                }
                            })
                            .then(response => {
                                // Anggap saja berhasil jika status 2xx, bahkan jika bukan JSON
                                if (response.ok) {
                                    return { status: 'success' };
                                } else {
                                    throw new Error('Server error');
                                }
                            })
                            .then(data => {
                                // Notifikasi sukses
                                Swal.fire({
                                    title: "Berhasil!",
                                    text: "Data pengisian BBM berhasil disimpan",
                                    icon: "success",
                                    confirmButtonColor: "#3085d6",
                                    confirmButtonText: "OK"
                                }).then(() => {
                                    // Redirect ke halaman admin.pengisianBBM SETELAH klik OK
                                    window.location.href = '/pengisianBBM';
                                });
                            })
                            .catch(error => {
                                console.error('Error:', error);
                                // Notifikasi gagal
                                Swal.fire({
                                    title: "Gagal!",
                                    text: "Terjadi kesalahan saat menyimpan data",
                                    icon: "error",
                                    confirmButtonColor: "#d33",
                                    confirmButtonText: "OK"
                                });
                            });
                        }
                    });
                });
            } else {
                console.error('Form dengan id bbmForm tidak ditemukan');
            }
        });
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Cek apakah ada flash message success
            <?php if (session('success')): ?>
                Swal.fire({
                    title: "Berhasil!",
                    text: "<?php echo session('success'); ?>",
                    icon: "success",
                    confirmButtonColor: "#3085d6",
                    confirmButtonText: "OK"
                });
            <?php endif; ?>
            
            // Cek apakah ada flash message error
            <?php if (session('error')): ?>
                Swal.fire({
                    title: "Gagal!",
                    text: "<?php echo session('error'); ?>",
                    icon: "error",
                    confirmButtonColor: "#d33",
                    confirmButtonText: "OK"
                });
            <?php endif; ?>
        });

            document.getElementById('nominalInput').addEventListener('input', function (e) {
                let value = e.target.value.replace(/\D/g, '');
                e.target.value = value.replace(/\B(?=(\d{3})+(?!\d))/g, ".");
            });

            {{--  document.getElementById('hargaInput').addEventListener('input', function (e) {
                let value = e.target.value.replace(/\D/g, '');
                e.target.value = value.replace(/\B(?=(\d{3})+(?!\d))/g, ".");
            });  --}}
        </script>
</x-app-layout>
