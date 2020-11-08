<?php

namespace Palshin\Enum\Exceptions;

class EnumException extends \Exception
{
  public function __construct(string $message)
  {
    parent::__construct($message);
  }
}
