<?php

namespace Nula;

interface NullObject {
  public function isNull(): bool;
  public function setNull(bool $isNull);
}