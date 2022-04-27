<?php

$wlans = $unifi_connection->list_wlanconf();
$networks = collect($unifi_connection->list_networkconf());
?>
<div class="table-responsive">
    <table class="table table-striped table-bordered caption-top shadow-sm">
        <caption>WLANs</caption>
        <thead>
            <tr>
                <th>Name</th>
                <th>Security</th>
                <th>Network</th>
                <th>Enabled</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach (collect($wlans)->sortBy('name') as $record) : ?>
                <tr>
                    <td><?= $record->name ?></td>
                    <td><?= $record->security ?></td>
                    <td><?= $networks->firstWhere('_id', $record->networkconf_id)->name ?></td>
                    <td><?= $record->enabled ? 'Yes' : 'No' ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
