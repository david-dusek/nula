<?php

namespace Nula\Controller\Error;


use Nula\Controller\Base;
use Slim\Http\Request;
use Slim\Http\Response;

class ApplicationError extends Base {

  public function __invoke(Request $request, Response $response, \Exception $exception) {
    $routeArguments = [];
    $routeArguments[$this->localeManager::LOCALE_KEY] = $this->localeManager->getLocaleCodeFromPath($request);
    $templateParameters = [
      'errorTitleTransKey' => 'chybaAplikace',
      'errorDescriptionTransKey' => 'chybaAplikacePopis',
    ];

    return $this->createTwigLocalizedResponse($request, $response->withStatus(503), $routeArguments, 'error.twig', $templateParameters);
  }

}