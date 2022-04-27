<?php

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
        <?php if (!$login_result) : ?>
            <div class="alert alert-danger">Unable to login to the Unifi controller <code><?= $controller_url ?></code>!</div>
        <?php else : ?>

            <?php require('inc/controller.php'); ?>

            <?php
            $site_stats = $unifi_connection->stat_sites();
            ?>
            <?php foreach ($site_stats as $site) : ?>
                <?php
                $unifi_connection->set_site($site->name);
                ?>
                <h2 class="display-6"><?= $site->desc ?></h2>

                <?php require('inc/overview.php'); ?>
                <?php require('inc/alarms.php'); ?>
                <?php require('inc/monthly_stats.php'); ?>
                <?php require('inc/devices.php'); ?>
                <?php require('inc/networks.php'); ?>
                <?php require('inc/wlans.php'); ?>

            <?php endforeach; ?>
            <?php
            $unifi_connection->logout();
            ?>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
</body>

</html>