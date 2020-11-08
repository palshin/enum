<?php

namespace Palshin\Enum\Tests\Enums;

use Palshin\Enum\Enum;

class WrongTypeEnum extends Enum
{
  private const A = [
    'foo' => 'bar',
  ];
}
