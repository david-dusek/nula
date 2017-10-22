<?php

namespace Nula\View;

class Factory {

  /**
   * @var string
   */
  private $cache;

  /**
   * @param string $cache
   */
  public function __construct(string $cache = '') {
    $this->cache = $cache;
  }

  /**
   * @param \Slim\Http\Request $request
   * @param \Slim\Router $router
   * @return \Slim\Views\Twig
   */
  public function createTwigView(\Slim\Http\Request $request, \Slim\Router $router): \Slim\Views\Twig {
    $view = new \Slim\Views\Twig('../app/templates', ['cache' => empty($this->cache) ? false : $this->cache]);
    $view->addExtension($this->createTwigExtension($request, $router));

    return $view;
  }

  /**
   * @param \Slim\Http\Request $request
   * @param \Slim\Router $router
   * @return \Slim\Views\TwigExtension
   */
  private function createTwigExtension(\Slim\Http\Request $request, \Slim\Router $router): \Slim\Views\TwigExtension {
    $basePath = rtrim(str_ireplace('index.php', '', $request->getUri()->getBasePath()), '/');

    return new \Slim\Views\TwigExtension($router, $basePath);
  }

}