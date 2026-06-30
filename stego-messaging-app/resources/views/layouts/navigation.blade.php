<nav x-data="{ mobileOpen: false }" class="sticky top-0 z-50 bg-slate-950/80 backdrop-blur-md border-b border-slate-800/50">

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-14">

            {{-- ── Left: Logo + Nav Links ── --}}
            <div class="flex items-center gap-8">

                {{-- Logo --}}
                <a href="{{ route('dashboard') }}" class="flex items-center gap-2.5 group shrink-0">
                    <div class="flex items-center justify-center w-10 h-10 rounded-xl bg-blue-600/10 border border-blue-500/25 group-hover:border-blue-500/60 group-hover:bg-blue-600/20 transition-all duration-200 shadow-sm shadow-blue-900/20">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" class="w-[18px] h-[18px] text-blue-400 group-hover:text-blue-300 transition-colors">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z" />
                        </svg>
                    </div>
                    <span class="text-base font-extrabold tracking-tight text-white">
                        Steg<span class="text-blue-400">Chat</span>
                    </span>
                </a>

                {{-- ── Desktop Nav Links ── --}}
                <div class="hidden sm:flex items-center gap-1">

                    {{-- Dashboard --}}
                    <a href="{{ route('dashboard') }}"
                       class="px-3 py-1.5 rounded-md text-sm font-medium transition-all duration-150
                              {{ request()->routeIs('dashboard')
                                  ? 'bg-slate-800 text-white'
                                  : 'text-slate-400 hover:text-white hover:bg-slate-800/60' }}">
                        Dashboard
                    </a>

                    {{-- Chats --}}
                    <a href="{{ url('/chats') }}"
                       class="px-3 py-1.5 rounded-md text-sm font-medium transition-all duration-150
                              {{ request()->routeIs('chats')
                                  ? 'bg-slate-800 text-white'
                                  : 'text-slate-400 hover:text-white hover:bg-slate-800/60' }}">
                        Chats
                    </a>

                    {{-- Requests (Livewire: binding preserved) --}}
                    <div class="flex items-center">
                        <livewire:request-inbox />
                    </div>

                    {{-- ── Add Friends Dropdown ── --}}
                    <div x-data="{ open: false }" class="relative" @click.away="open = false">
                        <button
                            @click="open = !open"
                            :class="open ? 'bg-slate-800 text-white' : 'text-slate-400 hover:text-white hover:bg-slate-800/60'"
                            class="flex items-center gap-1.5 px-3 py-1.5 rounded-md text-sm font-medium transition-all duration-150 focus:outline-none"
                        >
                            <svg class="w-3.5 h-3.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M18 7.5v3m0 0v3m0-3h3m-3 0h-3m-2.25-4.125a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0ZM3 19.235v-.11a6.375 6.375 0 0 1 12.75 0v.109A12.318 12.318 0 0 1 9.374 21c-2.331 0-4.512-.645-6.374-1.766Z" />
                            </svg>
                            Add Friends
                            <svg class="w-3 h-3 transition-transform duration-150" :class="{ 'rotate-180': open }" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </button>

                        {{-- Dropdown Panel --}}
                        <div
                            x-show="open"
                            x-transition:enter="transition ease-out duration-150"
                            x-transition:enter-start="opacity-0 translate-y-1"
                            x-transition:enter-end="opacity-100 translate-y-0"
                            x-transition:leave="transition ease-in duration-100"
                            x-transition:leave-start="opacity-100 translate-y-0"
                            x-transition:leave-end="opacity-0 translate-y-1"
                            class="absolute top-full left-0 mt-2 w-80 bg-slate-900 border border-slate-800/80 rounded-xl shadow-2xl shadow-black/40 overflow-hidden z-50"
                            style="display: none;"
                        >
                            <div class="px-4 py-2.5 border-b border-slate-800 flex items-center gap-2">
                                <svg class="w-3.5 h-3.5 text-blue-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M18 7.5v3m0 0v3m0-3h3m-3 0h-3m-2.25-4.125a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0ZM3 19.235v-.11a6.375 6.375 0 0 1 12.75 0v.109A12.318 12.318 0 0 1 9.374 21c-2.331 0-4.512-.645-6.374-1.766Z" />
                                </svg>
                                <span class="text-xs font-semibold text-slate-300 uppercase tracking-wider">Find Users</span>
                            </div>
                            <livewire:add-friend />
                        </div>
                    </div>

                    {{-- ── My Friends Dropdown ── --}}
                    <div x-data="{ open: false }" class="relative" @click.away="open = false">
                        <button
                            @click="open = !open"
                            :class="open ? 'bg-slate-800 text-white' : 'text-slate-400 hover:text-white hover:bg-slate-800/60'"
                            class="flex items-center gap-1.5 px-3 py-1.5 rounded-md text-sm font-medium transition-all duration-150 focus:outline-none"
                        >
                            <svg class="w-3.5 h-3.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z" />
                            </svg>
                            My Friends
                            <svg class="w-3 h-3 transition-transform duration-150" :class="{ 'rotate-180': open }" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </button>

                        {{-- Dropdown Panel --}}
                        <div
                            x-show="open"
                            x-transition:enter="transition ease-out duration-150"
                            x-transition:enter-start="opacity-0 translate-y-1"
                            x-transition:enter-end="opacity-100 translate-y-0"
                            x-transition:leave="transition ease-in duration-100"
                            x-transition:leave-start="opacity-100 translate-y-0"
                            x-transition:leave-end="opacity-0 translate-y-1"
                            class="absolute top-full left-0 mt-2 w-80 bg-slate-900 border border-slate-800/80 rounded-xl shadow-2xl shadow-black/40 overflow-hidden z-50"
                            style="display: none;"
                        >
                            <div class="px-4 py-2.5 border-b border-slate-800 flex items-center gap-2">
                                <svg class="w-3.5 h-3.5 text-emerald-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z" />
                                </svg>
                                <span class="text-xs font-semibold text-slate-300 uppercase tracking-wider">Active Connections</span>
                            </div>
                            {{-- Livewire component: binding preserved --}}
                            <div class="p-3">
                                <livewire:friends-list />
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            {{-- ── Right: User Dropdown ── --}}
            <div class="hidden sm:flex items-center gap-3">
                <div class="h-5 w-px bg-slate-800"></div>

                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="group flex items-center gap-2 px-2.5 py-1.5 rounded-lg border border-transparent hover:border-slate-700 hover:bg-slate-800/60 transition-all duration-150 focus:outline-none">
                            <span class="flex items-center justify-center w-8 h-8 rounded-full bg-blue-600/20 border border-blue-500/30 text-blue-300 text-xs font-bold uppercase select-none">
                                {{ substr(Auth::user()->name, 0, 1) }}
                            </span>
                            <span class="text-sm font-medium text-slate-300 group-hover:text-white transition-colors max-w-[120px] truncate">
                                {{ Auth::user()->name }}
                            </span>
                            <svg class="w-3.5 h-3.5 text-slate-500 group-hover:text-slate-300 transition-colors" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <div class="px-3 py-2 border-b border-slate-800">
                            <p class="text-xs font-medium text-white truncate">{{ Auth::user()->name }}</p>
                            <p class="text-xs text-slate-500 truncate mt-0.5">{{ Auth::user()->email }}</p>
                        </div>

                        <x-dropdown-link :href="route('profile.edit')" class="flex items-center gap-2 text-sm">
                            <svg class="w-3.5 h-3.5 text-slate-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                            </svg>
                            {{ __('Profile') }}
                        </x-dropdown-link>

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-dropdown-link :href="route('logout')"
                                onclick="event.preventDefault(); this.closest('form').submit();"
                                class="flex items-center gap-2 text-sm text-rose-400 hover:text-rose-300">
                                <svg class="w-3.5 h-3.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15M12 9l-3 3m0 0l3 3m-3-3h12.75" />
                                </svg>
                                {{ __('Sign Out') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            {{-- ── Mobile Hamburger ── --}}
            <div class="flex items-center sm:hidden">
                <button @click="mobileOpen = !mobileOpen"
                        class="p-2 rounded-md text-slate-400 hover:text-white hover:bg-slate-800 transition-all duration-150 focus:outline-none">
                    <svg class="h-5 w-5" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': mobileOpen, 'inline-flex': !mobileOpen}" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': !mobileOpen, 'inline-flex': mobileOpen}" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

        </div>
    </div>

    {{-- ── Mobile Drawer ── --}}
    <div :class="{'block': mobileOpen, 'hidden': !mobileOpen}" class="hidden sm:hidden border-t border-slate-800/60 bg-slate-950">
        <div class="px-4 pt-3 pb-2 space-y-1">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                Dashboard
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="url('/chats')" :active="request()->routeIs('chats')">
                Chats
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('dashboard')">
                Add Friends
            </x-responsive-nav-link>
            <x-responsive-nav-link :href="route('dashboard')">
                My Friends
            </x-responsive-nav-link>
        </div>

        <div class="border-t border-slate-800/60 px-4 py-3">
            <div class="flex items-center gap-3 mb-3">
                <span class="flex items-center justify-center w-8 h-8 rounded-full bg-blue-600/20 border border-blue-500/30 text-blue-300 text-sm font-bold uppercase">
                    {{ substr(Auth::user()->name, 0, 1) }}
                </span>
                <div>
                    <p class="text-sm font-medium text-white">{{ Auth::user()->name }}</p>
                    <p class="text-xs text-slate-500">{{ Auth::user()->email }}</p>
                </div>
            </div>

            <div class="space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')">
                    {{ __('Profile') }}
                </x-responsive-nav-link>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <x-responsive-nav-link :href="route('logout')"
                            onclick="event.preventDefault(); this.closest('form').submit();">
                        {{ __('Sign Out') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>

</nav>
