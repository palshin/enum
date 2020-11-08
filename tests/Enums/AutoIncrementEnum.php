<?php

namespace Palshin\Enum\Tests\Enums;

use Palshin\Enum\Enum;

class AutoIncrementEnum extends Enum
{
  public static function values(): array
  {
    return [
      'A' => 1,
      'B',
      'C',
      'E' => 5,
      'F',
      'G',
      'H' => 'FOO',
      'I',
    ];
  }
}
