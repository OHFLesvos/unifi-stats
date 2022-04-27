<?php

namespace OHF\UnifiStats\Controller;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Views\Twig;

class NetworksController
{
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        /** @var \UniFi_API\Client $unifi_connection */
        $unifi_connection = $request->getAttribute('unifi_connection');

        $sites = collect($unifi_connection->list_sites());

        $unifi_connection->set_site($args['site']);

        $networks = collect($unifi_connection->list_networkconf())->sortBy('vlan');
        return Twig::fromRequest($request)->render($response, 'sites/networks.html', [
            'site' => $sites->firstWhere('name', $args['site']),
            'networks' => $networks,
        ]);
    }
}