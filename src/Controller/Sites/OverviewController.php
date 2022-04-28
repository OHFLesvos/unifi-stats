<?php

namespace OHF\UnifiStats\Controller\Sites;

class OverviewController extends BaseSitesController
{
    protected function template(): string
    {
        return 'sites/overview.html';
    }

    protected function data(\UniFi_API\Client $unifi_connection, $site): array
    {
        $site_stats = collect($unifi_connection->stat_sites())->firstWhere('name', $site);
        $health = collect($site_stats->health);

        return [
            'wan' => $health->firstWhere('subsystem', 'wan'),
            'www' => $health->firstWhere('subsystem', 'www'),
            'wlan' => $health->firstWhere('subsystem', 'wlan'),
            'lan' => $health->firstWhere('subsystem', 'lan'),
            'vpn' => $health->firstWhere('subsystem', 'vpn'),
        ];
    }
}
