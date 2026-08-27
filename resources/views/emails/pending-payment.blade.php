<x-mail::message>
# Cotas reservadas — finalize o PIX

Olá **{{ $payment->user->name }}**,

Suas cotas foram reservadas. O pagamento PIX precisa ser concluído em até **30 minutos**.

- **Pedido:** #{{ $payment->id }}
- **Valor:** R$ {{ number_format((float) $payment->amount, 2, ',', '.') }}
- **Tempo restante aproximado:** {{ sprintf('%02d:%02d', intdiv($secondsRemaining, 60), $secondsRemaining % 60) }}

<x-mail::button :url="route('payments.show', $payment)">
Pagar com PIX agora
</x-mail::button>

@if ($whatsappUrl)
Precisa de ajuda? [Fale no WhatsApp]({{ $whatsappUrl }}).
@endif

Se o PIX não for confirmado a tempo, os números voltam para outros participantes.

Obrigado,<br>
{{ config('app.name') }}
</x-mail::message>
