<?php

use Carbon\CarbonInterval;
use CodeInc\HumanReadableFileSize\HumanReadableFileSize;

?>

<div class="row row-cols-1 row-cols-md-2 g-4 mb-4">

<!-- WAN -->
<?php
    $wan = collect($site->health)->filter(fn ($h) => $h->subsystem == 'wan')->first();
?>
<?php if ($wan !== null && $wan->status != 'unknown') : ?>
    <div class="col">
        <div class="card shadow-sm">
            <div class="card-header d-flex justify-content-between">
                <span><i class="bi-modem"></i> WAN</span>
                <strong class="<?= $wan->status == 'ok' ? 'text-success' : 'text-danger' ?>"><?= strtoupper($wan->status) ?></strong>
            </div>
            <ul class="list-group list-group-flush">
                <li class="list-group-item"><strong>Gateways:</strong> <?= $wan->num_gw ?>/<?= $wan->num_adopted ?></li>
                <li class="list-group-item"><strong>WAN IP:</strong> <?= $wan->wan_ip ?></li>
                <li class="list-group-item"><strong>Clients:</strong> <?= $wan->num_sta ?></li>
                <li class="list-group-item"><strong>Current traffic:</strong> <?= HumanReadableFileSize::getHumanSize($wan->{'tx_bytes-r'}) ?>/<?= HumanReadableFileSize::getHumanSize($wan->{'rx_bytes-r'}) ?> (up/down)</li>
                <li class="list-group-item"><strong>ISP:</strong> <?= $wan->isp_name ?> (<?= $wan->isp_organization ?>)</li>
                <li class="list-group-item"><strong>Uptime:</strong> Availability: <?= round($wan->uptime_stats->WAN->availability, 3) ?>%, Average latency <?= $wan->uptime_stats->WAN->latency_average ?>s</li>
            </ul>
        </div>
    </div>
<?php endif; ?>

<!-- WWW -->
<?php
    $www = collect($site->health)->filter(fn ($h) => $h->subsystem == 'www')->first();
?>
<?php if ($www !== null && $www->status != 'unknown') : ?>
    <div class="col">
        <div class="card shadow-sm">
            <div class="card-header d-flex justify-content-between">
                <span><i class="bi-globe"></i> WWW</span>
                <strong class="<?= $www->status == 'ok' ? 'text-success' : 'text-danger' ?>"><?= strtoupper($www->status) ?></strong>
            </div>
            <ul class="list-group list-group-flush">
                <li class="list-group-item"><strong>Latency:</strong> <?= $www->latency ?></li>
                <li class="list-group-item"><strong>Uptime:</strong> <?= CarbonInterval::seconds($www->uptime)->cascade()->forHumans() ?></li>
                <li class="list-group-item"><strong>Current traffic:</strong> <?= HumanReadableFileSize::getHumanSize($www->{'tx_bytes-r'}) ?>/<?= HumanReadableFileSize::getHumanSize($www->{'rx_bytes-r'}) ?> (up/down)</li>
            </ul>
        </div>
    </div>
<?php endif; ?>

<!-- WLAN -->
<?php
    $wlan = collect($site->health)->filter(fn ($h) => $h->subsystem == 'wlan')->first();
?>
<?php if ($wlan !== null) : ?>
    <div class="col">
        <div class="card shadow-sm">
            <div class="card-header d-flex justify-content-between">
                <span><i class="bi-wifi"></i> WLAN</span>
                <strong class="<?= $wlan->status == 'ok' ? 'text-success' : 'text-danger' ?>"><?= strtoupper($wlan->status) ?></strong>
            </div>
            <ul class="list-group list-group-flush">
                <li class="list-group-item"><strong>Access points:</strong> <?= $wlan->num_ap ?>/<?= $wlan->num_adopted ?></li>
                <li class="list-group-item"><strong>Users:</strong> <?= $wlan->num_user ?></li>
                <li class="list-group-item"><strong>Guests:</strong> <?= $wlan->num_guest ?></li>
            </ul>
        </div>
    </div>
<?php endif; ?>

<!-- LAN -->
<?php
    $lan = collect($site->health)->filter(fn ($h) => $h->subsystem == 'lan')->first();
?>
<?php if ($lan !== null && $lan->status != 'unknown') : ?>
    <div class="col">
        <div class="card shadow-sm">
            <div class="card-header d-flex justify-content-between">
                <span><i class="bi-ethernet"></i> LAN</span>
                <strong class="<?= $lan->status == 'ok' ? 'text-success' : 'text-danger' ?>"><?= strtoupper($lan->status) ?></strong>
            </div>
            <ul class="list-group list-group-flush">
                <li class="list-group-item"><strong>Switches:</strong> <?= $lan->num_sw ?>/<?= $lan->num_adopted ?></li>
                <?php if (isset($lan->lan_ip)) : ?>
                    <li class="list-group-item"><strong>LAN IP:</strong> <?= $lan->lan_ip ?></li>
                <?php endif; ?>
                <li class="list-group-item"><strong>Users:</strong> <?= $lan->num_user ?></li>
                <li class="list-group-item"><strong>Guests:</strong> <?= $lan->num_guest ?></li>
                <li class="list-group-item"><strong>Current traffic:</strong> <?= HumanReadableFileSize::getHumanSize($lan->{'tx_bytes-r'}) ?>/<?= HumanReadableFileSize::getHumanSize($lan->{'rx_bytes-r'}) ?> (up/down)</li>
            </ul>
        </div>
    </div>
<?php endif; ?>

<!-- VPN -->
<?php
    $vpn = collect($site->health)->filter(fn ($h) => $h->subsystem == 'vpn')->first();
?>
<?php if ($vpn !== null && $vpn->status != 'unknown') : ?>
    <div class="col">
        <div class="card shadow-sm">
            <div class="card-header d-flex justify-content-between">
                <span><i class="bi-bricks"></i> VPN</span>
                <strong class="<?= $vpn->status == 'ok' ? 'text-success' : 'text-danger' ?>"><?= strtoupper($vpn->status) ?></strong>
            </div>
            <ul class="list-group list-group-flush">
                <li class="list-group-item"><strong>Remote users enabled:</strong> <?= $vpn->remote_user_enabled ?></li>
                <li class="list-group-item"><strong>Remote users active:</strong> <?= $vpn->remote_user_num_active ?></li>
                <li class="list-group-item"><strong>Current traffic:</strong> <?= HumanReadableFileSize::getHumanSize($vpn->{'remote_user_rx_bytes'}) ?>/<?= HumanReadableFileSize::getHumanSize($vpn->{'remote_user_tx_bytes'}) ?> (received/transferred)</li>
            </ul>
        </div>
    </div>
<?php endif; ?>
</div>