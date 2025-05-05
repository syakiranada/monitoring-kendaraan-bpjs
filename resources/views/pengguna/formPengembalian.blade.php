<x-app-layout>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Form Pengembalian</title>
</head>
<body class="bg-gray-100">
<a href="{{  route('peminjaman', ['page' => request('page'), 'search' => request('search')])  }}" class="flex items-center text-blue-600 font-semibold hover:underline mb-5">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"></path>
            </svg>
            Kembali 
    </a>
    <div class="relative flex items-center justify-center">
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
                    <input type="text" name="kendaraan" class="w-full p-2.5 border rounded-lg disabled:bg-gray-100" disabled
                        value="{{ $peminjaman->kendaraan->merk ?? 'Tidak ada kendaraan yang dipinjam' }} {{ $peminjaman->kendaraan->tipe }} - {{ $peminjaman->kendaraan->plat_nomor ?? '' }}">
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label for="tgl_mulai" class="block text-sm font-medium text-gray-700 mb-1">Tanggal Mulai Pinjam</label>
                        <input type="date" id="tgl_mulai" name="tgl_mulai" class="w-full p-2.5 border rounded-lg disabled:bg-gray-100" 
                            value="{{ old('tgl_mulai', $peminjaman->tgl_mulai ? date('Y-m-d', strtotime($peminjaman->tgl_mulai)) : '') }}" 
                            disabled>
                    </div>
                    <div>
                        <label for="jam_mulai" class="block text-sm font-medium text-gray-700 mb-1">Jam Mulai Pinjam</label>
                        <input type="time" id="jam_mulai" name="jam_mulai" class="w-full p-2.5 border rounded-lg disabled:bg-gray-100" 
                            value="{{ old('jam_mulai', $peminjaman->jam_mulai ?? '') }}" 
                            disabled>
                    </div>
                </div>


                <!-- Tanggal & Jam Selesai Peminjaman -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label for="tgl_selesai" class="block text-sm font-medium text-gray-700 mb-1">Tanggal Selesai Pinjam</label>
                        <input type="date" id="tgl_selesai" name="tgl_selesai" class="w-full p-2.5 border rounded-lg disabled:bg-gray-100" 
                            value="{{ old('tgl_selesai', $peminjaman->tgl_selesai ? date('Y-m-d', strtotime($peminjaman->tgl_selesai)) : '') }}" 
                            disabled>
                    </div>
                    <div>
                        <label for="jam_selesai" class="block text-sm font-medium text-gray-700 mb-1">Jam Mulai Pinjam</label>
                        <input type="time" id="jam_selesai" name="jam_selesai" class="w-full p-2.5 border rounded-lg disabled:bg-gray-100" 
                            value="{{ old('jam_selesai', $peminjaman->jam_selesai ?? '') }}" 
                            disabled>
                    </div>
                </div>
            
                <!-- Tanggal & Jam Pengembalian -->
                <!-- <div class="grid grid-cols-2 gap-4 mb-4">
                    <div>
                        <label for="tgl" class="block text-sm font-medium text-gray-700 mb-1">Tanggal Pengembalian</label>
                        <input type="date" id="tgl" name="tgl" class="w-full p-2.5 border rounded-lg bg-white-100 disabled">
                        <p id="warning-tgl" class="text-red-500 text-sm mt-1 hidden">Tanggal pengembalian harus setelah tanggal mulai!</p>
                    </div>
                    <div>
                        <label for="jam" class="block text-sm font-medium text-gray-700 mb-1">Jam Pengembalian</label>
                        <input type="time" id="jam" name="jam" class="w-full p-2.5 border rounded-lg bg-white-100 disabled">
                        <p id="warning-jam" class="text-red-500 text-sm mt-1 hidden">Jam pengembalian harus setelah tanggal mulai!</p>
                    </div>
                </div> -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label for="tgl" class="block text-sm font-medium text-gray-700 mb-1">Tanggal Pengembalian</label>
                        <input type="date" id="tgl" name="tgl" class="w-full p-2.5 border rounded-lg bg-gray-100" readonly >
                        
                        <p id="warning-tgl" class="text-red-500 text-sm mt-1 hidden">Tanggal pengembalian harus setelah tanggal mulai!</p>
                    </div>
                    <div>
                        <label for="jam" class="block text-sm font-medium text-gray-700 mb-1">Jam Pengembalian</label>
                        <input type="time" id="jam" name="jam" class="w-full p-2.5 border rounded-lg bg-gray-100" readonly>
                        <p id="warning-jam" class="text-red-500 text-sm mt-1 hidden">Jam pengembalian harus setelah tanggal mulai!</p>
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
                    <p id="warning-kondisi" class="text-red-500 text-sm mt-1 hidden">Kondisi kendaraan harus dipilih!</p>
                </div>


                <!-- Detail Insiden -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Detail Insiden (Jika tidak ada bisa diisi '-' )</label>
                    <input type="text" name="detail" class="w-full p-2.5 border rounded-lg" required>
                    <p id="warning-isi-detail" class="text-red-500 text-sm mt-1 hidden">Isi detail insiden!</p>
                </div>

                <!-- Tombol Submit INI BENERIN-->
                <div class="flex justify-end space-x-4 mb-2">
                    <!-- <button id="btn-batal" type="button" class="bg-red-600 text-white px-6 py-2.5 rounded-lg font-semibold hover:bg-red-700 transition">Batal</button> -->
                    <button type="submit" class="bg-blue-600 text-white px-6 py-2.5 rounded-lg font-semibold hover:bg-blue-700 transition">Kembalikan</button>
                </div>
            </form>
        </div>
    </div>
