<?php

namespace OHF\UnifiStats\Controller;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Views\Twig;

class NetworksController
{
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, string $site, \UniFi_API\Client $unifi_connection): ResponseInterface
    {
        $unifi_connection->set_site($site);

        $networks = collect($unifi_connection->list_networkconf())->sortBy('vlan');
        return Twig::fromRequest($request)->render($response, 'sites/networks.html', [
            'site' => collect($unifi_connection->list_sites())->firstWhere('name', $site),
            'networks' => $networks,
        ]);
    }
}