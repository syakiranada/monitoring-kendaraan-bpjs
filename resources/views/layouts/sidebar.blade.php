<div class="fixed left-0 top-0 h-full w-64 bg-white shadow-md transition-all duration-300">
    <div class="p-4 border-b">
        <h2 class="text-xl font-bold text-gray-800">Admin</h2>
    </div>
    <nav class="py-4">
        <ul>
            @php
                $menuItems = [
                    ['icon' => 'home', 'title' => 'Beranda', 'route' => 'beranda'],

                ];
            @endphp

            @foreach($menuItems as $item)
                <li>
                    <a 
                        href="{{ route($item['route']) }}" 
                        class="flex items-center px-4 py-3 text-gray-700 hover:bg-blue-50 
                               {{ isset($item['active']) && $item['active'] ? 'bg-blue-100 text-blue-600' : '' }}"
                    >
                        <i class="fas fa-{{ $item['icon'] }} mr-3 w-5 text-center"></i>
                        {{ $item['title'] }}
                    </a>
                </li>
            @endforeach
        </ul>
    </nav>
</div>
