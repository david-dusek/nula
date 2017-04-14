<?php

namespace Nula\I18n;

class LanguagesManager {

  const LANG_CS = 'cs';
  const LANG_EN = 'en';
  const LANG_CS_ABBREVIATION = 'CZ';
  const LANG_EN_ABBREVIATION = 'EN';
  const LANG_KEY = 'lang';

  /**
   * @param string $currentLanguage
   * @return string \Nula\I18n\LanguagesManager
   */
  public function getAlternativeLanguage(string $currentLanguage): string {
    $this->checkSupportedLanguage($currentLanguage);

    return $this->isEnglish($currentLanguage) ? self::LANG_CS : self::LANG_EN;
  }

  /**
   * @return string
   */
  public function getDefaultLanguage(): string {
    return self::LANG_CS;
  }

  /**
   * @param mixed[] $array
   * @return string
   */
  public function getLanguageFromArray(array $array): string {
    if (!isset($array[self::LANG_KEY])) {
      $language = $this->getDefaultLanguage();
    } else {
      $this->checkSupportedLanguage($array[self::LANG_KEY]);
      $language = $array[self::LANG_KEY];
    }

    return $language;
  }

  /**
   * @param mixed[] $array
   * @param string $language
   */
  public function setLanguageToArray(array &$array, string $language) {
    $this->checkSupportedLanguage($language);
    $array[self::LANG_KEY] = $language;
  }

  /**
   * @param string $language
   * @return \Symfony\Bridge\Twig\Extension\TranslationExtension
   * @throws \InvalidArgumentException
   */
  public function createTranslatorExstension(string $language): \Symfony\Bridge\Twig\Extension\TranslationExtension {
    $this->checkSupportedLanguage($language);
    $languageCode = $language == self::LANG_EN ? 'en_US' : 'cs_CZ';
    $translator = new \Symfony\Component\Translation\Translator($languageCode,
            new \Symfony\Component\Translation\MessageSelector());
    $translator->setFallbackLocales(['cs_CZ']);
    $translator->addLoader('php', new \Symfony\Component\Translation\Loader\PhpFileLoader());
    $translator->addResource('php', '../app/lang/cs_CZ.php', 'cs_CZ');
    $translator->addResource('php', '../app/lang/en_US.php', 'en_US');

    return new \Symfony\Bridge\Twig\Extension\TranslationExtension($translator);
  }

  /**
   * @param string $language
   * @return string
   */
  public function getLanguageAbbreviation(string $language): string {
    return $this->isEnglish($language) ? self::LANG_EN_ABBREVIATION : self::LANG_CS_ABBREVIATION;
  }

  /**
   * @param string $language
   * @throws \InvalidArgumentException
   */
  private function checkSupportedLanguage(string $language) {
    if ($language !== self::LANG_CS && $language !== self::LANG_EN) {
      throw new \InvalidArgumentException("Language $language is not supported yet.");
    }
  }

  /**
   * @param string $language
   * @return bool
   */
  private function isEnglish(string $language): bool {
    return $language == self::LANG_EN;
  }

}