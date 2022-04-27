<?php

namespace OHF\UnifiStats\Controller;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Views\Twig;

class DevicesController
{
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, string $site, \UniFi_API\Client $unifi_connection): ResponseInterface
    {
        $unifi_connection->set_site($site);

        return Twig::fromRequest($request)->render($response, 'sites/devices.html', [
            'site' => collect($unifi_connection->list_sites())->firstWhere('name', $site),
            'devices' => collect($unifi_connection->list_devices())->sortBy('name'),
        ]);
    }
}