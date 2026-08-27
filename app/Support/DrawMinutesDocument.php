<?php

namespace App\Support;

use App\Models\Draw;
use App\Services\DrawCeremonyService;

class DrawMinutesDocument
{
    public function __construct(private DrawCeremonyService $ceremony) {}

    /**
     * Gera PDF texto simples da ata pública do sorteio.
     */
    public function toPdf(Draw $draw): string
    {
        $draw->loadMissing(['raffle', 'winningUser']);
        $proof = $this->ceremony->verificationPayload($draw, revealSeed: true);

        $lines = [
            'ATA PUBLICA DE SORTEIO - ACAO RR VEICULOS',
            '========================================',
            'Acao: '.$this->ascii((string) $draw->raffle?->title),
            'Premio: '.$this->ascii((string) $draw->raffle?->prize_name),
            'Sorteio ID: '.$draw->id,
            'Status: '.strtoupper((string) $draw->status),
            'Inicio: '.$draw->started_at?->timezone(config('app.timezone'))->format('d/m/Y H:i:s'),
            'Conclusao: '.$draw->completed_at?->timezone(config('app.timezone'))->format('d/m/Y H:i:s'),
            '----------------------------------------',
            'Elegiveis (bilhetes pagos): '.(string) ($proof['eligible_count'] ?? 0),
            'Hash da lista: '.(string) ($proof['eligible_hash'] ?? '-'),
            'Indice selecionado: '.(string) ($proof['selection_index'] ?? '-'),
            'Numero contemplado: '.(string) ($draw->winning_number_padded ?: $draw->winning_number),
            'Seed (revelada ao final): '.(string) ($proof['draw_seed'] ?? '-'),
            '----------------------------------------',
            'Ganhador (publico): '.$this->ascii((string) ($draw->winner_snapshot['name'] ?? $draw->winningUser?->name ?? '-')),
            'Cidade/UF: '.$this->ascii(trim(($draw->winner_snapshot['address_city'] ?? '').'/'.($draw->winner_snapshot['address_state'] ?? ''), '/')),
            '----------------------------------------',
            'Como verificar:',
            '1) Recalcule SHA-256 da lista ordenada de numeros elegiveis.',
            '2) Use a seed + hash para obter o indice modulo N.',
            '3) Confira se o numero na posicao do indice e o contemplado.',
            'Consulta online: '.route('draws.minutes', $draw->public_slug),
        ];

        return $this->buildPdf(implode("\n", $lines));
    }

    private function ascii(string $value): string
    {
        $converted = @iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $value);

        return $converted !== false ? $converted : $value;
    }

    private function buildPdf(string $text): string
    {
        $escaped = str_replace(['\\', '(', ')'], ['\\\\', '\\(', '\\)'], $text);
        $content = 'BT /F1 10 Tf 40 800 Td 12 TL ('.str_replace("\n", ') Tj T* (', $escaped).') Tj ET';
        $objects = [];
        $objects[] = '<< /Type /Catalog /Pages 2 0 R >>';
        $objects[] = '<< /Type /Pages /Kids [3 0 R] /Count 1 >>';
        $objects[] = '<< /Type /Page /Parent 2 0 R /MediaBox [0 0 595 842] /Contents 4 0 R /Resources << /Font << /F1 5 0 R >> >> >>';
        $objects[] = '<< /Length '.strlen($content)." >>\nstream\n".$content."\nendstream";
        $objects[] = '<< /Type /Font /Subtype /Type1 /BaseFont /Courier >>';

        $pdf = "%PDF-1.4\n";
        $offsets = [0];
        foreach ($objects as $i => $object) {
            $offsets[] = strlen($pdf);
            $pdf .= ($i + 1)." 0 obj\n".$object."\nendobj\n";
        }
        $xref = strlen($pdf);
        $pdf .= 'xref\n0 '.count($offsets)."\n";
        $pdf .= "0000000000 65535 f \n";
        for ($i = 1; $i < count($offsets); $i++) {
            $pdf .= sprintf("%010d 00000 n \n", $offsets[$i]);
        }
        $pdf .= 'trailer << /Size '.count($offsets).' /Root 1 0 R >>'."\n";
        $pdf .= "startxref\n".$xref."\n%%EOF";

        return $pdf;
    }
}
