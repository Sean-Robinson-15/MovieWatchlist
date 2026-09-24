<?php

declare(strict_types=1);

namespace Tests\Epic04\Us16\Unit;

use App\Infrastructure\Config;
use App\Presentation\View;
use App\Routing\Router;
use PHPUnit\Framework\TestCase;

final class ApplicationStructureTest extends TestCase
{
    public function test_us16_ac01_router_dispatches_static_and_parameterized_routes(): void
    {
        $router = new Router();
        $router->get('/health', static fn (): string => 'ok');
        $router->get('/movies/{id}', static fn (int $id): string => 'movie-' . $id);

        $this->assertSame('ok', $router->dispatch('GET', '/health'));
        $this->assertSame('movie-42', $router->dispatch('GET', '/movies/42'));
    }

    public function test_us16_ac02_through_ac06_keeps_view_and_configuration_as_separate_services(): void
    {
        $view = new View(dirname(__DIR__, 4) . '/templates');
        $config = new Config(dirname(__DIR__, 4));

        $this->assertStringContainsString('Your next great watch', $view->render('home', [
            'title' => 'Your next great watch',
            'userEmail' => null,
        ]));
        $this->assertSame('MovieWatchlist', $config->get('app_name'));
    }
}