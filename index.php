<?php

use Carbon\Carbon;
use Carbon\CarbonInterval;
use CodeInc\HumanReadableFileSize\HumanReadableFileSize;
use Dotenv\Dotenv;

require_once 'vendor/autoload.php';

$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->safeLoad();

?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">

    <title>Unifi Network Status</title>
</head>

<body class="pt-4">
    <div class="container">
        <h1 class="display-4">Unifi Network Status</h1>
        <?php
        $controller_user = $_ENV['CONTROLLER_USER'] ?? null;
        $controller_password = $_ENV['CONTROLLER_PASSWORD'] ?? null;
        $controller_url = $_ENV['CONTROLLER_URL'] ?? null;
        $unifi_connection = new UniFi_API\Client($controller_user, $controller_password, $controller_url, null, null, false);
        $login_result = $unifi_connection->login();
        ?>
        <?php if (!$login_result) : ?>
            <div class="alert alert-danger">Unable to login to the Unifi controller <code><?= $controller_url ?></code>!</div>
        <?php else : ?>
            <?php
            $site_stats = $unifi_connection->stat_sites();
            ?>
            <?php foreach ($site_stats as $site) : ?>
                <?php
                $unifi_connection->set_site($site->name);
                ?>
                <h2 class="display-6"><?= $site->desc ?></h2>

                <div class="row row-cols-1 row-cols-md-2 g-4 mb-4">

                    <!-- WAN -->
                    <?php
                    $wan = collect($site->health)->filter(fn ($h) => $h->subsystem == 'wan')->first();
                    ?>
                    <?php if ($wan !== null && $wan->status != 'unknown') : ?>
                        <div class="col">
                            <div class="card">
                                <div class="card-header d-flex justify-content-between">
                                    <span><i class="bi-modem"></i> WAN</span>
                                    <strong class="<?= $wan->status == 'ok' ? 'text-success' : 'text-danger' ?>"><?= strtoupper($wan->status) ?></strong>
                                </div>
                                <ul class="list-group list-group-flush">
                                    <li class="list-group-item"><strong>Gateways:</strong> <?= $wan->num_gw ?>/<?= $wan->num_adopted ?></li>
                                    <li class="list-group-item"><strong>WAN IP:</strong> <?= $wan->wan_ip ?></li>
                                    <li class="list-group-item"><strong>Clients:</strong> <?= $wan->num_sta ?></li>
                                    <li class="list-group-item"><strong>Current traffic:</strong> <?= HumanReadableFileSize::getHumanSize($wan->{'tx_bytes-r'}) ?>/<?= HumanReadableFileSize::getHumanSize($wan->{'rx_bytes-r'}) ?> (up/down)</li>
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
                            <div class="card">
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
                            <div class="card">
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
                            <div class="card">
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
                </div>

                <?php
                $start_date = (new Carbon())->subMonths(12)->getTimestampMs();
                $results = $unifi_connection->stat_monthly_site($start_date);
                ?>
                <div class="table-responsive">
                    <table class="table table-striped table-bordered">
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
            <?php endforeach; ?>
            <?php
            $unifi_connection->logout();
            ?>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
</body>

</html>