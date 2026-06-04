<?php

namespace App\Actions;

use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class LogActivityAction
{
    /**
     * Registrar uma ação de auditoria no banco de dados.
     */
    public function execute(string $action, ?string $payload = null)
    {
        ActivityLog::create([
            'user_id' => Auth::id(),
            'action' => $action,
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
            'payload' => $payload,
        ]);
    }
}
