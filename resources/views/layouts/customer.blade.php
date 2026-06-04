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
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
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

    <!-- Customer Sidebar -->
    <aside class="w-72 bg-slate-950 border-r border-slate-800/80 flex flex-col hidden md:flex min-h-screen sticky top-0">
        <!-- Logo -->
        <div class="h-20 flex items-center px-6 border-b border-slate-900 gap-3">
            <div class="bg-blue-600 p-2 rounded-xl text-white font-bold tracking-wide">
                <i class="fa-solid fa-user-tie text-lg"></i>
            </div>
            <span class="text-base font-bold text-white">Minha Área Cliente</span>
        </div>

        <!-- Navigation Links -->
        <div class="flex-1 px-4 py-6 space-y-2">
            <a href="/" class="flex items-center gap-3 px-4 py-3 rounded-xl transition text-slate-400 hover:text-white hover:bg-slate-900/60">
                <i class="fa-solid fa-house text-lg"></i>
                <span class="font-medium text-sm">Ir para o Início</span>
            </a>
            <a href="{{ route('raffles.my-tickets') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl transition text-slate-400 hover:text-white hover:bg-slate-900/60 {{ Route::currentRouteName() == 'raffles.my-tickets' ? 'bg-slate-900 text-white' : '' }}">
                <i class="fa-solid fa-ticket text-lg"></i>
                <span class="font-medium text-sm">Meus Bilhetes</span>
            </a>
            <a href="{{ route('support.index') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl transition text-slate-400 hover:text-white hover:bg-slate-900/60 {{ Route::currentRouteName() == 'support.index' ? 'bg-slate-900 text-white' : '' }}">
                <i class="fa-solid fa-headset text-lg"></i>
                <span class="font-medium text-sm">Suporte / FAQs</span>
            </a>
        </div>

        <!-- User profile summary -->
        <div class="p-4 border-t border-slate-900 flex flex-col gap-2">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-full bg-blue-600/25 flex items-center justify-center font-bold text-blue-400 text-xs">
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
        <header class="h-16 bg-slate-950 border-b border-slate-900 flex md:hidden items-center justify-between px-6 sticky top-0 z-50">
            <div class="flex items-center gap-2">
                <i class="fa-solid fa-user-tie text-blue-500 text-lg"></i>
                <span class="font-bold text-white text-sm">Área Cliente</span>
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

            @yield('content')
        </main>
    </div>

    <!-- Bottom Nav Mobile -->
    <nav class="md:hidden fixed bottom-0 left-0 right-0 h-16 bg-slate-950/95 border-t border-slate-900 backdrop-blur flex justify-around items-center z-50">
        <a href="/" class="flex flex-col items-center gap-1 text-slate-400 hover:text-white">
            <i class="fa-solid fa-house text-lg"></i>
            <span class="text-[10px]">Início</span>
        </a>
        <a href="{{ route('raffles.my-tickets') }}" class="flex flex-col items-center gap-1 text-slate-400 hover:text-white">
            <i class="fa-solid fa-ticket text-lg"></i>
            <span class="text-[10px]">Bilhetes</span>
        </a>
        <a href="{{ route('support.index') }}" class="flex flex-col items-center gap-1 text-slate-400 hover:text-white">
            <i class="fa-solid fa-headset text-lg"></i>
            <span class="text-[10px]">Suporte</span>
        </a>
    </nav>

</body>
</html>
