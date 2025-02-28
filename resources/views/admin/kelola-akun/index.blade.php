<x-app-layout>
    <div class="p-6">
        <h2 class="text-2xl font-semibold text-gray-800 dark:text-white mb-4">Kelola Akun Pengguna</h2>

        @if(session('success'))
            <div class="p-4 mb-4 text-sm text-green-800 rounded-lg bg-green-50 dark:bg-gray-800 dark:text-green-400">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="p-4 mb-4 text-sm text-red-800 rounded-lg bg-red-50 dark:bg-gray-800 dark:text-red-400">
                {{ session('error') }}
            </div>
        @endif

        <!-- Form Import Excel/CSV -->
        <div class="mb-6">
            <form action="{{ route('admin.kelola-akun.import') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                @csrf
                <div>
                    <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Unggah File Excel/CSV:</label>
                    <input type="file" name="file" accept=".xlsx,.xls,.csv" required 
                           class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 dark:text-gray-400 dark:border-gray-600 dark:placeholder-gray-400 focus:outline-none">
                    @error('file')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                
                <div class="flex items-center space-x-4">
                    <button type="submit" class="text-white bg-blue-500 hover:bg-blue-600 focus:outline-none focus:ring-4 focus:ring-blue-300 font-medium rounded-full text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                        Impor Akun
                    </button>
                    {{-- <a href="{{ route('admin.kelola-akun.template') }}" class="text-gray-900 bg-white border border-gray-300 hover:bg-gray-100 focus:ring-4 focus:ring-gray-200 font-medium rounded-full text-sm px-5 py-2.5 text-center dark:bg-gray-800 dark:text-white dark:border-gray-600 dark:hover:bg-gray-700 dark:hover:border-gray-600 dark:focus:ring-gray-700">
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
                    class="block p-2 ps-10 text-sm text-gray-900 border border-gray-300 rounded-lg w-60 bg-gray-50 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" 
                    placeholder="Cari pengguna, ..."
                    value="{{ request('search') }}"
                >
                <div class="absolute inset-y-0 start-0 flex items-center ps-3">
                    <svg class="w-4 h-4 text-gray-500 dark:text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 20">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 19-4-4m0-7A7 7 0 1 1 1 8a7 7 0 0 1 14 0Z"/>
                    </svg>
                </div>
            </div>
        </form>

        <!-- Tabel Daftar Akun -->
        <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
            <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
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
                        <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700">
                            <td class="px-6 py-4">{{ $user->name }}</td>
                            <td class="px-6 py-4">{{ $user->email }}</td>
                            <td class="px-6 py-4">{{ $user->peran }}</td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $user->status ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ $user->status ? 'Aktif' : 'Nonaktif' }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <form action="{{ route('admin.kelola-akun.toggle-status', $user->id) }}" method="POST" class="toggle-status-form">
                                    @csrf
                                    @method('PATCH')
                                    <button type="button" class="toggle-status-btn px-3 py-1 text-xs font-semibold text-white rounded-full {{ $user->status ? 'bg-red-500 hover:bg-red-600' : 'bg-green-500 hover:bg-green-600' }}">
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
                            Swal.fire({
                                title: 'Berhasil!',
                                text: 'Status akun telah diperbarui.',
                                icon: 'success'
                            });
                        }
                    });
                });
            });
        });
    </script>    
</x-app-layout>