<?php

namespace App\Services;

use App\Models\Draw;
use App\Models\Raffle;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use RuntimeException;

class DrawCeremonyService
{
    public const DIGIT_LENGTH = 6;

    public const MIN_REVEAL_SECONDS = 5;

    public const REVEAL_INTERVAL_SECONDS = 5;

    public function padNumber(int $number, int $length = self::DIGIT_LENGTH): string
    {
        return str_pad((string) $number, $length, '0', STR_PAD_LEFT);
    }

    public function maxNumberFor(?Raffle $raffle): int
    {
        return max(1, (int) ($raffle?->total_numbers ?: 1));
    }

    public function firstDigitMaxFor(?Raffle $raffle, int $length = self::DIGIT_LENGTH): int
    {
        $max = $this->maxNumberFor($raffle);
        $divisor = 10 ** max(0, $length - 1);

        return min(9, intdiv($max, $divisor));
    }

    public function normalizeDrawNumber(int $number, ?Raffle $raffle = null): int
    {
        $max = $this->maxNumberFor($raffle);

        return max(1, min($max, abs($number)));
    }

    public function youtubeEmbedUrl(?string $url): ?string
    {
        if (! filled($url)) {
            return null;
        }

        $url = trim($url);

        if (preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?|live)/|watch\?v=)|youtu\.be/)([^"&?/ ]{11})%i', $url, $match)) {
            return 'https://www.youtube.com/embed/'.$match[1].'?autoplay=1&rel=0';
        }

        if (str_contains($url, 'youtube.com/embed/')) {
            return $url;
        }

        return $url;
    }

    /**
     * @return array{full: array<string, mixed>, public: array<string, mixed>}
     */
    public function buildWinnerPayload(Ticket $ticket, User $user): array
    {
        $addressParts = array_filter([
            $user->address_street,
            $user->address_number,
            $user->address_neighborhood,
            $user->address_city,
            $user->address_state,
        ]);
        $addressFull = implode(', ', $addressParts);
        $phone = $user->whatsapp ?: $user->phone;
        $purchasedAt = $ticket->created_at;

        $full = [
            'name' => $user->name,
            'email' => $user->email,
            'cpf' => $user->cpf,
            'phone' => $phone,
            'whatsapp' => $user->whatsapp,
            'address' => $addressFull,
            'address_street' => $user->address_street,
            'address_number' => $user->address_number,
            'address_neighborhood' => $user->address_neighborhood,
            'address_city' => $user->address_city,
            'address_state' => $user->address_state,
            'zip_code' => $user->zip_code,
            'number' => $ticket->number,
            'number_padded' => $this->padNumber((int) $ticket->number),
            'purchased_at' => $purchasedAt?->timezone(config('app.timezone'))->format('d/m/Y H:i:s'),
            'purchased_at_iso' => $purchasedAt?->toIso8601String(),
            'ticket_id' => $ticket->id,
            'user_id' => $user->id,
        ];

        $public = [
            'name' => $user->name,
            'number' => $ticket->number,
            'number_padded' => $this->padNumber((int) $ticket->number),
            'purchased_at' => $full['purchased_at'],
            'phone' => $this->maskPhone((string) ($phone ?? '')),
            'address' => $this->maskAddress($addressFull, (string) ($user->address_city ?? '')),
            'city' => $user->address_city,
            'state' => $user->address_state,
        ];

        return compact('full', 'public');
    }

    /**
     * @return array{full: array<string, mixed>, public: array<string, mixed>}
     */
    public function buildFakeWinnerPayload(int $number): array
    {
        $ticket = new Ticket(['number' => $number, 'id' => 0]);
        $ticket->created_at = now()->subHours(random_int(2, 72));

        $user = new User([
            'id' => 0,
            'name' => 'Participante Demonstração Silva',
            'email' => 'demo.sorteio@rrsorteio.com',
            'cpf' => '39053344705',
            'phone' => '66999887766',
            'whatsapp' => '66999887766',
            'address_street' => 'Avenida das Nações',
            'address_number' => '1000',
            'address_neighborhood' => 'Centro',
            'address_city' => 'Água Boa',
            'address_state' => 'MT',
            'zip_code' => '78635000',
        ]);

        return $this->buildWinnerPayload($ticket, $user);
    }

    public function maskPhone(string $phone): string
    {
        $digits = preg_replace('/\D+/', '', $phone) ?? '';

        if (strlen($digits) < 8) {
            return '(**) *****-****';
        }

        $ddd = substr($digits, 0, 2);
        $last = substr($digits, -2);

        return sprintf('(%s) 9****-**%s', $ddd, $last);
    }

    public function maskAddress(string $address, string $city = ''): string
    {
        $address = trim($address);

        if ($address === '') {
            return $city !== '' ? substr($city, 0, 3).'***' : '***';
        }

        $prefix = mb_substr($address, 0, 4);
        $cityBit = $city !== '' ? ' · '.mb_substr($city, 0, 3).'***' : '';

        return $prefix.'***'.$cityBit;
    }

    public function publicSlugFor(Raffle $raffle): string
    {
        return 'acao-'.$raffle->id;
    }

    /**
     * Garante uma sala pública permanente para a ação (antes, durante e depois do sorteio).
     */
    public function ensurePublicRoom(Raffle $raffle): Draw
    {
        $draw = Draw::query()
            ->where('raffle_id', $raffle->id)
            ->where('is_test', false)
            ->whereIn('status', [Draw::STATUS_PENDING, Draw::STATUS_LIVE, Draw::STATUS_COMPLETED])
            ->latest('id')
            ->first();

        if ($draw) {
            $updates = [];

            if (! filled($draw->public_slug)) {
                $updates['public_slug'] = $this->uniquePublicSlug($raffle);
            }

            if ($draw->status === Draw::STATUS_PENDING && ! filled($draw->live_url)) {
                $updates['live_url'] = $raffle->live_url ?: $raffle->youtube_url;
            }

            if ($updates !== []) {
                $draw->forceFill($updates)->save();
            }

            return $draw->fresh(['raffle']);
        }

        return Draw::create([
            'raffle_id' => $raffle->id,
            'status' => Draw::STATUS_PENDING,
            'is_test' => false,
            'digit_length' => self::DIGIT_LENGTH,
            'revealed_digits' => 0,
            'live_url' => $raffle->live_url ?: $raffle->youtube_url,
            'public_slug' => $this->uniquePublicSlug($raffle),
        ])->fresh(['raffle']);
    }

    public function featuredPublicRaffle(): ?Raffle
    {
        $liveDraw = Draw::query()
            ->with('raffle')
            ->where('is_test', false)
            ->where('status', Draw::STATUS_LIVE)
            ->latest('id')
            ->first();

        if ($liveDraw?->raffle) {
            return $liveDraw->raffle;
        }

        $active = Raffle::query()
            ->where('status', 'active')
            ->orderByDesc('id')
            ->first();

        if ($active) {
            return $active;
        }

        return Raffle::query()->orderByDesc('id')->first();
    }

    public function startOfficial(Raffle $raffle, ?string $liveUrl = null): Draw
    {
        if ($raffle->status === 'completed') {
            throw ValidationException::withMessages([
                'raffle' => 'Esta ação promocional já foi encerrada.',
            ]);
        }

        $alreadyLive = Draw::query()
            ->where('raffle_id', $raffle->id)
            ->where('is_test', false)
            ->where('status', Draw::STATUS_LIVE)
            ->exists();

        if ($alreadyLive) {
            throw ValidationException::withMessages([
                'raffle' => 'Já existe um sorteio em andamento para esta ação.',
            ]);
        }

        $paidTickets = Ticket::query()
            ->where('raffle_id', $raffle->id)
            ->where('status', 'paid')
            ->with('user')
            ->orderBy('number')
            ->get();

        if ($paidTickets->isEmpty()) {
            throw ValidationException::withMessages([
                'raffle' => 'Não há bilhetes pagos para realizar o sorteio.',
            ]);
        }

        $eligibleNumbers = $paidTickets->pluck('number')->map(fn ($n) => (int) $n)->values()->all();
        $selection = $this->selectWinnerFromEligible($eligibleNumbers);
        /** @var Ticket $winnerTicket */
        $winnerTicket = $paidTickets->firstWhere('number', $selection['winning_number']);

        if (! $winnerTicket) {
            throw new RuntimeException('Falha ao localizar o bilhete contemplado pela prova do sorteio.');
        }

        $user = $winnerTicket->user;

        if (! $user) {
            throw new RuntimeException('Bilhete vencedor sem usuário associado.');
        }

        $payload = $this->buildWinnerPayload($winnerTicket, $user);
        $padded = $this->padNumber((int) $winnerTicket->number);

        return DB::transaction(function () use ($raffle, $winnerTicket, $user, $payload, $padded, $liveUrl, $selection, $eligibleNumbers) {
            $pending = Draw::query()
                ->where('raffle_id', $raffle->id)
                ->where('is_test', false)
                ->where('status', Draw::STATUS_PENDING)
                ->lockForUpdate()
                ->latest('id')
                ->first();

            $attributes = [
                'raffle_id' => $raffle->id,
                'status' => Draw::STATUS_LIVE,
                'is_test' => false,
                'digit_length' => self::DIGIT_LENGTH,
                'revealed_digits' => 0,
                'winning_number' => $winnerTicket->number,
                'winning_number_padded' => $padded,
                'winning_ticket_id' => $winnerTicket->id,
                'winning_user_id' => $user->id,
                'live_url' => $liveUrl ?: $raffle->live_url ?: $raffle->youtube_url,
                'winner_snapshot' => $payload['full'],
                'draw_seed' => $selection['draw_seed'],
                'eligible_hash' => $selection['eligible_hash'],
                'eligible_count' => $selection['eligible_count'],
                'selection_index' => $selection['selection_index'],
                'eligible_numbers' => $eligibleNumbers,
                'ops_checklist' => $this->defaultChecklist(),
                'started_at' => now(),
                'completed_at' => null,
                'last_reveal_at' => null,
                'auto_reveal_started_at' => null,
                'public_slug' => $pending?->public_slug ?: $this->uniquePublicSlug($raffle),
                'drawn_at' => now(),
            ];

            if ($pending) {
                $pending->forceFill($attributes)->save();

                return $pending->fresh(['raffle', 'winningUser', 'winningTicket']);
            }

            return Draw::create($attributes)->fresh(['raffle', 'winningUser', 'winningTicket']);
        });
    }

    /**
     * Seleção determinística e auditável a partir da lista de elegíveis.
     *
     * @param  list<int>  $eligibleNumbers  Números já ordenados
     * @return array{draw_seed: string, eligible_hash: string, eligible_count: int, selection_index: int, winning_number: int}
     */
    public function selectWinnerFromEligible(array $eligibleNumbers, ?string $seed = null): array
    {
        $eligibleNumbers = array_values(array_map('intval', $eligibleNumbers));
        sort($eligibleNumbers);

        if ($eligibleNumbers === []) {
            throw new RuntimeException('Lista de elegíveis vazia.');
        }

        $seed = $seed ?: bin2hex(random_bytes(32));
        $eligibleHash = hash('sha256', implode(',', $eligibleNumbers));
        $digest = hash('sha256', $seed.'|'.$eligibleHash);
        $index = hexdec(substr($digest, 0, 8)) % count($eligibleNumbers);

        return [
            'draw_seed' => $seed,
            'eligible_hash' => $eligibleHash,
            'eligible_count' => count($eligibleNumbers),
            'selection_index' => $index,
            'winning_number' => $eligibleNumbers[$index],
        ];
    }

    /**
     * @return array<string, bool>
     */
    public function defaultChecklist(): array
    {
        return array_fill_keys(Draw::checklistKeys(), false);
    }

    /**
     * @param  array<string, bool>  $checklist
     * @return array<string, bool>
     */
    public function updateOpsChecklist(Draw $draw, array $checklist): array
    {
        $normalized = $this->defaultChecklist();

        foreach (Draw::checklistKeys() as $key) {
            if (array_key_exists($key, $checklist)) {
                $normalized[$key] = (bool) $checklist[$key];
            } else {
                $normalized[$key] = (bool) (($draw->ops_checklist[$key] ?? false));
            }
        }

        $draw->forceFill(['ops_checklist' => $normalized])->save();

        return $normalized;
    }

    /**
     * Dados públicos de transparência. A seed só é revelada após a conclusão.
     *
     * @return array<string, mixed>
     */
    public function verificationPayload(Draw $draw, bool $revealSeed = false): array
    {
        $revealSeed = $revealSeed || $draw->isCompleted();

        return [
            'eligible_count' => $draw->eligible_count,
            'eligible_hash' => $draw->eligible_hash,
            'selection_index' => $revealSeed ? $draw->selection_index : null,
            'draw_seed' => $revealSeed ? $draw->draw_seed : null,
            'winning_number' => $draw->isCompleted() ? $draw->winning_number : null,
            'winning_number_padded' => $draw->isCompleted() ? $draw->winning_number_padded : null,
            'algorithm' => 'sha256(seed|eligible_hash)[:8] % N',
            'seed_revealed' => $revealSeed && filled($draw->draw_seed),
            'minutes_url' => $draw->public_slug ? route('draws.minutes', $draw->public_slug) : null,
            'minutes_pdf_url' => $draw->public_slug ? route('draws.minutes.pdf', $draw->public_slug) : null,
            'eligible_numbers_url' => ($revealSeed && $draw->public_slug)
                ? route('draws.eligible', $draw->public_slug)
                : null,
        ];
    }

    /**
     * Recalcula o índice e confere se bate com o número contemplado.
     */
    public function verifyDraw(Draw $draw): bool
    {
        if (! filled($draw->draw_seed) || ! is_array($draw->eligible_numbers) || $draw->eligible_numbers === []) {
            return false;
        }

        $selection = $this->selectWinnerFromEligible($draw->eligible_numbers, $draw->draw_seed);

        return $selection['eligible_hash'] === $draw->eligible_hash
            && $selection['selection_index'] === $draw->selection_index
            && $selection['winning_number'] === (int) $draw->winning_number;
    }

    private function uniquePublicSlug(Raffle $raffle): string
    {
        $base = $this->publicSlugFor($raffle);

        if (! Draw::query()->where('public_slug', $base)->exists()) {
            return $base;
        }

        do {
            $slug = $base.'-'.Str::lower(Str::random(4));
        } while (Draw::query()->where('public_slug', $slug)->exists());

        return $slug;
    }

    public function startTest(?string $liveUrl = null, ?int $forcedNumber = null, ?Raffle $raffle = null): Draw
    {
        $raffleId = $raffle?->id ?? Raffle::query()->value('id') ?? $this->ensureDemoRaffleId();
        $raffleModel = $raffle ?? Raffle::query()->find($raffleId);
        $maxNumber = $this->maxNumberFor($raffleModel);

        $number = $forcedNumber !== null
            ? $this->normalizeDrawNumber($forcedNumber, $raffleModel)
            : random_int(1, $maxNumber);
        $padded = $this->padNumber($number);
        $payload = $this->buildFakeWinnerPayload($number);

        return Draw::create([
            'raffle_id' => $raffleId,
            'status' => Draw::STATUS_LIVE,
            'is_test' => true,
            'digit_length' => self::DIGIT_LENGTH,
            'revealed_digits' => 0,
            'winning_number' => $number,
            'winning_number_padded' => $padded,
            'winning_ticket_id' => null,
            'winning_user_id' => null,
            'live_url' => $liveUrl,
            'winner_snapshot' => $payload['full'],
            'started_at' => now(),
            'auto_reveal_started_at' => null,
            'public_slug' => 'teste-'.Str::lower(Str::random(10)),
            'drawn_at' => now(),
        ]);
    }

    public function startAutoReveal(Draw $draw): Draw
    {
        if ($draw->status !== Draw::STATUS_LIVE) {
            throw ValidationException::withMessages([
                'draw' => 'Este sorteio não está ao vivo.',
            ]);
        }

        if ($draw->allDigitsRevealed()) {
            throw ValidationException::withMessages([
                'draw' => 'Todos os dígitos já foram revelados.',
            ]);
        }

        if ($draw->auto_reveal_started_at) {
            return $draw->fresh(['raffle', 'winningUser', 'winningTicket']);
        }

        $draw->forceFill([
            'auto_reveal_started_at' => now(),
        ])->save();

        return $draw->fresh(['raffle', 'winningUser', 'winningTicket']);
    }

    public function cancelCeremony(Draw $draw): Draw
    {
        if ($draw->status === Draw::STATUS_CANCELLED) {
            return $draw->fresh(['raffle']);
        }

        return DB::transaction(function () use ($draw) {
            $raffle = $draw->raffle()->lockForUpdate()->first();

            if (! $draw->is_test && $raffle && $raffle->status === 'completed') {
                $raffle->update(['status' => 'active']);
            }

            if ($draw->is_test) {
                $draw->forceFill([
                    'status' => Draw::STATUS_CANCELLED,
                    'auto_reveal_started_at' => null,
                    'completed_at' => $draw->completed_at ?: now(),
                ])->save();

                return $draw->fresh(['raffle']);
            }

            $draw->forceFill([
                'status' => Draw::STATUS_PENDING,
                'revealed_digits' => 0,
                'winning_number' => null,
                'winning_number_padded' => null,
                'winning_ticket_id' => null,
                'winning_user_id' => null,
                'winner_snapshot' => null,
                'draw_seed' => null,
                'eligible_hash' => null,
                'eligible_count' => null,
                'selection_index' => null,
                'eligible_numbers' => null,
                'ops_checklist' => null,
                'started_at' => null,
                'completed_at' => null,
                'last_reveal_at' => null,
                'auto_reveal_started_at' => null,
                'drawn_at' => null,
            ])->save();

            return $draw->fresh(['raffle']);
        });
    }

    public function revealNextDigit(Draw $draw): Draw
    {
        if ($draw->status !== Draw::STATUS_LIVE) {
            throw ValidationException::withMessages([
                'draw' => 'Este sorteio não está ao vivo.',
            ]);
        }

        if ($draw->allDigitsRevealed()) {
            throw ValidationException::withMessages([
                'draw' => 'Todos os dígitos já foram revelados.',
            ]);
        }

        if ($draw->last_reveal_at && $draw->last_reveal_at->gt(now()->subSeconds(self::MIN_REVEAL_SECONDS))) {
            throw ValidationException::withMessages([
                'draw' => 'Aguarde a animação do dígito anterior terminar.',
            ]);
        }

        return DB::transaction(function () use ($draw) {
            $draw->revealed_digits++;
            $draw->last_reveal_at = now();

            if ($draw->revealed_digits >= $draw->digit_length) {
                $draw->status = Draw::STATUS_COMPLETED;
                $draw->completed_at = now();
                $draw->drawn_at = now();

                if (! $draw->is_test && $draw->raffle) {
                    $draw->raffle->update(['status' => 'completed']);
                }
            }

            $draw->save();

            return $draw->fresh(['raffle', 'winningUser', 'winningTicket']);
        });
    }

    /**
     * @return array<string, mixed>
     */
    public function publicState(Draw $draw): array
    {
        $snapshot = $draw->winner_snapshot ?? [];
        $publicWinner = null;

        if ($draw->isCompleted()) {
            $publicWinner = [
                'name' => $snapshot['name'] ?? null,
                'number' => $snapshot['number'] ?? $draw->winning_number,
                'number_padded' => $snapshot['number_padded'] ?? $draw->winning_number_padded,
                'purchased_at' => $snapshot['purchased_at'] ?? null,
                'phone' => $this->maskPhone((string) ($snapshot['phone'] ?? $snapshot['whatsapp'] ?? '')),
                'address' => $this->maskAddress(
                    (string) ($snapshot['address'] ?? ''),
                    (string) ($snapshot['address_city'] ?? '')
                ),
                'city' => $snapshot['address_city'] ?? null,
                'state' => $snapshot['address_state'] ?? null,
            ];
        }

        return [
            'id' => $draw->id,
            'status' => $draw->status,
            'status_label' => match ($draw->status) {
                Draw::STATUS_PENDING => 'Aguardando início',
                Draw::STATUS_LIVE => $draw->auto_reveal_started_at ? 'Sorteando números' : 'Ao vivo',
                Draw::STATUS_COMPLETED => 'Finalizado',
                Draw::STATUS_CANCELLED => 'Cancelado',
                default => $draw->status,
            },
            'is_test' => $draw->is_test,
            'digit_length' => $draw->digit_length,
            'revealed_digits' => $draw->revealed_digits,
            'revealed_prefix' => $draw->revealedPrefix(),
            'winning_number_padded' => $draw->isCompleted() ? $draw->winning_number_padded : null,
            'target_digits' => $this->visibleTargetDigits($draw),
            'youtube_embed' => $this->youtubeEmbedUrl($draw->live_url),
            'live_url' => $draw->live_url,
            'auto_running' => $draw->status === Draw::STATUS_LIVE && $draw->auto_reveal_started_at !== null,
            'reveal_interval_seconds' => self::REVEAL_INTERVAL_SECONDS,
            'max_number' => $this->maxNumberFor($draw->raffle),
            'first_digit_max' => $this->firstDigitMaxFor($draw->raffle, (int) $draw->digit_length),
            'raffle' => [
                'id' => $draw->raffle_id,
                'title' => $draw->raffle?->title,
                'prize_name' => $draw->raffle?->prize_name,
                'total_numbers' => $draw->raffle?->total_numbers,
            ],
            'winner' => $publicWinner,
            'proof' => $this->verificationPayload($draw),
            'started_at' => $draw->started_at?->toIso8601String(),
            'completed_at' => $draw->completed_at?->toIso8601String(),
            'auto_reveal_started_at' => $draw->auto_reveal_started_at?->toIso8601String(),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function adminState(Draw $draw): array
    {
        $state = $this->publicState($draw);
        $snapshot = $draw->winner_snapshot ?? [];

        $state['winning_number'] = $draw->winning_number;
        $state['winning_number_padded'] = $draw->winning_number_padded;
        $state['target_digits'] = str_split((string) $draw->winning_number_padded);
        $state['winner_full'] = $draw->isCompleted() ? $snapshot : null;
        $state['ops_checklist'] = $draw->ops_checklist ?: $this->defaultChecklist();
        $state['verification_ok'] = $draw->isCompleted() ? $this->verifyDraw($draw) : null;
        $state['public_url'] = $draw->public_slug
            ? route('draws.watch', $draw->public_slug)
            : null;

        return $state;
    }

    /**
     * Digits known to the public UI (already revealed positions only).
     *
     * @return array<int, string|null>
     */
    public function visibleTargetDigits(Draw $draw): array
    {
        $padded = (string) ($draw->winning_number_padded ?? '');
        $digits = [];

        for ($i = 0; $i < $draw->digit_length; $i++) {
            $digits[] = $i < $draw->revealed_digits ? ($padded[$i] ?? null) : null;
        }

        return $digits;
    }

    private function ensureDemoRaffleId(): int
    {
        $raffle = Raffle::query()->create([
            'user_id' => User::query()->where('role', 'admin')->value('id') ?? User::query()->value('id') ?? 1,
            'title' => 'Sorteio Demonstração',
            'description' => 'Ação usada apenas para testes de cerimônia.',
            'price' => 0,
            'total_numbers' => 200000,
            'status' => 'paused',
            'prize_name' => 'Prêmio Demonstração',
            'draw_date' => now()->addDay(),
        ]);

        return $raffle->id;
    }
}
