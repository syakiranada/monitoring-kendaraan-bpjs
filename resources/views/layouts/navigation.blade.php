<nav style="background-color: #3575B8" class="fixed top-0 transition-all duration-300 ease-in-out z-500 w-full">
    <div class="mx-auto px-4 sm:px-6">
        <div class="flex justify-between items-center h-16">
            <div class="flex items-center">
                <div class="w-10 h-10">
                    <img 
                        alt="Logo" 
                        src="{{ url('images/logo_simonas.png') }}" 
                        class="w-full h-full object-contain"
                    />
                </div>
                <div><h1 class="text-white text-2xl md:text-3xl font-bold">SiMonas</h1>
                    
                    <p class="text-white text-xs md:text-sm">
                        Sistem Monitoring Kendaraan Dinas
                    </p>
                </div>
            </div>

            <!-- Existing Authentication Dropdown Preserved -->
            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button 
                            style="background-color: #D6DD50;" 
                            class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-full text-black transition ease-in-out duration-150 hover:brightness-90">   <div>{{ Auth::user()->name }}</div>

                            <div class="ms-1">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content" style="background-color: #D6DD50;">
                        <x-dropdown-link :href="route('profile.edit')">
                            {{ __('Profile') }}
                        </x-dropdown-link>

                        <!-- Authentication -->
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf

                            <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault();
                                                this.closest('form').submit();">
                                {{ __('Log Out') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>

                {{--  <!-- Chevron Button -->
                <button class="bg-yellow-400 hover:bg-yellow-500 text-black w-10 h-10 rounded-full flex items-center justify-center ml-2 transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </button>  --}}
            </div>

            <!-- Mobile Hamburger Menu -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-white hover:text-gray-200 hover:bg-blue-700 focus:outline-none focus:bg-blue-700 focus:text-white transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Existing Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden bg-blue-600">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('beranda')" :active="request()->routeIs('beranda')">
                {{ __('Beranda') }}
            </x-responsive-nav-link>
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-blue-700">
            <div class="px-4">
                <div class="font-medium text-base text-white">{{ Auth::user()->name }}</div>
                <div class="font-medium text-sm text-blue-100">{{ Auth::user()->email }}</div>
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')">
                    {{ __('Profile') }}
                </x-responsive-nav-link>

                <!-- Authentication -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf

                    <x-responsive-nav-link :href="route('logout')"
                            onclick="event.preventDefault();
                                        this.closest('form').submit();">
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>

<style>
    body {
        padding-top: 64px; /* Sesuaikan dengan tinggi navbar */
    }
</style>

{{--  <footer class="bg-[#3575B8] text-white py-4">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col md:flex-row justify-between items-center">
        <p class="text-sm">&copy; {{ date('Y') }} SiMonas. All rights reserved.</p>
        <div class="flex space-x-4 mt-2 md:mt-0">
            <a href="#" class="hover:underline">Privacy Policy</a>
            <a href="#" class="hover:underline">Terms of Service</a>
            <a href="#" class="hover:underline">Contact</a>
        </div>
    </div>
</footer>  --}}
