<?php

declare(strict_types=1);

use App\Infrastructure\Config;
use App\Infrastructure\Csrf;
use App\Infrastructure\Database;
use App\Routing\Router;
use App\Infrastructure\TmdbClient;
use App\Presentation\View;
use App\Repository\GroupRepository;
use App\Repository\MovieRepository;
use App\Repository\UserRepository;
use App\Repository\VoteRepository;
use App\Repository\WatchlistRepository;
use App\Service\AuthService;
use App\Service\GroupService;
use App\Service\MovieService;
use App\Service\VoteService;
use App\Service\WatchlistService;
require dirname(__DIR__) . '/vendor/autoload.php';

$root = dirname(__DIR__);
session_start();
$config = new Config($root);
$pdo = Database::connect($config);
$view = new View($root . '/templates');
$auth = new AuthService(new UserRepository($pdo));
$movieService = new MovieService(new TmdbClient($config), new MovieRepository($pdo));
$watchlistRepository = new WatchlistRepository($pdo);
$watchlist = new WatchlistService($watchlistRepository);
$groupRepository = new GroupRepository($pdo);
$groupService = new GroupService($groupRepository);
$voteService = new VoteService(new VoteRepository($pdo), $groupRepository, $watchlistRepository);
$router = new Router();

$router->get('/', static function () use ($view): string {
    return $view->render('home', [
        'title' => 'Your next great watch',
        'userEmail' => $_SESSION['user_email'] ?? null,
    ]);
});

$router->get('/register', static function () use ($view): string {
    return $view->render('auth/register', ['title' => 'Create your account']);
});

$router->post('/register', static function () use ($auth): never {
    try {
        Csrf::verify((string) ($_POST['_token'] ?? ''));
        $userId = $auth->register($_POST['email'] ?? '', $_POST['password'] ?? '');
        session_regenerate_id(true);
        $_SESSION['user_id'] = $userId;
        $_SESSION['user_email'] = strtolower(trim($_POST['email'] ?? ''));
        header('Location: /watchlist');
        exit;
    } catch (Throwable $error) {
        $_SESSION['error'] = $error->getMessage();
        header('Location: /register');
        exit;
    }
});

$router->get('/login', static function () use ($view): string {
    return $view->render('auth/login', ['title' => 'Welcome back']);
});

$router->post('/login', static function () use ($auth): never {
    try {
        Csrf::verify((string) ($_POST['_token'] ?? ''));
        $userId = $auth->login($_POST['email'] ?? '', $_POST['password'] ?? '');
        session_regenerate_id(true);
        $_SESSION['user_id'] = $userId;
        $_SESSION['user_email'] = strtolower(trim($_POST['email'] ?? ''));
        header('Location: /watchlist');
        exit;
    } catch (Throwable $error) {
        $_SESSION['error'] = $error->getMessage();
        header('Location: /login');
        exit;
    }
});

$router->post('/logout', static function (): never {
    Csrf::verify((string) ($_POST['_token'] ?? ''));
    $_SESSION = [];
    session_destroy();
    header('Location: /');
    exit;
});

$router->get('/watchlist', static function () use ($view): string {
    if (!isset($_SESSION['user_id'])) {
        header('Location: /login');
        exit;
    }

    global $watchlist;
    $status = $_GET['status'] ?? null;
    return $view->render('watchlist', [
        'title' => 'My watchlist',
        'userEmail' => $_SESSION['user_email'],
        'items' => $watchlist->list((int) $_SESSION['user_id'], is_string($status) ? $status : null),
    ]);
});

$router->get('/search', static function () use ($view, $movieService): string {
    if (!isset($_SESSION['user_id'])) {
        header('Location: /login');
        exit;
    }

    $query = trim((string) ($_GET['q'] ?? ''));
    $title = 'Search movies';
    $results = [];
    $searchError = null;
    if ($query !== '') {
        try {
            $results = $movieService->search($query);
        } catch (Throwable $error) {
            $searchError = $error->getMessage();
        }
    }

    return $view->render('search', compact('query', 'results', 'searchError', 'title'));
});

$router->post('/watchlist/add', static function () use ($movieService, $watchlist): never {
    if (!isset($_SESSION['user_id'])) {
        header('Location: /login');
        exit;
    }

    Csrf::verify((string) ($_POST['_token'] ?? ''));
    $movieId = $movieService->save([
        'tmdb_id' => (int) ($_POST['tmdb_id'] ?? 0),
        'title' => trim((string) ($_POST['title'] ?? 'Untitled')),
        'overview' => trim((string) ($_POST['overview'] ?? '')),
        'poster_path' => $_POST['poster_path'] ?: null,
        'release_date' => $_POST['release_date'] ?: null,
    ]);
    $watchlist->add((int) $_SESSION['user_id'], $movieId);
    header('Location: /watchlist');
    exit;
});

