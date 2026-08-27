<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;

class CronEndpointTest extends TestCase
{
    use RefreshDatabase;

    public function test_cron_endpoint_rejects_missing_token(): void
    {
        Config::set('app.cron_token', 'segredo-cron');

        $this->getJson(route('internal.cron.run'))
            ->assertUnauthorized();
    }

    public function test_cron_endpoint_runs_scheduler_with_valid_token(): void
    {
        Config::set('app.cron_token', 'segredo-cron');

        $this->getJson(route('internal.cron.run', ['token' => 'segredo-cron']))
            ->assertOk()
            ->assertJson(['success' => true]);
    }
}
