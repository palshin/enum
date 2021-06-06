<?php

namespace Palshin\Enum\Exceptions;

class UndefinedPropertyEnumException extends EnumException
{
  public function __construct(string $member, string $enumClass)
  {
    parent::__construct(
      sprintf(
        'Property %s is undefined in enum %s',
        $member,
        $enumClass
      )
    );
  }
}
