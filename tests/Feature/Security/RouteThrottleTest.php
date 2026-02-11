<?php

namespace Tests\Feature\Security;

use Illuminate\Routing\Route;
use Illuminate\Support\Facades\Route as Router;
use Illuminate\Support\Str;
use Tests\TestCase;

/**
 * Guards against new forms or actions being added without a rate limit.
 */
class RouteThrottleTest extends TestCase
{
    public function test_every_route_that_changes_data_is_rate_limited(): void
    {
        $unlimited = collect(Router::getRoutes()->getRoutes())
            ->filter(fn (Route $route) => array_diff($route->methods(), ['GET', 'HEAD', 'OPTIONS']) !== [])
            ->reject(fn (Route $route) => Str::startsWith($route->uri(), ['_ignition', 'broadcasting', 'livewire', 'sanctum']))
            ->reject(fn (Route $route) => collect($route->gatherMiddleware())->contains(fn ($m) => is_string($m) && str_starts_with($m, 'throttle:')))
            ->map(fn (Route $route) => implode('|', $route->methods()) . ' ' . $route->uri())
            ->values()
            ->all();

        $this->assertSame([], $unlimited, "These routes change data without a throttle:\n" . implode("\n", $unlimited));
    }

    public function test_the_dashboard_has_a_per_account_ceiling(): void
    {
        $route = Router::getRoutes()->getByName('dashboard.home');

        $this->assertContains('throttle:dashboard', $route->gatherMiddleware());
    }
}
