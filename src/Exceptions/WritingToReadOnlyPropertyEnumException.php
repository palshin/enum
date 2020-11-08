<?php

namespace Palshin\Enum\Exceptions;

class WritingToReadOnlyPropertyEnumException extends EnumException
{
  public function __construct(string $property, string $enumClass)
  {
    parent::__construct(
      sprintf(
        'Prevent writing to read only property %s of enum class %s',
        $property,
        $enumClass
      )
    );
  }
}
