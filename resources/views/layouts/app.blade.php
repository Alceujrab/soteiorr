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
            background-color: #0f172a; /* Slate 900 */
            color: #f1f5f9;
        }
        .glass-card {
            background: rgba(30, 41, 59, 0.7); /* Slate 800 with transparency */
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.08);
        }
    </style>
</head>
<body class="min-h-screen flex flex-col">
    <!-- Navbar -->
    <nav class="glass-card sticky top-0 z-50 border-b border-slate-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16 items-center">
                <div class="flex items-center gap-3">
                    <div class="bg-blue-600 p-2 rounded-lg text-white font-bold tracking-wide">
                        <i class="fa-solid fa-car-side text-lg"></i>
                    </div>
                    <a href="{{ route('raffles.index') }}" class="text-xl font-bold text-transparent bg-clip-text bg-gradient-to-r from-blue-400 to-indigo-400">
                        Ação RR Veículos
                    </a>
                </div>
                <div class="flex items-center gap-4">
                    <a href="{{ route('raffles.index') }}" class="text-slate-300 hover:text-white transition px-3 py-2 rounded-md text-sm font-medium">
                        Rifas
                    </a>
                    <a href="{{ route('admin.dashboard') }}" class="bg-slate-800 hover:bg-slate-700 text-white border border-slate-700 transition px-4 py-2 rounded-lg text-sm font-medium flex items-center gap-2">
                        <i class="fa-solid fa-lock"></i> Painel Admin
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="flex-grow max-w-7xl mx-auto w-full px-4 sm:px-6 lg:px-8 py-8">
        @if(session('success'))
            <div class="mb-6 p-4 rounded-lg bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 flex items-center gap-3">
                <i class="fa-solid fa-circle-check text-lg"></i>
                <div>{{ session('success') }}</div>
            </div>
        @endif

        @if($errors->any())
            <div class="mb-6 p-4 rounded-lg bg-red-500/10 border border-red-500/30 text-red-400">
                <div class="flex items-center gap-3 mb-1">
                    <i class="fa-solid fa-circle-exclamation text-lg"></i>
                    <strong class="font-semibold">Erro:</strong>
                </div>
                <ul class="list-disc pl-5">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="mt-auto border-t border-slate-800 bg-slate-950 py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center text-slate-500 text-sm">
            <p>&copy; {{ date('Y') }} Ação RR Veículos Entre Amigos. Todos os direitos reservados.</p>
            <p class="mt-1 text-slate-600">Desenvolvido em Laravel 13 para máxima performance e segurança.</p>
        </div>
    </footer>
</body>
</html>
