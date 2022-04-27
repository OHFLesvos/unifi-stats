<?php

use Dotenv\Dotenv;
use OHF\UnifiStats\Controller\AlarmsController;
use OHF\UnifiStats\Controller\ClientsController;
use OHF\UnifiStats\Controller\DevicesController;
use OHF\UnifiStats\Controller\HomeController;
use OHF\UnifiStats\Controller\MonthlyStatisticsController;
use OHF\UnifiStats\Controller\NetworksController;
use OHF\UnifiStats\Controller\OverviewController;
use OHF\UnifiStats\Controller\WLANsController;
use OHF\UnifiStats\Middleware\UnifiConnectionMiddleware;
use OHF\UnifiStats\Util\TwigConfigurationInitializer;
use Slim\Views\TwigMiddleware;

require __DIR__ . '/vendor/autoload.php';

$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->safeLoad();

$debug = $_ENV['APP_ENV'] ?? 'prod' == 'local';

$app = \DI\Bridge\Slim\Bridge::create();

$app->add(TwigMiddleware::create($app, TwigConfigurationInitializer::create($debug)));
$app->addErrorMiddleware($debug, true, true);

$app->group('/', function () use ($app) {
    $app->get('/', HomeController::class)
        ->setName('home');
    $app->get('/sites/{site:[\w\d]+}', OverviewController::class)
        ->setName('overview');
    $app->get('/sites/{site:[\w\d]+}/stats', MonthlyStatisticsController::class)
        ->setName('stats');
    $app->get('/sites/{site:[\w\d]+}/devices', DevicesController::class)
        ->setName('devices');
    $app->get('/sites/{site:[\w\d]+}/alarms', AlarmsController::class)
        ->setName('alarms');
    $app->get('/sites/{site:[\w\d]+}/networks', NetworksController::class)
        ->setName('networks');
    $app->get('/sites/{site:[\w\d]+}/wlans', WLANsController::class)
        ->setName('wlans');
    $app->get('/sites/{site:[\w\d]+}/clients', ClientsController::class)
        ->setName('clients');
})->add(UnifiConnectionMiddleware::class);

$app->setBasePath(getAppBasePath());

$app->run();
