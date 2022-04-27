<?php

namespace OHF\UnifiStats\Controller\Sites;

use Slim\Psr7\Response;
use Slim\Views\Twig;

class OverviewController
{
    public function __invoke(Response $response, string $site, \UniFi_API\Client $unifi_connection, Twig $twig): Response
    {
        $unifi_connection->set_site($site);

        $site_stats = collect($unifi_connection->stat_sites())->firstWhere('name', $site);

        return $twig->render($response, 'sites/overview.html', [
            'site' => [
                'name' => $site_stats->name,
                'desc' => $site_stats->desc,
                'wan' => collect($site_stats->health)->filter(fn ($h) => $h->subsystem == 'wan')->first(),
                'www' => collect($site_stats->health)->filter(fn ($h) => $h->subsystem == 'www')->first(),
                'wlan' => collect($site_stats->health)->filter(fn ($h) => $h->subsystem == 'wlan')->first(),
                'lan' => collect($site_stats->health)->filter(fn ($h) => $h->subsystem == 'lan')->first(),
                'vpn' => collect($site_stats->health)->filter(fn ($h) => $h->subsystem == 'vpn')->first(),
                'alarm_count' => $unifi_connection->count_alarms(false)[0]->count,
            ],
        ]);
    }
}
