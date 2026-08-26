<x-mail::message>
# Confirmação de exclusão

Olá,

Foi solicitada a exclusão da Ação Promocional **{{ $raffle->title }}** (ID #{{ $raffle->id }}).

Use o código abaixo para confirmar a exclusão. Ele expira em **{{ $expiresInMinutes }} minutos**.

<x-mail::panel>
**{{ $code }}**
</x-mail::panel>

Se você não solicitou essa exclusão, ignore este e-mail. Nenhuma ação será removida sem a confirmação do código.

Obrigado,<br>
{{ config('app.name') }}
</x-mail::message>
