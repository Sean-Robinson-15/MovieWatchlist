# MovieWatchlist

A server-rendered PHP movie watchlist built with an N-Tier architecture.

## Requirements

- PHP 8.2 or newer with the `pdo_sqlite` extension
- Composer
- A TMDB API key for movie search (added in the next feature slice)

## Layers

- `public/`: presentation entry point and browser assets
- `src/Service/`: business rules and validation
- `src/Repository/`: persistence access through PDO
- `src/Infrastructure/`: configuration, routing, database, and framework adapters
- `src/Presentation/`: view rendering
- `templates/`: server-rendered pages
- `database/migrations/`: SQLite schema
- `tests/`: automated tests

## Local setup

1. Install PHP and Composer, ensuring both commands are available on PATH.
2. Run `composer install`.
3. Copy `.env.example` to `.env` and set `TMDB_API_KEY` when movie search is enabled.
4. Start the local server:

```powershell
composer serve
```

5. Open <http://localhost:8000>.

The SQLite database is created automatically at `storage/database.sqlite` on the first request.

## Current foundation slice

The application currently includes the landing page, account registration, login, logout, protected routes, SQLite migrations, password hashing, CSRF protection, TMDB search, movie persistence, and user-scoped watchlist actions for adding, filtering, updating, and removing films.

When `TMDB_API_KEY` is empty, search returns no external results but the rest of the application remains available. Obtain a key from TMDB and add it only to your local `.env` file.
