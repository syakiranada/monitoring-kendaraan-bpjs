<x-app-layout>
    <div class="min-h-screen flex items-center justify-center py-12 px-4">
        <div class="max-w-2xl w-full bg-white p-6 rounded-lg shadow-lg">
            <h2 class="text-2xl font-bold mb-6 text-center">Edit Akun Pengguna</h2>

            <!-- Form Edit Akun -->
            <form id="edit-form" action="{{ route('admin.kelola-akun.update', $user->id) }}" method="POST">
                @csrf
                @method('PATCH')

                <input type="hidden" name="page" value="{{ request()->query('page', 1) }}">
                <input type="hidden" name="search" value="{{ request()->query('search') }}">

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nama</label>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}" 
                           class="w-full p-2.5 border rounded-lg" required>
                    @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}" 
                           class="w-full p-2.5 border rounded-lg" required>
                    @error('email')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Peran</label>
                    <select name="peran" required class="w-full p-2.5 border rounded-lg">
                        <option value="admin" {{ old('peran', $user->peran) == 'admin' ? 'selected' : '' }}>Admin</option>
                        <option value="pengguna" {{ old('peran', $user->peran) == 'pengguna' ? 'selected' : '' }}>Pengguna</option>
                    </select>
                    @error('peran')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>                

                <div class="flex justify-end space-x-4 mb-2">
                    <button type="button" onclick="window.location.href='{{ route('admin.kelola-akun.index', ['page' => request('page'), 'search' => request('search')]) }}'" 
                            class="bg-red-600 text-white px-6 py-2.5 rounded-lg font-semibold hover:bg-red-700 transition">
                        Batal
                    </button>
                    <button type="button" id="save-btn" class="bg-blue-600 text-white px-6 py-2.5 rounded-lg font-semibold hover:bg-blue-700 transition">
                        Simpan
                    </button>
                </div>
            </form>

            <!-- Form Reset Password -->
            <form id="reset-form" action="{{ route('admin.kelola-akun.reset-password', $user->id) }}" method="POST">
                @csrf
                <input type="hidden" name="page" value="{{ request()->query('page', 1) }}">
                <input type="hidden" name="search" value="{{ request()->query('search') }}">
                
                <button type="button" id="reset-btn" class="w-full bg-red-500 text-white px-6 py-2.5 rounded-lg font-semibold hover:bg-red-600 transition">
                    Reset Password
                </button>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // konfirmasi simpan perubahan
            document.getElementById('save-btn').addEventListener('click', function() {
                Swal.fire({
                    title: 'Konfirmasi Perubahan',
                    text: 'Apakah Anda yakin ingin menyimpan perubahan?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Ya, Simpan',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        document.getElementById('edit-form').submit();
                    }
                });
            });

            // konfirmasi reset password
            document.getElementById('reset-btn').addEventListener('click', function() {
                Swal.fire({
                    title: 'Reset Password',
                    text: 'Apakah Anda yakin ingin mereset password ke default?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Ya, Reset',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        document.getElementById('reset-form').submit();
                    }
                });
            });

            // notifikasi sukses jika ada session success
            @if(session('success'))
                Swal.fire({
                    title: "Berhasil!",
                    text: "{{ session('success') }}",
                    icon: "success",
                    confirmButtonText: "OK"
                });
            @endif
        });
    </script>
</x-app-layout>
