<?php

use DI\Container;
use Dotenv\Dotenv;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Slim\Views\TwigMiddleware;
use Slim\Views\Twig;

require __DIR__ . '/vendor/autoload.php';

$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->safeLoad();

$environment = ($_ENV['APP_ENV'] ?? 'production');
$is_prod = in_array($environment, ['prod', 'production']);
$debug = !$is_prod;

$twig = OHF\UnifiStats\Util\TwigConfigurationInitializer::create($debug, $is_prod);

$container = new Container();
$container->set(Twig::class, fn () => $twig);

$app = \DI\Bridge\Slim\Bridge::create($container);

$app->add(TwigMiddleware::create($app, $twig));

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
})->add(OHF\UnifiStats\Middleware\UnifiUserInfoMiddleware::class)
->add(OHF\UnifiStats\Middleware\UnifiConnectionMiddleware::class);

$app->addRoutingMiddleware();

$logger = new Logger('app');
$logger->pushHandler(new StreamHandler(__DIR__ . '/storage/logs/app.log', Logger::INFO));
if ($debug) {
    $logger->pushHandler(new StreamHandler(__DIR__ . '/storage/logs/debug.log', Logger::DEBUG));
}
$container->set(Logger::class, fn () => $logger);

$app->addErrorMiddleware($debug, true, true, $logger);

$app->setBasePath(getAppBasePath());

$app->add(new Tuupola\Middleware\HttpBasicAuthentication([
    "realm" => "Unifi Login",
    "authenticator" => new OHF\UnifiStats\Auth\UnifiAuthenticator, 
    "before" => function ($request, $arguments) {
        return $request
            ->withAttribute("user", $arguments["user"])
            ->withAttribute("password", $arguments["password"]);
    }
]));

$app->run();
