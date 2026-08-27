<?php

namespace App\Http\Controllers;

use App\Mail\WelcomeRegisteredMail;
use App\Models\User;
use App\Services\RecaptchaService;
use App\Support\Cpf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class AuthController extends Controller
{
    public function showRegisterForm()
    {
        return view('auth.register');
    }

    public function register(Request $request, RecaptchaService $recaptcha)
    {
        $recaptcha->validateOrFail(
            $request->input('g-recaptcha-response'),
            $request->ip(),
            'register'
        );

        $minBirthDate = now()->subYears(18)->toDateString();
        $cpfDigits = Cpf::digits((string) $request->input('cpf'));
        $request->merge(['cpf' => $cpfDigits]);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'cpf' => [
                'required',
                'string',
                'size:11',
                'unique:users,cpf',
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
            'generate_password' => ['nullable', 'boolean'],
            'password' => [
                Rule::requiredIf(! $request->boolean('generate_password')),
                'nullable',
                'string',
                'min:6',
                'confirmed',
            ],
            'accepted_regulation' => ['accepted'],
        ], [
            'email.unique' => 'Este e-mail já está cadastrado. Faça login ou use outro e-mail.',
            'cpf.unique' => 'Este CPF já está cadastrado. Faça login ou recupere sua senha.',
            'birth_date.before_or_equal' => 'É obrigatório ter 18 anos ou mais para se cadastrar.',
            'accepted_regulation.accepted' => 'Você precisa ler e aceitar o regulamento para se cadastrar.',
            'password.required' => 'Informe uma senha ou marque a opção de gerar automaticamente.',
            'password.confirmed' => 'A confirmação da senha não confere.',
            'password.min' => 'A senha deve ter no mínimo 6 caracteres.',
            'cpf.size' => 'Informe um CPF válido.',
            'name.required' => 'Informe o nome completo.',
            'email.required' => 'Informe o e-mail.',
            'email.email' => 'Informe um e-mail válido.',
            'cpf.required' => 'Informe o CPF.',
            'birth_date.required' => 'Informe a data de nascimento.',
            'whatsapp.required' => 'Informe o WhatsApp.',
            'zip_code.required' => 'Informe o CEP.',
            'address_street.required' => 'Informe a rua/avenida.',
            'address_number.required' => 'Informe o número.',
            'address_neighborhood.required' => 'Informe o bairro.',
            'address_city.required' => 'Informe a cidade.',
            'address_state.required' => 'Informe a UF.',
            'address_state.size' => 'A UF deve ter 2 letras.',
        ]);

        $plainPassword = $request->boolean('generate_password')
            ? Str::password(10)
            : (string) $validated['password'];

        $whatsapp = preg_replace('/\D+/', '', $validated['whatsapp']) ?? '';

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
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
            'role' => 'cliente',
            'password' => $plainPassword,
            'accepted_regulation_at' => now(),
        ]);

        Mail::to($user->email)->send(new WelcomeRegisteredMail(
            $user,
            $request->boolean('generate_password') ? $plainPassword : null
        ));

        Auth::login($user);
        $request->session()->regenerate();

        if ($request->session()->has('checkout.raffle_id') && $request->session()->has('checkout.package_id')) {
            return redirect()->route('checkout.continue')
                ->with('success', 'Cadastro realizado! Continuando para o pagamento.');
        }

        return redirect()->route('raffles.index')
            ->with('success', 'Cadastro realizado com sucesso! Confira seu e-mail de confirmação.');
    }

    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request, RecaptchaService $recaptcha)
    {
        $recaptcha->validateOrFail(
            $request->input('g-recaptcha-response'),
            $request->ip(),
            'login'
        );

        $credentials = $request->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            if (Auth::user()->role === 'cliente') {
                if ($request->session()->has('checkout.raffle_id') && $request->session()->has('checkout.package_id')) {
                    return redirect()->route('checkout.continue');
                }

                return redirect()->intended(route('raffles.index'));
            }

            return redirect()->intended(route('admin.dashboard'));
        }

        return back()->withErrors([
            'email' => 'E-mail ou senha incorretos.',
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
