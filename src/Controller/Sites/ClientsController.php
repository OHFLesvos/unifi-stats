<?php

namespace OHF\UnifiStats\Controller\Sites;

class ClientsController extends BaseSitesController
{
    protected function template(): string
    {
        return 'sites/clients.html';
    }

    protected function data(\UniFi_API\Client $unifi_connection, $site): array
    {
        return [
            'clients' => collect($unifi_connection->list_clients()),
        ];
    }
}