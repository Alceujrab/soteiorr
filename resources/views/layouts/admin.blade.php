<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel Administrativo - @yield('title', 'Ação RR Veículos')</title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- FontAwesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        :root {
            /* Tema Escuro: Luxury Gold & Obsidian */
            --bg-primary: #08080a;
            --bg-sidebar: #020203;
            --bg-card: rgba(22, 22, 26, 0.78);
            --border-color: rgba(170, 124, 17, 0.22);
            --text-primary: #f8fafc;
            --text-secondary: #a1a1aa;
            --accent: #aa7c11;
            --accent-hover: #8c6212;
            --badge-bg: rgba(170, 124, 17, 0.12);
            --badge-text: #aa7c11;
        }

        body.light-theme {
            /* Tema Claro: Indigo Lavender */
            --bg-primary: #f8fafc;
            --bg-sidebar: #f1f5f9;
            --bg-card: rgba(255, 255, 255, 0.82);
            --border-color: rgba(99, 102, 241, 0.28);
            --text-primary: #0f172a;
            --text-secondary: #475569;
            --accent: #6366f1;
            --accent-hover: #4f46e5;
            --badge-bg: rgba(99, 102, 241, 0.12);
            --badge-text: #6366f1;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--bg-primary) !important;
            color: var(--text-primary) !important;
            transition: background-color 0.3s ease, color 0.3s ease;
        }

        .glass-card {
            background: var(--bg-card) !important;
            border-color: var(--border-color) !important;
            box-shadow: 0 4px 30px rgba(0, 0, 0, 0.15);
        }

        /* Mapeamento dinâmico para os elementos do Tailwind */
        .text-slate-400, .text-slate-500, .text-slate-300 {
            color: var(--text-secondary) !important;
        }
        .text-white {
            color: var(--text-primary) !important;
        }
        .bg-blue-600, .bg-emerald-600, .bg-slate-800, .bg-slate-950 {
            background-color: var(--accent) !important;
        }
        .hover\:bg-blue-500:hover, .hover\:bg-emerald-500:hover, .hover\:bg-slate-700:hover {
            background-color: var(--accent-hover) !important;
        }
        .text-blue-500, .text-blue-400, .text-emerald-400 {
            color: var(--badge-text) !important;
        }
        .bg-blue-500\/10, .bg-emerald-500\/10, .bg-blue-600\/10 {
            background-color: var(--badge-bg) !important;
        }
        .border-blue-500\/30, .border-emerald-500\/30, .border-slate-800 {
            border-color: var(--border-color) !important;
        }
    </style>
