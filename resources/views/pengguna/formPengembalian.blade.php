<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <title>Form Pengembalian</title>
</head>
<body class="bg-gray-100">

    <div class="relative min-h-screen flex items-center justify-center py-12 px-4">
        <div class="max-w-2xl w-full bg-white p-6 rounded-lg shadow-lg">
            <h2 class="text-2xl font-bold mb-6 text-center">Form Pengembalian</h2>

            @if(session('success'))
                <div class="bg-green-100 text-green-800 p-3 rounded mb-4">
                    {{ session('success') }}
                </div>
            @endif

            <form id="form-pengembalian" method="POST" action="{{ route('peminjaman.pengembalian', ['id' => $peminjaman->id_peminjaman]) }}">
                @csrf
                <!-- Detail Kendaraan -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Detail Kendaraan</label>
                    <input type="text" name="kendaraan" class="w-full p-2.5 border rounded-lg" disabled
                        value="{{ $peminjaman->kendaraan->merk ?? 'Tidak ada kendaraan yang dipinjam' }} {{ $peminjaman->kendaraan->tipe }} - {{ $peminjaman->kendaraan->plat_nomor ?? '' }}">
                </div>
                
                <!-- Tanggal & Jam Pengembalian -->
                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div>
                        <label for="tgl" class="block text-sm font-medium text-gray-700 mb-1">Tanggal Pengembalian</label>
                        <input type="date" id="tgl" name="tgl" class="w-full p-2.5 border rounded-lg bg-white-100" required>
                        <p id="warning-tgl" class="text-red-500 text-sm mt-1 hidden">Tanggal pengembalian harus diisi!</p>
                    </div>
                    <div>
                        <label for="jam" class="block text-sm font-medium text-gray-700 mb-1">Jam Pengembalian</label>
                        <input type="time" id="jam" name="jam" class="w-full p-2.5 border rounded-lg bg-white-100" required>
                        <p id="warning-jam" class="text-red-500 text-sm mt-1 hidden">Jam pengembalian harus diisi!</p>
                    </div>
                </div>

               <!-- Kondisi Kendaraan -->
                <div class="mb-4">
                <label for="kondisi_kendaraan" class="block text-sm font-medium text-gray-700 mb-1">Kondisi Kendaraan</label>
                    <select id="kondisi_kendaraan" name="kondisi_kendaraan" class="w-full p-2.5 border rounded-lg bg-white" required>
                        <option value="">Pilih Kondisi</option>
                        <option value="Baik">Baik</option>
                        <option value="Terjadi Insiden">Terjadi Insiden</option>
                    </select>
                    <p id="warning-kondisi" class="text-red font-bold text-sm mt-1 hidden">Kondisi kendaraan harus diisi!</p>
                </div>

                <!-- Detail Insiden -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Detail Insiden (Jika Ada)</label>
                    <input type="text" name="detail" class="w-full p-2.5 border rounded-lg">
                </div>

                <!-- Tombol Submit -->
                <div class="flex justify-end space-x-4 mb-2">
                    <button id="btn-batal" type="button" class="bg-red-600 text-white px-6 py-2.5 rounded-lg font-semibold hover:bg-red-700 transition">Batal</button>
                    <button type="submit" class="bg-blue-600 text-white px-6 py-2.5 rounded-lg font-semibold hover:bg-blue-700 transition">Kembalikan</button>
                </div>
            </form>
        </div>
    </div>
</body>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        // Cancel button handler remains the same...

        $("#form-pengembalian").on("submit", function (e) {
            e.preventDefault();

            // Debug logging
            console.log('Form Values:', {
                tanggal: $("#tgl").val(),
                jam: $("#jam").val(),
                kondisi: $("#kondisi_kendaraan").val(),
                detail: $("input[name='detail']").val()
            });
            // Improved validation with specific error messages
            let errors = [];
            
            if (!$("#tgl").val()) {
                errors.push("Tanggal pengembalian harus diisi");
            }
            
            if (!$("#jam").val()) {
                errors.push("Jam pengembalian harus diisi");
            }
            
            if (!$("#kondisi_kendaraan").val()) {
                errors.push("Kondisi kendaraan harus dipilih");
            }

            // If there are validation errors
            if (errors.length > 0) {
                Swal.fire({
                    title: "Validasi Error!",
                    html: errors.join('<br>'),
                    icon: "error"
                });
                return false;
            }

            // Form data is valid, proceed with confirmation
            Swal.fire({
                title: "Konfirmasi Pengembalian",
                text: "Pastikan semua data sudah benar sebelum menyimpan.",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Ya, Simpan!",
                cancelButtonText: "Batal"
            }).then((result) => {
                if (result.isConfirmed) {
                    const form = $(this);
                    const url = form.attr('action');
                    const formData = form.serialize();

                    // Debug: Log the form data being sent
                    console.log('Form Data:', formData);

                    $.ajax({
                        url: url,
                        type: "POST",
                        data: formData,
                        beforeSend: function () {
                            Swal.fire({
                                title: "Menyimpan...",
                                text: "Mohon tunggu sebentar.",
                                allowOutsideClick: false,
                                didOpen: () => {
                                    Swal.showLoading();
                                }
                            });
                        },
                        success: function (response) {
                            console.log('Success Response:', response);
                            Swal.fire({
                                title: "Berhasil!",
                                text: "Pengembalian kendaraan berhasil disimpan.",
                                icon: "success",
                                confirmButtonText: "OK"
                            }).then(() => {
                                window.location.href = "{{ route('peminjaman') }}";
                            });
                        },
                        error: function (xhr, status, error) {
                            console.log('Error:', xhr.responseText);
                            Swal.fire({
                                title: "Gagal!",
                                text: xhr.responseJSON?.message || "Terjadi kesalahan pada server, silakan coba lagi.",
                                icon: "error",
                                confirmButtonText: "OK"
                            });
                        }
                    });
                }
            });
        });

        // Add input event listeners to clear validation messages
        $("#tgl, #jam, #kondisi_kendaraan").on('input change', function() {
            console.log(this.id + ' changed:', this.value);
        });
        $("#kondisi_kendaraan").on('change', function() {
            console.log('Kondisi changed:', this.value);
            if (this.value) {
                $(this).removeClass('border-red-500');
            }
        });
    </script>
</body>
</html>
