<?php

namespace OHF\UnifiStats\Controller;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Views\Twig;

class HomeController
{
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        /** @var \UniFi_API\Client $unifi_connection */
        $unifi_connection = $request->getAttribute('unifi_connection');

        $controller = $unifi_connection->stat_sysinfo()[0];

        return Twig::fromRequest($request)->render($response, 'home.html', [
            'controller_url' => $request->getAttribute('controller_url'),
            'controller' => $controller,
            'sites' => collect($unifi_connection->list_sites())->sortBy('name'),
        ]);
    }
}