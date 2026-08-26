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
            --bg-sidebar: #e2e8f0;
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
<body class="min-h-screen flex flex-col">

    <!-- Public Header -->
    <nav class="glass-card sticky top-0 z-50 border-b">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16 items-center">
                <div class="flex items-center gap-3">
                    <div class="p-2 rounded-xl text-white font-bold tracking-wide" style="background-color: var(--accent);">
                        <i class="fa-solid fa-car-side text-lg"></i>
                    </div>
                    <a href="/" class="text-lg font-bold text-transparent bg-clip-text bg-gradient-to-r" style="background-image: linear-gradient(to right, var(--accent), var(--text-primary));">
                        Ação RR Veículos
                    </a>
                </div>
                <div class="hidden md:flex items-center gap-4">
                    <!-- Theme Toggle Button -->
                    <button onclick="toggleTheme()" class="p-2 rounded-lg text-slate-400 hover:text-white transition" title="Alternar Tema">
                        <i class="fa-solid fa-circle-half-stroke text-lg"></i>
                    </button>

                    <a href="/" class="text-sm font-medium text-slate-300 hover:text-white transition">Ações Promocionais Ativas</a>
                    <a href="{{ route('pages.about') }}" class="text-sm font-medium text-slate-300 hover:text-white transition">Sobre Nós</a>
                    <a href="{{ route('pages.contact') }}" class="text-sm font-medium text-slate-300 hover:text-white transition">Contato</a>
                    <a href="{{ route('pages.faqs') }}" class="text-sm font-medium text-slate-300 hover:text-white transition">Dúvidas</a>
                    <a href="{{ route('pages.regulation') }}" class="text-sm font-medium text-slate-300 hover:text-white transition">Regulamento</a>
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
                        <a href="{{ route('register') }}" class="text-white font-semibold px-4 py-2 rounded-xl text-xs transition" style="background-color: var(--accent);">
                            Cadastrar-se
                        </a>
                    @endauth
                </div>

                <!-- Mobile Hamburger and Theme Toggle -->
                <div class="flex md:hidden items-center gap-2">
                    <button onclick="toggleTheme()" class="p-2 rounded-lg text-slate-400 hover:text-white transition">
                        <i class="fa-solid fa-circle-half-stroke text-lg"></i>
                    </button>
                    <button onclick="toggleMobileMenu()" class="p-2 rounded-lg text-slate-400 hover:text-white transition" title="Abrir Menu">
                        <i class="fa-solid fa-bars text-xl"></i>
                    </button>
                </div>
            </div>
        </div>
    </nav>

    <!-- Mobile Drawer Overlay -->
    <div id="mobile-drawer-overlay" class="fixed inset-0 bg-black/60 backdrop-blur-sm z-50 hidden opacity-0 transition-opacity duration-300" onclick="toggleMobileMenu()"></div>
    
    <!-- Mobile Drawer Content -->
    <div id="mobile-drawer" class="fixed top-0 bottom-0 left-0 w-80 max-w-[85vw] z-50 transform -translate-x-full transition-transform duration-300 ease-in-out border-r flex flex-col" style="background-color: var(--bg-sidebar); border-color: var(--border-color);">
        <div class="h-16 flex items-center justify-between px-6 border-b" style="border-color: var(--border-color);">
            <div class="flex items-center gap-2">
                <div class="p-1.5 rounded-lg text-white font-bold" style="background-color: var(--accent);">
                    <i class="fa-solid fa-car-side text-sm"></i>
                </div>
                <span class="font-bold text-white text-sm">Ação RR</span>
            </div>
            <button onclick="toggleMobileMenu()" class="p-2 rounded-lg text-slate-400 hover:text-white transition">
                <i class="fa-solid fa-xmark text-xl"></i>
            </button>
        </div>

        <div class="flex-1 px-4 py-6 space-y-3">
            <a href="/" class="flex items-center gap-3 px-4 py-3 rounded-xl text-slate-300 hover:text-white hover:bg-slate-900/60 transition">
                <i class="fa-solid fa-ticket text-lg"></i>
                <span class="font-medium text-sm">Ações Promocionais Ativas</span>
            </a>
            <a href="{{ route('pages.about') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-slate-300 hover:text-white hover:bg-slate-900/60 transition">
                <i class="fa-solid fa-circle-info text-lg"></i>
                <span class="font-medium text-sm">Sobre Nós</span>
            </a>
            <a href="{{ route('pages.contact') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-slate-300 hover:text-white hover:bg-slate-900/60 transition">
                <i class="fa-solid fa-envelope text-lg"></i>
                <span class="font-medium text-sm">Contato</span>
            </a>
            <a href="{{ route('pages.faqs') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-slate-300 hover:text-white hover:bg-slate-900/60 transition">
                <i class="fa-solid fa-circle-question text-lg"></i>
                <span class="font-medium text-sm">Dúvidas Frequentes</span>
            </a>
            <a href="{{ route('pages.regulation') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-slate-300 hover:text-white hover:bg-slate-900/60 transition">
                <i class="fa-solid fa-scale-balanced text-lg"></i>
                <span class="font-medium text-sm">Regulamento</span>
            </a>
            
            <div class="border-t my-4" style="border-color: var(--border-color);"></div>

            @auth
                @if(in_array(auth()->user()->role, ['cliente', 'vendedor']))
                    <a href="{{ route('customer.dashboard') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-blue-400 hover:bg-slate-900/60 transition">
                        <i class="fa-solid fa-user text-lg"></i>
                        <span class="font-medium text-sm">Minha Área</span>
                    </a>
                @else
                    <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-blue-400 hover:bg-slate-900/60 transition">
                        <i class="fa-solid fa-chart-line text-lg"></i>
                        <span class="font-medium text-sm">Painel Admin</span>
                    </a>
                @endif
                
                <form action="{{ route('logout') }}" method="POST" class="w-full pt-4">
                    @csrf
                    <button type="submit" class="w-full bg-red-500/10 hover:bg-red-500/20 text-red-400 font-semibold py-2.5 rounded-xl text-xs transition">
                        Desconectar-se
                    </button>
                </form>
            @else
                <a href="{{ route('login') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl text-slate-300 hover:text-white hover:bg-slate-900/60 transition">
                    <i class="fa-solid fa-right-to-bracket text-lg"></i>
                    <span class="font-medium text-sm">Entrar</span>
                </a>
                <a href="{{ route('register') }}" class="flex items-center justify-center gap-2 px-4 py-3 rounded-xl text-white font-semibold text-xs transition" style="background-color: var(--accent);">
                    <i class="fa-solid fa-user-plus"></i>
                    Cadastrar-se
                </a>
            @endauth
        </div>
    </div>

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
    <footer class="border-t py-12" style="background-color: var(--bg-sidebar); border-color: var(--border-color);">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-8 text-left">
                <!-- Info Section -->
                <div class="space-y-3">
                    <h3 class="text-sm font-bold text-white uppercase tracking-wider">Ação RR Veículos</h3>
                    <p class="text-xs text-slate-500 leading-relaxed">
                        Sua plataforma premium de ações entre amigos e Ações Promocionais digitais. Concorra aos melhores veículos com segurança e total transparência.
                    </p>
                    <div class="text-xs text-slate-500 font-medium">
                        <strong>RR Veículos Água Boa - MT</strong><br>
                        CNPJ: 12.345.678/0001-90<br>
                        Água Boa - MT
                    </div>
                </div>

                <!-- Links Section -->
                <div class="space-y-3">
                    <h3 class="text-sm font-bold text-white uppercase tracking-wider">Navegação</h3>
                    <ul class="space-y-2 text-xs text-slate-500">
                        <li><a href="/" class="hover:text-white transition">Ações Promocionais Ativas</a></li>
                        <li><a href="{{ route('pages.about') }}" class="hover:text-white transition">Sobre Nós</a></li>
                        <li><a href="{{ route('pages.contact') }}" class="hover:text-white transition">Fale Conosco / Contato</a></li>
                        <li><a href="{{ route('pages.faqs') }}" class="hover:text-white transition">Dúvidas Frequentes (FAQs)</a></li>
                    </ul>
                </div>

                <!-- Legal Section -->
                <div class="space-y-3">
                    <h3 class="text-sm font-bold text-white uppercase tracking-wider">Termos e Regulamento</h3>
                    <ul class="space-y-2 text-xs text-slate-500">
                        <li><a href="{{ route('pages.privacy') }}" class="hover:text-white transition">Política de Privacidade</a></li>
                        <li><a href="{{ route('pages.terms') }}" class="hover:text-white transition">Termos de Uso do Site</a></li>
                        <li><a href="{{ route('pages.regulation') }}" class="hover:text-white transition">Regulamento da Promoção</a></li>
                        <li><a href="{{ route('raffles.validate-ticket') }}" class="hover:text-white transition">Validador de Bilhetes Online</a></li>
                    </ul>
                </div>
            </div>

            <div class="border-t pt-8 flex flex-col md:flex-row justify-between items-center gap-4 text-center md:text-left text-xs text-slate-600" style="border-color: var(--border-color);">
                <div>
                    <p>&copy; 2026 RR Veículos Água Boa - MT. Todos os direitos reservados.</p>
                    <p class="mt-1 text-[10px] text-slate-700">Proibido o uso de qualquer informação ou parte deste sem autorização.</p>
                </div>
                <div class="flex items-center gap-1 font-medium">
                    <span>Desenvolvido por</span>
                    <a href="https://cfauto.com.br" target="_blank" class="text-blue-500/80 hover:text-blue-400 font-bold transition">cfauto.com.br</a>
                </div>
            </div>
        </div>
    </footer>

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
