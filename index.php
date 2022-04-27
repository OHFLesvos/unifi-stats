<?php

use Dotenv\Dotenv;
use OHF\UnifiStats\Controller\OverviewController;
use OHF\UnifiStats\Middleware\UnifiConnectionMiddleware;
use OHF\UnifiStats\Util\TwigConfigurationInitializer;
use Slim\Factory\AppFactory;
use Slim\Views\TwigMiddleware;

require __DIR__ . '/vendor/autoload.php';

$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->safeLoad();

$debug = $_ENV['APP_ENV'] ?? 'prod' == 'local';

$app = AppFactory::create();

$app->add(TwigMiddleware::create($app, TwigConfigurationInitializer::create()));
$app->addErrorMiddleware($debug, true, true);

$app->redirect('/', 'overview');
$app->group('/', function () use ($app) {
    $app->get('/overview', OverviewController::class);

})->add(UnifiConnectionMiddleware::class);

$app->setBasePath(getAppBasePath());

$app->run();
