<?php

namespace OHF\UnifiStats\Controller;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Views\Twig;

class WLANsController
{
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, string $site, \UniFi_API\Client $unifi_connection): ResponseInterface
    {
        $unifi_connection->set_site($site);

        return Twig::fromRequest($request)->render($response, 'sites/wlans.html', [
            'site' => collect($unifi_connection->list_sites())->firstWhere('name', $site),
            'networks' => collect($unifi_connection->list_networkconf())->sortBy('vlan'),
            'wlans' => collect($unifi_connection->list_wlanconf())->sortBy('name'),
        ]);
    }
}