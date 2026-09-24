<?php

declare(strict_types=1);

namespace Tests\Epic04\Us16\Unit;

use App\Routing\Router;
use PHPUnit\Framework\TestCase;

final class ApplicationStructureUnitTest extends TestCase
{
    public function test_us16_router_returns_a_not_found_response_for_unknown_routes(): void
    {
        $router = new Router();

        $this->assertSame('Page not found', $router->dispatch('GET', '/missing'));
    }
}