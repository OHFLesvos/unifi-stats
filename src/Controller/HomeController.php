<?php

namespace OHF\UnifiStats\Controller;

use Slim\Psr7\Response;
use Slim\Views\Twig;

class HomeController
{
    public function __invoke(Response $response, \UniFi_API\Client $unifi_connection, string $controller_url, Twig $twig): Response
    {
        return $twig->render($response, 'home.html', [
            'controller_url' => $controller_url,
            'controller' => $unifi_connection->stat_sysinfo()[0],
            'sites' => collect($unifi_connection->list_sites())->sortBy('name')->map(function($site) use ($unifi_connection) {
                $unifi_connection->set_site($site->name);

                $site_stats = collect($unifi_connection->stat_sites())->firstWhere('name', $site->name);
                $health = collect($site_stats->health);

                $lan = $health->firstWhere('subsystem', 'lan');
                $wlan = $health->firstWhere('subsystem', 'wlan');

                return [
                    'name' => $site->name,
                    'desc' => $site->desc,
                    'anonymous_id' => $site->anonymous_id,
                    'alarm_count' => $unifi_connection->count_alarms(false)[0]->count,
                    'active_devices' => ($lan->num_sw ?? 0) + ($wlan->num_ap ?? 0),
                    'adopted_devices' => ($lan->num_adopted ?? 0) + ($wlan->num_adopted ?? 0),
                    'users' => ($lan->num_user ?? 0) + ($wlan->num_user ?? 0),
                    'guests' => ($lan->num_guest ?? 0) + ($wlan->num_guest ?? 0),
                    'bytes_rx' => ($lan->{'rx_bytes-r'} ?? 0) + ($wlan->{'rx_bytes-r'} ?? 0),
                    'bytes_tx' => ($lan->{'tx_bytes-r'} ?? 0) + ($wlan->{'tx_bytes-r'} ?? 0),
                ];
            }),
        ]);
    }
}
