<?php

namespace OHF\UnifiStats\Controller;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Views\Twig;

class HomeController
{
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, \UniFi_API\Client $unifi_connection, string $controller_url): ResponseInterface
    {
        return Twig::fromRequest($request)->render($response, 'home.html', [
            'controller_url' => $controller_url,
            'controller' => $unifi_connection->stat_sysinfo()[0],
            'sites' => collect($unifi_connection->list_sites())->sortBy('name')->map(function($site) use ($unifi_connection) {
                $unifi_connection->set_site($site->name);
                return [
                    'name' => $site->name,
                    'desc' => $site->desc,
                    'alarm_count' => $unifi_connection->count_alarms(false)[0]->count,
                ];
            }),
        ]);
    }
}
