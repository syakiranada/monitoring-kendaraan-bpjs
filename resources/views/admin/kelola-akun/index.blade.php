<x-app-layout>
    <div class="p-6">
        <h2 class="text-2xl font-semibold text-gray-800 mb-4">Kelola Akun Pengguna</h2>

        {{-- @if(session('success'))
            <div class="p-4 mb-4 text-sm text-green-800 rounded-lg bg-green-50">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="p-4 mb-4 text-sm text-red-800 rounded-lg bg-red-50">
                {{ session('error') }}
            </div>
        @endif --}}

        <!-- Form Import Excel/CSV -->
        <div class="mb-6">
            <form action="{{ route('admin.kelola-akun.import') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                @csrf
                <div>
                    <label class="block mb-2 text-sm font-medium text-gray-900">Unggah File Excel/CSV:</label>
                    <input type="file" name="file" accept=".xlsx,.xls,.csv" required 
                           class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 focus:outline-none">
                    @error('file')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror

                    <p class="mt-2 text-sm text-gray-500">
                        Format file: <span class="font-medium">.xlsx, .xls, .csv</span><br>
                        Maksimal ukuran file: <span class="font-medium">2MB</span><br>
                        Pastikan file memiliki header <span class="font-medium">nama, email, password, peran</span> (huruf kecil, sesuai field database).
                    </p>
                    <!-- Contoh Format Excel/CSV -->
                        <p class="text-sm font-medium text-gray-700 mt-4 mb-2">Contoh format .csv:</p>
                        <pre class="text-sm text-gray-700 bg-white p-2 rounded overflow-x-auto">
nama,email,password,peran
John Doe,john@example.com,password123,pengguna
Jane Doe,jane@example.com,password123,pengguna
                        </pre>
                </div>
                
                <div class="flex items-center space-x-4">
                    <button type="submit" class="text-white bg-blue-500 hover:bg-blue-600 focus:outline-none focus:ring-4 focus:ring-blue-300 font-medium rounded-full text-sm px-5 py-2.5 text-center">
                        Impor Akun
                    </button>
                    {{-- <a href="{{ route('admin.kelola-akun.template') }}" class="text-gray-900 bg-white border border-gray-300 hover:bg-gray-100 focus:ring-4 focus:ring-gray-200 font-medium rounded-full text-sm px-5 py-2.5 text-center">
                        Unduh Template
                    </a> --}}
                </div>
            </form>
        </div>

        <!-- Search Form -->
        <form action="{{ route('admin.kelola-akun.index') }}" method="GET" class="flex justify-end pb-4">
            <div class="relative">
                <input 
                    type="text" 
                    name="search"
                    class="block p-2 ps-10 text-sm text-gray-900 border border-gray-300 rounded-lg w-60 bg-gray-50 focus:ring-blue-500 focus:border-blue-500" 
                    placeholder="Cari pengguna, ..."
                    value="{{ request('search') }}"
                >
                <div class="absolute inset-y-0 start-0 flex items-center ps-3">
                    <svg class="w-4 h-4 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z"/>
                    </svg>
                </div>
            </div>
        </form>

        <!-- Tabel Daftar Akun -->
        <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
            <table class="w-full text-sm text-left text-gray-500">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3">Nama</th>
                        <th scope="col" class="px-6 py-3">Email</th>
                        <th scope="col" class="px-6 py-3">Peran</th>
                        <th scope="col" class="px-6 py-3">Status</th>
                        <th scope="col" class="px-6 py-3">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($users as $index => $user)
                        <tr class="bg-white border-b">
                            <td class="px-6 py-4">{{ $user->name }}</td>
                            <td class="px-6 py-4">{{ $user->email }}</td>
                            <td class="px-6 py-4">{{ $user->peran }}</td>
                            <td class="px-6 py-4">
                                <span class="{{ $user->status ? 'text-green-600' : 'text-red-600' }}">
                                    {{ $user->status ? 'AKTIF' : 'NONAKTIF' }}
                                </span>
                            </td>
                            <td class="flex space-x-2 px-6 py-4">
                                <a href="{{ route('admin.kelola-akun.edit', ['id' => $user->id, 'page' => request()->query('page'), 'search' => request()->query('search')]) }}"
                                    class="font-medium text-yellow-600 hover:underline">
                                    Edit
                                </a>

                                <form action="{{ route('admin.kelola-akun.toggle-status', $user->id) }}" method="POST" class="toggle-status-form">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="page" value="{{ request()->query('page', 1) }}">
                                    <input type="hidden" name="search" value="{{ request()->query('search') }}">

                                    <button type="button" class="toggle-status-btn font-medium {{ $user->status ? 'text-red-600 hover:underline' : 'text-green-600 hover:underline' }}">
                                        {{ $user->status ? 'Nonaktifkan' : 'Aktifkan' }}
                                    </button>
                                </form>                                
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-4 text-center">Tidak ada data pengguna.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="mt-4">
            {{ $users->appends(request()->query())->links() }}
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // notifikasi impor akun
            @if(session('success'))
                Swal.fire({
                    title: "Berhasil!",
                    text: "{{ session('success') }}",
                    icon: "success",
                    confirmButtonText: "OK"
                });
                // }).then(() => {
                //     location.reload(); // Refresh halaman setelah klik OK
                // });
            @endif

            @if(session('error'))
                Swal.fire({
                    title: "Gagal!",
                    text: "{{ session('error') }}",
                    icon: "error",
                    confirmButtonText: "OK"
                });
            @endif

            // aktivasi/nonaktifkan akun
            const buttons = document.querySelectorAll('.toggle-status-btn');
            buttons.forEach(button => {
                button.addEventListener('click', function() {
                    const form = this.closest('.toggle-status-form');
                    Swal.fire({
                        title: 'Apakah Anda yakin?',
                        // text: "Anda tidak dapat mengubah kembali setelah ini!",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Ya, lanjutkan!',
                        cancelButtonText: 'Batal'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            form.submit();
                            // Swal.fire({
                            //     title: 'Berhasil!',
                            //     text: 'Status akun telah diperbarui.',
                            //     icon: 'success'
                            // });
                        }
                    });
                });
            });
        });
    </script>    
</x-app-layout>