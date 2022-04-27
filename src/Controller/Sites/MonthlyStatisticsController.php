<?php

namespace OHF\UnifiStats\Controller\Sites;

use Carbon\Carbon;

class MonthlyStatisticsController extends BaseSitesController
{
    protected function template(): string
    {
        return 'sites/monthly_stats.html';
    }

    protected function data(\UniFi_API\Client $unifi_connection, $site): array
    {
        $start_date = (new Carbon())->subMonths(12)->getTimestampMs();

        return [
            'results' => $unifi_connection->stat_monthly_site($start_date),
        ];
    }
}