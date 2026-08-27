<x-mail::message>
# Lembrete de pagamento PIX

Olá **{{ $payment->user->name }}**,

Sua reserva ainda está aguardando pagamento. Restam cerca de **{{ sprintf('%02d:%02d', intdiv($secondsRemaining, 60), $secondsRemaining % 60) }}**.

- **Pedido:** #{{ $payment->id }}
- **Valor:** R$ {{ number_format((float) $payment->amount, 2, ',', '.') }}

<x-mail::button :url="route('payments.show', $payment)">
Concluir PIX agora
</x-mail::button>

@if ($whatsappUrl)
Dúvidas? [Chame no WhatsApp]({{ $whatsappUrl }}).
@endif

Obrigado,<br>
{{ config('app.name') }}
</x-mail::message>
