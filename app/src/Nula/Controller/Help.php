<?php

namespace Nula\Controller;

class Help extends \Nula\Controller\Base {

  /**
   * @param \Slim\Http\Request $request
   * @param \Slim\Http\Response $response
   * @param array $args
   * @return \Psr\Http\Message\ResponseInterface
   */
  public function actionFAQ(\Slim\Http\Request $request, \Slim\Http\Response $response, array $args): \Psr\Http\Message\ResponseInterface {
    return $this->createTwigI18nResponse($request, $response, $args, 'help/faq.twig');
  }

}