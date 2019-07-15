<?php

namespace Nula\Controller;

use Interop\Container\ContainerInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Slim\Http\Request;
use Slim\Http\Response;

class Base {

  /**
   * @var \Nula\I18n\LocaleManager
   */
  protected $localeManager;

  /**
   * @var \Slim\Router
   */
  protected $router;

  /**
   * @var ContainerInterface
   */
  private $container;

  /**
   * @param ContainerInterface $container
   * @throws ContainerExceptionInterface
   * @throws NotFoundExceptionInterface
   */
  public function __construct(ContainerInterface $container) {
    $this->localeManager = $container->get('localeManager');
    $this->router = $container->get('router');
    $this->container = $container;
  }

  /**
   * @param string $serviceName
   * @return mixed
   * @throws ContainerExceptionInterface
   * @throws NotFoundExceptionInterface
   */
  protected function getService(string $serviceName) {
    return $this->container->get($serviceName);
  }

  /**
   * @param Request $request
   * @param Response $response
   * @param array $routerArgs
   * @param string $template
   * @param array $templateParameters
   * @return \Psr\Http\Message\ResponseInterface
   * @throws \Exception
   */
  protected function createTwigLocalizedResponse(Request $request, Response $response,
                                                 array $routerArgs, string $template, array $templateParameters = []): \Psr\Http\Message\ResponseInterface {
    $view = $this->localeManager->createLocalizedTwigView($request, $routerArgs);
    $localizedViewParameters = $this->localeManager->getLocalizedTwigViewTemplateParameters($request, $routerArgs, $response);
    $localizedViewParameters['resources_version'] = 28;

    return $view->render($response, $template, array_merge($templateParameters, $localizedViewParameters));
  }

  /**
   * @param array $routerArgs
   * @return string
   */
  protected function getLocale(array $routerArgs) {
    return $this->localeManager->getLocaleCodeFromRouteArguments($routerArgs);
  }

}