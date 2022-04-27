<?php

use Dotenv\Dotenv;
use OHF\UnifiStats\Controller\AlarmsController;
use OHF\UnifiStats\Controller\DevicesController;
use OHF\UnifiStats\Controller\MonthlyStatisticsController;
use OHF\UnifiStats\Controller\NetworksController;
use OHF\UnifiStats\Controller\OverviewController;
use OHF\UnifiStats\Controller\WLANsController;
use OHF\UnifiStats\Middleware\UnifiConnectionMiddleware;
use OHF\UnifiStats\Util\TwigConfigurationInitializer;
use Slim\Factory\AppFactory;
use Slim\Views\TwigMiddleware;

require __DIR__ . '/vendor/autoload.php';

$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->safeLoad();

$debug = $_ENV['APP_ENV'] ?? 'prod' == 'local';

$app = AppFactory::create();

$app->add(TwigMiddleware::create($app, TwigConfigurationInitializer::create($debug)));
$app->addErrorMiddleware($debug, true, true);

$app->redirect('/', 'overview');
$app->group('/', function () use ($app) {
    $app->get('/overview', OverviewController::class)
        ->setName('overview');
    $app->get('/stats/{site:[\w\d]+}', MonthlyStatisticsController::class)
        ->setName('stats');
    $app->get('/devices/{site:[\w\d]+}', DevicesController::class)
        ->setName('devices');
    $app->get('/alarms/{site:[\w\d]+}', AlarmsController::class)
        ->setName('alarms');
    $app->get('/networks/{site:[\w\d]+}', NetworksController::class)
        ->setName('networks');
    $app->get('/wlans/{site:[\w\d]+}', WLANsController::class)
        ->setName('wlans');
})->add(UnifiConnectionMiddleware::class);

$app->setBasePath(getAppBasePath());

$app->run();
