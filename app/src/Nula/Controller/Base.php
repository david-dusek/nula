<?php

namespace Nula\Controller;

class Base {

  /**
   * @var \Slim\Router
   */
  protected $router;

  /**
   * @var \Nula\View\Factory
   */
  protected $viewFactory;

  /**
   * @param \Interop\Container\ContainerInterface $container
   */
  public function __construct(\Interop\Container\ContainerInterface $container) {
    $this->router = $container->get('router');
    $this->viewFactory = $container->get('viewFactory');
  }

  /**
   * @param \Slim\Http\Request $request
   * @param \Slim\Http\Response $response
   * @param array $routerArgs
   * @param string $template
   * @param array $templateParameters
   * @return \Psr\Http\Message\ResponseInterface
   */
  protected function createTwigI18nResponse(\Slim\Http\Request $request, \Slim\Http\Response $response,
                                            array $routerArgs, string $template, array $templateParameters = []): \Psr\Http\Message\ResponseInterface {
    $language = $this->getLanguageFromRouterArgs($routerArgs);
    $view = $this->viewFactory->createTwigI18nView($request, $this->router, $language);

    return $view->render($response, $template, $templateParameters);
  }

  /**
   * @param mixed[] $routerArgs
   * @return string
   */
  protected function getLanguageFromRouterArgs($routerArgs) {
    if (isset($routerArgs['lang']) && $routerArgs['lang'] == 'en') {
      return $routerArgs['lang'];
    } else {
      return 'cs';
    }
  }

}