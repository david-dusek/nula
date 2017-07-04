<?php

namespace Nula\Project;

class Project {

  /**
   * @var string
   */
  private $rewrite;

  public function getRewrite(): string {

    return $this->rewrite;
  }

  public function setRewrite(string $rewrite) {
    $this->rewrite = $rewrite;
  }

}