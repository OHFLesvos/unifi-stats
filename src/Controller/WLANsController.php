<?php

namespace OHF\UnifiStats\Controller;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Views\Twig;

class WLANsController
{
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, string $site, \UniFi_API\Client $unifi_connection): ResponseInterface
    {
        $sites = collect($unifi_connection->list_sites());

        $unifi_connection->set_site($site);

        $wlans = $unifi_connection->list_wlanconf();
        $networks = collect($unifi_connection->list_networkconf())->sortBy('vlan');
        return Twig::fromRequest($request)->render($response, 'sites/wlans.html', [
            'site' => $sites->firstWhere('name', $site),
            'networks' => $networks,
            'wlans' => collect($wlans)->sortBy('name'),
        ]);
    }
}