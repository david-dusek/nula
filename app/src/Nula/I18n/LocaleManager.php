<?php

namespace Nula\I18n;

use Symfony\Component\Finder\Iterator\FilenameFilterIterator;
use Symfony\Component\Finder\SplFileInfo;

class LocaleManager {

  const LOCALE_KEY = 'lang';
  const DEFAULT_LOCALE = 'cs_CZ';
  const LOCALE_FILES_BASE_DIR = '../app/lang';
  const LOCALE_FILES_FORMAT = 'php';

  /**
   * @var \Slim\Router
   */
  private $router;

  /**
   * LocaleManager constructor.
   * @param \Slim\Router $router
   */
  public function __construct(\Slim\Router $router)
  {
    $this->router = $router;
  }

  /**
   * @param \Slim\Http\Request $request
   * @return array
   */
  public function getLocales(\Slim\Http\Request $request) {
    $route = $request->getAttribute('route'); /* @var $route \Slim\Route */
    $routeName = $route->getName() ?? 'homepage';
    $routeArguments = $route->getArguments();
    $localeFromRequest = $this->getLocaleCodeFromArray($routeArguments);

    $locales = [];
    foreach ($this->getSupportedLocales() as $locale) {
      $abbreviation = $this->localeToAbbreviation($locale);
      $routeArguments[self::LOCALE_KEY] = $this->localeUnderscoreToDashFormat($locale);
      $localeUrl = $this->router->pathFor($routeName, $routeArguments);
      $isActive = $localeFromRequest === $locale;
      $locales[] = new Locale($abbreviation, $localeUrl, $isActive);
    }

    return $locales;
  }

  /**
   * @param array $array
   * @return string
   */
  public function getLocaleCodeFromArray(array $array): string {
    if (isset($array[self::LOCALE_KEY])) {
      $localeCode = $this->localeDashToUnderscoreFormat($array[self::LOCALE_KEY]);
      $this->checkLocaleSupported($localeCode);
    } else {
      $localeCode = self::DEFAULT_LOCALE;
    }

    return $localeCode;
  }

  /**
   * @param string $locale
   * @return \Symfony\Bridge\Twig\Extension\TranslationExtension
   * @throws \Exception
   */
  public function createTranslatorExstension(string $locale): \Symfony\Bridge\Twig\Extension\TranslationExtension {
    $this->checkLocaleSupported($locale);
    $translator = new \Symfony\Component\Translation\Translator($locale,
      new \Symfony\Component\Translation\MessageSelector());
    $translator->setFallbackLocales([self::DEFAULT_LOCALE]);
    $translator->addLoader(self::LOCALE_FILES_FORMAT, new \Symfony\Component\Translation\Loader\PhpFileLoader());
    $translator->addResource(self::LOCALE_FILES_FORMAT, $this->getFileByLocaleCode($locale), $locale);

    return new \Symfony\Bridge\Twig\Extension\TranslationExtension($translator);
  }

  /**
   * @param string $localeCode
   * @return string
   * @throws \Exception
   */
  private function getFileByLocaleCode(string $localeCode): string {
    $filePath = self::LOCALE_FILES_BASE_DIR . '/' . $localeCode . '.' . self::LOCALE_FILES_FORMAT;
    if (is_file($filePath) && is_readable($filePath)) {
      return $filePath;
    }

    throw new \Exception("Locale file $filePath not exists or is not readable.");
  }

  /**
   * @param string $locale
   */
  private function checkLocaleSupported(string $locale) {
    $supportedLocales = $this->getSupportedLocales();

    if (!array_key_exists($locale, $supportedLocales)) {
      throw new \InvalidArgumentException("Locale '$locale' is not supported yet.");
    }
  }

  /**
   * @return string[]
   */
  private function getSupportedLocales(): array {
    $languagesDirectoryIterator = new \DirectoryIterator(self::LOCALE_FILES_BASE_DIR);
    $languagesFilesIterator = new FilenameFilterIterator($languagesDirectoryIterator, ['/[a-z]{2}_[A-Z]{2}\.php/'], []);

    $supportedLocales = [];
    foreach ($languagesFilesIterator as $item) { /* @var $item SplFileInfo */
      $localeCode = $item->getBasename('.' . $item->getExtension());
      $supportedLocales[$localeCode] = $localeCode;
    }

    return $supportedLocales;
  }

  /**
   * @param $locale
   * @return string
   */
  private function localeToAbbreviation($locale): string {
    return substr($locale, strpos($locale, '_') + 1);
  }

  /**
   * @param string $locale
   * @return string
   */
  public function localeUnderscoreToDashFormat(string $locale): string {
    return str_replace('_', '-', $locale);
  }

  /**
   * @param string $locale
   * @return string
   */
  private function localeDashToUnderscoreFormat(string $locale): string {
    return str_replace('-', '_', $locale);
  }

}