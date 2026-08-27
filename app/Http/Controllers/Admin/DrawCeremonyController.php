<?php

namespace App\Http\Controllers\Admin;

use App\Actions\LogActivityAction;
use App\Http\Controllers\Controller;
use App\Models\Draw;
use App\Models\Raffle;
use App\Services\DrawCeremonyService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DrawCeremonyController extends Controller
{
    public function index(): View
    {
        $raffles = Raffle::query()
            ->withCount([
                'tickets as paid_tickets_count' => fn ($q) => $q->where('status', 'paid'),
            ])
            ->with(['draw' => fn ($q) => $q->where('is_test', false)->latest()])
            ->orderByDesc('id')
            ->get();

        $liveDraws = Draw::query()
            ->with('raffle')
            ->where('is_test', false)
            ->whereIn('status', [Draw::STATUS_PENDING, Draw::STATUS_LIVE, Draw::STATUS_COMPLETED])
            ->latest()
            ->limit(10)
            ->get();

        return view('admin.draws.index', compact('raffles', 'liveDraws'));
    }

    public function show(Raffle $raffle, DrawCeremonyService $ceremony): View
    {
        $draw = $ceremony->ensurePublicRoom($raffle);
        $paidCount = $raffle->tickets()->where('status', 'paid')->count();

        return view('admin.draws.room', [
            'raffle' => $raffle,
            'draw' => $draw,
            'paidCount' => $paidCount,
            'firstDigitMax' => $ceremony->firstDigitMaxFor($raffle),
            'maxNumber' => $ceremony->maxNumberFor($raffle),
        ]);
    }

    public function start(Request $request, Raffle $raffle, DrawCeremonyService $ceremony, LogActivityAction $logActivity): RedirectResponse
    {
        $validated = $request->validate([
            'live_url' => ['nullable', 'url', 'max:500'],
        ]);

        $draw = $ceremony->startOfficial($raffle, $validated['live_url'] ?? null);

        $logActivity->execute(
            "Iniciou cerimônia de sorteio da ação #{$raffle->id}",
            json_encode(['draw_id' => $draw->id, 'is_test' => false])
        );

        return redirect()
            ->route('admin.draws.room', $raffle)
            ->with('success', 'Sorteio iniciado. Clique em Sortear números quando estiver pronto.');
    }

    public function reveal(Draw $draw, DrawCeremonyService $ceremony, LogActivityAction $logActivity): JsonResponse|RedirectResponse
    {
        abort_if($draw->is_test === false && $draw->status === Draw::STATUS_CANCELLED, 404);

        $draw = $ceremony->revealNextDigit($draw);

        if ($draw->isCompleted()) {
            $logActivity->execute(
                "Finalizou sorteio #{$draw->id} — número {$draw->winning_number_padded}",
                json_encode(['draw_id' => $draw->id, 'is_test' => $draw->is_test])
            );
        }

        if (request()->wantsJson()) {
            return response()->json($ceremony->adminState($draw));
        }

        return redirect()
            ->route($draw->is_test ? 'admin.draws.test' : 'admin.draws.room', $draw->is_test ? [] : [$draw->raffle_id])
            ->with('success', $draw->isCompleted() ? 'Sorteio concluído!' : 'Dígito revelado.');
    }

    public function startAutoReveal(Draw $draw, DrawCeremonyService $ceremony, LogActivityAction $logActivity): JsonResponse
    {
        abort_if($draw->status === Draw::STATUS_CANCELLED, 404);

        $draw = $ceremony->startAutoReveal($draw);

        $logActivity->execute(
            "Iniciou revelação automática do sorteio #{$draw->id}",
            json_encode(['draw_id' => $draw->id, 'is_test' => $draw->is_test])
        );

        return response()->json($ceremony->adminState($draw));
    }

    public function cancel(Draw $draw, DrawCeremonyService $ceremony, LogActivityAction $logActivity): RedirectResponse
    {
        abort_if($draw->status === Draw::STATUS_CANCELLED, 404);

        $wasTest = $draw->is_test;
        $raffleId = $draw->raffle_id;

        $draw = $ceremony->cancelCeremony($draw);

        $logActivity->execute(
            "Cancelou sorteio #{$draw->id}".($wasTest ? ' (teste)' : ''),
            json_encode(['draw_id' => $draw->id, 'is_test' => $wasTest])
        );

        if ($wasTest) {
            return redirect()
                ->route('admin.draws.test')
                ->with('success', 'Sorteio de teste cancelado. Você pode iniciar outra simulação.');
        }

        return redirect()
            ->route('admin.draws.room', $raffleId)
            ->with('success', 'Sorteio cancelado. A página pública voltou ao modo de espera e você pode iniciar de novo.');
    }

    public function state(Draw $draw, DrawCeremonyService $ceremony): JsonResponse
    {
        return response()->json($ceremony->adminState($draw));
    }

    public function updateChecklist(Request $request, Draw $draw, DrawCeremonyService $ceremony, LogActivityAction $logActivity): RedirectResponse
    {
        abort_unless($draw->isCompleted() && ! $draw->is_test, 404);

        $request->validate([
            'checklist' => ['required', 'array'],
        ]);

        $checklist = [];
        foreach (Draw::checklistKeys() as $key) {
            $checklist[$key] = $request->boolean('checklist.'.$key);
        }

        $ceremony->updateOpsChecklist($draw, $checklist);

        $logActivity->execute(
            "Atualizou checklist pós-sorteio #{$draw->id}",
            json_encode($checklist)
        );

        return redirect()
            ->route('admin.draws.room', $draw->raffle_id)
            ->with('success', 'Checklist operacional atualizado.');
    }

    public function test(DrawCeremonyService $ceremony): View
    {
        $draw = Draw::query()
            ->where('is_test', true)
            ->whereIn('status', [Draw::STATUS_LIVE, Draw::STATUS_COMPLETED])
            ->latest()
            ->first();

        $raffle = $draw?->raffle
            ?? Raffle::query()->where('status', 'active')->orderByDesc('id')->first()
            ?? Raffle::query()->orderByDesc('id')->first();

        return view('admin.draws.test', [
            'draw' => $draw,
            'state' => $draw ? $ceremony->adminState($draw) : null,
            'raffle' => $raffle,
            'firstDigitMax' => $ceremony->firstDigitMaxFor($raffle),
            'maxNumber' => $ceremony->maxNumberFor($raffle),
        ]);
    }

    public function startTest(Request $request, DrawCeremonyService $ceremony, LogActivityAction $logActivity): RedirectResponse
    {
        $raffle = Raffle::query()->where('status', 'active')->orderByDesc('id')->first()
            ?? Raffle::query()->orderByDesc('id')->first();

        $maxNumber = $ceremony->maxNumberFor($raffle);

        $validated = $request->validate([
            'live_url' => ['nullable', 'url', 'max:500'],
            'forced_number' => ['nullable', 'integer', 'min:1', 'max:'.$maxNumber],
        ]);

        Draw::query()
            ->where('is_test', true)
            ->where('status', Draw::STATUS_LIVE)
            ->update(['status' => Draw::STATUS_CANCELLED]);

        $draw = $ceremony->startTest(
            $validated['live_url'] ?? null,
            $validated['forced_number'] ?? null,
            $raffle,
        );

        $logActivity->execute(
            "Iniciou sorteio de TESTE #{$draw->id}",
            json_encode(['draw_id' => $draw->id])
        );

        return redirect()
            ->route('admin.draws.test')
            ->with('success', 'Sorteio de teste iniciado. Nenhum bilhete real será afetado.');
    }

    public function revealTest(Request $request, DrawCeremonyService $ceremony, LogActivityAction $logActivity): JsonResponse|RedirectResponse
    {
        $draw = Draw::query()
            ->where('is_test', true)
            ->where('status', Draw::STATUS_LIVE)
            ->latest()
            ->firstOrFail();

        return $this->reveal($draw, $ceremony, $logActivity);
    }
}
