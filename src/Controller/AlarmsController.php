<?php

namespace OHF\UnifiStats\Controller;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Views\Twig;

class AlarmsController
{
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, string $site, \UniFi_API\Client $unifi_connection): ResponseInterface
    {
        $sites = collect($unifi_connection->list_sites());

        $unifi_connection->set_site($site);

        return Twig::fromRequest($request)->render($response, 'sites/alarms.html', [
            'site' => $sites->firstWhere('name', $site),
            'alarms' => collect($unifi_connection->list_alarms(['archived' => false]))->sortByDesc('datetime'),
        ]);
    }
}