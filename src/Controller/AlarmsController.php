<?php

namespace OHF\UnifiStats\Controller;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Views\Twig;

class AlarmsController
{
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        /** @var \UniFi_API\Client $unifi_connection */
        $unifi_connection = $request->getAttribute('unifi_connection');

        $sites = collect($unifi_connection->list_sites());

        $unifi_connection->set_site($args['site']);
        $alarm_count = $unifi_connection->count_alarms(false)[0]->count;

        return Twig::fromRequest($request)->render($response, 'alarms.html', [
            'site' => $sites->firstWhere('name', $args['site']),
            'alarms' => $alarm_count > 0 ? collect($unifi_connection->list_alarms(['archived' => false]))->sortByDesc('datetime') : [],
        ]);
    }
}