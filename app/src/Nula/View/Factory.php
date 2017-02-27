<?php

namespace Nula\View;

class Factory {

  /**
   * @var string
   */
  private $cache;

  public function __construct(string $cache = '') {
    $this->cache = $cache;
  }

  /**
   * @param \Slim\Http\Request $request
   * @param \Slim\Router $router
   * @param string $language
   * @return \Slim\Views\Twig
   */
  public function createTwigI18nView(\Slim\Http\Request $request, \Slim\Router $router, string $language): \Slim\Views\Twig {
    $view = new \Slim\Views\Twig('../app/templates', ['cache' => empty($this->cache) ? false : $this->cache]);
    $view->addExtension($this->createTwigExtension($request, $router));
    $view->addExtension($this->createTranslatorExstension($language));

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

  /**
   * @param string $language
   * @return \Symfony\Bridge\Twig\Extension\TranslationExtension
   */
  private function createTranslatorExstension(string $language): \Symfony\Bridge\Twig\Extension\TranslationExtension {
    $languageCode = $language == 'en' ? 'en_US' : 'cs_CZ';
    $translator = new \Symfony\Component\Translation\Translator($languageCode,
            new \Symfony\Component\Translation\MessageSelector());
    $translator->setFallbackLocales(['cs_CZ']);
    $translator->addLoader('php', new \Symfony\Component\Translation\Loader\PhpFileLoader());
    $translator->addResource('php', '../app/lang/cs_CZ.php', 'cs_CZ');
    $translator->addResource('php', '../app/lang/en_US.php', 'en_US');

    return new \Symfony\Bridge\Twig\Extension\TranslationExtension($translator);
  }

}