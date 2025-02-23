<x-app-layout>

    <!DOCTYPE html>
    <html lang="en">
    <head>
        <title>Form Input Servis Insidental Kendaraan</title>
        <script src="https://cdn.tailwindcss.com"></script>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
        <!-- SweetAlert2 CSS -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/11.7.12/sweetalert2.min.css">
        <!-- SweetAlert2 JS -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/11.7.12/sweetalert2.all.min.js"></script>
    </head>
    <body class="bg-gray-100">
        <div class="flex">
            <!-- Main Content -->
            <div class="w-4/5 p-8">
                <h1 class="text-3xl font-bold mb-8">Form Input Servis Insidental Kendaraan</h1>
                <div class="bg-white p-8 rounded shadow-md">
                    <h2 class="text-xl font-semibold mb-4">Detail Servis</h2>
                    <form id="servisForm" action="{{ route('servisInsidental.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="grid grid-cols-2 gap-4 mb-4">
                            <div>
                                <label class="block text-gray-700">Merk dan Tipe Kendaraan</label>
                                <input type="text" id="merkTipe" name="merk_tipe" 
                                       class="w-full p-2 border border-gray-300 rounded bg-gray-100" 
                                       readonly 
                                       value="{{ request('merk') . ' ' . request('tipe') }}">
                                <input type="hidden" id="id_kendaraan" name="id_kendaraan" value="{{ request('id_kendaraan') }}">
                                <input type="hidden" id="id_peminjaman" name="id_peminjaman" value="{{ request('id_peminjaman') }}">
                            </div>
                            <div>
                                <label class="block text-gray-700">Nomor Plat</label>
                                <input type="text" id="nomorPlat" class="w-full p-2 border border-gray-300 rounded bg-gray-100" 
                                       readonly value="{{ request('plat') }}">
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-4 mb-4">
                            <div>
                                <label class="block text-gray-700">Tanggal Servis</label>
                                <input type="date" name="tgl_servis" class="w-full p-2 border border-gray-300 rounded" required>
                            </div>
                            <div>
                                <label class="block text-gray-700">Jumlah Pembayaran</label>
                                <input type="text" id="hargaInput" name="harga" class="w-full p-2 border border-gray-300 rounded" required>
                            </div>
                        </div>
                        <div class="mb-4">
                            <label class="block text-gray-700">Lokasi Servis</label>
                            <input type="text" name="lokasi" class="w-full p-2 border border-gray-300 rounded" required>
                        </div>
                        <div class="mb-4">
                            <label class="block text-gray-700">Deskripsi Servis</label>
                            <textarea name="deskripsi" class="w-full p-2 border border-gray-300 rounded" rows="3" required></textarea>
                        </div>
                        <div class="mb-6 flex justify-start space-x-4">
                            <!-- Upload Bukti Pembayaran Servis -->
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Upload Bukti Pembayaran Servis</label>
                                <div class="flex flex-col items-center">
                                    <label id="uploadLabelBuktiBayar" class="cursor-pointer flex flex-col items-center justify-center w-32 h-14 border border-blue-500 text-blue-600 font-medium rounded-lg hover:bg-blue-100 transition">
                                        <span id="uploadTextBuktiBayar" class="text-sm">Upload</span>
                                        <input type="file" name="bukti_bayar" id="fotoInputBuktiBayar" class="hidden" accept=".jpg, .jpeg, .png, .pdf">
                                    </label>
                                    <p id="fileNameBuktiBayar" class="text-sm mt-2 text-gray-600 hidden"></p>
                                    <a href="#" id="removeFileBuktiBayar" class="hidden text-red-600 font-medium text-sm mt-2 hover:underline text-center">Remove</a>
                                </div>
                            </div>
                        
                            <!-- Garis Pemisah -->
                            <div class="h-20 bg-gray-300" style="width: 0.5px;"></div>
                        
                            <!-- Upload Bukti Fisik Servis -->
                            <div class="mb-4">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Upload Bukti Fisik Servis</label>
                                <div class="flex flex-col items-center">
                                    <label id="uploadLabelBuktiFisik" class="cursor-pointer flex flex-col items-center justify-center w-32 h-14 border border-blue-500 text-blue-600 font-medium rounded-lg hover:bg-blue-100 transition">
                                        <span id="uploadTextBuktiFisik" class="text-sm">Upload</span>
                                        <input type="file" name="bukti_fisik" id="fotoInputBuktiFisik" class="hidden" accept=".jpg, .jpeg, .png, .pdf">
                                    </label>
                                    <p id="fileNameBuktiFisik" class="text-sm mt-2 text-gray-600 hidden"></p>
                                    <a href="#" id="removeFileBuktiFisik" class="hidden text-red-600 font-medium text-sm mt-2 hover:underline text-center">Remove</a>
                                </div>
                            </div>
                        
                            <!-- Garis Pemisah -->
                            <div class="h-20 bg-gray-300" style="width: 0.5px;"></div>
                        
                            <!-- Image Requirements -->
                            <div>
                                <p class="font-medium text-gray-700">Image requirements:</p>
                                <ul class="text-sm text-gray-600">
                                    <li>1. Format: PNG, JPG, atau PDF</li>
                                    <li>2. Ukuran maksimal: 5MB</li>
                                    <li>3. Foto harus jelas dan tidak buram</li>
                                </ul>
                            </div>
                        </div>
                        
                        <!-- Tombol submit dan navigasi -->
                        <div class="flex justify-between items-center">
                            <!-- Tombol Kembali (di kiri) -->
                            <a href="{{ url()->previous() }}" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-700 shadow-md">
                                Kembali
                            </a>
                        
                            <!-- Tombol Batal dan Simpan (di kanan) -->
                            <div class="flex space-x-2">
                                <button type="button" onclick="history.back()" class="bg-red-500 text-white px-4 py-2 rounded">Batal</button>
                                <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-700">Simpan</button>
                            </div>
                        </div>
                    </form>
                    
                </div>
            </div>
        </div>
    
        <!-- Display SweetAlert based on session messages -->
        @if(session('success'))
        <script>
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: "{{ session('success') }}",
                confirmButtonColor: '#3085d6'
            });
        </script>
        @endif
    
        @if(session('error'))
        <script>
            Swal.fire({
                icon: 'error',
                title: 'Gagal!',
                text: "{{ session('error') }}",
                confirmButtonColor: '#d33'
            });
        </script>
        @endif
    
        <script>
            document.addEventListener("DOMContentLoaded", function () {
                // Form submission with AJAX
                const form = document.getElementById('servisForm');
                
                form.addEventListener('submit', function(e) {
                    e.preventDefault();
                    
                    const formData = new FormData(form);
                    
                    fetch(form.action, {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Berhasil!',
                                text: data.message || 'Data servis berhasil disimpan',
                                confirmButtonColor: '#3085d6'
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    window.location.href = data.redirect || "{{ url()->previous() }}";
                                }
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Gagal!',
                                text: data.message || 'Terjadi kesalahan saat menyimpan data',
                                confirmButtonColor: '#d33'
                            });
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        Swal.fire({
                            icon: 'error',
                            title: 'Gagal!',
                            text: 'Terjadi kesalahan pada sistem',
                            confirmButtonColor: '#d33'
                        });
                    });
                });
    
                document.querySelectorAll(".kendaraan-row").forEach(row => {
                    row.addEventListener("click", async function () {
                        let id_kendaraan = this.getAttribute("data-id");
                        if (!id_kendaraan) return;
    
                        try {
                            let response = await fetch(`/api/kendaraan/${id_kendaraan}`);
                            let data = await response.json();
    
                            document.getElementById("merkTipe").value = `${data.merk} ${data.tipe}`;
                            document.getElementById("nomorPlat").value = data.plat_nomor;
                            document.getElementById("id_kendaraan").value = id_kendaraan;
                        } catch (error) {
                            console.error("Error fetching kendaraan data:", error);
                        }
                    });
                });
    
                function setupFileUpload(inputId, fileNameId, removeBtnId) {
                    const inputFile = document.getElementById(inputId);
                    const fileNameDisplay = document.getElementById(fileNameId);
                    const removeBtn = document.getElementById(removeBtnId);
            
                    inputFile.addEventListener("change", function () {
                        if (inputFile.files.length > 0) {
                            fileNameDisplay.textContent = inputFile.files[0].name;
                            fileNameDisplay.classList.remove("hidden");
                            removeBtn.classList.remove("hidden");
                        }
                    });
            
                    removeBtn.addEventListener("click", function (e) {
                        e.preventDefault();
                        inputFile.value = "";
                        fileNameDisplay.textContent = "";
                        fileNameDisplay.classList.add("hidden");
                        removeBtn.classList.add("hidden");
                    });
                }
            
                setupFileUpload("fotoInputBuktiBayar", "fileNameBuktiBayar", "removeFileBuktiBayar");
                setupFileUpload("fotoInputBuktiFisik", "fileNameBuktiFisik", "removeFileBuktiFisik");
            });
    
            {{--  document.getElementById('hargaInput').addEventListener('input', function (e) {
                // Ambil nilai input
                let value = e.target.value;
            
                // Hapus semua titik yang dimasukkan pengguna
                value = value.replace(/\./g, '');
            
                // Pastikan hanya angka yang dapat diinput
                value = value.replace(/[^0-9]/g, '');
            
                // Perbarui nilai input dengan angka yang sudah bersih
                e.target.value = value;
            });  --}}

            document.getElementById('hargaInput').addEventListener('input', function (e) {
                let value = e.target.value.replace(/\D/g, '');
                e.target.value = value.replace(/\B(?=(\d{3})+(?!\d))/g, ".");
            });
        </script>
    </body>
    </html>
    
    </x-app-layout>