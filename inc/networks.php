<?php

$networks = $unifi_connection->list_networkconf();
?>
<div class="table-responsive">
    <table class="table table-striped table-bordered caption-top shadow-sm">
        <caption>Networks</caption>
        <thead>
            <tr>
                <th>Name</th>
                <th>Purpose</th>
                <th>Type</th>
                <th>VLAN</th>
                <th>IP/Subnet</th>
                <th>DHCP</th>
                <th>Enabled</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach (collect($networks)->sortBy('vlan') as $record) : ?>
                <tr>
                    <td><?= $record->name ?></td>
                    <td>
                        <?= match ($record->purpose) {
                            'wan' => 'WAN',
                            'remote-user-vpn' => 'Remote-User VPN',
                            'vlan-only' => 'VLAN-only',
                            default => ucfirst($record->purpose),
                        } ?>
                    </td>
                    <td>
                        <?php if (isset($record->wan_type)) : ?>
                            <?= match ($record->wan_type) {
                                'pppoe' => 'PPPoE',
                                default => ucfirst($record->wan_type),
                            } ?>
                        <?php endif; ?>
                        <?php if (isset($record->vpn_type)) : ?>
                            <?= match ($record->vpn_type) {
                                'l2tp-server' => 'L2TP Server',
                                default => ($record->vpn_type),
                            } ?>
                        <?php endif; ?>
                    </td>
                    <td><?= $record->vlan ?? '-' ?></td>
                    <td><?= $record->ip_subnet ?? '-' ?></td>
                    <td>
                        <?php if (isset($record->dhcpd_enabled) && $record->dhcpd_enabled) : ?>
                            <?= $record->dhcpd_start ?> - <?= $record->dhcpd_stop ?>
                        <?php else : ?>
                            -
                        <?php endif; ?>
                    </td>
                    <td><?= isset($record->enabled) ? ($record->enabled ? 'Yes' : 'No') : '-' ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>