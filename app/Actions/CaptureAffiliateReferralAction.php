<?php

namespace App\Actions;

use App\Models\User;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;

class CaptureAffiliateReferralAction
{
    public const SESSION_KEY = 'affiliate_ref';

    public function execute(string $code): ?User
    {
        $code = Str::upper(trim($code));

        if ($code === '') {
            return null;
        }

        $affiliate = User::query()
            ->where('affiliate_code', $code)
            ->whereIn('role', ['vendedor', 'admin_organizador', 'super_admin', 'cliente'])
            ->first();

        if (! $affiliate) {
            return null;
        }

        Session::put(self::SESSION_KEY, $affiliate->id);

        return $affiliate;
    }

    public function currentAffiliateId(?int $buyerId = null): ?int
    {
        $affiliateId = Session::get(self::SESSION_KEY);

        if (! $affiliateId) {
            return null;
        }

        if ($buyerId && (int) $affiliateId === (int) $buyerId) {
            return null;
        }

        return (int) $affiliateId;
    }
}
