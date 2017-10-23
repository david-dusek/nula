<?php

namespace Nula\Controller;

class Base {

  /**
   * @var \Nula\I18n\LocaleManager
   */
  private $localeManager;

  /**
   * @var \Slim\Router
   */
  protected $router;

  /**
   * @var \Interop\Container\ContainerInterface
   */
  private $container;

  /**
   * @param \Interop\Container\ContainerInterface $container
   * @throws \Psr\Container\ContainerExceptionInterface
   * @throws \Psr\Container\NotFoundExceptionInterface
   */
  public function __construct(\Interop\Container\ContainerInterface $container) {
    $this->localeManager = $container->get('localeManager');
    $this->router = $container->get('router');
    $this->container = $container;
  }

  /**
   * @param string $serviceName
   * @return mixed
   * @throws \Psr\Container\ContainerExceptionInterface
   * @throws \Psr\Container\NotFoundExceptionInterface
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
   * @throws \Exception
   */
  protected function createTwigLocalizedResponse(\Slim\Http\Request $request, \Slim\Http\Response $response,
                                                 array $routerArgs, string $template, array $templateParameters = []): \Psr\Http\Message\ResponseInterface {
    $view = $this->localeManager->createLocalizedTwigView($request, $routerArgs);
    $localizedViewParameters = $this->localeManager->getLocalizedTwigViewTemplateParameters($request, $routerArgs);

    return $view->render($response, $template, array_merge($templateParameters, $localizedViewParameters));
  }

}