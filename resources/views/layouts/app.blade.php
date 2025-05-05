<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Monitoring Kendaraan Dinas') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
        
        <!-- Font Awesome -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <!-- Custom Styles -->
        <style>
            .sidebar-hidden {
                width: 50px !important;
                transform: none;
            }
            
            .sidebar-hidden .sidebar-text,
            .sidebar-hidden .sidebar-header-text,
            .sidebar-hidden #collapseBtn {
                display: none;
            }

            .sidebar-hidden #logoContainer {
                display: none; /* Menyembunyikan logo saat sidebar collapse */
            }
            
            .sidebar-hidden #expandBtn {
                display: block;
            }
            
            .content-expanded {
                margin-left: 50px !important;
                width: calc(100% - 50px);
            }
        
            .sidebar-icon {
                width: 20px;
                height: 20px;
                display: flex;
                justify-content: center;
                align-items: center;
            }
        
            .sidebar-header {
                height: 64px;
                display: flex;
                align-items: center;
                gap: 10px;
            }
        
            #expandBtn {
                left: 4px;
                margin-top: 4px;
                display: none;
                padding: 8px;
                background: transparent;
                border: none;
                cursor: pointer;
            }

            #collapseBtn {
                padding: 8px;
                background: transparent;
                border: none;
                cursor: pointer;
            }
        
            .toggle-btn {
                color: #000000;
                transition: color 0.2s;
            }
        
            .toggle-btn:hover {
                color: #333;
            }

            #content {
                display: flex;
                flex-direction: column;
                min-height: 100vh;
            }
        </style>
    </head>
    <body class="font-sans antialiased">
        {{--  <header>
            <!-- Logo -->
            <div class="logo">
                <a href="{{ url('/') }}">
                    <img src="{{ asset('logo_simonas_hitam.png') }}" alt="Logo SiMonas" class="w-32 h-auto">
                </a>
            </div>
        </header>  --}}
        <div id="main-container" class="flex min-h-screen top-0 h-full bg-gray-100">
            <!-- Sidebar -->
            @php
            use Illuminate\Support\Facades\Auth;

            $menuItems = [];
            $user = Auth::user();

            if ($user->peran === 'admin') {
                $menuItems = array_merge($menuItems, [
                    ['icon' => 'home', 'title' => 'Beranda', 'route' => 'admin.beranda', 'active_routes' => [
                        'admin.beranda'
                    ]],
                    ['icon' => 'car', 'title' => 'Daftar Kendaraan', 'route' => 'kendaraan.daftar_kendaraan', 'active_routes' => [
                        'kendaraan.daftar_kendaraan',
                        'kendaraan.tambah',
                        'kendaraan.edit',
                        'kendaraan.update',
                        'kendaraan.detail',
                        'kendaraan.hapus',
                        'kendaraan.store',
                        'kendaraan.hitungDepresiasi'
                    ]],
                    ['icon' => 'file-lines', 'title' => 'Pengajuan Peminjaman', 'route' => 'admin.pengajuan-peminjaman.index', 'active_routes' => [
                        'admin.pengajuan-peminjaman.index',
                        'admin.pengajuan-peminjaman.detail',
                    ]],
                    ['icon' => 'money-bill', 'title' => 'Pajak', 'route' => 'pajak.daftar_kendaraan_pajak', 'active_routes' => [
                        'pajak.daftar_kendaraan_pajak',
                        'pajak.kelola',
                        'pajak.edit',
                        'pajak.update',
                        'pajak.detail',
                        'pajak.hapus',
                        'pajak.store',
                        'pajak.deleteFile'
                    ]],
                    ['icon' => 'shield', 'title' => 'Asuransi', 'route' => 'asuransi.daftar_kendaraan_asuransi', 'active_routes' => [
                        'asuransi.daftar_kendaraan_asuransi',
                        'asuransi.kelola',
                        'asuransi.edit',
                        'asuransi.update',
                        'asuransi.detail',
                        'asuransi.hapus',
                        'asuransi.store',
                        'asuransi.deleteFile'
                    ]], 
                    ['icon' => 'screwdriver-wrench', 'title' => 'Servis Rutin', 'route' => 'admin.servisRutin', 'active_routes' => [
                        'admin.servisRutin',
                        'admin.servisRutin.create',
                        'admin.servisRutin.store',
                        'admin.servisRutin.detail',
                        'admin.servisRutin.edit',
                        'admin.servisRutin.update',
                        'admin.servisRutin.destroy'
                    ]],
                    ['icon' => 'gears', 'title' => 'Servis Insidental', 'route' => 'admin.servisInsidental', 'active_routes' => [
                        'admin.servisInsidental',
                        'admin.servisInsidental.create',
                        'admin.servisInsidental.store',
                        'admin.servisInsidental.detail',
                        'admin.servisInsidental.edit',
                        'admin.servisInsidental.update',
                        'admin.servisInsidental.destroy'
                    ]],
                    ['icon' => 'gas-pump', 'title' => 'Pengisian BBM', 'route' => 'admin.pengisianBBM', 'active_routes' => [
                        'admin.pengisianBBM',
                        'admin.pengisianBBM.create',
                        'admin.pengisianBBM.store',
                        'admin.pengisianBBM.detail',
                        'admin.pengisianBBM.edit',
                        'admin.pengisianBBM.update',
                        'admin.pengisianBBM.destroy'
                    ]],
                    ['icon' => 'list-check', 'title' => 'Cek Fisik', 'route' => 'admin.cek-fisik.index', 'active_routes' => [
                        'admin.cek-fisik.index',
                        'admin.cek-fisik.create',
                        'admin.cek-fisik.edit',
                        'admin.cek-fisik.detail',
                    ]],
                    ['icon' => 'clock-rotate-left', 'title' => 'Riwayat', 'route' => 'admin.riwayat.index', 'active_routes' => [
                        'admin.riwayat.index',
                        'admin.riwayat.asuransi',
                        'admin.riwayat.cek-fisik',
                        'admin.riwayat.pajak',
                        'admin.riwayat.peminjaman',
                        'admin.riwayat.pengisian-bbm',
                        'admin.riwayat.servis-insidental',
                        'admin.riwayat.servis-rutin',
                        'admin.riwayat.detail-asuransi',
                        'admin.riwayat.detail-cek-fisik',
                        'admin.riwayat.detail-pajak',
                        'admin.riwayat.detail-peminjaman',
                        'admin.riwayat.detail-pengisian-bbm',
                        'admin.riwayat.detail-servis-insidental',
                        'admin.riwayat.detail-servis-rutin',
                    ]],
                    ['icon' => 'users', 'title' => 'Kelola Akun', 'route' => 'admin.kelola-akun.index', 'active_routes' => [
                        'admin.kelola-akun.index',
                        'admin.kelola-akun.edit',
                    ]],
                ]);
            } elseif ($user->peran === 'pengguna') {
                $menuItems = array_merge($menuItems, [
                    ['icon' => 'home', 'title' => 'Beranda', 'route' => 'beranda', 'active_routes' => [
                        'beranda'
                    ]],
                    ['icon' => 'car', 'title' => 'Daftar Kendaraan', 'route' => 'kendaraan', 'active_routes' => 
                        ['kendaraan.getDetail'
                    ]],
                    ['icon' => 'pen-to-square', 'title' => 'Peminjaman', 'route' => 'peminjaman', 'active_routes' => 
                        ['peminjaman.showForm', 'peminjaman.simpan', 'peminjaman.getKendaraan', 'peminjaman.detail', 'peminjaman.batal','peminjaman.showFormPengembalian', 'peminjaman.pengembalian','peminjaman.showfromperpanjangan','peminjaman.perpanjang'
                    ]],
                    ['icon' => 'gears', 'title' => 'Servis Insidental', 'route' => 'servisInsidental', 'active_routes' => [
                        'servisInsidental',
                        'servisInsidental.create',
                        'servisInsidental.store',
                        'servisInsidental.detail',
                        'servisInsidental.edit',
                        'servisInsidental.update',
                        'servisInsidental.destroy'
                    ]],
                    ['icon' => 'gas-pump', 'title' => 'Pengisian BBM', 'route' => 'pengisianBBM', 'active_routes' => [
                        'pengisianBBM',
                        'pengisianBBM.create',
                        'pengisianBBM.store',
                        'pengisianBBM.detail',
                        'pengisianBBM.edit',
                        'pengisianBBM.update',
                        'pengisianBBM.destroy'
                    ]],
                ]);
            }
            @endphp
            
            <div id="sidebar" class="fixed left-0 top-0 h-full w-64 bg-white shadow-md transition-all duration-300 ease-in-out z-50">
                <div class="p-4 border-b sidebar-header flex items-center justify-between">
                    <div id="logoContainer" class="flex items-center justify-between">
                        <img src="{{ asset('logo_bpjs.png') }}" alt="BPJS Logo">
                    </div>
                    
                    <div>
                        <!-- Tombol collapse -->
                        <button id="collapseBtn" class="toggle-btn">
                            <i class="fas fa-chevron-left"></i>
                        </button>
                        <!-- Tombol expand -->
                        <button id="expandBtn" class="toggle-btn">
                            <i class="fas fa-chevron-right"></i>
                        </button>
                    </div>
                </div>
                <nav class="py-4">
                    <ul>
                        @foreach($menuItems as $item)
                            @php
                                $isActive = request()->routeIs($item['route']);
                                
                                // Tambahkan pengecekan untuk active_routes jika ada
                                if (!$isActive && isset($item['active_routes'])) {
                                    foreach ($item['active_routes'] as $activeRoute) {
                                        if (request()->routeIs($activeRoute)) {
                                            $isActive = true;
                                            break;
                                        }
                                    }
                                }
                            @endphp
                            <li>
                                <a 
                                    href="{{ route($item['route']) }}" 
                                    class="flex items-center px-4 py-3 text-gray-700 hover:bg-blue-50 
                                        {{ $isActive ? 'bg-blue-100 text-blue-600' : '' }}"
                                >
                                    <div class="sidebar-icon">
                                        <i class="fas fa-{{ $item['icon'] }}"></i>
                                    </div>
                                    <span class="ml-3 sidebar-text">{{ $item['title'] }}</span>
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </nav>
             </div>

            <!-- Content Area -->
            <div id="content" class="ml-64 w-full transition-all duration-300 ease-in-out">
                @include('layouts.navigation')
                
                {{--  @isset($header)
                    <header class="bg-white shadow">
                        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                            {{ $header }}
                        </div>
                    </header>
                @endisset  --}}

                <!-- Konten akan masuk di sini -->
                <main class="p-6">
                    {{ $slot }}
                </main>
            </div>
        </div>

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const sidebar = document.getElementById('sidebar');
                const content = document.getElementById('content');
                const collapseBtn = document.getElementById('collapseBtn');
                const expandBtn = document.getElementById('expandBtn');
            
                function collapseSidebar() {
                    sidebar.classList.add('sidebar-hidden');
                    content.classList.add('content-expanded');
                    content.style.marginLeft = '50px';
                }
            
                function expandSidebar() {
                    sidebar.classList.remove('sidebar-hidden');
                    content.classList.remove('content-expanded');
                    content.style.marginLeft = '256px';
                }
            
                // Collapse button click
                collapseBtn.addEventListener('click', collapseSidebar);
            
                // Expand button click
                expandBtn.addEventListener('click', expandSidebar);
            });
        </script>
    </body>
</html>