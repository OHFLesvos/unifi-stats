<?php

namespace OHF\UnifiStats\Controller\Sites;

class NetworksController extends BaseSitesController
{
    protected function template(): string
    {
        return 'sites/networks.html';
    }

    protected function data(\UniFi_API\Client $unifi_connection, $site): array
    {
        return [
            'networks' => collect($unifi_connection->list_networkconf())->sortBy('vlan'),
        ];
    }
}