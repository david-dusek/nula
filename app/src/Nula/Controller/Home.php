<?php

namespace Nula\Controller;

class Home extends \Nula\Controller\Base {

  /**
   * @param \Slim\Http\Request $request
   * @param \Slim\Http\Response $response
   * @param array $args
   * @return \Psr\Http\Message\ResponseInterface
   * @throws \Exception
   */
  public function actionHomepage(\Slim\Http\Request $request, \Slim\Http\Response $response, array $args): \Psr\Http\Message\ResponseInterface {
    return $this->createTwigLocalizedResponse($request, $response, $args, 'home/homepage.twig', ['activeLink' => 'homepage']);
  }

}