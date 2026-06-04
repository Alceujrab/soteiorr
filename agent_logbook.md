# Diário de Bordo do Agente - Ação RR Veículos Entre Amigos

Este arquivo serve como a única fonte de verdade sobre o estado atual do projeto para economizar tokens de contexto de IA. **Sempre atualize este arquivo a cada mudança relevante.**

## 1. Mapeamento de Arquivos Importantes
- **Models**:
  - [Raffle](file:///d:/sorteio/soteiorr/app/Models/Raffle.php) (Rifas)
  - [Ticket](file:///d:/sorteio/soteiorr/app/Models/Ticket.php) (Bilhetes)
  - [Payment](file:///d:/sorteio/soteiorr/app/Models/Payment.php) (Pagamentos/PIX)
  - [Draw](file:///d:/sorteio/soteiorr/app/Models/Draw.php) (Sorteios)
  - [ActivityLog](file:///d:/sorteio/soteiorr/app/Models/ActivityLog.php) (Logs de Auditoria)
  - [SupportTicket](file:///d:/sorteio/soteiorr/app/Models/SupportTicket.php) (Tickets de Suporte)
  - [NotificationLog](file:///d:/sorteio/soteiorr/app/Models/NotificationLog.php) (Registros de Notificações)
- **Controllers & Rotas**:
  - [web.php](file:///d:/sorteio/soteiorr/routes/web.php) (Rotas)
  - [RaffleController](file:///d:/sorteio/soteiorr/app/Http/Controllers/RaffleController.php)
  - [PaymentController](file:///d:/sorteio/soteiorr/app/Http/Controllers/PaymentController.php)
  - [AdminController](file:///d:/sorteio/soteiorr/app/Http/Controllers/AdminController.php)
  - [SupportController](file:///d:/sorteio/soteiorr/app/Http/Controllers/SupportController.php)
  - [ApiController](file:///d:/sorteio/soteiorr/app/Http/Controllers/ApiController.php) (Endpoints REST e Webhooks)
  - [AuthController](file:///d:/sorteio/soteiorr/app/Http/Controllers/AuthController.php) (Autenticação real)
- **Ações & Serviços**:
  - [ReserveTicketsAction](file:///d:/sorteio/soteiorr/app/Actions/ReserveTicketsAction.php) (Garante atomicidade na reserva)
  - [PaymentService](file:///d:/sorteio/soteiorr/app/Services/PaymentService.php) (Checkout PIX simulado e webhook)
  - [LogActivityAction](file:///d:/sorteio/soteiorr/app/Actions/LogActivityAction.php) (Registrador de auditoria)
- **Views (Blade)**:
  - [public layout](file:///d:/sorteio/soteiorr/resources/views/layouts/public.blade.php) (Visitor headers)
  - [customer layout](file:///d:/sorteio/soteiorr/resources/views/layouts/customer.blade.php) (Client specific sidebar)
  - [admin layout](file:///d:/sorteio/soteiorr/resources/views/layouts/admin.blade.php) (Full admin sidebar & profile switcher)
  - [index](file:///d:/sorteio/soteiorr/resources/views/raffles/index.blade.php)
  - [show](file:///d:/sorteio/soteiorr/resources/views/raffles/show.blade.php)
  - [checkout](file:///d:/sorteio/soteiorr/resources/views/payments/show.blade.php)
  - [my_tickets](file:///d:/sorteio/soteiorr/resources/views/raffles/my_tickets.blade.php)
  - [login](file:///d:/sorteio/soteiorr/resources/views/auth/login.blade.php) (Tela de login dark)
  - [register](file:///d:/sorteio/soteiorr/resources/views/auth/register.blade.php) (Tela de cadastro com CPF)
  - [admin dashboard](file:///d:/sorteio/soteiorr/resources/views/admin/dashboard.blade.php)
  - [admin logs](file:///d:/sorteio/soteiorr/resources/views/admin/logs.blade.php)
  - [admin settings](file:///d:/sorteio/soteiorr/resources/views/admin/settings.blade.php)
  - [admin participants](file:///d:/sorteio/soteiorr/resources/views/admin/participants.blade.php)
  - [admin reports](file:///d:/sorteio/soteiorr/resources/views/admin/reports.blade.php)
  - [admin users](file:///d:/sorteio/soteiorr/resources/views/admin/users.blade.php)
  - [admin notifications](file:///d:/sorteio/soteiorr/resources/views/admin/notifications.blade.php)
  - [support index](file:///d:/sorteio/soteiorr/resources/views/support/index.blade.php)

## 2. Status das Funcionalidades
- [x] Inicialização do Laravel (Concluído)
- [x] Estrutura de Banco de Dados (Concluído)
- [x] Módulo de Pagamentos / Checkout (Concluído)
- [x] Compra de Números (Concluído)
- [x] Painel do Administrador e Sorteios (Concluído)
- [x] Histórico/Meus Bilhetes para Clientes (Concluído)
- [x] Gestão de Participantes (Concluído)
- [x] Auditoria e Compliance logs (Concluído)
- [x] Configurações Globais (Concluído)
- [x] Suporte e Tickets (Concluído)
- [x] Relatórios e Analytics (Concluído)
- [x] Gestão de Usuários e Permissões (Concluído)
- [x] API e Integrações / Webhooks (Concluído)
- [x] Notificações e Comunicação / Disparo em Massa (Concluído)
- [x] Autenticação e Cadastro Real (Concluído)
- [x] Separação de Painéis (Público, Cliente e Admin) (Concluído)

## 3. Últimas Alterações Realizadas
- Refatorado toda a arquitetura de layouts, separando o site para visitantes (`public.blade.php`), a área do comprador (`customer.blade.php`) e o painel administrativo (`admin.blade.php`).
- Criado o `AuthController` e as telas correspondentes de Cadastro e Login com autenticação e validação segura (CPF, E-mail, confirmação de senha).
- Atualizadas todas as views existentes para herdar seus respectivos novos layouts de acordo com a finalidade da página.
- Removido o arquivo de layout legado (`app.blade.php`).
- Validadas todas as rotas e funcionamento de testes unitários.
- Adicionado suporte a menus responsivos hamburger e painel drawer deslizante nos layouts público, admin e cliente.
- Implementado sistema de configurações persistentes via banco de dados (`settings` table e `Setting` model) no Laravel.
- Integrado APIs diretas de PIX PJ do Itaú e Santander no `PaymentService` com suporte a autenticação mTLS (certificados SSL) e fallback de simulação.

