<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <title>Form Peminjaman</title>
</head>
<body class="bg-gray-100">

    <div class="relative min-h-screen flex items-center justify-center py-12 px-4">
        <div class="max-w-2xl w-full bg-white p-6 rounded-lg shadow-lg">
            <h2 class="text-2xl font-bold mb-6 text-center">Form Perpanjangan</h2>

            @if(session('success'))
                <div class="bg-green-100 text-green-800 p-3 rounded mb-4">
                    {{ session('success') }}
                </div>
            @endif

            <form id="form-perpanjangan"action="{{ route('peminjaman.perpanjang') }}" method="POST">
                @csrf
                <input type="hidden" name="id_peminjaman" value="{{ $peminjaman->id_peminjaman }}">
                <!-- Tanggal & Jam Mulai Perpanjangan DISABLED OTOMATIS DARI TANGGAL SELESAI SEBELUMNYA --> 
                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div>
                        <label for="tgl_mulai" class="block text-sm font-medium text-gray-700 mb-1">Tanggal Mulai Pinjam</label>
                        <input type="date" id="tgl_mulai" name="tgl_mulai" class="w-full p-2.5 border rounded-lg bg-white-100" 
                            value="{{ old('tgl_mulai', $peminjaman->tgl_selesai ? date('Y-m-d', strtotime($peminjaman->tgl_selesai)) : '') }}" 
                            disabled>
                    </div>
                    <div>
                        <label for="jam_mulai" class="block text-sm font-medium text-gray-700 mb-1">Jam Mulai Pinjam</label>
                        <input type="time" id="jam_mulai" name="jam_mulai" class="w-full p-2.5 border rounded-lg bg-white-100" 
                            value="{{ old('jam_mulai', $peminjaman->jam_selesai ?? '') }}" 
                            disabled>
                    </div>
                </div>


                <!-- Tanggal & Jam Selesai Peminjaman -->
                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div>
                        <label for="tgl_selesai" class="block text-sm font-medium text-gray-700 mb-1">Tanggal Selesai Pinjam</label>
                        <input type="date" id="tgl_selesai" name="tgl_selesai" class="w-full p-2.5 border rounded-lg bg-white-100" required>
                        <p id="warning-tgl-selesai" class="text-red-500 text-sm mt-1 hidden">Tanggal selesai harus setelah tanggal mulai!</p>
                    </div>
                    <div>
                        <label for="jam_selesai" class="block text-sm font-medium text-gray-700 mb-1">Jam Selesai Pinjam</label>
                        <input type="time" id="jam_selesai" name="jam_selesai" class="w-full p-2.5 border rounded-lg bg-white-100" required>
                        <p id="warning-jam-selesai" class="text-red-500 text-sm mt-1 hidden">Jam selesai harus setelah jam mulai!</p>
                    </div>
                </div>

               <!-- Pilihan Kendaraan DISABLED OTOMATIS SAMA KAYA YG SEBELUMNYA-->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Detail Kendaraan</label>
                    <input type="text" name="kendaraan" class="w-full p-2.5 border rounded-lg" disabled
                        value="{{ $peminjaman->kendaraan->merk ?? 'Tidak ada kendaraan yang dipinjam' }} {{ $peminjaman->kendaraan->tipe }} - {{ $peminjaman->kendaraan->plat_nomor ?? '' }}">
                </div>

                <!-- Tujuan Peminjaman -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tujuan</label>
                    <input type="text" name="tujuan" class="w-full p-2.5 border rounded-lg" placeholder="Masukkan tujuan peminjaman" required>
                </div>

                <!-- Tombol Submit -->
                <div class="flex justify-end space-x-4 mb-2">
                    <button id="btn-batal" type="button" class="bg-red-600 text-white px-6 py-2.5 rounded-lg font-semibold hover:bg-red-700 transition">Batal</button>
                    <button type="submit" class="bg-blue-600 text-white px-6 py-2.5 rounded-lg font-semibold hover:bg-blue-700 transition">Simpan</button>
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

