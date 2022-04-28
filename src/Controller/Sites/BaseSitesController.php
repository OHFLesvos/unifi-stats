<?php

namespace OHF\UnifiStats\Controller\Sites;

use Fig\Http\Message\StatusCodeInterface;
use Monolog\Logger;
use Slim\Psr7\Request;
use Slim\Psr7\Response;
use Slim\Routing\RouteContext;
use Slim\Views\Twig;

abstract class BaseSitesController
{
    public function __invoke(Request $request, Response $response, string $site, \UniFi_API\Client $unifi_connection, Twig $twig, Logger $logger): Response
    {
        $logger->debug('Using site ' . $site . ' in ' . RouteContext::fromRequest($request)->getRoute()->getName());

        $unifi_connection->set_site($site);

        $siteData = collect($unifi_connection->list_sites())->firstWhere('name', $site);

        if ($siteData === null) {
            return $twig->render(new Response(StatusCodeInterface::STATUS_NOT_FOUND), 'errors/unknown-site.html');
        }

        $data = array_merge([
            'site' => $siteData,
            'alarm_count' => $unifi_connection->count_alarms(false)[0]->count,
        ], $this->data($unifi_connection, $site));

        return $twig->render($response, $this->template(), $data);
    }

    protected abstract function template(): string;
    protected abstract function data(\UniFi_API\Client $unifi_connection, $site): array;
}
