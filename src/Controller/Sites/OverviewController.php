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

        return [
            'wan' => collect($site_stats->health)->filter(fn ($h) => $h->subsystem == 'wan')->first(),
            'www' => collect($site_stats->health)->filter(fn ($h) => $h->subsystem == 'www')->first(),
            'wlan' => collect($site_stats->health)->filter(fn ($h) => $h->subsystem == 'wlan')->first(),
            'lan' => collect($site_stats->health)->filter(fn ($h) => $h->subsystem == 'lan')->first(),
            'vpn' => collect($site_stats->health)->filter(fn ($h) => $h->subsystem == 'vpn')->first(),
        ];
    }
}