</body>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
    document.addEventListener("DOMContentLoaded", function() {
        // Ambil elemen input tanggal dan waktu
        let inputTanggal = document.getElementById("tgl");
        let inputJam = document.getElementById("jam");

        // Ambil waktu sekarang
        let now = new Date();

        // Format tanggal (YYYY-MM-DD)
        let todayDate = now.toISOString().split("T")[0];

        // Format jam (HH:MM)
        let hours = String(now.getHours()).padStart(2, '0');
        let minutes = String(now.getMinutes()).padStart(2, '0');
        let currentTime = `${hours}:${minutes}`;

        // Set nilai default untuk input
        inputTanggal.value = todayDate;
        inputJam.value = currentTime;
    });
</script>
    <script>
        
$(document).ready(function () {
    $.ajaxSetup({
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
    });

    function showWarning(input, warningElement) {
        if (!input.val()) {
            warningElement.removeClass("hidden");
            input.addClass("border-red-500");
            console.log("Warning shown for empty condition");  // Debug log
        }
    }

    function hideWarning(input, warningElement) {
        if (input.val()) {
            warningElement.addClass("hidden");
            input.removeClass("border-red-500");
            console.log("Warning shown for empty condition");  // Debug log
        }
    }


    function validateDateTime() {
    let tglMulai = $("#tgl_mulai").val();
    let jamMulai = $("#jam_mulai").val();
    let tgl = $("#tgl").val();
    let jam = $("#jam").val();

    let isValid = true;

    if (tglMulai && tgl) {
        // Pisahkan tanggal (YYYY-MM-DD)
        let startParts = tglMulai.split("-");
        let endParts = tgl.split("-");

        // Buat Date object secara eksplisit
        let startDate = new Date(startParts[0], startParts[1] - 1, startParts[2]); // (YYYY, MM-1, DD)
        let endDate = new Date(endParts[0], endParts[1] - 1, endParts[2]); // (YYYY, MM-1, DD)

        // Validasi tanggal selesai
        if (endDate <= startDate) {
            $("#warning-tgl").removeClass("hidden");
            $("#tgl").addClass("border-red-500");
            isValid = false;
        } else {
            $("#warning-tgl").addClass("hidden");
            $("#tgl").removeClass("border-red-500");
        }

        // Validasi jam selesai (hanya jika tanggal sama)
        if (startDate.getTime() === endDate.getTime() && jamMulai && jam) {
            let startTime = new Date(`2000-01-01T${jamMulai}`);
            let endTime = new Date(`2000-01-01T${jam}`);

            if (endTime <= startTime) {
                $("#warning-jam").removeClass("hidden");
                $("#jam").addClass("border-red-500");
                isValid = false;
            } else {
                $("#warning-jam").addClass("hidden");
                $("#jam").removeClass("border-red-500");
            }
        } else {
            $("#warning-jam").addClass("hidden");
            $("#jam").removeClass("border-red-500");
        }
    }

    return isValid;
}
    $("input").on("blur", function () {
        showWarning($(this), $(`#warning-${this.id}`));
    });
    
    $("input").on("input change", function () {
        hideWarning($(this), $(`#warning-${this.id}`));

    }); 

    $("#tgl, #jam, #tgl_mulai, #jam_mulai").on("input change", function () {
        validateDateTime();
    });
    // Validasi untuk memilih kondisi kendaraan
     // Menampilkan warning saat dropdown diklik, tapi belum memilih nilai
    $("#kondisi_kendaraan").on("focus", function () {
        // Menampilkan peringatan jika dropdown kosong (belum dipilih)
        if (!$(this).val()) {
            showWarning($(this), $("#warning-kondisi"));
        }
    });

    // Menyembunyikan warning saat nilai dipilih
    $("#kondisi_kendaraan").on("change", function () {
        if ($(this).val()) {
            hideWarning($(this), $("#warning-kondisi"));
        }
    });
    
    $("input[name='detail']").on("blur", function () {
            if (!$(this).val()) {
                $("#warning-isi-detail").removeClass("hidden");
                $(this).addClass("border-red-500");
            }
        }).on("input", function () {
            if ($(this).val()) {
                $("#warning-isi-detail").addClass("hidden");
                $(this).removeClass("border-red-500");
            }
        });
    // $("#btn-batal").on("click", function () {
    //     Swal.fire({
    //         title: "Yakin ingin membatalkan?",
    //         text: "Semua perubahan tidak akan disimpan.",
    //         icon: "warning",
    //         showCancelButton: true,
    //         confirmButtonText: "Ya, Batal",
    //         cancelButtonText: "Tidak",
    //     }).then((result) => {
    //         if (result.isConfirmed) {
    //             window.location.href = "{{ route('peminjaman') }}";
    //         }
    //     });
    // });

    $("#form-pengembalian").on("submit", function (e) {
        e.preventDefault();

        let errors = [];

        // if (!$("#tgl").val()) {
        //     errors.push("Tanggal pengembalian harus diisi");
        // }

        // if (!$("#jam").val()) {
        //     errors.push("Jam pengembalian harus diisi");
        // }
        

        if (!$("#kondisi_kendaraan").val()) {
            errors.push("Kondisi kendaraan harus dipilih");
        }

        if (errors.length > 0) {
            Swal.fire({
                title: "Validasi Error!",
                html: errors.join("<br>"),
                icon: "error",
            });
            return false;
        }

        Swal.fire({
            title: "Konfirmasi Pengembalian",
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
                const form = $(this);
                const url = form.attr("action");
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
                            },
                        });
                    },
                    success: function (response) {
                        Swal.fire({
                            title: "Berhasil!",
                            text: "Pengembalian kendaraan berhasil disimpan.",
                            icon: "success",
                            confirmButtonColor: '#3085d6',
                            confirmButtonText: "OK",
                        }).then(() => {
                            const urlParams = new URLSearchParams(window.location.search);
                            const page = urlParams.get('page') || 1;
                            const search = urlParams.get('search') || '';
                            
                                // window.location.href = response.redirect;
                            if (response.redirect) {
                                // window.location.href = response.redirect;
                                window.location.href = response.redirect + '?page=' + page + '&search=' + search;
                            }
                            
                            // window.location.href = "{{ route('peminjaman') }}";
                        });
                    },
                    error: function (xhr) {
                        Swal.fire({
                            title: "Gagal!",
                            text:
                                xhr.responseJSON?.message ||
                                "Terjadi kesalahan pada server, silakan coba lagi.",
                            icon: "error",
                            confirmButtonColor: '#3085d6',
                            confirmButtonText: "OK",
                        });
                    },
                });
            }
        });
    });

    $("#tgl, #jam, #kondisi_kendaraan").on("input change", function () {
        if ($(this).val()) {
            $(this).removeClass("border-red-500");
        }
    });
});

    </script>
    

</body>
</html>
</x-app-layout>