$(document).ready(function() {
    // Cancel button handler
    // let previousSelectedKendaraan = null;

    function showWarning(input, warningElement) {
        if (!input.val()) {
            warningElement.removeClass("hidden");
            input.addClass("border-red-500");
        }
    }

    function hideWarning(input, warningElement) {
        if (input.val()) {
            warningElement.addClass("hidden");
            input.removeClass("border-red-500");
        }
    }

    function validateDateTime() {
        let tglMulai = $("#tgl_mulai").val();
        let jamMulai = $("#jam_mulai").val();
        let tglSelesai = $("#tgl_selesai").val();
        let jamSelesai = $("#jam_selesai").val();

        let isValid = true;

        if (tglMulai && tglSelesai) {
            let startDate = new Date(tglMulai);
            let endDate = new Date(tglSelesai);

            // Validasi tanggal selesai
            if (endDate < startDate) {
                $("#warning-tgl-selesai").removeClass("hidden");
                $("#tgl_selesai").addClass("border-red-500");
                isValid = false;
            } else {
                $("#warning-tgl-selesai").addClass("hidden");
                $("#tgl_selesai").removeClass("border-red-500");
            }

            // Validasi jam selesai (hanya jika tanggal sama)
            if (startDate.getTime() === endDate.getTime() && jamMulai && jamSelesai) {
                let startTime = new Date(`2000-01-01T${jamMulai}`);
                let endTime = new Date(`2000-01-01T${jamSelesai}`);

                if (endTime <= startTime) {
                    $("#warning-jam-selesai").removeClass("hidden");
                    $("#jam_selesai").addClass("border-red-500");
                    isValid = false;
                } else {
                    $("#warning-jam-selesai").addClass("hidden");
                    $("#jam_selesai").removeClass("border-red-500");
                }
            } else {
                $("#warning-jam-selesai").addClass("hidden");
                $("#jam_selesai").removeClass("border-red-500");
            }
        }

        return isValid;
    }
    $("input").on("blur", function () {
        showWarning($(this), $(`#warning-${this.id}`));
    });
    
    $("input").on("input change", function () {
        hideWarning($(this), $(`#warning-${this.id}`));

        // Jalankan validasi hanya jika input tanggal/jam berubah
        if (["tgl_mulai", "jam_mulai", "tgl_selesai", "jam_selesai"].includes(this.id)) {
            validateDateTime();
            fetchAvailableKendaraan();
        }
    }); 
    $("#btn-batal").on("click", function () {
        Swal.fire({
            title: "Yakin ingin membatalkan?",
            text: "Semua perubahan tidak akan disimpan.",
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: "Ya, Batal",
            cancelButtonText: "Tidak"
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = "{{ route('peminjaman') }}";
            }
        });
    });

    // Form submission handler with the correct ID
    $("#form-perpanjangan").on("submit", function (e) {
        e.preventDefault();

        // Validate date/time first
        if (!validateDateTime()) {
            Swal.fire({
                title: "Validasi Error!",
                text: "Waktu selesai harus setelah waktu mulai!",
                icon: "error"
            });
            return false;
        }

        // Form data is valid, proceed with confirmation
        Swal.fire({
            title: "Konfirmasi Perpanjangan",
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
                        Swal.fire({
                            title: "Berhasil!",
                            text: response.message,
                            icon: "success",
                            confirmButtonText: "OK"
                        }).then(() => {
                            if (response.redirect) {
                                window.location.href = response.redirect;
                            }
                        });
                    },
                    error: function (xhr) {
                    let errorMessage = "Terjadi kesalahan pada server";
                    
                    if (xhr.responseJSON) {
                        if (xhr.responseJSON.type === 'conflict') {
                            // Format conflicts for display
                            let conflictDetails = '<div class="text-left">';
                            conflictDetails += '<p class="mb-2">Kendaraan sudah dibooking pada periode:</p>';
                            
                            xhr.responseJSON.conflicts.forEach(conflict => {
                                conflictDetails += `
                                    <div class="mb-2 p-2 bg-gray-100 rounded">
                                        <p><strong>Tanggal:</strong> ${conflict.tanggal}</p>
                                        <p><strong>Waktu:</strong> ${conflict.waktu}</p>
                                        <p><strong>Status:</strong> ${conflict.status}</p>
                                        <p><strong>Peminjam:</strong> ${conflict.peminjam}</p>
                                    </div>
                                `;
                            });
                            
                            conflictDetails += '</div>';

                            Swal.fire({
                                title: "Bentrok Jadwal!",
                                html: conflictDetails,
                                icon: "error",
                                confirmButtonText: "OK",
                                width: '600px'
                            });
                        } else if (xhr.responseJSON.type === 'validation') {
                            // Handle validation errors
                            errorMessage = Object.values(xhr.responseJSON.errors || {})
                                .flat()
                                .join('\n');
                            
                            Swal.fire({
                                title: "Validasi Gagal!",
                                text: errorMessage,
                                icon: "error",
                                confirmButtonText: "OK"
                            });
                        } else {
                            errorMessage = xhr.responseJSON.message || errorMessage;
                            Swal.fire({
                                title: "Gagal!",
                                text: errorMessage,
                                icon: "error",
                                confirmButtonText: "OK"
                            });
                        }
                    } else {
                        Swal.fire({
                            title: "Gagal!",
                            text: errorMessage,
                            icon: "error",
                            confirmButtonText: "OK"
                        });
                    }
                }

                });
            }
        });
    });
});
</script>
</html>

