<?php

namespace Tests\Feature;

use App\Models\Setting;
use App\Models\User;
use App\Services\AsaasService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class AsaasPaymentServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_create_pix_charge_calls_asaas_endpoints(): void
    {
        Setting::set('gateway_asaas_key', 'test_asaas_key');
        Setting::set('asaas_sandbox', '1');

        Http::fake([
            'https://api-sandbox.asaas.com/v3/customers' => Http::response(['id' => 'cus_123'], 200),
            'https://api-sandbox.asaas.com/v3/payments' => Http::response(['id' => 'pay_123'], 200),
            'https://api-sandbox.asaas.com/v3/payments/pay_123/pixQrCode' => Http::response([
                'payload' => '00020126PIXTEST',
                'encodedImage' => 'iVBORw0KGgo=',
            ], 200),
        ]);

        $user = User::factory()->create([
            'name' => 'Cliente Asaas',
            'cpf' => '39053344705',
            'email' => 'asaas@example.com',
            'whatsapp' => '66999999999',
            'phone' => '66999999999',
            'zip_code' => '78680000',
            'address_street' => 'Rua A',
            'address_number' => '10',
            'address_neighborhood' => 'Centro',
            'address_city' => 'Agua Boa',
            'address_state' => 'MT',
            'birth_date' => now()->subYears(30)->toDateString(),
            'role' => 'cliente',
        ]);

        $result = app(AsaasService::class)->createPixCharge($user, 21.90, 'tx_ref_1', 'Teste PIX');

        $this->assertSame('pay_123', $result['id']);
        $this->assertSame('00020126PIXTEST', $result['payload']);
        $this->assertSame('cus_123', $user->fresh()->asaas_customer_id);

        Http::assertSentCount(3);
    }
}
