<?php

namespace Nula\View;

class Factory {

  /**
   * @var string
   */
  private $cache;

  /**
   * @var \Nula\I18n\LocaleManager
   */
  private $languagesManager;

  /**
   * @param \Nula\I18n\LocaleManager $languageManager
   * @param string $cache
   */
  public function __construct(\Nula\I18n\LocaleManager $languageManager, string $cache = '') {
    $this->languagesManager = $languageManager;
    $this->cache = $cache;
  }

  /**
   * @param \Slim\Http\Request $request
   * @param \Slim\Router $router
   * @param string $locale
   * @return \Slim\Views\Twig
   */
  public function createTwigI18nView(\Slim\Http\Request $request, \Slim\Router $router, string $locale): \Slim\Views\Twig {
    $view = new \Slim\Views\Twig('../app/templates', ['cache' => empty($this->cache) ? false : $this->cache]);
    $view->addExtension($this->createTwigExtension($request, $router));
    $view->addExtension($this->languagesManager->createTranslatorExstension($locale));

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