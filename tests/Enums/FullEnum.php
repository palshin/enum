<?php

namespace Palshin\Enum\Tests\Enums;

use Palshin\Enum\Enum;

/**
 * @method static self A()
 * @method static self B()
 */
class FullEnum extends Enum
{
  public static function values(): array
  {
    return [
      'A' => 'FOO',
      'B' => 'BAR',
    ];
  }

  public static function labels(): array
  {
    return [
      'A' => 'A Label',
      'B' => 'B Label',
    ];
  }

  public static function descriptions(): array
  {
    return [
      'A' => 'A description',
      'B' => 'B description',
    ];
  }
}
