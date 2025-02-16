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
            
            .sidebar-hidden #expandBtn {
                display: block;
            }
            
            .content-expanded {
                margin-left: 50px !important;
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
            }

            #collapseBtn {
                left: 200px;
                margin-top: 4px;
                padding: 10px;
            }
        
            .toggle-btn {
                padding: 8px;
                background: transparent;
                border: none;
                cursor: pointer;
                color: #000000;
                transition: color 0.2s;
            }
        
            .toggle-btn:hover {
                color: #333;
            }
        </style>
    </head>
    <body class="font-sans antialiased">
        {{--  <!-- Floating button to show sidebar -->
        <button id="showSidebarBtn" class="p-2 bg-gray-700 text-white rounded-full hover:bg-gray-600 focus:outline-none shadow-lg">
            <i class="fas fa-bars"></i>
        </button>  --}}

        <div class="flex min-h-screen top-0 h-full bg-gray-100">
            <!-- Sidebar -->
            @php
            use Illuminate\Support\Facades\Auth;

            $user = Auth::user();
            $menuItems = [
                ['icon' => 'home', 'title' => 'Beranda', 'route' => 'beranda'],
            ];

            

            @endphp
            {{--  Buat Sidebar kalo udah ada controller lain, di matiin aja commentnya
            if ($user->peran === 'admin') {
                $menuItems = array_merge($menuItems, [
                    ['icon' => 'car', 'title' => 'Daftar Kendaraan', 'route' => 'admin.kendaraan'],
                    ['icon' => 'hand-paper', 'title' => 'Pengajuan Peminjaman', 'route' => 'admin.peminjaman'],
                    ['icon' => 'file-invoice', 'title' => 'Pajak', 'route' => 'admin.pajak'],
                    ['icon' => 'shield-alt', 'title' => 'Asuransi', 'route' => 'admin.asuransi'],
                    ['icon' => 'wrench', 'title' => 'Servis Rutin', 'route' => 'admin.servis-rutin'],
                    ['icon' => 'gas-pump', 'title' => 'Pengisian BBM', 'route' => 'admin.bbm'],
                    ['icon' => 'clipboard-check', 'title' => 'Cek Fisik', 'route' => 'admin.cek-fisik'],
                    ['icon' => 'history', 'title' => 'Riwayat', 'route' => 'admin.riwayat']
                ]);
            } elseif ($user->peran === 'pengguna') {
                $menuItems = array_merge($menuItems, [
                    ['icon' => 'car', 'title' => 'Daftar Kendaraan', 'route' => 'kendaraan.index'],
                    ['icon' => 'hand-paper', 'title' => 'Peminjaman', 'route' => 'peminjaman.index'],
                    ['icon' => 'wrench', 'title' => 'Servis Insidental', 'route' => 'servis.insidental'],
                    ['icon' => 'gas-pump', 'title' => 'Pengisian BBM', 'route' => 'bbm.index']
                ]);
            }  --}}
            
            <div id="sidebar" class="fixed left-0 top-0 h-full w-64 bg-white shadow-md transition-all duration-300 ease-in-out z-50">
                <div class="p-4 border-b sidebar-header flex items-center justify-between">
                    <div class="flex items-center">
                        <img src="{{ asset('images/logo_bpjs.png') }}" alt="BPJS Logo" class="w-10 h-10 mr-3">
                        {{--  <h2 class="text-xl font-bold text-gray-800 sidebar-header-text">{{ ucfirst($user->peran) }}</h2>  --}}
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
                            <li>
                                <a 
                                    href="{{ route($item['route']) }}" 
                                    class="flex items-center px-4 py-3 text-gray-700 hover:bg-blue-50 
                                        {{ request()->routeIs($item['route']) ? 'bg-blue-100 text-blue-600' : '' }}"
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
            <div id="content" class="sticky top-0 ml-64 w-full transition-all duration-300 ease-in-out">
                @include('layouts.navigation')
                
            
                @isset($header)
                    <header class="bg-white shadow">
                        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                            {{ $header }}
                        </div>
                    </header>
                @endisset
            
                {{--  <main>
                    {{ $slot }}
                </main>  --}}
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
                }
            
                function expandSidebar() {
                    sidebar.classList.remove('sidebar-hidden');
                    content.classList.remove('content-expanded');
                }
            
                // Collapse button click
                collapseBtn.addEventListener('click', collapseSidebar);
            
                // Expand button click
                expandBtn.addEventListener('click', expandSidebar);
            });
            
        </script>
    </body>
</html>