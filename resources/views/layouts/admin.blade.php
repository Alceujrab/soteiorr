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
        body {
            font-family: 'Inter', sans-serif;
            background-color: #0f172a;
            color: #f1f5f9;
        }
        .glass-card {
            background: rgba(30, 41, 59, 0.6);
            backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.08);
            box-shadow: 0 4px 30px rgba(0, 0, 0, 0.2);
        }
    </style>
</head>
<body class="min-h-screen flex flex-col md:flex-row">

    <!-- Admin Sidebar -->
    <aside class="w-72 bg-slate-950 border-r border-slate-800/80 flex flex-col hidden md:flex min-h-screen sticky top-0">
        <!-- Logo -->
        <div class="h-20 flex items-center px-6 border-b border-slate-900 gap-3">
            <div class="bg-blue-600 p-2 rounded-xl text-white font-bold tracking-wide shadow-lg">
                <i class="fa-solid fa-user-shield text-lg"></i>
            </div>
            <a href="/" class="text-sm font-bold text-transparent bg-clip-text bg-gradient-to-r from-blue-400 to-indigo-400">
                Ação RR Admin
            </a>
        </div>

        <!-- Navigation Links -->
        <div class="flex-1 px-4 py-6 space-y-1 overflow-y-auto max-h-[calc(100vh-220px)]">
            <a href="/" class="flex items-center gap-3 px-4 py-2.5 rounded-xl transition text-slate-400 hover:text-white hover:bg-slate-900/60">
                <i class="fa-solid fa-house text-base"></i>
                <span class="font-medium text-sm">Ver Site Público</span>
            </a>
            <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 px-4 py-2.5 rounded-xl transition text-slate-400 hover:text-white hover:bg-slate-900/60 {{ Route::currentRouteName() == 'admin.dashboard' ? 'bg-slate-900 text-white' : '' }}">
                <i class="fa-solid fa-chart-line text-base"></i>
                <span class="font-medium text-sm">Dashboard / Rifas</span>
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

        <!-- Profile switcher area -->
        <div class="p-4 border-t border-slate-900 flex flex-col gap-2 bg-slate-950">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-full bg-blue-600/20 flex items-center justify-center font-bold text-blue-400 text-xs">
                    {{ auth()->check() ? substr(auth()->user()->name, 0, 1) : 'A' }}
                </div>
                <div class="flex-1 min-w-0">
                    <div class="text-xs font-semibold text-white truncate">{{ auth()->check() ? auth()->user()->name : 'Admin' }}</div>
                    <div class="text-[10px] text-slate-500 uppercase font-bold truncate">{{ auth()->check() ? str_replace('_', ' ', auth()->user()->role) : '' }}</div>
                </div>
            </div>
            <div class="mt-2 space-y-1">
                <label class="text-[9px] font-bold text-slate-500 uppercase block">Simular Perfil:</label>
                <select onchange="location.href='/simulate-login/' + this.value" class="w-full bg-slate-900 border border-slate-805 rounded-lg px-2 py-1.5 text-[11px] text-slate-300 focus:outline-none">
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
        <header class="h-16 bg-slate-950 border-b border-slate-900 flex md:hidden items-center justify-between px-6 sticky top-0 z-50">
            <div class="flex items-center gap-2">
                <i class="fa-solid fa-user-shield text-blue-500 text-lg"></i>
                <span class="font-bold text-white text-sm">Painel Admin</span>
            </div>
            <a href="/" class="text-slate-400 hover:text-white">
                <i class="fa-solid fa-house text-lg"></i>
            </a>
        </header>

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
    <nav class="md:hidden fixed bottom-0 left-0 right-0 h-16 bg-slate-950/95 border-t border-slate-900 backdrop-blur flex justify-around items-center z-50">
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

</body>
</html>
