<?php

namespace OHF\UnifiStats\Controller\Sites;

class DevicesController extends BaseSitesController
{
    protected function template(): string
    {
        return 'sites/devices.html';
    }

    protected function data(\UniFi_API\Client $unifi_connection, $site): array
    {
        return [
            'devices' => collect($unifi_connection->list_devices())->sortBy('name'),
        ];
    }
}