$router->post('/watchlist/update', static function () use ($watchlist): never {
    if (!isset($_SESSION['user_id'])) {
        header('Location: /login');
        exit;
    }

    try {
        Csrf::verify((string) ($_POST['_token'] ?? ''));
        $watchlist->update(
            (int) $_SESSION['user_id'],
            (int) ($_POST['item_id'] ?? 0),
            (string) ($_POST['status'] ?? 'planned'),
            (string) ($_POST['rating'] ?? ''),
            (string) ($_POST['notes'] ?? '')
        );
    } catch (Throwable $error) {
        $_SESSION['error'] = $error->getMessage();
    }
    header('Location: /watchlist');
    exit;
});

$router->post('/watchlist/remove', static function () use ($watchlist): never {
    if (isset($_SESSION['user_id'])) {
        Csrf::verify((string) ($_POST['_token'] ?? ''));
        $watchlist->remove((int) $_SESSION['user_id'], (int) ($_POST['item_id'] ?? 0));
    }
    header('Location: /watchlist');
    exit;
});

$router->get('/groups', static function () use ($view, $groupService): string {
    if (!isset($_SESSION['user_id'])) {
        header('Location: /login');
        exit;
    }

    return $view->render('groups', [
        'title' => 'My groups',
        'groups' => $groupService->allForUser((int) $_SESSION['user_id']),
    ]);
});

$router->post('/groups/create', static function () use ($groupService): never {
    if (!isset($_SESSION['user_id'])) {
        header('Location: /login');
        exit;
    }

    try {
        Csrf::verify((string) ($_POST['_token'] ?? ''));
        $groupId = $groupService->create((int) $_SESSION['user_id'], (string) ($_POST['name'] ?? ''));
        header('Location: /groups/' . $groupId);
        exit;
    } catch (Throwable $error) {
        $_SESSION['error'] = $error->getMessage();
        header('Location: /groups');
        exit;
    }
});

$router->post('/groups/join', static function () use ($groupService): never {
    if (!isset($_SESSION['user_id'])) {
        header('Location: /login');
        exit;
    }

    try {
        Csrf::verify((string) ($_POST['_token'] ?? ''));
        $groupId = $groupService->join((int) $_SESSION['user_id'], (string) ($_POST['join_code'] ?? ''));
        header('Location: /groups/' . $groupId);
        exit;
    } catch (Throwable $error) {
        $_SESSION['error'] = $error->getMessage();
        header('Location: /groups');
        exit;
    }
});

$router->get('/groups/{id}', static function (int $groupId) use ($view, $groupService, $watchlistRepository): string {
    if (!isset($_SESSION['user_id'])) {
        header('Location: /login');
        exit;
    }

    $userId = (int) $_SESSION['user_id'];
    $group = $groupService->getForMember($groupId, $userId);
    $requestedFilter = $_GET['filter'] ?? 'planned';
    $filter = is_string($requestedFilter) && in_array($requestedFilter, ['all', 'planned', 'watching', 'watched'], true)
        ? $requestedFilter
        : 'planned';

    return $view->render('group', [
        'title' => $group['name'],
        'group' => $group,
        'filter' => $filter,
        'items' => $watchlistRepository->aggregateForGroup($groupId, $filter),
        'members' => $groupService->members($groupId, $userId),
    ]);
});

$router->post('/groups/{id}/leave', static function (int $groupId) use ($groupService): never {
    if (!isset($_SESSION['user_id'])) {
        header('Location: /login');
        exit;
    }

    try {
        Csrf::verify((string) ($_POST['_token'] ?? ''));
        $groupService->leave($groupId, (int) $_SESSION['user_id']);
        header('Location: /groups');
        exit;
    } catch (Throwable $error) {
        $_SESSION['error'] = $error->getMessage();
        header('Location: /groups/' . $groupId);
        exit;
    }
});

$router->get('/groups/{id}/watch-next', static function (int $groupId) use ($view, $groupService, $voteService): string {
    if (!isset($_SESSION['user_id'])) {
        header('Location: /login');
        exit;
    }

    $userId = (int) $_SESSION['user_id'];
    $group = $groupService->getForMember($groupId, $userId);
    return $view->render('watch-next', [
        'title' => 'Watch next · ' . $group['name'],
        'group' => $group,
        'items' => $voteService->listForGroup($groupId, $userId),
    ]);
});

$router->post('/groups/{id}/vote', static function (int $groupId) use ($voteService): never {
    if (!isset($_SESSION['user_id'])) {
        header('Location: /login');
        exit;
    }

    try {
        Csrf::verify((string) ($_POST['_token'] ?? ''));
        $voteService->toggle($groupId, (int) $_SESSION['user_id'], (int) ($_POST['movie_id'] ?? 0));
    } catch (Throwable $error) {
        $_SESSION['error'] = $error->getMessage();
    }
    header('Location: /groups/' . $groupId . '/watch-next');
    exit;
});

$path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
$result = $router->dispatch($_SERVER['REQUEST_METHOD'] ?? 'GET', $path);

echo $result;
