<?php

namespace OHF\UnifiStats\Middleware;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response;
use Slim\Views\Twig;

class UnifiUserInfoMiddleware
{
    public function __construct(private Twig $twig)
    {
    }

    public function __invoke(Request $request, RequestHandler $handler): Response
    {
        $unifi_connection = $request->getAttribute('unifi_connection');

        $this->twig->getEnvironment()->addGlobal('unifi_user', $unifi_connection->list_self()[0]);

        return $handler->handle($request);
    }
}
