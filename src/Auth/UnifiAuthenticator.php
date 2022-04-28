<?php

namespace OHF\UnifiStats\Auth;

use Tuupola\Middleware\HttpBasicAuthentication\AuthenticatorInterface;

class UnifiAuthenticator implements AuthenticatorInterface
{
    public function __invoke(array $arguments): bool
    {
        $controller_user = $arguments['user'];
        $controller_password = $arguments['password'];
        $controller_url = $_ENV['CONTROLLER_URL'] ?? null;

        $unifi_connection = new \UniFi_API\Client($controller_user, $controller_password, $controller_url, null, null, false);
        return @$unifi_connection->login() === true;
    }
}
