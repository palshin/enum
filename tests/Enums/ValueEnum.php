<?php

namespace Palshin\Enum\Tests\Enums;

use Palshin\Enum\Enum;

class ValueEnum extends Enum
{
  public static function values(): array
  {
    return [
      'FOO' => 1,
      'BAR' => 2,
    ];
  }
}
