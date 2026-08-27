<?php

namespace App\Http\Middleware;

use App\Actions\CaptureAffiliateReferralAction;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CaptureAffiliateReferral
{
    public function __construct(private CaptureAffiliateReferralAction $capture) {}

    /**
     * @param  Closure(Request): Response  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $code = $request->query('ref') ?? $request->query('afiliado');

        if (filled($code)) {
            $this->capture->execute((string) $code);
        }

        return $next($request);
    }
}
