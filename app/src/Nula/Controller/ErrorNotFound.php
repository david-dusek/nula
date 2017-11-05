<?php

namespace Nula\Controller;


use Slim\Http\Request;
use Slim\Http\Response;

class ErrorNotFound extends Base {

  /**
   * @param Request $request
   * @param Response $response
   * @return \Psr\Http\Message\ResponseInterface
   * @throws \Exception
   */
  public function __invoke(Request $request, Response $response) {
    $routeArguments = [];
    $routeArguments[$this->localeManager::LOCALE_KEY] = $this->localeManager->getLocaleCodeFromPath($request);

    return $this->createTwigLocalizedResponse($request, $response->withStatus(404), $routeArguments, 'error/404.twig');
  }

}