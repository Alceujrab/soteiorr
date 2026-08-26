<?php

namespace App\Http\Controllers;

use App\Mail\WelcomeRegisteredMail;
use App\Models\User;
use App\Support\Cpf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function showRegisterForm()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $minBirthDate = now()->subYears(18)->toDateString();
        $cpfDigits = Cpf::digits((string) $request->input('cpf'));
        $request->merge(['cpf' => $cpfDigits]);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'cpf' => ['required', 'string', 'size:11', 'unique:users,cpf'],
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
            'birth_date.before_or_equal' => 'É obrigatório ter 18 anos ou mais para se cadastrar.',
            'accepted_regulation.accepted' => 'Você precisa ler e aceitar o regulamento para se cadastrar.',
            'password.required' => 'Informe uma senha ou marque a opção de gerar automaticamente.',
            'cpf.size' => 'Informe um CPF válido.',
        ]);

        if (! Cpf::isValid($cpfDigits)) {
            throw ValidationException::withMessages([
                'cpf' => 'Informe um CPF válido.',
            ]);
        }

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

    public function login(Request $request)
    {
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
            'email' => 'As credenciais fornecidas não correspondem aos nossos registros.',
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
