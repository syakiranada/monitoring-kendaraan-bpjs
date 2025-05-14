<x-app-layout>
    <a href="{{ route('admin.pengisianBBM') }}" class="flex items-center text-blue-600 font-semibold hover:underline mb-5">
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"></path>
        </svg>
        Kembali
    </a>
        <div class="flex justify-center">
            <div class="w-full max-w-lg bg-white p-8 rounded shadow-md">
                <h1 class="text-2xl font-bold mb-6 text-center">Form Pengisian BBM Kendaraan</h1>
                <form id="bbmForm" action="{{ route('admin.pengisianBBM.store') }}" method="POST">
                    @csrf
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">Merk dan Tipe Kendaraan</label>
                        <input type="text" name="merk_tipe" class="w-full p-2 border border-gray-300 rounded bg-gray-100" readonly value="{{ request('merk') . ' ' . request('tipe') }}">
                        <input type="hidden" name="id_kendaraan" value="{{ request('id_kendaraan') }}">
                        <input type="hidden" id="id_peminjaman" name="id_peminjaman" value="{{ request('id_peminjaman') }}">
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">Nomor Plat</label>
                        <input type="text" class="w-full p-2 border border-gray-300 rounded bg-gray-100" readonly value="{{ request('plat') }}">
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">Jenis BBM</label>
                        <select name="jenis_bbm" class="w-full p-2 border border-gray-300 rounded">
                            <option value="" disabled selected>Pilih Jenis BBM</option>
                            <option value="Pertalite">Pertalite</option>
                            <option value="Pertamax">Pertamax</option>
                            <option value="Pertamax Turbo">Pertamax Turbo</option>
                            <option value="Dexlite">Dexlite</option>
                            <option value="Pertamina Dex">Pertamina Dex</option>
                            <option value="Solar">Solar</option>
                            <option value="Bio Solar">Bio Solar</option>
                        </select>
                        <p id="warning-jenis-bbm" class="text-red-500 text-sm mt-1 hidden">Jenis BBM wajib diisi!</p>
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">Tanggal Pengisian BBM</label>
                        <input type="date" id="tglIsiBBM" name="tgl_isi" class="w-full p-2 border border-gray-300 rounded" max="{{ \Carbon\Carbon::now()->format('Y-m-d') }}">
                        <p id="warning-tanggal" class="text-red-500 text-sm mt-1 hidden">Tanggal pengisian BBM wajib diisi!</p>
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">Nominal</label>
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500">Rp</span>
                            <input type="text" id="nominalInput" name="nominal" class="w-full pl-10 p-2 border border-gray-300 rounded">
                        </div>
                        <div id="nominalAlert" class="text-red-500 text-sm mt-1"></div>
                        <p id="warning-nominal" class="text-red-500 text-sm mt-1 hidden">Nominal pengisian BBM wajib diisi!</p>
                    </div>                                      
                    <div class="flex justify-end">
                        {{--  <a href="{{ url()->previous() }}" class="bg-gray-500 text-white px-4 py-2 rounded">Kembali</a>  --}}
                        <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">Simpan</button>
                    </div>
                </form>
            </div>
        </div>

    <style>
        .swal2-cancel-gray {
            background-color: #6c757d !important;
            color: white !important;
            border: none !important;
        }
        
        .swal2-confirm-blue {
            background-color: #3085d6 !important;
            color: white !important;
            border: none !important;
        }
    </style> 

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const form = document.getElementById('bbmForm');
            if (!form) return;

            const jenisBBMSelect = form.querySelector('select[name="jenis_bbm"]');
            const tanggalInput = form.querySelector('input[name="tgl_isi"]');
            const nominalInput = document.getElementById('nominalInput');
            const warningJenisBBM = document.getElementById('warning-jenis-bbm');
            const warningTanggal = document.getElementById('warning-tanggal');
            const warningNominal = document.getElementById('warning-nominal');
            const nominalAlert = document.getElementById('nominalAlert');

            const maxHarga = 1000000000000;

            // Set max date for tanggal input
            const today = new Date().toISOString().split("T")[0];
            const tglIsiBBM = document.getElementById('tglIsiBBM');
            if (tglIsiBBM) {
                tglIsiBBM.setAttribute('max', today);
            }

            // Hide warnings on valid input
            jenisBBMSelect?.addEventListener('change', function () {
                if (jenisBBMSelect.value) {
                    warningJenisBBM.classList.add('hidden');
                }
            });

            tanggalInput?.addEventListener('input', function () {
                if (tanggalInput.value) {
                    warningTanggal.classList.add('hidden');
                }
            });

            nominalInput?.addEventListener('input', function (e) {
                const onlyDigits = e.target.value.replace(/\D/g, '');
                
                if (onlyDigits === '') {
                    e.target.value = '';
                    warningNominal.classList.remove('hidden');
                    nominalAlert.textContent = '';
                    return;
                }

                const valueNum = Number(onlyDigits);

                if (valueNum > maxHarga) {
                    nominalAlert.textContent = 'Nominal melebihi batas maksimum Rp 1.000.000.000.000.';
                } else {
                    nominalAlert.textContent = '';
                }

                // Format ribuan
                e.target.value = Math.min(valueNum, maxHarga)
                    .toString()
                    .replace(/\B(?=(\d{3})+(?!\d))/g, ".");

                // Hide warning if valid
                if (valueNum > 0 && valueNum <= maxHarga) {
                    warningNominal.classList.add('hidden');
                }
            });

            // Form validation & confirmation
            form.addEventListener('submit', function (event) {
                event.preventDefault();
                let isValid = true;

                if (!jenisBBMSelect.value) {
                    warningJenisBBM.classList.remove('hidden');
                    isValid = false;
                }

                if (!tanggalInput.value) {
                    warningTanggal.classList.remove('hidden');
                    isValid = false;
                }

                if (!nominalInput.value.trim()) {
                    warningNominal.classList.remove('hidden');
                    isValid = false;
                }

                if (!isValid) return;

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
                    cancelButtonText: "Batal",
                    customClass: {
                        confirmButton: "swal2-confirm-blue",
                        cancelButton: "swal2-cancel-gray"
                    }
                }).then((result) => {
                    if (result.isConfirmed) {

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
                                window.location.href = '/admin/pengisianBBM';
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

    </script>
</x-app-layout>
