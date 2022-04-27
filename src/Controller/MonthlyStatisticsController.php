<?php

namespace OHF\UnifiStats\Controller;

use Carbon\Carbon;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Views\Twig;

class MonthlyStatisticsController
{
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, string $site, \UniFi_API\Client $unifi_connection): ResponseInterface
    {
        $unifi_connection->set_site($site);

        $start_date = (new Carbon())->subMonths(12)->getTimestampMs();

        return Twig::fromRequest($request)->render($response, 'sites/monthly_stats.html', [
            'site' => collect($unifi_connection->list_sites())->firstWhere('name', $site),
            'results' => $unifi_connection->stat_monthly_site($start_date),
        ]);
    }
}