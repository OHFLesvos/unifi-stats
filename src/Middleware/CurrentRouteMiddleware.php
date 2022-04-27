<?php

namespace OHF\UnifiStats\Middleware;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response;
use Slim\Routing\RouteContext;
use Slim\Views\Twig;

class CurrentRouteMiddleware
{
    public function __construct(private Twig $twig)
    {
    }

    public function __invoke(Request $request, RequestHandler $handler): Response
    {
        $routeContext = RouteContext::fromRequest($request);
        $route = $routeContext->getRoute();

        $this->twig->getEnvironment()->addGlobal('currentRoute', $route->getName());

        return $handler->handle($request);
    }
}
