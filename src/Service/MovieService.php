<?php

declare(strict_types=1);

namespace App\Service;

use App\Infrastructure\TmdbClient;
use App\Repository\MovieRepository;

final class MovieService
{
    public function __construct(private TmdbClient $client, private MovieRepository $movies)
    {
    }

    public function search(string $query): array
    {
        return $this->client->search($query);
    }

    public function save(array $movie): int
    {
        return $this->movies->findOrCreate($movie);
    }
}
