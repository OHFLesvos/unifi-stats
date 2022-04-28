<?php

namespace OHF\UnifiStats\Middleware;

use Fig\Http\Message\StatusCodeInterface;
use Monolog\Logger;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response;
use Slim\Views\Twig;

class UnifiConnectionMiddleware
{
    public function __construct(private Twig $twig, private Logger $logger)
    {
    }

    public function __invoke(Request $request, RequestHandler $handler): Response
    {
        $controller_user = $_ENV['CONTROLLER_USER'] ?? null;
        $controller_password = $_ENV['CONTROLLER_PASSWORD'] ?? null;
        $controller_url = $_ENV['CONTROLLER_URL'] ?? null;

        $unifi_connection = new \UniFi_API\Client($controller_user, $controller_password, $controller_url, null, null, false);
        $login_result = @$unifi_connection->login();
        if ($login_result !== true) {
            $this->logger->error("Unable to login to Unifi controller $controller_url with user $controller_user.", [
                'return_code' => $login_result,
            ]);
            return $this->twig->render(new Response(StatusCodeInterface::STATUS_BAD_REQUEST), 'errors/connection-error.html', [
                'controller_url' => $controller_url,
                'result' => $login_result,
            ]);
        }

        $request = $request->withAttribute('unifi_connection', $unifi_connection);
        $request = $request->withAttribute('controller_url', $controller_url);

        $response = $handler->handle($request);

        $unifi_connection->logout();

        return $response;
    }
}
