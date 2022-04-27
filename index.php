<?php

use Carbon\Carbon;
use Carbon\CarbonInterval;
use CodeInc\HumanReadableFileSize\HumanReadableFileSize;
use Dotenv\Dotenv;
use OHF\UnifiStats\Middleware\UnifiConnectionMiddleware;
use Slim\Factory\AppFactory;
use Slim\Psr7\Request;
use Slim\Psr7\Response;
use Slim\Views\Twig;
use Slim\Views\TwigMiddleware;

require __DIR__ . '/vendor/autoload.php';

$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->safeLoad();

$timezone = $_ENV['TIMEZONE'] ?? 'UTC';
$debug = $_ENV['APP_ENV'] ?? 'prod' == 'local';

$app = AppFactory::create();

$twig = Twig::create('templates', ['cache' => false, 'debug' => $debug]);
if ($debug) {
    $twig->addExtension(new \Twig\Extension\DebugExtension());
}

$twig->getEnvironment()->addFilter(new \Twig\TwigFilter('humanInterval', function ($value) {
    return CarbonInterval::seconds($value)->cascade()->forHumans();
}));
$twig->getEnvironment()->addFilter(new \Twig\TwigFilter('humanSize', function ($value) {
    return $value !== null ? HumanReadableFileSize::getHumanSize($value, 1) : null;
}));
$twig->getEnvironment()->addFilter(new \Twig\TwigFilter('diffForHumans', function ($value) {
    return Carbon::createFromDate($value)->diffForHumans();
}));
$twig->getEnvironment()->addFilter(new \Twig\TwigFilter('diffForHumansFromTimestamp', function ($value) {
    return Carbon::createFromTimestamp($value)->diffForHumans();
}));
$twig->getEnvironment()->addFilter(new \Twig\TwigFilter('localDateFormat', function ($value) use ($timezone) {
    return Carbon::createFromDate($value)->setTimezone($timezone)->isoFormat('LLL');
}));
$twig->getEnvironment()->addFilter(new \Twig\TwigFilter('localDateFormatFromTimestamp', function ($value) use ($timezone) {
    return Carbon::createFromTimestamp($value)->setTimezone($timezone)->isoFormat('LLL');
}));
$twig->getEnvironment()->addFilter(new \Twig\TwigFilter('monthYear', function ($value) {
    return Carbon::createFromTimestampMs($value)->format('F Y');
}));
$twig->getEnvironment()->addFilter(new \Twig\TwigFilter('unifiDeviceType', function ($value) {
    return match ($value) {
        'uap' => 'Access point',
        'usw' => 'Switch',
        'udm' => 'Dream Machine',
        default => $value,
    };
}));
$twig->getEnvironment()->addFilter(new \Twig\TwigFilter('unifiDeviceModel', function ($value) {
    return match ($value) {
        'U7MSH' => 'Access Point AC Mesh', // UAP-AC-M
        'U7NHD' => 'Access Point nanoHD', // UAP-nanoHD
        'U7LR' => 'Access Point AC Long-range', // UAP-AC-LR
        'U7LT' => 'Access Point AC Lite', // UAP-AC-LITE
        'UAL6' => 'Access Point WiFi 6 Lite', // U6-Lite
        'US8P60' => 'Switch 8 PoE (60W)', // US-8-60W
        'USMINI' => 'Switch Flex Mini', // USW-Flex-Mini
        'USL8LP' => 'Switch Lite 8 PoE', // USW-Lite-8-PoE
        'USL16LP' => 'Switch Lite 16 PoE', // USW-Lite-16-PoE
        'USF5P' => 'Switch Flex', // USW-Flex
        'US16P150' => 'Switch 16 PoE', // USW-16-PoE
        'UDMPRO' => 'Dream Machine Pro', // UDM-Pro
        default => $value,
    };
}));
$twig->getEnvironment()->addFilter(new \Twig\TwigFilter('unifiNetworkPurpose', function ($value) {
    return match ($value) {
        'wan' => 'WAN',
        'remote-user-vpn' => 'Remote-User VPN',
        'vlan-only' => 'VLAN-only',
        default => ucfirst($value),
    };
}));
$twig->getEnvironment()->addFilter(new \Twig\TwigFilter('unifiNetworkType', function ($value) {
    return match ($value) {
        'pppoe' => 'PPPoE',
        'l2tp-server' => 'L2TP Server',
        default => ucfirst($value),
    };
}));

$app->add(TwigMiddleware::create($app, $twig));
$app->addErrorMiddleware($debug, true, true);

$app->add(new UnifiConnectionMiddleware());

$app->get('/', function (Request $request, Response $response, $args) {
    /** @var \UniFi_API\Client $unifi_connection */
    $unifi_connection = $request->getAttribute('unifi_connection');

    $controller = $unifi_connection->stat_sysinfo()[0];
    $site_stats = $unifi_connection->stat_sites();
    $sites = [];
    foreach ($site_stats as $site) {
        $unifi_connection->set_site($site->name);
        $alarm_count = $unifi_connection->count_alarms(false)[0]->count;
        $start_date = (new Carbon())->subMonths(12)->getTimestampMs();
        $wlans = $unifi_connection->list_wlanconf();
        $networks = collect($unifi_connection->list_networkconf())->sortBy('vlan');
        $sites[] = [
            'name' => $site->name,
            'desc' => $site->desc,
            'wan' => collect($site->health)->filter(fn ($h) => $h->subsystem == 'wan')->first(),
            'www' => collect($site->health)->filter(fn ($h) => $h->subsystem == 'www')->first(),
            'wlan' => collect($site->health)->filter(fn ($h) => $h->subsystem == 'wlan')->first(),
            'lan' => collect($site->health)->filter(fn ($h) => $h->subsystem == 'lan')->first(),
            'vpn' => collect($site->health)->filter(fn ($h) => $h->subsystem == 'vpn')->first(),
            'alarm_count' => $alarm_count,
            'alarms' => $alarm_count > 0 ? collect($unifi_connection->list_alarms(['archived' => false]))->sortByDesc('datetime') : [],
            'monthly_stats' => $unifi_connection->stat_monthly_site($start_date),
            'devices' => collect($unifi_connection->list_devices())->sortBy('name'),
            'networks' => $networks,
            'wlans' => collect($wlans)->sortBy('name'),
        ];
    }

    return Twig::fromRequest($request)->render($response, 'index.html', [
        'controller_url' => $request->getAttribute('controller_url'),
        'controller' => $controller,
        'sites' => $sites,
    ]);
});

$app->setBasePath(getAppBasePath());

$app->run();
