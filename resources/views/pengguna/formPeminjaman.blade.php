<x-app-layout>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Form Peminjaman</title>
</head>

<body class="bg-gray-100">
    <a href="{{  route('peminjaman', ['page' => request('page'), 'search' => request('search')])  }}" class="flex items-center text-blue-600 font-semibold hover:underline mb-5">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"></path>
            </svg>
            Kembali 
    </a>
   

    <div class="relative flex items-center justify-center px-4 py-6 sm:py-8">
        <div class="max-w-2xl w-full bg-white p-6 rounded-lg shadow-lg ">
            <h2 class="text-2xl font-bold mb-6 text-center">Form Peminjaman</h2>

            @if(session('success'))
                <div class="bg-green-100 text-green-800 p-3 rounded mb-4">
                    {{ session('success') }}
                </div>
            @endif

            <form id="form-peminjaman"action="{{ route('peminjaman.simpan') }}" method="POST">
                @csrf
                <!-- Tanggal & Jam Mulai Peminjaman -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label for="tgl_mulai" class="block text-sm font-medium text-gray-700 mb-1">Tanggal Mulai Pinjam</label>
                        <input type="date" id="tgl_mulai" name="tgl_mulai" class="w-full p-2.5 border rounded-lg bg-white-100" required>
                        <p id="warning-tgl-mulai" class="text-red-500 text-sm mt-1 hidden">Tanggal mulai harus diisi!</p>
                        <p id="warning-isi-tgl-mulai" class="text-red-500 text-sm mt-1 hidden">Tanggal mulai harus diisi!</p>
                    </div>
                    <div>
                        <label for="jam_mulai" class="block text-sm font-medium text-gray-700 mb-1">Jam Mulai Pinjam</label>
                        <input type="time" id="jam_mulai" name="jam_mulai" class="w-full p-2.5 border rounded-lg bg-white-100" required>
                        <p id="warning-jam-mulai" class="text-red-500 text-sm mt-1 hidden">Jam mulai harus diisi!</p>
                        <p id="warning-isi-jam-mulai" class="text-red-500 text-sm mt-1 hidden">Jam mulai harus diisi!</p>
                    </div>
                </div>

                <!-- Tanggal & Jam Selesai Peminjaman -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label for="tgl_selesai" class="block text-sm font-medium text-gray-700 mb-1">Tanggal Selesai Pinjam</label>
                        <input type="date" id="tgl_selesai" name="tgl_selesai" class="w-full p-2.5 border rounded-lg bg-white-100" required>
                        <p id="warning-tgl-selesai" class="text-red-500 text-sm mt-1 hidden">Tanggal selesai harus setelah tanggal mulai!</p>
                        <p id="warning-isi-tgl-selesai" class="text-red-500 text-sm mt-1 hidden">Tanggal selesai harus diisi!</p>
                    </div>
                    <div>
                        <label for="jam_selesai" class="block text-sm font-medium text-gray-700 mb-1">Jam Selesai Pinjam</label>
                        <input type="time" id="jam_selesai" name="jam_selesai" class="w-full p-2.5 border rounded-lg bg-white-100" required>
                        <p id="warning-jam-selesai" class="text-red-500 text-sm mt-1 hidden">Jam selesai harus setelah jam mulai!</p>
                        <p id="warning-isi-jam-selesai" class="text-red-500 text-sm mt-1 hidden">Jam selesai harus diisi!</p>
                    </div>
                </div>

               <!-- Pilihan Kendaraan -->
                <div class="mb-4">
                <label for="pilih-kendaraan" class="block text-sm font-medium text-gray-700 mb-1">Pilih Kendaraan</label>
                    <select id="pilih-kendaraan" name="kendaraan" class="w-full p-2.5 border rounded-lg bg-white" disabled>
                        <option value="" disabled selected>Pilih Kendaraan</option>
                    </select>
                    <p id="warning-kendaraan" class="text-yellow-600 text-sm mt-1 hidden">Silakan isi tanggal & jam terlebih dahulu sebelum memilih kendaraan</p>
                    <p id="warning-isi-kendaraan" class="text-red-500 text-sm mt-1 hidden">Pilih kendaraan terlebih dahulu!</p>
                </div>

                <!-- Tujuan Peminjaman -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tujuan</label>
                    <input type="text" name="tujuan" class="w-full p-2.5 border rounded-lg" placeholder="Masukkan tujuan peminjaman" required>
                    <p id="warning-isi-tujuan" class="text-red-500 text-sm mt-1 hidden">Isi tujuan peminjaman!</p>
                </div>

                <!-- Tombol Submit -->
                <div class="flex justify-end space-x-4 mb-2">
                    <!-- <button id="btn-batal" type="button" class="bg-red-600 text-white px-6 py-2.5 rounded-lg font-semibold hover:bg-red-700 transition">Batal</button> -->
                    <button type="submit" class="bg-blue-600 text-white px-6 py-2.5 rounded-lg font-semibold hover:bg-blue-700 transition">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    $(document).ready(function () {
        let previousSelectedKendaraan = null;
//INI KODENYA 
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
        // Menampilkan warning kendaraan sejak awal
        $("#warning-kendaraan").removeClass("hidden");

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

        function fetchAvailableKendaraan() {
            if (!validateDateTime()) return;

            let tglMulai = $("#tgl_mulai").val();
            let jamMulai = $("#jam_mulai").val();
            let tglSelesai = $("#tgl_selesai").val();
            let jamSelesai = $("#jam_selesai").val();

            if (tglMulai && jamMulai && tglSelesai && jamSelesai) {
                $("#warning-kendaraan").addClass("hidden");

                $.ajax({
                    url: "{{ route('peminjaman.getKendaraan') }}",
                    type: "GET",
                    data: {
                        tgl_mulai: tglMulai,
                        jam_mulai: jamMulai,
                        tgl_selesai: tglSelesai,
                        jam_selesai: jamSelesai
                    },
                    success: function (response) {
                        let kendaraanDropdown = $("#pilih-kendaraan");
                        previousSelectedKendaraan = kendaraanDropdown.val(); // Simpan pilihan sebelumnya

                        kendaraanDropdown.empty();
                        kendaraanDropdown.append('<option value="" disabled selected>Pilih Kendaraan</option>');

                        if (response.length > 0) {
                            response.forEach(kendaraan => {
                                kendaraanDropdown.append(`<option value="${kendaraan.id_kendaraan}">${kendaraan.merk} ${kendaraan.tipe} - ${kendaraan.plat_nomor}</option>`);
                            });
                            kendaraanDropdown.prop("disabled", false);

                            // Kembalikan pilihan sebelumnya jika masih ada
                            if (response.some(k => k.id_kendaraan === previousSelectedKendaraan)) {
                                kendaraanDropdown.val(previousSelectedKendaraan);
                            } else {
                                previousSelectedKendaraan = null; // Reset jika kendaraan sebelumnya tidak ada
                            }
                        } else {
                            kendaraanDropdown.empty();
                            kendaraanDropdown.append('<option value="" disabled selected>Tidak Ada Kendaraan yang Tersedia</option>');
                            kendaraanDropdown.prop("disabled", false);
                            previousSelectedKendaraan = null; // Pastikan tidak ada pilihan sebelumnya
                        }

                    },
                    error: function () {
                        Swal.fire({
                            title: "Error",
                            text: "Gagal mengambil data kendaraan, silakan coba lagi.",
                            icon: "error",
                            confirmButtonText: "OK"
                        });
                    }
                });
            } else {
                $("#warning-kendaraan").removeClass("hidden");
                $("#pilih-kendaraan").prop("disabled", true);
            }
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
         // Validasi untuk tujuan peminjaman
        $("input[name='tujuan']").on("blur", function () {
            if (!$(this).val()) {
                $("#warning-isi-tujuan").removeClass("hidden");
                $(this).addClass("border-red-500");
            }
        }).on("input", function () {
            if ($(this).val()) {
                $("#warning-isi-tujuan").addClass("hidden");
                $(this).removeClass("border-red-500");
            }
        });

        // Validasi untuk pilihan kendaraan
        // $("#pilih-kendaraan").on("change", function () {
        //     if (!$(this).val()) {
        //         $("#warning-isi-kendaraan").removeClass("hidden");
        //     } else {
        //         $("#warning-isi-kendaraan").addClass("hidden");
        //     }
        // });
        // Validasi untuk memilih kondisi kendaraan
     // Menampilkan warning saat dropdown diklik, tapi belum memilih nilai
    $("#pilih-kendaraan").on("focus", function () {
        // Menampilkan peringatan jika dropdown kosong (belum dipilih)
        if (!$(this).val()) {
            showWarning($(this), $("#warning-isi-kendaraan"));
        }
    });

    // Menyembunyikan warning saat nilai dipilih
    $("#pilih-kendaraan").on("change", function () {
        if ($(this).val()) {
            hideWarning($(this), $("#warning-isi-kendaraan"));
        }
    });

        // Menampilkan warning jika input tanggal mulai kosong
        $("#tgl_mulai").on("blur", function () {
            showWarning($(this), $("#warning-isi-tgl-mulai"));
        }).on("input", function () {
            hideWarning($(this), $("#warning-isi-tgl-mulai"));
        });

        // Menampilkan warning jika input jam mulai kosong
        $("#jam_mulai").on("blur", function () {
            showWarning($(this), $("#warning-isi-jam-mulai"));
        }).on("input", function () {
            hideWarning($(this), $("#warning-isi-jam-mulai"));
        });

        // Menampilkan warning jika input tanggal selesai kosong
        $("#tgl_selesai").on("blur", function () {
            showWarning($(this), $("#warning-isi-tgl-selesai"));
        }).on("input", function () {
            hideWarning($(this), $("#warning-isi-tgl-selesai"));
        });

        // Menampilkan warning jika input jam selesai kosong
        $("#jam_selesai").on("blur", function () {
            showWarning($(this), $("#warning-isi-jam-selesai"));
        }).on("input", function () {
            hideWarning($(this), $("#warning-isi-jam-selesai"));
        });

        
       
        // Konfirmasi tombol batal
        // $("#btn-batal").on("click", function () {
        //     Swal.fire({
        //         title: "Yakin ingin membatalkan?",
        //         text: "Semua perubahan tidak akan disimpan.",
        //         icon: "warning",
        //         showCancelButton: true,
        //         confirmButtonText: "Ya, Batal",
        //         cancelButtonText: "Tidak"
        //     }).then((result) => {
        //         if (result.isConfirmed) {
        //             window.location.href = "{{ route('peminjaman') }}"; // Sesuaikan rute ke halaman awal
        //         }
        //     });
        // });
        $("#form-peminjaman").on("submit", function (e) {
            e.preventDefault(); // Mencegah form submit secara default

            let kendaraanId = $("#pilih-kendaraan").val();
            if (!kendaraanId) {
                Swal.fire({
                    title: "Warning!",
                    text: "Anda harus memilih kendaraan sebelum menyimpan.",
                    icon: "warning",
                    confirmButtonText: "OK"
                });
                return;
            }

            Swal.fire({
                title: "Apakah Anda yakin?",
                text: "Pastikan semua data sudah benar sebelum menyimpan.",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Ya, Simpan!",
                cancelButtonText: "Batal",
                reverseButtons: true, 
                confirmButtonColor: "#3085d6"
                // customClass: {
                //     confirmButton: "text-white bg-blue-700 hover:bg-blue-800 font-medium rounded-m text-sm p-3"
                // }
            }).then((result) => {
                if (result.isConfirmed) {
                    // Ambil data form
                    const formData = $("#form-peminjaman").serialize();

                    // Kirim data ke server menggunakan AJAX
                    $.ajax({
                        url: "{{ route('peminjaman.simpan') }}", // URL tujuan
                        type: "POST", // Tipe request
                        data: formData, // Data yang dikirim
                        beforeSend: function () {
                            // Tampilkan loading sebelum request selesai
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
                            // Tampilkan pesan berhasil
                            Swal.fire({
                                title: "Berhasil!",
                                text: response.message || "Peminjaman telah berhasil disimpan.",
                                icon: "success",
                                confirmButtonText: "OK",
                                confirmButtonColor: '#3085d6'
                            }).then(() => {
                                // Redirect ke halaman peminjaman
                                window.location.href = "{{ route('peminjaman') }}";
                            });
                        },
                        error: function (xhr) {
                            // Tangani error dari server
                            Swal.fire({
                                title: "Gagal!",
                                text: xhr.responseJSON?.message || "Terjadi kesalahan, silakan coba lagi.",
                                icon: "error",
                                confirmButtonText: "OK",
                                confirmButtonColor: '#3085d6'
                            });
                        }
                    });
                }
            });
        });
    });
</script>
</x-app-layout>
