<?php

use Carbon\CarbonInterval;

$controller = $unifi_connection->stat_sysinfo()[0];
?>
<div class="card shadow-sm mb-4">
    <div class="card-header d-flex justify-content-between">
        <span>Controller</span>
    </div>
    <ul class="list-group list-group-flush">
        <li class="list-group-item">
            <strong>URL:</strong>
            <a href="<?= $controller_url ?>" target="_blank"><?= $controller_url ?></a>
        </li>
        <?php if (isset($controller->ubnt_device_type)) : ?>
            <li class="list-group-item"><strong>Device type:</strong> <?= $controller->ubnt_device_type ?> (<?= $controller->console_display_version ?>)</li>
        <?php endif; ?>
        <li class="list-group-item"><strong>Name:</strong> <?= $controller->name ?></li>
        <li class="list-group-item"><strong>Version:</strong>
            <?= $controller->version ?>
            <?php if ($controller->update_available) : ?>
                (Update available: <?= $controller->update_available ?>)
            <?php endif; ?>
        </li>
        <li class="list-group-item"><strong>Uptime:</strong> <?= CarbonInterval::seconds($controller->uptime)->cascade()->forHumans() ?></li>
        <li class="list-group-item"><strong>IP Addresses:</strong> <?= implode(', ', $controller->ip_addrs) ?></li>
        <li class="list-group-item"><strong>HTTPS Port:</strong> <?= $controller->https_port ?></li>
        <li class="list-group-item"><strong>Inform Port:</strong> <?= $controller->inform_port ?></li>
        <li class="list-group-item"><strong>Timezone:</strong> <?= $controller->timezone ?></li>
    </ul>
</div>