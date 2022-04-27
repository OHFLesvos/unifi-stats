<?php

use Carbon\Carbon;
use Carbon\CarbonInterval;
use CodeInc\HumanReadableFileSize\HumanReadableFileSize;
use Dotenv\Dotenv;

require_once 'vendor/autoload.php';

$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->safeLoad();

$timezone = $_ENV['TIMEZONE'] ?? 'UTC';

?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css">

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
        <p>Controller: <a href="<?= $controller_url ?>" target="_blank"><?= $controller_url ?></a></p>
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

                <!-- Monthly statistics -->
                <?php
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

                <!-- Alarms -->
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

                <!-- Devices -->
                <?php
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
                                        <?php if (isset($record->last_seen)): ?>
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

            <?php endforeach; ?>
            <?php
                $unifi_connection->logout();
            ?>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
</body>

</html>