<?php

declare(strict_types=1);

namespace Tests\Epic04\Us16\Acceptance;

use App\Infrastructure\Config;
use App\Presentation\View;
use Tests\Support\IsolatedDatabaseTestCase;

final class ApplicationStructureAcceptanceTest extends IsolatedDatabaseTestCase
{
    public function test_us16_the_presentation_layer_renders_a_template_with_configuration_separate(): void
    {
        $view = new View(dirname(__DIR__, 4) . '/templates');

        $html = $view->render('home', [
            'title' => 'Your next great watch',
            'userEmail' => null,
        ]);

        $this->assertStringContainsString('Your next great watch', $html);
        $this->assertSame('MovieWatchlist', (new Config(dirname(__DIR__, 4)))->get('app_name'));
    }
}