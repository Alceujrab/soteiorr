<?php

use Illuminate\Database\Migrations\Migration;
use App\Models\Setting;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Setting::set('page_about_us', '<h1>Sobre Nós</h1>
        <p>A <strong>Ação RR Veículos</strong>, com sede em Água Boa - MT, é uma plataforma referência em ações entre amigos, unindo transparência, credibilidade e tecnologia para realizar sonhos de norte a sul do país.</p>
        <h2>Nossa História</h2>
        <p>Fundada com a premissa de criar uma experiência de participação segura e confiável, a Ação RR Veículos já entregou dezenas de veículos de alta qualidade. Nosso foco é garantir que cada participante sinta-se seguro sabendo que as regras são claras e os resultados são auditados.</p>
        <h2>Missão, Visão e Valores</h2>
        <ul>
            <li><strong>Missão:</strong> Realizar o sonho da conquista de um carro ou moto de forma acessível e com total integridade.</li>
            <li><strong>Visão:</strong> Ser a plataforma de sorteios e ações entre amigos mais transparente e admirada do Brasil.</li>
            <li><strong>Valores:</strong> Transparência total, compromisso com a verdade, segurança de dados (LGPD) e respeito incondicional aos regulamentos de sorteios.</li>
        </ul>');

        Setting::set('page_contact', '<h1>Fale Conosco</h1>
        <p>Precisa de suporte com suas cotas ou tem alguma dúvida? Nossa central de atendimento da Ação RR Veículos Água Boa - MT está de braços abertos para ajudar.</p>
        <h2>Canais de Atendimento Oficiais</h2>
        <ul>
            <li><strong>WhatsApp Suporte:</strong> (66) 99999-9999 (Atendimento prioritário)</li>
            <li><strong>E-mail Corporativo:</strong> suporte@acaorrveiculos.com.br</li>
            <li><strong>Endereço Comercial:</strong> Avenida das Nações, 1000 - Centro, Água Boa - MT</li>
        </ul>
        <h2>Horário de Atendimento</h2>
        <p>Segunda a Sexta-feira: 08:00 às 18:00<br>Sábados: 08:00 às 12:00</p>');

        Setting::set('page_faqs', '<h1>Dúvidas Frequentes (FAQs)</h1>
        <p>Encontre respostas rápidas para as principais dúvidas de nossos participantes sobre as compras e sorteios.</p>
        
        <h2>1. Como posso comprar números da sorte?</h2>
        <p>Basta navegar pelas ações ativas em nossa página inicial, selecionar os números desejados no grid (ou utilizar a escolha automática pela "Surpresinha") e prosseguir para a tela de finalização de compra com Pix.</p>

        <h2>2. Qual o prazo máximo de pagamento do PIX?</h2>
        <p>As cotas reservadas possuem prazo de validade de <strong>30 minutos</strong>. Caso o pagamento via QR Code ou Copia e Cola não seja confirmado nesse prazo, os números retornam ao grid público para outros interessados.</p>

        <h2>3. Como é definido o ganhador do sorteio?</h2>
        <p>Nossos sorteios oficiais utilizam a extração da <strong>Loteria Federal</strong> ou realizamos transmissões ao vivo auditadas em nossas redes sociais. O número vencedor é sempre baseado na combinação correspondente e anunciado publicamente.</p>

        <h2>4. Onde posso acompanhar as minhas cotas compradas?</h2>
        <p>Ao realizar o login, acesse a aba <strong>"Meus Bilhetes"</strong> no seu painel para visualizar o histórico de compras, status e comprovantes em PDF.</p>

        <h2>5. Como funciona a entrega do veículo sorteado?</h2>
        <p>O prêmio é entregue sem custos adicionais ao ganhador (incluindo transferência) na cidade de Água Boa - MT ou enviado com frete sob nossa responsabilidade para o endereço do vencedor cadastrado.</p>');

        Setting::set('page_privacy_policy', '<h1>Política de Privacidade</h1>
        <p>Esta política descreve o compromisso da <strong>Ação RR Veículos</strong> em proteger a privacidade e os dados pessoais de seus usuários de acordo com a Lei Geral de Proteção de Dados (LGPD).</p>
        <h2>Coleta e Finalidade dos Dados</h2>
        <p>Coletamos nome completo, CPF, e-mail e telefone para identificar unicamente o participante da ação e possibilitar a entrega legítima do prêmio sorteado. Não compartilhamos informações pessoais com fins publicitários ou comerciais de terceiros.</p>
        <h2>Máscara de Dados LGPD</h2>
        <p>Implementamos uma camada activa de segurança no validador público de bilhetes. Qualquer visitante comum que consulte um recibo pelo código ou QR Code verá apenas as duas primeiras letras de cada nome e o início/fim do CPF, protegendo a identidade completa do comprador.</p>');

        Setting::set('page_terms_of_use', '<h1>Termos de Uso</h1>
        <p>Ao se cadastrar e participar das ações entre amigos da <strong>Ação RR Veículos Água Boa - MT</strong>, você declara estar de acordo com os seguintes termos e regulamentos vigentes.</p>
        <h2>Elegibilidade</h2>
        <p>Para concorrer, você deve ser maior de 18 anos ou possuir representação legal válida. O descumprimento desta cláusula acarretará na desqualificação imediata da cota e estorno legal.</p>
        <h2>Reserva e Pagamentos</h2>
        <p>A reserva de bilhetes só é confirmada mediante o recebimento da transação aprovada pelo nosso gateway Pix integrado. Reservas não pagas em até 30 minutos são canceladas sem aviso prévio.</p>
        <h2>Entrega e Transmissão</h2>
        <p>O prêmio prometido é pessoal e intransferível no ato da assinatura da transferência. O sorteio respeitará os critérios de data informados no site da ação.</p>');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
