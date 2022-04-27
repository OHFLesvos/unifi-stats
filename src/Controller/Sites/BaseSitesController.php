<?php

namespace OHF\UnifiStats\Controller\Sites;

use Slim\Psr7\Response;
use Slim\Views\Twig;

abstract class BaseSitesController
{
    public function __invoke(Response $response, string $site, \UniFi_API\Client $unifi_connection, Twig $twig): Response
    {
        $unifi_connection->set_site($site);

        $data = array_merge([
            'site' => collect($unifi_connection->list_sites())->firstWhere('name', $site),
            'alarm_count' => $unifi_connection->count_alarms(false)[0]->count,
        ], $this->data($unifi_connection, $site));

        return $twig->render($response, $this->template(), $data);
    }

    protected abstract function template(): string;
    protected abstract function data(\UniFi_API\Client $unifi_connection, $site): array;
}
