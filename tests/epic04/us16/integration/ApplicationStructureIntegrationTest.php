<?php

declare(strict_types=1);

namespace Tests\Epic04\Us16\Integration;

use App\Infrastructure\Config;
use App\Presentation\View;
use App\Routing\Router;
use PHPUnit\Framework\TestCase;

final class ApplicationStructureIntegrationTest extends TestCase
{
    public function test_us16_router_and_presentation_components_work_together(): void
    {
        $router = new Router();
        $view = new View(dirname(__DIR__, 4) . '/templates');
        $router->get('/home', static fn (): string => $view->render('home', [
            'title' => 'Your next great watch',
            'userEmail' => null,
        ]));

        $response = $router->dispatch('GET', '/home');

        $this->assertStringContainsString('Your next great watch', $response);
        $this->assertSame('MovieWatchlist', (new Config(dirname(__DIR__, 4)))->get('app_name'));
    }
}