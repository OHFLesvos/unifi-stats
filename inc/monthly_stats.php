
<?php

use Carbon\Carbon;
use CodeInc\HumanReadableFileSize\HumanReadableFileSize;

$start_date = (new Carbon())->subMonths(12)->getTimestampMs();
$results = $unifi_connection->stat_monthly_site($start_date);
?>
<div class="table-responsive">
    <table class="table table-striped table-bordered caption-top shadow-sm">
        <caption>Monthly statistics</caption>
        <thead>
            <tr>
                <th>Month</th>
                <th class="text-end">WAN transmitted</th>
                <th class="text-end">WAN received</th>
                <th class="text-end">WLAN Traffic</th>
                <th class="text-end">WLAN clients</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($results as $record) : ?>
                <tr>
                    <td><?= Carbon::createFromTimestampMs($record->time)->format('M Y') ?></td>
                    <td class="text-end"><?= isset($record->{'wan-tx_bytes'}) ? HumanReadableFileSize::getHumanSize($record->{'wan-tx_bytes'}) : '' ?></td>
                    <td class="text-end"><?= isset($record->{'wan-rx_bytes'}) ? HumanReadableFileSize::getHumanSize($record->{'wan-rx_bytes'}) : '' ?></td>
                    <td class="text-end"><?= HumanReadableFileSize::getHumanSize($record->wlan_bytes) ?></td>
                    <td class="text-end"><?= $record->{'wlan-num_sta'} ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>