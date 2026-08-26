<?php

namespace App\Http\Controllers;

use App\Actions\ReserveTicketsAction;
use App\Models\Banner;
use App\Models\Payment;
use App\Models\Raffle;
use App\Models\Setting;
use App\Models\Ticket;
use App\Models\User;
use App\Services\PaymentService;
use App\Support\DefaultRegulationContent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RaffleController extends Controller
{
    /**
     * Listar todas as Ações Promocionais ativas.
     */
    public function index()
    {
        $raffles = Raffle::where('status', 'active')
            ->withCount([
                'tickets as paid_tickets_count' => fn ($q) => $q->where('status', 'paid'),
                'tickets as taken_tickets_count' => fn ($q) => $q->whereIn('status', ['paid', 'reserved']),
            ])
            ->get();
        $banners = Banner::where('active', true)->get();

        // Se não houver nenhum banner, cria alguns banners padrão para exibição inicial premium
        if ($banners->isEmpty()) {
            Banner::create([
                'title' => 'Ação Promocional de Luxo: Mustang GT',
                'subtitle' => 'Adquira seus bilhetes a partir de R$ 5,00 e concorra!',
                'image_url' => 'https://images.unsplash.com/photo-1583121274602-3e2820c69888?auto=format&fit=crop&w=1200&q=80',
                'active' => true,
            ]);
            Banner::create([
                'title' => 'BMW M4 Competition',
                'subtitle' => 'O esportivo dos seus sonhos pode ser seu neste domingo.',
                'image_url' => 'https://images.unsplash.com/photo-1555215695-3004980ad54e?auto=format&fit=crop&w=1200&q=80',
                'active' => true,
            ]);
            $banners = Banner::where('active', true)->get();
        }

        return view('raffles.index', compact('raffles', 'banners'));
    }

    /**
     * Exibir detalhes de uma Ação Promocional e o grid de números.
     */
    public function show(Raffle $raffle)
    {
        // Carregar bilhetes ocupados (reservados e pagos)
        $takenTickets = Ticket::where('raffle_id', $raffle->id)
            ->get()
            ->keyBy('number');

        return view('raffles.show', compact('raffle', 'takenTickets'));
    }

    /**
     * Processar a compra/reserva de números.
     */
    public function buy(Request $request, Raffle $raffle, ReserveTicketsAction $reserveAction, PaymentService $paymentService)
    {
        $mode = $request->input('mode', 'manual');

        if ($mode === 'auto') {
            $request->validate([
                'quantity' => 'required|integer|min:1|max:100',
            ]);

            $quantity = (int) $request->input('quantity');

            // Encontrar números disponíveis
            $takenNumbers = Ticket::where('raffle_id', $raffle->id)
                ->pluck('number')
                ->toArray();

            $availableNumbers = [];
            for ($i = 1; $i <= $raffle->total_numbers; $i++) {
                if (! in_array($i, $takenNumbers)) {
                    $availableNumbers[] = $i;
                }
            }

            if (count($availableNumbers) < $quantity) {
                return redirect()->back()->withErrors(['error' => 'Não há números disponíveis suficientes para a quantidade solicitada.']);
            }

            // Selecionar aleatoriamente
            shuffle($availableNumbers);
            $numbers = array_slice($availableNumbers, 0, $quantity);
        } else {
            $request->validate([
                'numbers' => 'required|array|min:1',
                'numbers.*' => 'integer',
            ]);
            $numbers = $request->input('numbers');
        }

        // Simular login do cliente de teste se não estiver logado
        if (! Auth::check()) {
            $user = User::where('role', 'cliente')->first() ?: User::first();
            Auth::login($user);
        }

        $user = Auth::user();

        try {
            // 1. Reservar os números
            $tickets = $reserveAction->execute($user, $raffle, $numbers);

            // 2. Criar o pagamento correspondente
            $payment = $paymentService->createPayment($user, $tickets);

            return redirect()->route('payments.show', $payment->id)
                ->with('success', 'Números reservados! Efetue o pagamento PIX para confirmar.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * Exibir os bilhetes adquiridos pelo cliente logado.
     */
    public function myTickets()
    {
        if (! Auth::check()) {
            $user = User::where('role', 'cliente')->first() ?: User::first();
            Auth::login($user);
        }

        $user = Auth::user();

        $tickets = Ticket::with(['raffle', 'payment'])
            ->where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('raffles.my_tickets', compact('tickets'));
    }

    /**
     * Exibir o recibo de compra.
     */
    public function receipt(Payment $payment)
    {
        $payment->load(['user', 'tickets.raffle']);

        return view('payments.receipt', compact('payment'));
    }

    /**
     * Exibir validador de bilhetes público.
     */
    public function validateTicket(Request $request, $id = null)
    {
        $payment = null;
        if ($id) {
            $payment = Payment::with(['user', 'tickets.raffle'])->find($id);
        }

        $maskedUser = null;
        if ($payment && $payment->user) {
            $user = $payment->user;

            // Regra LGPD: Verificar se está logado e se é o proprietário ou admin
            $canViewFullDetails = Auth::check() && (Auth::id() === $user->id || in_array(Auth::user()->role, ['super_admin', 'admin_organizador']));

            if (! $canViewFullDetails) {
                $maskedUser = (object) [
                    'name' => $this->maskName($user->name),
                    'cpf' => $this->maskCpf($user->cpf),
                ];
            } else {
                $maskedUser = $user;
            }
        }

        return view('raffles.validate', compact('payment', 'maskedUser'));
    }

    public function validateTicketPost(Request $request)
    {
        $request->validate([
            'code' => 'required|string',
        ]);

        $code = str_replace('tx_', '', $request->input('code'));
        $payment = Payment::where('gateway_transaction_id', 'tx_'.$code)
            ->orWhere('gateway_transaction_id', $code)
            ->orWhere('id', $code)
            ->first();

        if (! $payment) {
            return redirect()->back()->withErrors(['error' => 'Código de bilhete ou recibo não encontrado ou inválido.']);
        }

        return redirect()->route('raffles.validate-ticket', $payment->id);
    }

    private function maskName($name)
    {
        $parts = explode(' ', $name);
        $maskedParts = array_map(function ($part) {
            if (strlen($part) <= 2) {
                return $part;
            }

            return substr($part, 0, 2).str_repeat('*', strlen($part) - 2);
        }, $parts);

        return implode(' ', $maskedParts);
    }

    private function maskCpf($cpf)
    {
        $clean = preg_replace('/\D/', '', $cpf);
        if (strlen($clean) !== 11) {
            return $cpf;
        }

        return $clean[0].str_repeat('*', 9).$clean[10];
    }

    public function about()
    {
        $default = '<h1>Sobre Nós</h1>
        <p>A <strong>Ação RR Veículos</strong>, com sede em Água Boa - MT, é uma plataforma referência em ações entre amigos, unindo transparência, credibilidade e tecnologia para realizar sonhos de norte a sul do país.</p>
        <h2>Nossa História</h2>
        <p>Fundada com a premissa de criar uma experiência de participação segura e confiável, a Ação RR Veículos já entregou dezenas de veículos de alta qualidade. Nosso foco é garantir que cada participante sinta-se seguro sabendo que as regras são claras e os resultados são auditados.</p>
        <h2>Missão, Visão e Valores</h2>
        <ul>
            <li><strong>Missão:</strong> Realizar o sonho da conquista de um carro ou moto de forma acessível e com total integridade.</li>
            <li><strong>Visão:</strong> Ser a plataforma de Ações Promocionais e ações entre amigos mais transparente e admirada do Brasil.</li>
            <li><strong>Valores:</strong> Transparência total, compromisso com a verdade, segurança de dados (LGPD) e respeito incondicional aos regulamentos de Ações Promocionais.</li>
        </ul>';
        $content = Setting::get('page_about_us', $default);

        return view('pages.about', compact('content'));
    }

    public function contact()
    {
        $default = '<h1>Fale Conosco</h1>
        <p>Precisa de suporte com suas cotas ou tem alguma dúvida? Nossa central de atendimento da Ação RR Veículos Água Boa - MT está de braços abertos para ajudar.</p>
        <h2>Canais de Atendimento Oficiais</h2>
        <ul>
            <li><strong>WhatsApp Suporte:</strong> (66) 99999-9999 (Atendimento prioritário)</li>
            <li><strong>E-mail Corporativo:</strong> suporte@acaorrveiculos.com.br</li>
            <li><strong>Endereço Comercial:</strong> Avenida das Nações, 1000 - Centro, Água Boa - MT</li>
        </ul>
        <h2>Horário de Atendimento</h2>
        <p>Segunda a Sexta-feira: 08:00 às 18:00<br>Sábados: 08:00 às 12:00</p>';
        $content = Setting::get('page_contact', $default);

        return view('pages.contact', compact('content'));
    }

    public function faqs()
    {
        $default = '<h1>Dúvidas Frequentes (FAQs)</h1>
        <p>Encontre respostas rápidas para as principais dúvidas de nossos participantes sobre as compras e Ações Promocionais.</p>
        
        <h2>1. Como posso comprar números da sorte?</h2>
        <p>Basta navegar pelas Ações Promocionais ativas em nossa página inicial, selecionar os números desejados no grid (ou utilizar a escolha automática pela "Surpresinha") e prosseguir para a tela de finalização de compra com Pix.</p>

        <h2>2. Qual o prazo máximo de pagamento do PIX?</h2>
        <p>As cotas reservadas possuem prazo de validade de <strong>30 minutos</strong>. Caso o pagamento via QR Code ou Copia e Cola não seja confirmado nesse prazo, os números retornam ao grid público para outros interessados.</p>

        <h2>3. Como é definido o ganhador da Ação Promocional?</h2>
        <p>Nossas Ações Promocionais oficiais utilizam a extração da <strong>Loteria Federal</strong> ou realizamos transmissões ao vivo auditadas em nossas redes sociais. O número vencedor é sempre baseado na combinação correspondente e anunciado publicamente.</p>

        <h2>4. Onde posso acompanhar as minhas cotas compradas?</h2>
        <p>Ao realizar o login, acesse a aba <strong>"Meus Bilhetes"</strong> no seu painel para visualizar o histórico de compras, status e comprovantes em PDF.</p>

        <h2>5. Como funciona a entrega do veículo da Ação Promocional?</h2>
        <p>O prêmio é entregue sem custos adicionais ao ganhador (incluindo transferência) na cidade de Água Boa - MT ou enviado com frete sob nossa responsabilidade para o endereço do vencedor cadastrado.</p>';
        $content = Setting::get('page_faqs', $default);

        return view('pages.faqs', compact('content'));
    }

    public function privacy()
    {
        $default = '<h1>Política de Privacidade</h1>
        <p>Esta política descreve o compromisso da <strong>Ação RR Veículos</strong> em proteger a privacidade e os dados pessoais de seus usuários de acordo com a Lei Geral de Proteção de Dados (LGPD).</p>
        <h2>Coleta e Finalidade dos Dados</h2>
        <p>Coletamos nome completo, CPF, e-mail e telefone para identificar unicamente o participante da ação e possibilitar a entrega legítima do prêmio da Ação Promocional. Não compartilhamos informações pessoais com fins publicitários ou comerciais de terceiros.</p>
        <h2>Máscara de Dados LGPD</h2>
        <p>Implementamos uma camada ativa de segurança no validador público de bilhetes. Qualquer visitante comum que consulte um recibo pelo código ou QR Code verá apenas as duas primeiras letras de cada nome e o início/fim do CPF, protegendo a identidade completa do comprador.</p>';
        $content = Setting::get('page_privacy_policy', $default);

        return view('pages.privacy', compact('content'));
    }

    public function terms()
    {
        $default = '<h1>Termos de Uso</h1>
        <p>Ao se cadastrar e participar das ações entre amigos da <strong>Ação RR Veículos Água Boa - MT</strong>, você declara estar de acordo com os seguintes termos e regulamentos vigentes.</p>
        <h2>Elegibilidade</h2>
        <p>Para concorrer, você deve ser maior de 18 anos ou possuir representação legal válida. O descumprimento desta cláusula acarretará na desqualificação imediata da cota e estorno legal.</p>
        <h2>Reserva e Pagamentos</h2>
        <p>A reserva de bilhetes só é confirmada mediante o recebimento da transação aprovada pelo nosso gateway Pix integrado. Reservas não pagas em até 30 minutos são canceladas sem aviso prévio.</p>
        <h2>Entrega e Transmissão</h2>
        <p>O prêmio prometido é pessoal e intransferível no ato da assinatura da transferência. A Ação Promocional respeitará os critérios de data informados no site da ação.</p>';
        $content = Setting::get('page_terms_of_use', $default);

        return view('pages.terms', compact('content'));
    }

    public function regulation()
    {
        $content = Setting::get('page_regulation', DefaultRegulationContent::html());

        return view('pages.regulation', compact('content'));
    }
}
