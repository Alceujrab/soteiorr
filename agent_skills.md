# Guia de Habilidades para Agentes de IA (Skills)

Este documento contém diretrizes operacionais de como manter a base de código do sistema **Ação RR Veículos Entre Amigos** limpa, legível e otimizada para tokens.

## Padrões de Projeto (Design Patterns)
1. **Service Pattern**: Toda integração externa (Asaas, PagSeguro, Mercado Pago) deve ser envelopada em um Service class sob `app/Services`.
2. **Action Pattern**: Ações críticas de negócio (como reservar bilhetes, confirmar sorteio) devem usar classes Action sob `app/Actions`.
3. **Responsive UI**: A estilização deve seguir o CSS padrão/Laravel Blade. Evite carregar scripts pesados desnecessários.

## Como retomar o trabalho (Para novas IAs)
1. **NÃO varra todos os arquivos de código**. Leia o `agent_logbook.md` primeiro.
2. Siga a lista de tarefas atualizada em `C:/Users/alceujr/.gemini/antigravity-ide/brain/5d8a2c90-43cb-47fe-b6ef-6c1514df5207/task.md`.
3. Mantenha os comentários do código concisos, focando no "porquê" e não no "o quê".
