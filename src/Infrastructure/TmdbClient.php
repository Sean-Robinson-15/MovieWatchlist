<?php

declare(strict_types=1);

namespace App\Infrastructure;

use RuntimeException;

final class TmdbClient
{
    public function __construct(private Config $config)
    {
    }

    public function search(string $query): array
    {
        $query = trim($query);
        if ($query === '' || $this->config->get('tmdb_api_key') === '') {
            return [];
        }

        $url = $this->config->get('tmdb_base_url') . '/search/movie?api_key='
            . rawurlencode($this->config->get('tmdb_api_key')) . '&query=' . rawurlencode($query);
        $response = @file_get_contents($url);
        if ($response === false) {
            throw new RuntimeException('Movie search is temporarily unavailable.');
        }

        $payload = json_decode($response, true);
        if (!is_array($payload) || !isset($payload['results'])) {
            throw new RuntimeException('Movie search returned an unexpected response.');
        }

        return array_map(static fn (array $movie): array => [
            'tmdb_id' => (int) ($movie['id'] ?? 0),
            'title' => (string) ($movie['title'] ?? 'Untitled'),
            'overview' => (string) ($movie['overview'] ?? ''),
            'poster_path' => $movie['poster_path'] ?? null,
            'release_date' => $movie['release_date'] ?? null,
        ], array_slice($payload['results'], 0, 12));
    }
}
