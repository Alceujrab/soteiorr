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
- **Controllers & Rotas**:
  - [web.php](file:///d:/sorteio/soteiorr/routes/web.php) (Rotas)
  - [RaffleController](file:///d:/sorteio/soteiorr/app/Http/Controllers/RaffleController.php)
  - [PaymentController](file:///d:/sorteio/soteiorr/app/Http/Controllers/PaymentController.php)
  - [AdminController](file:///d:/sorteio/soteiorr/app/Http/Controllers/AdminController.php)
  - [SupportController](file:///d:/sorteio/soteiorr/app/Http/Controllers/SupportController.php)
  - [ApiController](file:///d:/sorteio/soteiorr/app/Http/Controllers/ApiController.php) (Endpoints REST e Webhooks)
- **Ações & Serviços**:
  - [ReserveTicketsAction](file:///d:/sorteio/soteiorr/app/Actions/ReserveTicketsAction.php) (Garante atomicidade na reserva)
  - [PaymentService](file:///d:/sorteio/soteiorr/app/Services/PaymentService.php) (Checkout PIX simulado e webhook)
  - [LogActivityAction](file:///d:/sorteio/soteiorr/app/Actions/LogActivityAction.php) (Registrador de auditoria)
- **Views (Blade)**:
  - [layout](file:///d:/sorteio/soteiorr/resources/views/layouts/app.blade.php) (Glassmorphism dark theme)
  - [index](file:///d:/sorteio/soteiorr/resources/views/raffles/index.blade.php)
  - [show](file:///d:/sorteio/soteiorr/resources/views/raffles/show.blade.php)
  - [checkout](file:///d:/sorteio/soteiorr/resources/views/payments/show.blade.php)
  - [my_tickets](file:///d:/sorteio/soteiorr/resources/views/raffles/my_tickets.blade.php)
  - [admin dashboard](file:///d:/sorteio/soteiorr/resources/views/admin/dashboard.blade.php)
  - [admin logs](file:///d:/sorteio/soteiorr/resources/views/admin/logs.blade.php)
  - [admin settings](file:///d:/sorteio/soteiorr/resources/views/admin/settings.blade.php)
  - [admin participants](file:///d:/sorteio/soteiorr/resources/views/admin/participants.blade.php)
  - [admin reports](file:///d:/sorteio/soteiorr/resources/views/admin/reports.blade.php) (Visualização de relatórios do Chart.js)
  - [admin users](file:///d:/sorteio/soteiorr/resources/views/admin/users.blade.php) (Gestão de papéis de usuários)
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

## 3. Últimas Alterações Realizadas
- Criadas as tabelas e migrações para logs de auditoria (`activity_logs`) e tickets de suporte (`support_tickets`).
- Criados os modelos `ActivityLog` e `SupportTicket` com suas relações.
- Criado o helper de auditoria `LogActivityAction` e acoplado na criação de rifas, sorteios e confirmações de pagamento.
- Implementado o `SupportController` e tela de FAQ com formulário de envio de ticket de ajuda.
- Adicionadas as telas administrativas de Logs de Auditoria, Configurações Globais (gateways e limites de cotas) e listagem/contador de cotas de Participantes.
- Implementados os endpoints de API de listagem/detalhes de rifas e webhooks integrados para Asaas/MercadoPago em `ApiController`.
- Adicionadas as páginas administrativas de Relatórios Avançados (faturamento com Chart.js) e Gestão de Usuários (cargos e permissões).
- Atualizados os links de navegação da sidebar principal e validados testes via PHPUnit.
