<x-mail::message>
# Cadastro confirmado

Olá **{{ $user->name }}**,

Seu cadastro na **Ação RR Veículos** foi realizado com sucesso.

@if ($generatedPassword)
Sua senha gerada automaticamente é:

**{{ $generatedPassword }}**

Recomendamos alterá-la após o primeiro acesso.
@endif

<x-mail::button :url="route('login')">
Acessar minha conta
</x-mail::button>

Se você não solicitou este cadastro, ignore este e-mail.

Obrigado,<br>
{{ config('app.name') }}
</x-mail::message>
