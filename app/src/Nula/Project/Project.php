<?php

namespace Nula\Project;

class Project implements \Nula\NullObject {

  /**
   * @var bool
   */
  private $isNull = false;

  /**
   * @var string
   */
  private $rewrite;

  /**
   * @var string
   */
  private $mainImagePublicSourceName;

  /**
   * @var string
   */
  private $name;

  /**
   * @var string
   */
  private $typology;

  /**
   * @var string
   */
  private $place;

  /**
   * @var string[] 
   */
  private $authors = [];

  /**
   * @var string[] 
   */
  private $cooperation = [];

  /**
   * @var string
   */
  private $study;

  /**
   * @var string
   */
  private $realization;

  /**
   * @var string
   */
  private $competition;

  /**
   * @var string
   */
  private $publication;

  public function isNull(): bool {
    return $this->isNull;
  }

  public function setNull(bool $isNull) {
    $this->isNull = $isNull;
  }

  public function getRewrite() {

    return $this->rewrite;
  }

  public function setRewrite(string $rewrite) {
    $this->rewrite = $rewrite;
  }

  public function getMainImagePublicSourceName() {
    return $this->mainImagePublicSourceName;
  }

  public function setMainImagePublicSourceName(string $mainImagePublicSourceName) {
    $this->mainImagePublicSourceName = $mainImagePublicSourceName;
  }

  public function getName() {
    return $this->name;
  }

  public function setName(string $name) {
    $this->name = $name;
  }

  public function getTypology() {
    return $this->typology;
  }

  public function setTypology(string $typology) {
    $this->typology = $typology;
  }

  public function getPlace() {
    return $this->place;
  }

  public function setPlace(string $place) {
    $this->place = $place;
  }

  public function getAuthors(): array {
    return $this->authors;
  }

  public function setAuthors(array $authors) {
    $this->authors = $authors;
  }

  public function getCooperation(): array {
    return $this->cooperation;
  }

  public function setCooperation(array $cooperation) {
    $this->cooperation = $cooperation;
  }

  public function getStudy() {
    return $this->study;
  }

  public function setStudy(string $study) {
    $this->study = $study;
  }

  public function getRealization() {
    return $this->realization;
  }

  public function setRealization(string $realization) {
    $this->realization = $realization;
  }

  public function getCompetition() {
    return $this->competition;
  }

  public function setCompetition(string $competition) {
    $this->competition = $competition;
  }

  public function getPublication() {
    return $this->publication;
  }

  public function setPublication(string $publication) {
    $this->publication = $publication;
  }

}