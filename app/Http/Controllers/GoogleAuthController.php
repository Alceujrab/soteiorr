<?php

namespace App\Http\Controllers;

use App\Mail\WelcomeRegisteredMail;
use App\Models\User;
use App\Services\GoogleOAuthService;
use App\Services\RecaptchaService;
use App\Support\Cpf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class GoogleAuthController extends Controller
{
    public function redirect(Request $request, GoogleOAuthService $google)
    {
        if (! $google->isEnabled()) {
            return redirect()->route('login')
                ->withErrors(['email' => 'Login com Google ainda não está ativado. Configure no painel admin.']);
        }

        $state = Str::random(40);
        $request->session()->put('google_oauth_state', $state);

        return redirect()->away($google->authorizationUrl($state));
    }

    public function callback(Request $request, GoogleOAuthService $google)
    {
        if (! $google->isEnabled()) {
            return redirect()->route('login')
                ->withErrors(['email' => 'Login com Google ainda não está ativado.']);
        }

        if ($request->filled('error')) {
            return redirect()->route('login')
                ->withErrors(['email' => 'Login com Google cancelado.']);
        }

        $state = (string) $request->session()->pull('google_oauth_state', '');
        if ($state === '' || $state !== (string) $request->input('state')) {
            return redirect()->route('login')
                ->withErrors(['email' => 'Sessão do Google inválida. Tente novamente.']);
        }

        $code = (string) $request->input('code');
        if ($code === '') {
            return redirect()->route('login')
                ->withErrors(['email' => 'Código de autorização do Google não recebido.']);
        }

        try {
            $googleUser = $google->userFromAuthorizationCode($code);
        } catch (\Throwable $e) {
            return redirect()->route('login')
                ->withErrors(['email' => $e->getMessage()]);
        }

        $user = User::query()->where('google_id', $googleUser['id'])->first();

        if (! $user) {
            $user = User::query()->where('email', $googleUser['email'])->first();
        }

        $isNew = false;

        if (! $user) {
            $isNew = true;
            $user = User::create([
                'name' => $googleUser['name'],
                'email' => $googleUser['email'],
                'google_id' => $googleUser['id'],
                'avatar' => $googleUser['picture'],
                'password' => Str::password(32),
                'role' => 'cliente',
                'email_verified_at' => $googleUser['email_verified'] ? now() : null,
            ]);

            Mail::to($user->email)->send(new WelcomeRegisteredMail($user));
        } else {
            $user->forceFill([
                'google_id' => $user->google_id ?: $googleUser['id'],
                'avatar' => $googleUser['picture'] ?: $user->avatar,
                'name' => $user->name ?: $googleUser['name'],
                'email_verified_at' => $user->email_verified_at ?: ($googleUser['email_verified'] ? now() : null),
            ])->save();
        }

        Auth::login($user, true);
        $request->session()->regenerate();

        if (! $user->hasCompleteCheckoutProfile()) {
            return redirect()->route('profile.complete')
                ->with('success', $isNew
                    ? 'Conta Google conectada! Complete seus dados para participar.'
                    : 'Complete seu cadastro para continuar.');
        }

        if ($request->session()->has('checkout.raffle_id') && $request->session()->has('checkout.package_id')) {
            return redirect()->route('checkout.continue');
        }

        return redirect()->intended(route('raffles.index'))
            ->with('success', 'Login com Google realizado com sucesso!');
    }

    public function showCompleteProfile()
    {
        $user = Auth::user();
        if (! $user) {
            return redirect()->route('login');
        }

        if ($user->hasCompleteCheckoutProfile()) {
            return redirect()->route('raffles.index');
        }

        return view('auth.complete-profile', compact('user'));
    }

    public function completeProfile(Request $request, RecaptchaService $recaptcha)
    {
        $user = Auth::user();
        if (! $user) {
            return redirect()->route('login');
        }

        $recaptcha->validateOrFail(
            $request->input('g-recaptcha-response'),
            $request->ip(),
            'complete_profile'
        );

        $minBirthDate = now()->subYears(18)->toDateString();
        $cpfDigits = Cpf::digits((string) $request->input('cpf'));
        $request->merge(['cpf' => $cpfDigits]);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'cpf' => [
                'required',
                'string',
                'size:11',
                'unique:users,cpf,'.$user->id,
                function (string $attribute, mixed $value, \Closure $fail): void {
                    if (! Cpf::isValid((string) $value)) {
                        $fail('Informe um CPF válido.');
                    }
                },
            ],
            'birth_date' => ['required', 'date', 'before_or_equal:'.$minBirthDate],
            'zip_code' => ['required', 'string', 'max:12'],
            'address_street' => ['required', 'string', 'max:255'],
            'address_number' => ['required', 'string', 'max:30'],
            'address_complement' => ['nullable', 'string', 'max:255'],
            'address_neighborhood' => ['required', 'string', 'max:255'],
            'address_city' => ['required', 'string', 'max:255'],
            'address_state' => ['required', 'string', 'size:2'],
            'whatsapp' => ['required', 'string', 'max:20'],
            'phone_extra' => ['nullable', 'string', 'max:20'],
            'accepted_regulation' => ['accepted'],
        ], [
            'birth_date.before_or_equal' => 'É obrigatório ter 18 anos ou mais para se cadastrar.',
            'accepted_regulation.accepted' => 'Você precisa ler e aceitar o regulamento para se cadastrar.',
            'cpf.unique' => 'Este CPF já está cadastrado.',
        ]);

        $whatsapp = preg_replace('/\D+/', '', $validated['whatsapp']) ?? '';

        $user->forceFill([
            'name' => $validated['name'],
            'cpf' => $cpfDigits,
            'phone' => $whatsapp,
            'birth_date' => $validated['birth_date'],
            'whatsapp' => $whatsapp,
            'phone_extra' => filled($validated['phone_extra'] ?? null)
                ? (preg_replace('/\D+/', '', $validated['phone_extra']) ?: null)
                : null,
            'zip_code' => preg_replace('/\D+/', '', $validated['zip_code']) ?? '',
            'address_street' => $validated['address_street'],
            'address_number' => $validated['address_number'],
            'address_complement' => $validated['address_complement'] ?? null,
            'address_neighborhood' => $validated['address_neighborhood'],
            'address_city' => $validated['address_city'],
            'address_state' => strtoupper($validated['address_state']),
            'accepted_regulation_at' => $user->accepted_regulation_at ?: now(),
        ])->save();

        if ($request->session()->has('checkout.raffle_id') && $request->session()->has('checkout.package_id')) {
            return redirect()->route('checkout.continue')
                ->with('success', 'Cadastro completo! Continuando para o pagamento.');
        }

        return redirect()->route('raffles.index')
            ->with('success', 'Cadastro completo com sucesso!');
    }
}
