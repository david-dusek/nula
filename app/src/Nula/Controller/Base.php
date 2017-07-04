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
   * @var \Nula\I18n\LanguagesManager
   */
  private $languagesManager;
  
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
    $this->languagesManager = $container->get('languagesManager');
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
    $language = $this->languagesManager->getLanguageFromArray($routerArgs);
    $view = $this->viewFactory->createTwigI18nView($request, $this->router, $language);
    $templateParameters['lang'] = $language;
    $templateParameters['languageSwitchParameters'] = $this->getLanguageSwitchParameters($language, $request, $response);

    return $view->render($response, $template, $templateParameters);
  }

  /**
   * @param string $currentLanguage
   * @param \Slim\Http\Request $request
   * @param \Slim\Http\Response $response
   * @return string[]
   */
  private function getLanguageSwitchParameters(string $currentLanguage, \Slim\Http\Request $request,
                                               \Slim\Http\Response $response): array {
    $alternativeLanguage = $this->languagesManager->getAlternativeLanguage($currentLanguage);
    $route = $this->getRoute($request, $response);
    $routeName = $route->getName() ?? 'homepage';
    $routeArguments = $route->getArguments();
    $this->languagesManager->setLanguageToArray($routeArguments, $alternativeLanguage);

    return [
      'currentAbbreviation' => $this->languagesManager->getLanguageAbbreviation($currentLanguage),
      'alternativeAbbreviation' => $this->languagesManager->getLanguageAbbreviation($alternativeLanguage),
      'alternativeUrl' => $this->router->pathFor($routeName, $routeArguments),
    ];
  }

  /**
   * @param \Slim\Http\Request $request
   * @param \Slim\Http\Response $response
   * @return string
   */
  protected function getRouteName(\Slim\Http\Request $request, \Slim\Http\Response $response): string {
    $route = $this->getRoute($request, $response);

    return $route->getName() ?? 'homepage';
  }

  /**
   * @param \Slim\Http\Request $request
   * @param \Slim\Http\Response $response
   * @return \Slim\Route
   * @throws \Slim\Exception\NotFoundException
   */
  protected function getRoute(\Slim\Http\Request $request, \Slim\Http\Response $response): \Slim\Route {
    $route = $request->getAttribute('route'); /* @var $route \Slim\Route */
    if (!\is_object($route)) {
      throw new \Slim\Exception\NotFoundException($request, $response);
    }

    return $route;
  }

}