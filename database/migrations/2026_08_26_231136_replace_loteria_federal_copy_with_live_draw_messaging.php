<?php

use App\Models\Setting;
use App\Support\DefaultRegulationContent;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        $liveDrawFaq = '<p>O sorteio é realizado <strong>ao vivo</strong> pelo site oficial e pelo canal no YouTube, na data e horário divulgados na página da ação. O número contemplado é escolhido entre os bilhetes pagos e revelado dígito a dígito durante a transmissão.</p>';

        $faqs = (string) Setting::get('page_faqs', '');
        if ($faqs !== '' && str_contains($faqs, 'Loteria Federal')) {
            $faqs = preg_replace(
                '/<p>Nossas Ações Promocionais oficiais utilizam a extração da <strong>Loteria Federal<\/strong>.*?<\/p>/si',
                $liveDrawFaq,
                $faqs
            ) ?? $faqs;

            $faqs = str_replace(
                [
                    'utilizamos a extração oficial da <strong>Loteria Federal</strong> ou realizamos uma transmissão ao vivo de forma aleatória e auditada através de nossas redes sociais',
                    'extração da <strong>Loteria Federal</strong> ou realizamos transmissões ao vivo auditadas em nossas redes sociais',
                    'Loteria Federal',
                ],
                [
                    'realizamos o sorteio <strong>ao vivo pelo site e pelo canal no YouTube</strong>',
                    'realizamos o sorteio <strong>ao vivo pelo site e pelo canal no YouTube</strong>',
                    'sorteio ao vivo no site e YouTube',
                ],
                $faqs
            );

            Setting::set('page_faqs', $faqs);
        }

        $regulation = (string) Setting::get('page_regulation', '');
        if ($regulation === '' || str_contains($regulation, 'Loteria Federal') || str_contains($regulation, 'Extração da Loteria')) {
            Setting::set('page_regulation', DefaultRegulationContent::html());
        }
    }

    public function down(): void
    {
        // Conteúdo institucional não é revertido automaticamente.
    }
};
