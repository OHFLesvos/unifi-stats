<?php

use Carbon\Carbon;
use Carbon\CarbonInterval;
use CodeInc\HumanReadableFileSize\HumanReadableFileSize;
use Symfony\Component\Dotenv\Dotenv;

require_once 'vendor/autoload.php';

$dotenv = new Dotenv();
$dotenv->load(__DIR__ . '/.env');

$controller_user = $_ENV['CONTROLLER_USER'];
$controller_password = $_ENV['CONTROLLER_PASSWORD'];
$controller_url = $_ENV['CONTROLLER_URL'];

$start_date = (new Carbon())->subMonths(12)->getTimestampMs();
$end_date = (new Carbon())->getTimestampMs();

?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">

    <title>Unifi Network Statistics</title>
</head>

<body class="pt-4">
    <div class="container">
        <?php
        $unifi_connection = new UniFi_API\Client($controller_user, $controller_password, $controller_url, null, null, false);
        $unifi_connection->login();

        $site_stats = $unifi_connection->stat_sites();
        ?>
        <h1 class="display-4">Unifi Network Statistics</h1>

        <?php foreach ($site_stats as $site) : ?>
            <?php
            $unifi_connection->set_site($site->name);
            ?>

            <div class="card mb-4">
                <div class="card-header">
                    <?= $site->desc ?>
                </div>
                <div class="card-body">

                    <?php
                    $wan = collect($site->health)->filter(fn ($h) => $h->subsystem == 'wan')->first();
                    ?>
                    <?php if ($wan !== null && $wan->status != 'unknown') : ?>
                        <i class="bi-modem" title="WAN"></i>
                        Status: <?= $wan->status ?>,
                        Gateways: <?= $wan->num_gw ?>/<?= $wan->num_adopted ?>,
                        WAN IP: <?= $wan->wan_ip ?>,
                        Clients: <?= $wan->num_sta ?>,
                        Bandwidth up/down: <?= HumanReadableFileSize::getHumanSize($wan->{'tx_bytes-r'}) ?>/<?= HumanReadableFileSize::getHumanSize($wan->{'rx_bytes-r'}) ?>
                        <br>
                    <?php endif; ?>

                    <?php
                    $www = collect($site->health)->filter(fn ($h) => $h->subsystem == 'www')->first();
                    ?>
                    <?php if ($www !== null && $www->status != 'unknown') : ?>
                        <i class="bi-globe" title="WWW"></i>
                        Status: <?= $www->status ?>,
                        Latency: <?= $www->latency ?>,
                        Uptime: <?= CarbonInterval::seconds($www->uptime)->cascade()->forHumans() ?>,
                        Bandwidth up/down: <?= HumanReadableFileSize::getHumanSize($www->{'tx_bytes-r'}) ?>/<?= HumanReadableFileSize::getHumanSize($www->{'rx_bytes-r'}) ?>
                        <br>
                    <?php endif; ?>

                    <?php
                    $wlan = collect($site->health)->filter(fn ($h) => $h->subsystem == 'wlan')->first();
                    ?>
                    <?php if ($wlan !== null) : ?>
                        <i class="bi-wifi" title="WLAN"></i>
                        Status: <?= $wlan->status ?>,
                        Access points: <?= $wlan->num_ap ?>/<?= $wlan->num_adopted ?>,
                        Users: <?= $wlan->num_user ?>,
                        Guests: <?= $wlan->num_guest ?>
                        <br>
                    <?php endif; ?>

                    <?php
                    $lan = collect($site->health)->filter(fn ($h) => $h->subsystem == 'lan')->first();
                    ?>
                    <?php if ($lan !== null && $lan->status != 'unknown') : ?>
                        <i class="bi-ethernet" title="LAN"></i>
                        Status: <?= $lan->status ?>,
                        Switches: <?= $lan->num_sw ?>/<?= $lan->num_adopted ?>,
                        <?php if (isset($lan->lan_ip)) : ?>
                            LAN IP: <?= $lan->lan_ip ?>,
                        <?php endif; ?>
                        Users: <?= $lan->num_user ?>,
                        Guests: <?= $lan->num_guest ?>
                        Bandwidth up/down: <?= HumanReadableFileSize::getHumanSize($lan->{'tx_bytes-r'}) ?>/<?= HumanReadableFileSize::getHumanSize($lan->{'rx_bytes-r'}) ?>
                        <br>
                    <?php endif; ?>
                </div>

                <?php
                $results = $unifi_connection->stat_monthly_site($start_date, $end_date);
                ?>
                <table class="table table-striped mb-0">
                    <tr>
                        <th>Month</th>
                        <th class="text-end">WAN transmitted</th>
                        <th class="text-end">WAN received</th>
                        <th class="text-end">WLAN Traffic</th>
                        <th class="text-end">WLAN clients</th>
                    </tr>
                    <?php foreach ($results as $record) : ?>
                        <tr>
                            <td><?= Carbon::createFromTimestampMs($record->time)->format('M Y') ?></td>
                            <td class="text-end"><?= isset($record->{'wan-tx_bytes'}) ? HumanReadableFileSize::getHumanSize($record->{'wan-tx_bytes'}) : '' ?></td>
                            <td class="text-end"><?= isset($record->{'wan-rx_bytes'}) ? HumanReadableFileSize::getHumanSize($record->{'wan-rx_bytes'}) : '' ?></td>
                            <td class="text-end"><?= HumanReadableFileSize::getHumanSize($record->wlan_bytes) ?></td>
                            <td class="text-end"><?= $record->{'wlan-num_sta'} ?></td>
                        </tr>
                    <?php endforeach; ?>
                </table>
            </div>
        <?php endforeach; ?>
        <?php
        $unifi_connection->logout();
        ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
</body>

</html>