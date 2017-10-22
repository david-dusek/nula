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
   * @var \Nula\I18n\LocaleManager
   */
  private $localeManager;
  
  /**
   * @var \Interop\Container\ContainerInterface
   */
  private $container;

  /**
   * @param \Interop\Container\ContainerInterface $container
   */
  public function __construct(\Interop\Container\ContainerInterface $container) {
    $this->router = $container->get('router');
    $this->viewFactory = $container->get('viewFactory');
    $this->localeManager = $container->get('localeManager');
    $this->container = $container;
  }
  
  /**
   * @param string $serviceName
   * @return mixed
   */
  protected function getService(string $serviceName) {
    return $this->container->get($serviceName);
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
    $locale = $this->localeManager->getLocaleCodeFromArray($routerArgs);
    $view = $this->viewFactory->createTwigI18nView($request, $this->router, $locale);
    $templateParameters['lang'] = $this->localeManager->localeUnderscoreToDashFormat($locale);
    $templateParameters['request'] = $request;
    $templateParameters['localeManager'] = $this->localeManager;

    return $view->render($response, $template, $templateParameters);
  }

}