<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class AffiliateController extends Controller
{
    public function index(): View
    {
        $commissionRate = (float) Setting::get('affiliate_commission_percent', '5');

        $affiliates = User::query()
            ->whereNotNull('affiliate_code')
            ->withCount([
                'referredPayments as referred_count',
                'referredPayments as approved_count' => fn ($q) => $q->where('status', 'approved'),
            ])
            ->withSum([
                'referredPayments as approved_volume' => fn ($q) => $q->where('status', 'approved'),
            ], 'amount')
            ->orderByDesc('approved_volume')
            ->get()
            ->map(function (User $user) use ($commissionRate) {
                $volume = (float) ($user->approved_volume ?? 0);
                $user->estimated_commission = round($volume * ($commissionRate / 100), 2);

                return $user;
            });

        $candidates = User::query()
            ->whereNull('affiliate_code')
            ->whereIn('role', ['vendedor', 'cliente', 'admin_organizador'])
            ->orderBy('name')
            ->limit(50)
            ->get();

        return view('admin.affiliates.index', compact('affiliates', 'candidates', 'commissionRate'));
    }

    public function ensureCode(User $user): RedirectResponse
    {
        $code = $user->ensureAffiliateCode();

        return redirect()
            ->route('admin.affiliates')
            ->with('success', "Código gerado para {$user->name}: {$code}");
    }
}
