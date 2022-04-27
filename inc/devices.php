<?php

use Carbon\Carbon;
use Carbon\CarbonInterval;
use CodeInc\HumanReadableFileSize\HumanReadableFileSize;

$devices = $unifi_connection->list_devices();
?>
<div class="table-responsive">
    <table class="table table-striped table-bordered caption-top shadow-sm">
        <caption>Devices</caption>
        <thead>
            <tr>
                <th>Type</th>
                <th>Name</th>
                <th>IP</th>
                <th>MAC</th>
                <th>Model</th>
                <th>Version</th>
                <th>Uptime</th>
                <th>Satisfaction</th>
                <th>Transmitted</th>
                <th>Received</th>
                <th># Stations</th>
                <th>Adopted</th>
                <th>Last seen</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach (collect($devices)->sortBy('name') as $record) : ?>
                <tr>
                    <?php
                    $type = match ($record->type) {
                        'uap' => 'Access point',
                        'usw' => 'Switch',
                        'udm' => 'Dream Machine',
                        default => $record->type,
                    };
                    ?>
                    <td><?= $type ?></td>
                    <td><?= $record->{'name'} ?></td>
                    <td><?= $record->{'ip'} ?></td>
                    <td><?= $record->{'mac'} ?></td>
                    <td><?= $record->{'model'} ?></td>
                    <td><?= $record->{'version'} ?></td>
                    <td><?= isset($record->uptime) ? CarbonInterval::seconds($record->uptime)->cascade()->forHumans() : '-' ?></td>
                    <td><?= isset($record->{'satisfaction'}) && $record->{'satisfaction'} >= 0 ? $record->{'satisfaction'} : '-' ?></td>
                    <td><?= HumanReadableFileSize::getHumanSize($record->{'tx_bytes'}) ?></td>
                    <td><?= HumanReadableFileSize::getHumanSize($record->{'rx_bytes'}) ?></td>
                    <td><?= $record->{'num_sta'} ?></td>
                    <td><?= $record->{'adopted'} ? 'Yes' : 'No' ?></td>
                    <td>
                        <?php if (isset($record->last_seen)) : ?>
                            <span title="<?= Carbon::createFromTimestamp($record->last_seen)->setTimezone($timezone)->isoFormat('LLL') ?>">
                                <?= Carbon::createFromTimestamp($record->last_seen)->diffForHumans() ?>
                            </span>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>