<?php
/**
 * Created by PhpStorm.
 * User: david
 * Date: 22.10.17
 * Time: 16:29
 */

namespace Nula\I18n;


class Locale
{

  /**
   * @var string
   */
  private $abbreviation;

  /**
   * @var bool
   */
  private $isActive;

  /**
   * @var string
   */
  private $url;

  /**
   * Locale constructor.
   * @param string $abbreviation
   * @param string $url
   * @param bool $isActive
   */
  public function __construct(string $abbreviation, string $url, bool $isActive) {
    $this->abbreviation = $abbreviation;
    $this->url = $url;
    $this->isActive = $isActive;
  }

  /**
   * @return string
   */
  public function getAbbreviation(): string
  {
    return $this->abbreviation;
  }

  /**
   * @return bool
   */
  public function isActive(): bool
  {
    return $this->isActive;
  }

  /**
   * @return string
   */
  public function getUrl(): string
  {
    return $this->url;
  }

}