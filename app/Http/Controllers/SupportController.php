<?php

namespace App\Http\Controllers;

use App\Actions\LogActivityAction;
use App\Models\SupportTicket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SupportController extends Controller
{
    /**
     * Exibir FAQ e formulário de suporte.
     */
    public function index()
    {
        $tickets = SupportTicket::where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->get();

        return view('support.index', compact('tickets'));
    }

    /**
     * Salvar um novo ticket de suporte.
     */
    public function storeTicket(Request $request, LogActivityAction $logActivity)
    {
        $request->validate([
            'subject' => 'required|string|max:255',
            'category' => 'required|string',
            'priority' => 'required|string',
            'message' => 'required|string',
        ]);

        $ticket = SupportTicket::create([
            'user_id' => Auth::id(),
            'subject' => $request->subject,
            'category' => $request->category,
            'priority' => $request->priority,
            'message' => $request->message,
            'status' => 'aberto',
        ]);

        $logActivity->execute("Criou um Ticket de Suporte ID: {$ticket->id}", json_encode($ticket->toArray()));

        return redirect()->route('support.index')->with('success', 'Ticket de suporte aberto com sucesso! Retornaremos o mais breve possível.');
    }
}
