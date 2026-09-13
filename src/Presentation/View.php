<?php

declare(strict_types=1);

namespace App\Presentation;

final class View
{
    public function __construct(private string $templatePath)
    {
    }

    public function render(string $template, array $data = []): string
    {
        extract($data, EXTR_SKIP);
        ob_start();
        require $this->templatePath . '/' . $template . '.php';

        return (string) ob_get_clean();
    }
}
