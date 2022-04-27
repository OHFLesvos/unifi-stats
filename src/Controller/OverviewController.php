<?php

namespace OHF\UnifiStats\Controller;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Views\Twig;

class OverviewController
{
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        /** @var \UniFi_API\Client $unifi_connection */
        $unifi_connection = $request->getAttribute('unifi_connection');


        $sites = collect($unifi_connection->stat_sites());
        $site = $sites->firstWhere('name', $args['site']);

        $unifi_connection->set_site($args['site']);
        $alarm_count = $unifi_connection->count_alarms(false)[0]->count;

        return Twig::fromRequest($request)->render($response, 'overview.html', [
            'site' => [
                'name' => $site->name,
                'desc' => $site->desc,
                'wan' => collect($site->health)->filter(fn ($h) => $h->subsystem == 'wan')->first(),
                'www' => collect($site->health)->filter(fn ($h) => $h->subsystem == 'www')->first(),
                'wlan' => collect($site->health)->filter(fn ($h) => $h->subsystem == 'wlan')->first(),
                'lan' => collect($site->health)->filter(fn ($h) => $h->subsystem == 'lan')->first(),
                'vpn' => collect($site->health)->filter(fn ($h) => $h->subsystem == 'vpn')->first(),
                'alarm_count' => $alarm_count,
            ],
        ]);
    }
}