<?php

namespace Nula\Controller;

class Home extends \Nula\Controller\Base {

  /**
   * @param \Slim\Http\Request $request
   * @param \Slim\Http\Response $response
   * @param array $args
   * @return \Slim\Http\Response
   */
  public function actionHomepage(\Slim\Http\Request $request, \Slim\Http\Response $response, array $args): \Psr\Http\Message\ResponseInterface {
    return $this->createTwigI18nResponse($request, $response, $args, 'home/homepage.twig', ['activeLink' => 'homepage']);
  }

}