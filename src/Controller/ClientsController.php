<?php

namespace OHF\UnifiStats\Controller;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Views\Twig;

class ClientsController
{
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, string $site, \UniFi_API\Client $unifi_connection): ResponseInterface
    {
        $unifi_connection->set_site($site);

        return Twig::fromRequest($request)->render($response, 'sites/clients.html', [
            'site' => collect($unifi_connection->list_sites())->firstWhere('name', $site),
            'clients' => collect($unifi_connection->list_clients()),
        ]);
    }
}