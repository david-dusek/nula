<?php

namespace Nula\Controller\Error;


use Nula\Controller\Base;
use Slim\Http\Request;
use Slim\Http\Response;

class NotFound extends Base {

  /**
   * @param Request $request
   * @param Response $response
   * @return \Psr\Http\Message\ResponseInterface
   * @throws \Exception
   */
  public function __invoke(Request $request, Response $response) {
    $routeArguments = [];
    $routeArguments[$this->localeManager::LOCALE_KEY] = $this->localeManager->getLocaleCodeFromPath($request);
    $templateParameters = [
      'errorTitleTransKey' => 'chybaStrankaNenalezena',
      'errorDescriptionTransKey' => 'chybaStrankaNenalezenaPopis',
    ];

    return $this->createTwigLocalizedResponse($request, $response->withStatus(404), $routeArguments, 'error.twig', $templateParameters);
  }

}