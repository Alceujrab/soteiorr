<x-mail::message>
# Pagamento confirmado

Olá **{{ $payment->user->name }}**,

Recebemos o pagamento da sua compra e anexamos o **PDF do comprovante**.

- **Recibo:** {{ $payment->gateway_transaction_id ?: '#'.$payment->id }}
- **Valor:** R$ {{ number_format((float) $payment->amount, 2, ',', '.') }}
- **Status:** Confirmado

<x-mail::button :url="route('payments.receipt', $payment)">
Ver comprovante online
</x-mail::button>

Obrigado por participar,<br>
{{ config('app.name') }}
</x-mail::message>