</head>
<body class="min-h-screen flex flex-col md:flex-row">

    <!-- Admin Sidebar -->
    <aside class="w-72 border-r flex flex-col hidden md:flex min-h-screen sticky top-0" style="background-color: var(--bg-sidebar); border-color: var(--border-color);">
        <!-- Logo -->
        <div class="h-20 flex items-center px-6 border-b gap-3" style="border-color: var(--border-color);">
            <div class="p-2 rounded-xl text-white font-bold tracking-wide shadow-lg" style="background-color: var(--accent);">
                <i class="fa-solid fa-user-shield text-lg"></i>
            </div>
            <a href="/" class="text-sm font-bold text-transparent bg-clip-text bg-gradient-to-r" style="background-image: linear-gradient(to right, var(--accent), var(--text-primary));">
                Ação RR Admin
            </a>
        </div>

        <!-- Navigation Links -->
        <div class="flex-1 px-4 py-6 space-y-1 overflow-y-auto max-h-[calc(100vh-250px)]">
            <a href="/" class="flex items-center gap-3 px-4 py-2.5 rounded-xl transition text-slate-400 hover:text-white hover:bg-slate-900/60">
                <i class="fa-solid fa-house text-base"></i>
                <span class="font-medium text-sm">Ver Site Público</span>
            </a>
            <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl transition text-slate-400 hover:text-white hover:bg-slate-900/60 {{ Route::currentRouteName() == 'admin.dashboard' ? 'bg-slate-900 text-white' : '' }}">
                <i class="fa-solid fa-chart-line text-base"></i>
                <span class="font-medium text-sm">Dashboard / Ações Promocionais</span>
            </a>
            <a href="{{ route('admin.participants') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl transition text-slate-400 hover:text-white hover:bg-slate-900/60 {{ Route::currentRouteName() == 'admin.participants' ? 'bg-slate-900 text-white' : '' }}">
                <i class="fa-solid fa-users text-base"></i>
                <span class="font-medium text-sm">Participantes</span>
            </a>
            <a href="{{ route('admin.reports') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl transition text-slate-400 hover:text-white hover:bg-slate-900/60 {{ Route::currentRouteName() == 'admin.reports' ? 'bg-slate-900 text-white' : '' }}">
                <i class="fa-solid fa-chart-pie text-base"></i>
                <span class="font-medium text-sm">Relatórios</span>
            </a>
            <a href="{{ route('admin.users') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl transition text-slate-400 hover:text-white hover:bg-slate-900/60 {{ Route::currentRouteName() == 'admin.users' ? 'bg-slate-900 text-white' : '' }}">
                <i class="fa-solid fa-user-gear text-base"></i>
                <span class="font-medium text-sm">Usuários / Perfis</span>
            </a>
            <a href="{{ route('admin.notifications') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl transition text-slate-400 hover:text-white hover:bg-slate-900/60 {{ Route::currentRouteName() == 'admin.notifications' ? 'bg-slate-900 text-white' : '' }}">
                <i class="fa-solid fa-bullhorn text-base"></i>
                <span class="font-medium text-sm">Notificações</span>
            </a>
            <a href="{{ route('admin.logs') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl transition text-slate-400 hover:text-white hover:bg-slate-900/60 {{ Route::currentRouteName() == 'admin.logs' ? 'bg-slate-900 text-white' : '' }}">
                <i class="fa-solid fa-shield-halved text-base"></i>
                <span class="font-medium text-sm">Logs de Auditoria</span>
            </a>
            <a href="{{ route('admin.settings') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl transition text-slate-400 hover:text-white hover:bg-slate-900/60 {{ Route::currentRouteName() == 'admin.settings' ? 'bg-slate-900 text-white' : '' }}">
                <i class="fa-solid fa-gears text-base"></i>
                <span class="font-medium text-sm">Configurações</span>
            </a>
        </div>

        <!-- Profile switcher & Theme Switcher area -->
        <div class="p-4 border-t flex flex-col gap-2" style="border-color: var(--border-color); background-color: var(--bg-sidebar);">
            <!-- Theme Switcher -->
            <div class="flex items-center justify-between border-b pb-2 mb-1" style="border-color: var(--border-color);">
                <span class="text-xs text-slate-400">Alternar Tema:</span>
                <button onclick="toggleTheme()" class="p-1 rounded bg-slate-800 text-slate-300 hover:text-white transition">
                    <i class="fa-solid fa-circle-half-stroke text-base"></i>
                </button>
            </div>

            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-full flex items-center justify-center font-bold text-xs" style="background-color: var(--badge-bg); color: var(--badge-text);">
                    {{ auth()->check() ? substr(auth()->user()->name, 0, 1) : 'A' }}
                </div>
                <div class="flex-1 min-w-0">
                    <div class="text-xs font-semibold text-white truncate">{{ auth()->check() ? auth()->user()->name : 'Admin' }}</div>
                    <div class="text-[10px] text-slate-500 uppercase font-bold truncate">{{ auth()->check() ? str_replace('_', ' ', auth()->user()->role) : '' }}</div>
                </div>
            </div>
            <div class="mt-2 space-y-1">
                <label class="text-[9px] font-bold text-slate-500 uppercase block">Simular Perfil:</label>
                <select onchange="location.href='/simulate-login/' + this.value" class="w-full bg-slate-900 border border-slate-800 rounded-lg px-2 py-1.5 text-[11px] text-slate-300 focus:outline-none">
                    <option value="super_admin" {{ auth()->check() && auth()->user()->role == 'super_admin' ? 'selected' : '' }}>Super Admin</option>
                    <option value="admin_organizador" {{ auth()->check() && auth()->user()->role == 'admin_organizador' ? 'selected' : '' }}>Admin Organizador</option>
                    <option value="gerente_operacional" {{ auth()->check() && auth()->user()->role == 'gerente_operacional' ? 'selected' : '' }}>Gerente Operacional</option>
                    <option value="vendedor" {{ auth()->check() && auth()->user()->role == 'vendedor' ? 'selected' : '' }}>Vendedor</option>
                    <option value="cliente" {{ auth()->check() && auth()->user()->role == 'cliente' ? 'selected' : '' }}>Cliente/Participante</option>
                    <option value="financeiro" {{ auth()->check() && auth()->user()->role == 'financeiro' ? 'selected' : '' }}>Financeiro</option>
                    <option value="suporte" {{ auth()->check() && auth()->user()->role == 'suporte' ? 'selected' : '' }}>Suporte</option>
                    <option value="auditor" {{ auth()->check() && auth()->user()->role == 'auditor' ? 'selected' : '' }}>Auditor</option>
                </select>
            </div>
            <form action="{{ route('logout') }}" method="POST" class="w-full mt-1">
                @csrf
                <button type="submit" class="w-full bg-red-500/10 hover:bg-red-500/20 text-red-400 font-semibold py-1 rounded-lg text-xs transition">
                    Sair
                </button>
            </form>
        </div>
    </aside>

    <!-- Main Content Area -->
    <div class="flex-grow flex flex-col min-h-screen">
        <!-- Topbar Mobile only -->
        <header class="h-16 border-b flex md:hidden items-center justify-between px-6 sticky top-0 z-50" style="background-color: var(--bg-sidebar); border-color: var(--border-color);">
            <div class="flex items-center gap-2">
                <button onclick="toggleMobileMenu()" class="p-2 -ml-2 rounded-lg text-slate-400 hover:text-white transition">
                    <i class="fa-solid fa-bars text-xl"></i>
                </button>
                <i class="fa-solid fa-user-shield text-lg" style="color: var(--accent);"></i>
                <span class="font-bold text-white text-sm">Painel Admin</span>
            </div>
            <div class="flex items-center gap-3">
                <button onclick="toggleTheme()" class="text-slate-400 hover:text-white transition">
                    <i class="fa-solid fa-circle-half-stroke text-lg"></i>
                </button>
                <a href="/" class="text-slate-400 hover:text-white">
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
                        <i class="fa-solid fa-user-shield text-sm"></i>
                    </div>
                    <span class="font-bold text-white text-sm">Ação RR Admin</span>
                </div>
                <button onclick="toggleMobileMenu()" class="p-2 rounded-lg text-slate-400 hover:text-white transition">
                    <i class="fa-solid fa-xmark text-xl"></i>
                </button>
            </div>

            <div class="flex-1 px-4 py-6 space-y-1 overflow-y-auto">
                <a href="/" class="flex items-center gap-3 px-4 py-2.5 rounded-xl transition text-slate-400 hover:text-white hover:bg-slate-900/60">
                    <i class="fa-solid fa-house text-base"></i>
                    <span class="font-medium text-sm">Ver Site Público</span>
                </a>
                <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl transition text-slate-400 hover:text-white hover:bg-slate-900/60 {{ Route::currentRouteName() == 'admin.dashboard' ? 'bg-slate-900 text-white' : '' }}">
                    <i class="fa-solid fa-chart-line text-base"></i>
                    <span class="font-medium text-sm">Dashboard / Ações Promocionais</span>
                </a>
                <a href="{{ route('admin.participants') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl transition text-slate-400 hover:text-white hover:bg-slate-900/60 {{ Route::currentRouteName() == 'admin.participants' ? 'bg-slate-900 text-white' : '' }}">
                    <i class="fa-solid fa-users text-base"></i>
                    <span class="font-medium text-sm">Participantes</span>
                </a>
                <a href="{{ route('admin.reports') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl transition text-slate-400 hover:text-white hover:bg-slate-900/60 {{ Route::currentRouteName() == 'admin.reports' ? 'bg-slate-900 text-white' : '' }}">
                    <i class="fa-solid fa-chart-pie text-base"></i>
                    <span class="font-medium text-sm">Relatórios</span>
                </a>
                <a href="{{ route('admin.users') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl transition text-slate-400 hover:text-white hover:bg-slate-900/60 {{ Route::currentRouteName() == 'admin.users' ? 'bg-slate-900 text-white' : '' }}">
                    <i class="fa-solid fa-user-gear text-base"></i>
                    <span class="font-medium text-sm">Usuários / Perfis</span>
                </a>
                <a href="{{ route('admin.notifications') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl transition text-slate-400 hover:text-white hover:bg-slate-900/60 {{ Route::currentRouteName() == 'admin.notifications' ? 'bg-slate-900 text-white' : '' }}">
                    <i class="fa-solid fa-bullhorn text-base"></i>
                    <span class="font-medium text-sm">Notificações</span>
                </a>
                <a href="{{ route('admin.logs') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl transition text-slate-400 hover:text-white hover:bg-slate-900/60 {{ Route::currentRouteName() == 'admin.logs' ? 'bg-slate-900 text-white' : '' }}">
                    <i class="fa-solid fa-shield-halved text-base"></i>
                    <span class="font-medium text-sm">Logs de Auditoria</span>
                </a>
                <a href="{{ route('admin.settings') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl transition text-slate-400 hover:text-white hover:bg-slate-900/60 {{ Route::currentRouteName() == 'admin.settings' ? 'bg-slate-900 text-white' : '' }}">
                    <i class="fa-solid fa-gears text-base"></i>
                    <span class="font-medium text-sm">Configurações</span>
                </a>
            </div>

            <div class="p-4 border-t flex flex-col gap-2 bg-slate-950" style="border-color: var(--border-color); background-color: var(--bg-sidebar);">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-full flex items-center justify-center font-bold text-xs" style="background-color: var(--badge-bg); color: var(--badge-text);">
                        {{ auth()->check() ? substr(auth()->user()->name, 0, 1) : 'A' }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="text-xs font-semibold text-white truncate">{{ auth()->check() ? auth()->user()->name : 'Admin' }}</div>
                        <div class="text-[10px] text-slate-500 uppercase font-bold truncate">{{ auth()->check() ? str_replace('_', ' ', auth()->user()->role) : '' }}</div>
                    </div>
                </div>
                <div class="mt-2 space-y-1">
                    <label class="text-[9px] font-bold text-slate-500 uppercase block">Simular Perfil:</label>
                    <select onchange="location.href='/simulate-login/' + this.value" class="w-full bg-slate-900 border border-slate-800 rounded-lg px-2 py-1.5 text-[11px] text-slate-300 focus:outline-none">
                        <option value="super_admin" {{ auth()->check() && auth()->user()->role == 'super_admin' ? 'selected' : '' }}>Super Admin</option>
                        <option value="admin_organizador" {{ auth()->check() && auth()->user()->role == 'admin_organizador' ? 'selected' : '' }}>Admin Organizador</option>
                        <option value="gerente_operacional" {{ auth()->check() && auth()->user()->role == 'gerente_operacional' ? 'selected' : '' }}>Gerente Operacional</option>
                        <option value="vendedor" {{ auth()->check() && auth()->user()->role == 'vendedor' ? 'selected' : '' }}>Vendedor</option>
                        <option value="cliente" {{ auth()->check() && auth()->user()->role == 'cliente' ? 'selected' : '' }}>Cliente/Participante</option>
                        <option value="financeiro" {{ auth()->check() && auth()->user()->role == 'financeiro' ? 'selected' : '' }}>Financeiro</option>
                        <option value="suporte" {{ auth()->check() && auth()->user()->role == 'suporte' ? 'selected' : '' }}>Suporte</option>
                        <option value="auditor" {{ auth()->check() && auth()->user()->role == 'auditor' ? 'selected' : '' }}>Auditor</option>
                    </select>
                </div>
                <form action="{{ route('logout') }}" method="POST" class="w-full mt-1">
                    @csrf
                    <button type="submit" class="w-full bg-red-500/10 hover:bg-red-500/20 text-red-400 font-semibold py-1 rounded-lg text-xs transition">
                        Sair
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

            @if($errors->any())
                <div class="mb-6 p-4 rounded-xl bg-red-500/10 border border-red-500/30 text-red-400">
                    <ul class="list-disc pl-5 text-xs">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @yield('content')
        </main>
    </div>

    <!-- Bottom Nav Mobile -->
    <nav class="md:hidden fixed bottom-0 left-0 right-0 h-16 border-t backdrop-blur flex justify-around items-center z-50" style="background-color: var(--bg-sidebar); border-color: var(--border-color);">
        <a href="/" class="flex flex-col items-center gap-1 text-slate-400 hover:text-white">
            <i class="fa-solid fa-house text-lg"></i>
            <span class="text-[10px]">Início</span>
        </a>
        <a href="{{ route('admin.dashboard') }}" class="flex flex-col items-center gap-1 text-slate-400 hover:text-white">
            <i class="fa-solid fa-chart-line text-lg"></i>
            <span class="text-[10px]">Dashboard</span>
        </a>
        <a href="{{ route('admin.reports') }}" class="flex flex-col items-center gap-1 text-slate-400 hover:text-white">
            <i class="fa-solid fa-chart-pie text-lg"></i>
            <span class="text-[10px]">Relatórios</span>
        </a>
        <a href="{{ route('admin.settings') }}" class="flex flex-col items-center gap-1 text-slate-400 hover:text-white">
            <i class="fa-solid fa-gears text-lg"></i>
            <span class="text-[10px]">Config</span>
        </a>
    </nav>

    <!-- Theme Switcher Logic -->
    <script>
        const savedTheme = localStorage.getItem('theme') || 'dark';
        if (savedTheme === 'light') {
            document.body.classList.add('light-theme');
        }

        function toggleTheme() {
            if (document.body.classList.contains('light-theme')) {
                document.body.classList.remove('light-theme');
                localStorage.setItem('theme', 'dark');
            } else {
                document.body.classList.add('light-theme');
                localStorage.setItem('theme', 'light');
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
