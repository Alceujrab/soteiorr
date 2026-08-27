<?php

namespace App\Support;

use App\Models\Setting;

class ContactInfo
{
    /**
     * @return array{
     *     whatsapp: string,
     *     whatsapp_digits: string,
     *     email: string,
     *     address: string,
     *     city: string,
     *     hours_weekdays: string,
     *     hours_saturday: string,
     *     map_query: string
     * }
     */
    public static function all(): array
    {
        $whatsapp = trim((string) Setting::get('contact_whatsapp', ''));
        $digits = preg_replace('/\D+/', '', $whatsapp) ?? '';

        $email = trim((string) Setting::get('contact_email', 'contato@rrsorteio.com'));
        $address = trim((string) Setting::get('contact_address', 'Água Boa - MT'));
        $city = trim((string) Setting::get('contact_city', 'Água Boa - MT'));
        $hoursWeekdays = trim((string) Setting::get('contact_hours_weekdays', 'Segunda a Sexta, 08h às 18h'));
        $hoursSaturday = trim((string) Setting::get('contact_hours_saturday', 'Sábados, 08h às 12h'));

        $mapQuery = trim($address) !== '' ? $address : $city;

        $whatsappLink = null;
        if (strlen($digits) >= 10) {
            $international = str_starts_with($digits, '55') && strlen($digits) >= 12
                ? $digits
                : '55'.$digits;
            $whatsappLink = 'https://wa.me/'.$international;
        }

        return [
            'whatsapp' => $whatsapp,
            'whatsapp_digits' => $digits,
            'whatsapp_url' => $whatsappLink,
            'email' => $email,
            'address' => $address,
            'city' => $city,
            'hours_weekdays' => $hoursWeekdays,
            'hours_saturday' => $hoursSaturday,
            'map_query' => $mapQuery,
        ];
    }

    public static function whatsappUrl(?string $prefilledMessage = null): ?string
    {
        $base = self::all()['whatsapp_url'] ?? null;
        if (! $base) {
            return null;
        }

        if (blank($prefilledMessage)) {
            return $base;
        }

        return $base.'?text='.rawurlencode($prefilledMessage);
    }
}
