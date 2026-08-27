<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

class CronController extends Controller
{
    /**
     * Dispara o scheduler Laravel (útil em hosting sem crontab via SSH).
     */
    public function run(Request $request): JsonResponse
    {
        $expected = (string) config('app.cron_token', '');
        $provided = (string) ($request->query('token') ?: $request->header('X-Cron-Token', ''));

        if ($expected === '' || ! hash_equals($expected, $provided)) {
            abort(401, 'Cron não autorizado.');
        }

        Artisan::call('schedule:run');

        return response()->json([
            'success' => true,
            'output' => trim(Artisan::output()),
        ]);
    }
}
