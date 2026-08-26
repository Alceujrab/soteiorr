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
    <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;600;700&family=DM+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    @include('partials.theme-styles')
</head>
<body class="min-h-screen flex flex-col">

    <!-- Public Header -->
    <nav class="glass-card sticky top-0 z-50 border-b">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16 items-center">
                <div class="flex items-center gap-3">
                    <a href="/" class="flex items-center gap-2">
                        <img src="{{ asset('images/logo-rr.png') }}" alt="RR Veículos" class="brand-logo">
                    </a>
                </div>
                <div class="hidden md:flex items-center gap-5">
                    <!-- Theme Toggle Button -->
                    <button onclick="toggleTheme()" class="p-2 rounded-lg text-slate-400 hover:text-white transition" title="Alternar Tema">
                        <i class="fa-solid fa-circle-half-stroke text-lg"></i>
                    </button>

                    <a href="/" class="text-[11px] font-semibold uppercase tracking-[0.14em] text-slate-300 hover:text-white transition">Início</a>
                    <a href="#pacotes" class="text-[11px] font-semibold uppercase tracking-[0.14em] text-slate-300 hover:text-white transition">Pacotes</a>
                    <a href="#acoes-promocionais" class="text-[11px] font-semibold uppercase tracking-[0.14em] text-slate-300 hover:text-white transition">Ações</a>
                    <a href="{{ route('pages.faqs') }}" class="text-[11px] font-semibold uppercase tracking-[0.14em] text-slate-300 hover:text-white transition">FAQ</a>
                    <a href="{{ route('pages.regulation') }}" class="text-[11px] font-semibold uppercase tracking-[0.14em] text-slate-300 hover:text-white transition">Regulamento</a>
                    <a href="{{ route('pages.contact') }}" class="text-[11px] font-semibold uppercase tracking-[0.14em] text-slate-300 hover:text-white transition">Contato</a>
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
                <img src="{{ asset('images/logo-rr.png') }}" alt="RR Veículos" class="h-8 w-auto">
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
    <main class="flex-grow max-w-7xl mx-auto w-full px-4 sm:px-6 lg:px-8 py-8 pb-28 md:pb-16">
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

    <!-- Mobile bottom navigation -->
    <nav class="md:hidden fixed bottom-0 left-0 right-0 z-50 border-t backdrop-blur-xl" style="background-color: var(--bg-sidebar); border-color: var(--border-color); padding-bottom: env(safe-area-inset-bottom);">
        <div class="h-16 flex justify-around items-center px-1">
            <a href="/" class="flex flex-col items-center gap-1 min-w-[3.5rem] {{ request()->routeIs('raffles.index') ? 'text-white' : 'text-slate-400' }}">
                <i class="fa-solid fa-house text-base" style="{{ request()->routeIs('raffles.index') ? 'color: var(--accent);' : '' }}"></i>
                <span class="text-[10px] font-medium">Início</span>
            </a>
            <a href="/#pacotes" class="flex flex-col items-center gap-1 min-w-[3.5rem] text-slate-400">
                <i class="fa-solid fa-boxes-stacked text-base"></i>
                <span class="text-[10px] font-medium">Pacotes</span>
            </a>
            <a href="/#acoes-promocionais" class="flex flex-col items-center gap-1 min-w-[3.5rem] text-slate-400">
                <i class="fa-solid fa-car text-base"></i>
                <span class="text-[10px] font-medium">Ações</span>
            </a>
            <a href="{{ route('pages.regulation') }}" class="flex flex-col items-center gap-1 min-w-[3.5rem] {{ request()->routeIs('pages.regulation') ? 'text-white' : 'text-slate-400' }}">
                <i class="fa-solid fa-scale-balanced text-base" style="{{ request()->routeIs('pages.regulation') ? 'color: var(--accent);' : '' }}"></i>
                <span class="text-[10px] font-medium">Regras</span>
            </a>
            @auth
                @if(in_array(auth()->user()->role, ['cliente', 'vendedor']))
                    <a href="{{ route('customer.dashboard') }}" class="flex flex-col items-center gap-1 min-w-[3.5rem] text-slate-400">
                        <i class="fa-solid fa-user text-base"></i>
                        <span class="text-[10px] font-medium">Perfil</span>
                    </a>
                @else
                    <a href="{{ route('admin.dashboard') }}" class="flex flex-col items-center gap-1 min-w-[3.5rem] text-slate-400">
                        <i class="fa-solid fa-gauge-high text-base"></i>
                        <span class="text-[10px] font-medium">Admin</span>
                    </a>
                @endif
            @else
                <a href="{{ route('login') }}" class="flex flex-col items-center gap-1 min-w-[3.5rem] text-slate-400">
                    <i class="fa-solid fa-right-to-bracket text-base"></i>
                    <span class="text-[10px] font-medium">Entrar</span>
                </a>
            @endauth
        </div>
    </nav>

    <!-- Back to top (hidden near top; stays clear of mobile nav/drawer) -->
    <button
        type="button"
        id="back-to-top"
        onclick="window.scrollTo({ top: 0, behavior: 'smooth' })"
        aria-label="Voltar ao topo"
        title="Voltar ao topo"
        class="fixed z-40 opacity-0 pointer-events-none translate-y-2 transition-all duration-300 ease-out
               right-4 bottom-20 md:right-6 md:bottom-8
               w-10 h-10 sm:w-11 sm:h-11
               rounded-full border shadow-lg backdrop-blur
               flex items-center justify-center
               text-white hover:scale-105 active:scale-95
               focus:outline-none focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:ring-offset-slate-950"
        style="background-color: color-mix(in srgb, var(--accent) 88%, #0f172a); border-color: color-mix(in srgb, var(--accent) 45%, transparent); box-shadow: 0 10px 25px rgba(0,0,0,0.35);"
    >
        <i class="fa-solid fa-arrow-up text-sm sm:text-base"></i>
    </button>

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
            updateBackToTopVisibility();
        }

        function updateBackToTopVisibility() {
            const btn = document.getElementById('back-to-top');
            const drawer = document.getElementById('mobile-drawer');
            if (!btn) return;

            const drawerOpen = drawer && !drawer.classList.contains('-translate-x-full');
            const scrolledEnough = window.scrollY > 420;
            const show = scrolledEnough && !drawerOpen;

            btn.classList.toggle('opacity-0', !show);
            btn.classList.toggle('pointer-events-none', !show);
            btn.classList.toggle('translate-y-2', !show);
            btn.classList.toggle('opacity-100', show);
            btn.classList.toggle('translate-y-0', show);
            btn.setAttribute('aria-hidden', show ? 'false' : 'true');
        }

        window.addEventListener('scroll', updateBackToTopVisibility, { passive: true });
        window.addEventListener('resize', updateBackToTopVisibility);
        document.addEventListener('DOMContentLoaded', updateBackToTopVisibility);
    </script>
</body>
</html>
