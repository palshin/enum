<?php

namespace Palshin\Enum\Tests\Enums;

use Palshin\Enum\Enum;

/**
 * @method static self FOO()
 * @method static self A() = 5
 * @method static self B()
 * @method static self C()
 * @method static self D() = 100500
 * @method static self E()
 * @method static self F() = BAZ
 * @method static self BAR()
 */
class DocCommentEnum extends Enum
{
}
