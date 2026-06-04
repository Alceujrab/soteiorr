<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Ação RR Veículos Entre Amigos')</title>
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
<body class="min-h-screen flex flex-col">

    <!-- Public Header -->
    <nav class="glass-card sticky top-0 z-50 border-b border-slate-800/80">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16 items-center">
                <div class="flex items-center gap-3">
                    <div class="bg-blue-600 p-2 rounded-xl text-white font-bold tracking-wide">
                        <i class="fa-solid fa-car-side text-lg"></i>
                    </div>
                    <a href="/" class="text-lg font-bold text-transparent bg-clip-text bg-gradient-to-r from-blue-400 to-indigo-400">
                        Ação RR Veículos
                    </a>
                </div>
                <div class="flex items-center gap-4">
                    <a href="/" class="text-sm font-medium text-slate-300 hover:text-white transition">Rifas Ativas</a>
                    @auth
                        @if(in_array(auth()->user()->role, ['cliente', 'vendedor']))
                            <a href="{{ route('customer.dashboard') }}" class="text-sm font-medium text-blue-400 hover:text-blue-300 transition flex items-center gap-1">
                                <i class="fa-solid fa-user"></i> Minha Área
                            </a>
                        @else
                            <a href="{{ route('admin.dashboard') }}" class="text-sm font-medium text-blue-400 hover:text-blue-300 transition flex items-center gap-1">
                                <i class="fa-solid fa-chart-line"></i> Painel Admin
                            </a>
                        @endif
                        <form action="{{ route('logout') }}" method="POST" class="inline">
                            @csrf
                            <button type="submit" class="text-xs font-semibold text-red-400 hover:text-red-300 transition">
                                Sair
                            </button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="text-sm font-medium text-slate-300 hover:text-white transition">Entrar</a>
                        <a href="{{ route('register') }}" class="bg-blue-600 hover:bg-blue-500 text-white font-semibold px-4 py-2 rounded-xl text-xs transition">
                            Cadastrar-se
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Public Area -->
    <main class="flex-grow max-w-7xl mx-auto w-full px-4 sm:px-6 lg:px-8 py-8 pb-16">
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

    <!-- Footer -->
    <footer class="border-t border-slate-800 bg-slate-950 py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center text-slate-500 text-sm">
            <p>&copy; {{ date('Y') }} Ação RR Veículos Entre Amigos. Todos os direitos reservados.</p>
        </div>
    </footer>

</body>
</html>
