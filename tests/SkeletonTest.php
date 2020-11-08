<?php


namespace Palshin\PhpPackageSkeleton\Tests;

use Palshin\PhpPackageSkeleton\Skeleton;
use PHPUnit\Framework\TestCase;

class SkeletonTest extends TestCase
{
  public function test_basic()
  {
    $skeleton = new Skeleton();
    $this->assertSame(
      'Code something awesome!',
      $skeleton->giveTip()
    );
  }
}
