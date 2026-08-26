<?php

namespace Tests\Feature;

use App\Mail\PurchaseReceiptMail;
use App\Mail\WelcomeRegisteredMail;
use App\Models\Payment;
use App\Models\Raffle;
use App\Models\User;
use App\Services\PaymentService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class CheckoutRegistrationFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_register_page_shows_regulation_button_and_checkbox(): void
    {
        $response = $this->get(route('register'));

        $response->assertOk();
        $response->assertSee('Ler regulamento');
        $response->assertSee('name="accepted_regulation"', false);
        $response->assertSee(route('pages.regulation'), false);
    }

    public function test_cannot_register_without_accepting_regulation(): void
    {
        Mail::fake();

        $payload = $this->validRegistrationPayload();
        unset($payload['accepted_regulation']);

        $response = $this->from(route('register'))->post(route('register'), $payload);

        $response->assertRedirect(route('register'));
        $response->assertSessionHasErrors('accepted_regulation');
        $this->assertDatabaseCount('users', 0);
        Mail::assertNothingSent();
    }

    public function test_cannot_register_under_18(): void
    {
        Mail::fake();

        $payload = $this->validRegistrationPayload([
            'birth_date' => now()->subYears(17)->toDateString(),
        ]);

        $response = $this->from(route('register'))->post(route('register'), $payload);

        $response->assertRedirect(route('register'));
        $response->assertSessionHasErrors('birth_date');
        $this->assertDatabaseCount('users', 0);
    }

    public function test_can_register_with_full_profile_and_receives_welcome_email(): void
    {
        Mail::fake();

        $response = $this->post(route('register'), $this->validRegistrationPayload());

        $response->assertRedirect(route('raffles.index'));
        $this->assertAuthenticated();
        $this->assertDatabaseHas('users', [
            'email' => 'cliente@example.com',
            'cpf' => '39053344705',
            'role' => 'cliente',
        ]);

        $user = User::where('email', 'cliente@example.com')->first();
        $this->assertNotNull($user->accepted_regulation_at);
        $this->assertNotNull($user->birth_date);

        Mail::assertSent(WelcomeRegisteredMail::class, function (WelcomeRegisteredMail $mail) {
            return $mail->user->email === 'cliente@example.com';
        });
    }

    public function test_guest_buying_package_is_sent_to_register(): void
    {
        [$admin, $raffle] = $this->createRaffle();
        $package = $raffle->packages()->firstOrFail();

        $response = $this->post(route('raffles.buy', $raffle), [
            'package_id' => $package->id,
        ]);

        $response->assertRedirect(route('register'));
        $this->assertEquals($raffle->id, session('checkout.raffle_id'));
        $this->assertEquals($package->id, session('checkout.package_id'));
    }

    public function test_payment_confirmation_sends_receipt_email(): void
    {
        Mail::fake();

        $user = User::factory()->create([
            'role' => 'cliente',
            'email' => 'comprador@example.com',
        ]);

        $payment = Payment::create([
            'user_id' => $user->id,
            'amount' => 21.90,
            'gateway' => 'asaas',
            'gateway_transaction_id' => 'pay_test_123',
            'status' => 'pending',
            'payment_method' => 'pix',
            'pix_qr_code' => 'pix-code',
            'pix_qr_code_url' => 'https://example.com/qr.png',
        ]);

        app(PaymentService::class)->confirmPayment($payment);

        Mail::assertSent(PurchaseReceiptMail::class, function (PurchaseReceiptMail $mail) use ($user) {
            return $mail->payment->user_id === $user->id
                && count($mail->attachments()) === 1;
        });
    }

    /**
     * @param  array<string, mixed>  $overrides
     * @return array<string, mixed>
     */
    private function validRegistrationPayload(array $overrides = []): array
    {
        return array_merge([
            'name' => 'Cliente Teste',
            'email' => 'cliente@example.com',
            'cpf' => '390.533.447-05',
            'birth_date' => now()->subYears(25)->toDateString(),
            'whatsapp' => '66999999999',
            'phone_extra' => '',
            'zip_code' => '78680000',
            'address_street' => 'Rua das Flores',
            'address_number' => '100',
            'address_complement' => 'Casa',
            'address_neighborhood' => 'Centro',
            'address_city' => 'Água Boa',
            'address_state' => 'MT',
            'password' => 'senha123',
            'password_confirmation' => 'senha123',
            'accepted_regulation' => '1',
        ], $overrides);
    }

    /**
     * @return array{0: User, 1: Raffle}
     */
    private function createRaffle(): array
    {
        $admin = User::factory()->create([
            'role' => 'admin_organizador',
        ]);

        $raffle = Raffle::create([
            'user_id' => $admin->id,
            'title' => 'Ação Teste',
            'description' => 'Desc',
            'price' => 9.90,
            'total_numbers' => 1000,
            'status' => 'active',
            'prize_name' => 'Carro',
            'draw_date' => now()->addDays(20),
        ]);
        $raffle->seedDefaultPackages();

        return [$admin, $raffle->fresh('packages')];
    }
}
