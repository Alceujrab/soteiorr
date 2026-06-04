# Diário de Bordo do Agente - Ação RR Veículos Entre Amigos

Este arquivo serve como a única fonte de verdade sobre o estado atual do projeto para economizar tokens de contexto de IA. **Sempre atualize este arquivo a cada mudança relevante.**

## 1. Mapeamento de Arquivos Importantes
- **Models**:
  - [Raffle](file:///d:/sorteio/soteiorr/app/Models/Raffle.php) (Rifas)
  - [Ticket](file:///d:/sorteio/soteiorr/app/Models/Ticket.php) (Bilhetes)
  - [Payment](file:///d:/sorteio/soteiorr/app/Models/Payment.php) (Pagamentos/PIX)
  - [Draw](file:///d:/sorteio/soteiorr/app/Models/Draw.php) (Sorteios)
- **Controllers & Rotas**:
  - [web.php](file:///d:/sorteio/soteiorr/routes/web.php) (Rotas)
  - [RaffleController](file:///d:/sorteio/soteiorr/app/Http/Controllers/RaffleController.php)
  - [PaymentController](file:///d:/sorteio/soteiorr/app/Http/Controllers/PaymentController.php)
  - [AdminController](file:///d:/sorteio/soteiorr/app/Http/Controllers/AdminController.php)
- **Ações & Serviços**:
  - [ReserveTicketsAction](file:///d:/sorteio/soteiorr/app/Actions/ReserveTicketsAction.php) (Garante atomicidade na reserva)
  - [PaymentService](file:///d:/sorteio/soteiorr/app/Services/PaymentService.php) (Checkout PIX simulado e webhook)
- **Views (Blade)**:
  - [layout](file:///d:/sorteio/soteiorr/resources/views/layouts/app.blade.php) (Glassmorphism dark theme)
  - [index](file:///d:/sorteio/soteiorr/resources/views/raffles/index.blade.php)
  - [show](file:///d:/sorteio/soteiorr/resources/views/raffles/show.blade.php)
  - [checkout](file:///d:/sorteio/soteiorr/resources/views/payments/show.blade.php)
  - [admin dashboard](file:///d:/sorteio/soteiorr/resources/views/admin/dashboard.blade.php)

## 2. Status das Funcionalidades
- [x] Inicialização do Laravel (Concluído)
- [x] Estrutura de Banco de Dados (Concluído)
- [x] Módulo de Pagamentos / Checkout (Concluído)
- [x] Compra de Números (Concluído)
- [x] Painel do Administrador e Sorteios (Concluído)

## 3. Últimas Alterações Realizadas
- Inicializado o projeto Laravel 13 e configurada a base de dados SQLite.
- Criadas as migrações e modelos para `User`, `Raffle`, `Ticket`, `Payment` e `Draw`.
- Implementados os padrões Service/Action para reservas seguras e checkout PIX.
- Criada a interface frontend com Tailwind CSS e Glassmorphism.
- Desenvolvido o Painel Admin para controle de KPIs e sorteio instantâneo.
- Escritos testes automatizados em `tests/Feature/RaffleTest.php` e validados via PHPUnit.
