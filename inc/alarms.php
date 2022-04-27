<?php

use Carbon\Carbon;

?>
<?php if ($unifi_connection->count_alarms(false)[0]->count > 0): ?>
    <?php
        $alarms = $unifi_connection->list_alarms(['archived' => false]);
    ?>
    <div class="table-responsive">
        <table class="table table-striped table-bordered caption-top shadow-sm">
            <caption>Alarms</caption>
            <thead>
                <tr>
                    <th>Date/Time</th>
                    <th>Message</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach (collect($alarms)->sortByDesc('datetime') as $record) : ?>
                    <tr>
                        <td title="<?= Carbon::createFromDate($record->{'datetime'})->setTimezone($timezone)->isoFormat('LLL') ?>"><?= Carbon::createFromDate($record->{'datetime'})->diffForHumans() ?></td>
                        <td><?= $record->{'msg'} ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>