<?php

namespace App\Http\Controllers;

use App\Models\Draw;
use App\Models\Raffle;
use App\Services\DrawCeremonyService;
use App\Support\DrawMinutesDocument;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\View\View;

class DrawController extends Controller
{
    public function index(DrawCeremonyService $ceremony): RedirectResponse|View
    {
        $raffle = $ceremony->featuredPublicRaffle();

        if (! $raffle) {
            return view('draws.empty');
        }

        $draw = $ceremony->ensurePublicRoom($raffle);

        return redirect()->route('draws.watch', $draw->public_slug);
    }

    public function watch(string $slug, DrawCeremonyService $ceremony): View
    {
        $draw = Draw::query()
            ->with('raffle')
            ->where('public_slug', $slug)
            ->firstOrFail();

        return view('draws.watch', [
            'draw' => $draw,
            'state' => $ceremony->publicState($draw),
            'embedUrl' => $ceremony->youtubeEmbedUrl($draw->live_url),
            'firstDigitMax' => $ceremony->firstDigitMaxFor($draw->raffle, (int) $draw->digit_length),
            'maxNumber' => $ceremony->maxNumberFor($draw->raffle),
        ]);
    }

    public function state(string $slug, DrawCeremonyService $ceremony): JsonResponse
    {
        $draw = Draw::query()
            ->with('raffle')
            ->where('public_slug', $slug)
            ->firstOrFail();

        return response()->json($ceremony->publicState($draw));
    }

    public function minutes(string $slug, DrawCeremonyService $ceremony): View
    {
        $draw = Draw::query()
            ->with('raffle')
            ->where('public_slug', $slug)
            ->where('is_test', false)
            ->firstOrFail();

        abort_unless(in_array($draw->status, [Draw::STATUS_LIVE, Draw::STATUS_COMPLETED], true), 404);

        return view('draws.minutes', [
            'draw' => $draw,
            'proof' => $ceremony->verificationPayload($draw),
            'verified' => $draw->isCompleted() ? $ceremony->verifyDraw($draw) : null,
        ]);
    }

    public function minutesPdf(string $slug, DrawMinutesDocument $document): Response
    {
        $draw = Draw::query()
            ->with('raffle')
            ->where('public_slug', $slug)
            ->where('is_test', false)
            ->where('status', Draw::STATUS_COMPLETED)
            ->firstOrFail();

        $pdf = $document->toPdf($draw);

        return response($pdf, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="ata-sorteio-'.$draw->id.'.pdf"',
        ]);
    }

    public function eligible(string $slug): JsonResponse
    {
        $draw = Draw::query()
            ->where('public_slug', $slug)
            ->where('is_test', false)
            ->where('status', Draw::STATUS_COMPLETED)
            ->firstOrFail();

        return response()->json([
            'draw_id' => $draw->id,
            'eligible_count' => $draw->eligible_count,
            'eligible_hash' => $draw->eligible_hash,
            'numbers' => $draw->eligible_numbers ?? [],
        ]);
    }

    public function liveForRaffle(Raffle $raffle, DrawCeremonyService $ceremony): RedirectResponse
    {
        $draw = $ceremony->ensurePublicRoom($raffle);

        return redirect()->route('draws.watch', $draw->public_slug);
    }
}
