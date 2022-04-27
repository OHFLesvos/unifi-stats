<?php

use DI\Container;
use Dotenv\Dotenv;
use Slim\Views\TwigMiddleware;
use Slim\Views\Twig;

require __DIR__ . '/vendor/autoload.php';

$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->safeLoad();

$debug = $_ENV['APP_ENV'] ?? 'prod' == 'local';

$twig = OHF\UnifiStats\Util\TwigConfigurationInitializer::create($debug);

$container = new Container();
$container->set(Twig::class, fn () => $twig);

$app = \DI\Bridge\Slim\Bridge::create($container);

$app->add(TwigMiddleware::create($app, $twig));

$app->addErrorMiddleware($debug, true, true);

$app->group('/', function () use ($app) {
    $app->get('/', OHF\UnifiStats\Controller\HomeController::class)
        ->setName('home');
    $app->get('/sites/{site:[\w\d]+}', OHF\UnifiStats\Controller\Sites\OverviewController::class)
        ->setName('overview');
    $app->get('/sites/{site:[\w\d]+}/stats', OHF\UnifiStats\Controller\Sites\MonthlyStatisticsController::class)
        ->setName('stats');
    $app->get('/sites/{site:[\w\d]+}/devices', OHF\UnifiStats\Controller\Sites\DevicesController::class)
        ->setName('devices');
    $app->get('/sites/{site:[\w\d]+}/alarms', OHF\UnifiStats\Controller\Sites\AlarmsController::class)
        ->setName('alarms');
    $app->get('/sites/{site:[\w\d]+}/networks', OHF\UnifiStats\Controller\Sites\NetworksController::class)
        ->setName('networks');
    $app->get('/sites/{site:[\w\d]+}/wlans', OHF\UnifiStats\Controller\Sites\WLANsController::class)
        ->setName('wlans');
    $app->get('/sites/{site:[\w\d]+}/clients', OHF\UnifiStats\Controller\Sites\ClientsController::class)
        ->setName('clients');
})->add(OHF\UnifiStats\Middleware\UnifiConnectionMiddleware::class);

$app->add(OHF\UnifiStats\Middleware\CurrentRouteMiddleware::class);

$app->addRoutingMiddleware();

$app->setBasePath(getAppBasePath());

$app->run();
