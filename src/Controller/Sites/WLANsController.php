<?php

namespace OHF\UnifiStats\Controller\Sites;

class WLANsController extends BaseSitesController
{
    protected function template(): string
    {
        return 'sites/wlans.html';
    }

    protected function data(\UniFi_API\Client $unifi_connection, $site): array
    {
        return [
            'networks' => collect($unifi_connection->list_networkconf())->sortBy('vlan'),
            'wlans' => collect($unifi_connection->list_wlanconf())->sortBy('name'),
        ];
    }
}