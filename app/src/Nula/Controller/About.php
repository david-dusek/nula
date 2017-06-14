<?php

namespace Nula\Controller;

class About extends \Nula\Controller\Base {
  /**
   * @param \Slim\Http\Request $request
   * @param \Slim\Http\Response $response
   * @param array $args
   * @return \Psr\Http\Message\ResponseInterface
   */
  public function actionContact(\Slim\Http\Request $request, \Slim\Http\Response $response, array $args): \Psr\Http\Message\ResponseInterface {
    return $this->createTwigI18nResponse($request, $response, $args, 'about/contact.twig', ['activeLink' => 'contact']);
  }

  /**
   * @param \Slim\Http\Request $request
   * @param \Slim\Http\Response $response
   * @param array $args
   * @return \Psr\Http\Message\ResponseInterface
   */
  public function actionAtelier(\Slim\Http\Request $request, \Slim\Http\Response $response, array $args): \Psr\Http\Message\ResponseInterface {
    return $this->createTwigI18nResponse($request, $response, $args, 'about/atelier.twig', ['activeLink' => 'atelier']);
  }

}