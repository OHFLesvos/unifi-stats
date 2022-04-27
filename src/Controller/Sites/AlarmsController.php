<?php

namespace OHF\UnifiStats\Controller\Sites;

class AlarmsController extends BaseSitesController
{
    protected function template(): string
    {
        return 'sites/alarms.html';
    }

    protected function data(\UniFi_API\Client $unifi_connection, $site): array
    {
        return [
            'alarms' => collect($unifi_connection->list_alarms(['archived' => false]))->sortByDesc('datetime'),
        ];
    }
}