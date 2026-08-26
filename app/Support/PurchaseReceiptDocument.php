<?php

namespace App\Support;

use App\Models\Payment;

class PurchaseReceiptDocument
{
    /**
     * Gera um PDF texto simples (sem dependências) do comprovante.
     */
    public function toPdf(Payment $payment): string
    {
        $payment->loadMissing(['user', 'tickets.raffle', 'package']);

        $lines = [
            'COMPROVANTE DE COMPRA - ACAO RR VEICULOS',
            '----------------------------------------',
            'Recibo: '.($payment->gateway_transaction_id ?: 'ID-'.$payment->id),
            'Status: '.strtoupper((string) $payment->status),
            'Data: '.$payment->created_at?->format('d/m/Y H:i'),
            'Cliente: '.$this->ascii($payment->user->name),
            'CPF: '.$payment->user->cpf,
            'E-mail: '.$payment->user->email,
            'Valor: R$ '.number_format((float) $payment->amount, 2, ',', '.'),
            'Metodo: '.strtoupper((string) $payment->payment_method),
            'Gateway: '.strtoupper((string) $payment->gateway),
        ];

        if ($payment->package) {
            $lines[] = 'Pacote: '.$this->ascii($payment->package->name);
        }

        $raffleTitle = optional($payment->tickets->first()?->raffle)->title;
        if ($raffleTitle) {
            $lines[] = 'Acao: '.$this->ascii($raffleTitle);
        }

        $numbers = $payment->tickets->pluck('number')->sort()->values()->implode(', ');
        if ($numbers !== '') {
            $lines[] = 'Numeros: '.$numbers;
        }

        $lines[] = '----------------------------------------';
        $lines[] = 'Consulte online: '.route('payments.receipt', $payment);

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
        $content = 'BT /F1 11 Tf 40 780 Td 14 TL ('.str_replace("\n", ') Tj T* (', $escaped).') Tj ET';
        $length = strlen($content);

        return "%PDF-1.4\n".
            "1 0 obj<< /Type /Catalog /Pages 2 0 R >>endobj\n".
            "2 0 obj<< /Type /Pages /Kids [3 0 R] /Count 1 >>endobj\n".
            "3 0 obj<< /Type /Page /Parent 2 0 R /MediaBox [0 0 595 842] /Contents 4 0 R /Resources<< /Font<< /F1 5 0 R >> >> >>endobj\n".
            "4 0 obj<< /Length {$length} >>stream\n{$content}\nendstream\nendobj\n".
            "5 0 obj<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica >>endobj\n".
            "xref\n0 6\n0000000000 65535 f \n".
            "trailer<< /Size 6 /Root 1 0 R >>\nstartxref\n0\n%%EOF";
    }
}
