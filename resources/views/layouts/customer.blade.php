<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Área do Cliente - @yield('title', 'Ação RR Veículos')</title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- FontAwesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;600;700&family=DM+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    @include('partials.site-icons')
    @include('partials.theme-styles')
</head>
<body class="min-h-screen flex flex-col md:flex-row">

    <!-- Customer Sidebar -->
    <aside class="w-72 border-r flex flex-col hidden md:flex min-h-screen sticky top-0" style="background-color: var(--bg-sidebar); border-color: var(--border-color);">
        <!-- Logo -->
        <div class="h-20 flex items-center px-6 border-b gap-3" style="border-color: var(--border-color);">
            <a href="/" class="flex items-center gap-2">
                <img src="{{ asset('images/logo-rr.png') }}" alt="RR Veículos" class="brand-logo">
            </a>
        </div>

        <!-- Navigation Links -->
        <div class="flex-1 px-4 py-6 space-y-2">
            <a href="/" class="nav-link flex items-center gap-3 px-4 py-3 rounded-xl">
                <i class="fa-solid fa-house text-lg"></i>
                <span class="font-medium text-sm">Ir para o Início</span>
            </a>
            <a href="{{ route('raffles.my-tickets') }}" class="nav-link flex items-center gap-3 px-4 py-3 rounded-xl {{ Route::currentRouteName() == 'raffles.my-tickets' ? 'is-active' : '' }}">
                <i class="fa-solid fa-ticket text-lg"></i>
                <span class="font-medium text-sm">Meus Bilhetes</span>
            </a>
            <a href="{{ route('support.index') }}" class="nav-link flex items-center gap-3 px-4 py-3 rounded-xl {{ Route::currentRouteName() == 'support.index' ? 'is-active' : '' }}">
                <i class="fa-solid fa-headset text-lg"></i>
                <span class="font-medium text-sm">Suporte / FAQs</span>
            </a>
        </div>

        <!-- User profile summary & Profile Switcher & Theme Switcher -->
        <div class="p-4 border-t flex flex-col gap-3" style="border-color: var(--border-color);">
            <!-- Theme Switcher -->
            <div class="flex items-center justify-between border-b pb-2" style="border-color: var(--border-color);">
                <span class="text-xs text-slate-400">Alternar Tema:</span>
                <button onclick="toggleTheme()" class="p-1 rounded bg-slate-800 text-slate-300 hover:text-white transition">
                    <i class="fa-solid fa-circle-half-stroke text-base"></i>
                </button>
            </div>

            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-full flex items-center justify-center font-bold text-xs" style="background-color: var(--badge-bg); color: var(--badge-text);">
                    {{ auth()->check() ? substr(auth()->user()->name, 0, 1) : 'C' }}
                </div>
                <div class="flex-1 min-w-0">
                    <div class="text-xs font-semibold text-white truncate">{{ auth()->check() ? auth()->user()->name : 'Cliente' }}</div>
                    <div class="text-[10px] text-slate-500 truncate">{{ auth()->check() ? auth()->user()->email : '' }}</div>
                </div>
            </div>
            <form action="{{ route('logout') }}" method="POST" class="w-full">
                @csrf
                <button type="submit" class="w-full mt-2 bg-red-500/10 hover:bg-red-500/20 text-red-400 font-semibold py-1.5 rounded-lg text-xs transition">
                    Desconectar-se
                </button>
            </form>
        </div>
    </aside>

    <!-- Main Content Area -->
    <div class="flex-grow flex flex-col min-h-screen">
        <!-- Topbar Mobile only -->
        <header class="h-16 border-b flex md:hidden items-center justify-between px-6 sticky top-0 z-50" style="background-color: var(--bg-sidebar); border-color: var(--border-color);">
            <div class="flex items-center gap-2">
                <button onclick="toggleMobileMenu()" class="nav-link-quiet p-2 -ml-2 rounded-lg transition">
                    <i class="fa-solid fa-bars text-xl"></i>
                </button>
                <i class="fa-solid fa-user-tie text-lg" style="color: var(--accent);"></i>
                <span class="font-bold text-white text-sm">Área Cliente</span>
            </div>
            <div class="flex items-center gap-3">
                <button onclick="toggleTheme()" class="nav-link-quiet transition">
                    <i class="fa-solid fa-circle-half-stroke text-lg"></i>
                </button>
                <a href="/" class="nav-link-quiet">
                    <i class="fa-solid fa-house text-lg"></i>
                </a>
            </div>
        </header>

        <!-- Mobile Drawer Overlay -->
        <div id="mobile-drawer-overlay" class="fixed inset-0 bg-black/60 backdrop-blur-sm z-50 hidden opacity-0 transition-opacity duration-300" onclick="toggleMobileMenu()"></div>
        
        <!-- Mobile Drawer Content -->
        <div id="mobile-drawer" class="fixed top-0 bottom-0 left-0 w-80 max-w-[85vw] z-50 transform -translate-x-full transition-transform duration-300 ease-in-out border-r flex flex-col" style="background-color: var(--bg-sidebar); border-color: var(--border-color);">
            <div class="h-16 flex items-center justify-between px-6 border-b" style="border-color: var(--border-color);">
                <div class="flex items-center gap-2">
                    <div class="p-1.5 rounded-lg text-white font-bold" style="background-color: var(--accent);">
                        <i class="fa-solid fa-user-tie text-sm"></i>
                    </div>
                    <span class="font-bold text-white text-sm">Ação RR Cliente</span>
                </div>
                <button onclick="toggleMobileMenu()" class="nav-link-quiet p-2 rounded-lg transition">
                    <i class="fa-solid fa-xmark text-xl"></i>
                </button>
            </div>

            <div class="flex-1 px-4 py-6 space-y-2 overflow-y-auto">
                <a href="/" class="nav-link flex items-center gap-3 px-4 py-3 rounded-xl">
                    <i class="fa-solid fa-house text-lg"></i>
                    <span class="font-medium text-sm">Ir para o Início</span>
                </a>
                <a href="{{ route('raffles.my-tickets') }}" class="nav-link flex items-center gap-3 px-4 py-3 rounded-xl {{ Route::currentRouteName() == 'raffles.my-tickets' ? 'is-active' : '' }}">
                    <i class="fa-solid fa-ticket text-lg"></i>
                    <span class="font-medium text-sm">Meus Bilhetes</span>
                </a>
                <a href="{{ route('support.index') }}" class="nav-link flex items-center gap-3 px-4 py-3 rounded-xl {{ Route::currentRouteName() == 'support.index' ? 'is-active' : '' }}">
                    <i class="fa-solid fa-headset text-lg"></i>
                    <span class="font-medium text-sm">Suporte / FAQs</span>
                </a>
            </div>

            <div class="p-4 border-t flex flex-col gap-3" style="border-color: var(--border-color); background-color: var(--bg-sidebar);">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-full flex items-center justify-center font-bold text-xs" style="background-color: var(--badge-bg); color: var(--badge-text);">
                        {{ auth()->check() ? substr(auth()->user()->name, 0, 1) : 'C' }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="text-xs font-semibold text-white truncate">{{ auth()->check() ? auth()->user()->name : 'Cliente' }}</div>
                        <div class="text-[10px] text-slate-500 truncate">{{ auth()->check() ? auth()->user()->email : '' }}</div>
                    </div>
                </div>
                <form action="{{ route('logout') }}" method="POST" class="w-full">
                    @csrf
                    <button type="submit" class="w-full mt-2 bg-red-500/10 hover:bg-red-500/20 text-red-400 font-semibold py-1.5 rounded-lg text-xs transition">
                        Desconectar-se
                    </button>
                </form>
            </div>
        </div>

        <!-- Dynamic Content -->
        <main class="flex-grow p-4 sm:p-8 max-w-7xl mx-auto w-full pb-24 md:pb-8">
            @if(session('success'))
                <div class="mb-6 p-4 rounded-xl bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 flex items-center gap-3">
                    <i class="fa-solid fa-circle-check text-lg"></i>
                    <div class="text-sm font-medium">{{ session('success') }}</div>
                </div>
            @endif

            @yield('content')
        </main>
    </div>

    <!-- Bottom Nav Mobile -->
    <nav class="md:hidden fixed bottom-0 left-0 right-0 h-16 border-t backdrop-blur flex justify-around items-center z-50" style="background-color: var(--bg-sidebar); border-color: var(--border-color);">
        <a href="/" class="nav-link-quiet flex flex-col items-center gap-1">
            <i class="fa-solid fa-house text-lg"></i>
            <span class="text-[10px]">Início</span>
        </a>
        <a href="{{ route('raffles.my-tickets') }}" class="nav-link-quiet flex flex-col items-center gap-1">
            <i class="fa-solid fa-ticket text-lg"></i>
            <span class="text-[10px]">Bilhetes</span>
        </a>
        <a href="{{ route('support.index') }}" class="nav-link-quiet flex flex-col items-center gap-1">
            <i class="fa-solid fa-headset text-lg"></i>
            <span class="text-[10px]">Suporte</span>
        </a>
    </nav>

    <!-- Theme Switcher Logic -->
    <script>
        const savedTheme = localStorage.getItem('theme') || 'light';
        if (savedTheme === 'dark') {
            document.body.classList.add('dark-theme');
        }

        function toggleTheme() {
            if (document.body.classList.contains('dark-theme')) {
                document.body.classList.remove('dark-theme');
                localStorage.setItem('theme', 'light');
            } else {
                document.body.classList.add('dark-theme');
                localStorage.setItem('theme', 'dark');
            }
        }

        function toggleMobileMenu() {
            const drawer = document.getElementById('mobile-drawer');
            const overlay = document.getElementById('mobile-drawer-overlay');
            if (drawer.classList.contains('-translate-x-full')) {
                drawer.classList.remove('-translate-x-full');
                overlay.classList.remove('hidden');
                setTimeout(() => {
                    overlay.classList.add('opacity-100');
                }, 10);
            } else {
                drawer.classList.add('-translate-x-full');
                overlay.classList.remove('opacity-100');
                setTimeout(() => {
                    overlay.classList.add('hidden');
                }, 300);
            }
        }
    </script>
</body>
</html>
