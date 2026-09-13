<?php

declare(strict_types=1);

namespace App\Infrastructure;

final class Config
{
    private array $values;

    public function __construct(string $rootPath)
    {
        $fileValues = is_file($rootPath . '/.env')
            ? (parse_ini_file($rootPath . '/.env', false, INI_SCANNER_RAW) ?: [])
            : [];

        $this->values = [
            'app_name' => $fileValues['APP_NAME'] ?? getenv('APP_NAME') ?: 'MovieWatchlist',
            'db_path' => $rootPath . DIRECTORY_SEPARATOR . ($fileValues['DB_PATH'] ?? getenv('DB_PATH') ?: 'storage/database.sqlite'),
            'tmdb_api_key' => $fileValues['TMDB_API_KEY'] ?? getenv('TMDB_API_KEY') ?: '',
            'tmdb_base_url' => $fileValues['TMDB_BASE_URL'] ?? getenv('TMDB_BASE_URL') ?: 'https://api.themoviedb.org/3',
        ];
    }

    public function get(string $key): string
    {
        return (string) ($this->values[$key] ?? '');
    }
}
