<x-app-layout>
    <div class="p-6">
        <h2 class="text-2xl font-semibold text-gray-800 dark:text-white mb-4">Kelola Akun Pengguna</h2>

        <!-- Form Import Excel/CSV -->
        <form action="{{ route('admin.kelola-akun.import') }}" method="POST" enctype="multipart/form-data" class="mb-6">
            @csrf
            <label class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Unggah File Excel/CSV:</label>
            <input type="file" name="file" required class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-gray-50 dark:text-gray-400 dark:border-gray-600 dark:placeholder-gray-400 focus:outline-none">
            <button type="submit" class="mt-4 text-white bg-blue-700 hover:bg-blue-800 focus:outline-none focus:ring-4 focus:ring-blue-300 font-medium rounded-full text-sm px-5 py-2.5 text-center me-2 mb-2 dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                Impor Akun
            </button>
        </form>

        <!-- Tabel Daftar Akun -->
        <table class="min-w-full bg-white dark:bg-gray-800 border">
            <thead class="border-b">
                <tr>
                    <th class="px-4 py-2 text-left text-sm font-semibold text-gray-600">No</th>
                    <th class="px-4 py-2 text-left text-sm font-semibold text-gray-600">Nama</th>
                    <th class="px-4 py-2 text-left text-sm font-semibold text-gray-600">Email</th>
                    <th class="px-4 py-2 text-left text-sm font-semibold text-gray-600">Peran</th>

                </tr>
            </thead>
            <tbody>
                @foreach ($users as $index => $user)
                <tr class="border-b">
                    <td class="px-4 py-2">{{ $index + 1 }}</td>
                    <td class="px-4 py-2">{{ $user->name }}</td>
                    <td class="px-4 py-2">{{ $user->email }}</td>
                    <td class="px-4 py-2">{{ $user->peran }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Pagination -->
        <div class="mt-4">
            {{ $users->links() }}
        </div>
    </div>
</x-app-layout>
