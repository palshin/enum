<?php

namespace Palshin\Enum\Tests;

use Palshin\Enum\Exceptions\UndefinedPropertyEnumException;
use Palshin\Enum\Exceptions\WritingToReadOnlyPropertyEnumException;
use Palshin\Enum\Tests\Enums\AutoIncrementEnum;
use Palshin\Enum\Tests\Enums\ConstEnum;
use Palshin\Enum\Tests\Enums\DocCommentEnum;
use Palshin\Enum\Tests\Enums\ValueEnum;
use Palshin\Enum\Tests\Enums\WrongTypeEnum;
use PHPUnit\Framework\TestCase;
use TypeError;

class EnumTest extends TestCase
{
  public function test_const_enum_can_create()
  {
    $instance = ConstEnum::FOO();
    $this->assertInstanceOf(ConstEnum::class, $instance);
  }

  public function test_const_enum_can_return_value()
  {
    $this->assertEquals(1, ConstEnum::FOO()->value);
  }

  public function test_const_enum_can_return_all_values()
  {
    $values = ConstEnum::toValues();
    $this->assertEquals([
      'FOO' => 1,
      'BAR' => 2,
    ], $values);
  }

  public function test_value_enum_can_create()
  {
    $instance = ValueEnum::FOO();
    $this->assertInstanceOf(ValueEnum::class, $instance);
  }

  public function test_autoincrement_enum_can_autoincrement()
  {
    $this->assertEquals([
      'A' => 1,
      'B' => 2,
      'C' => 3,
      'E' => 5,
      'F' => 6,
      'G' => 7,
      'H' => 'FOO',
      'I' => 'I',
    ], AutoIncrementEnum::toValues());
  }

  public function test_doc_comment_enum_can_initialize()
  {
    $this->assertEquals([
      'FOO' => 'FOO',
      'A' => 5,
      'B' => 6,
      'C' => 7,
      'D' => 100500,
      'E' => 100501,
      'F' => 'BAZ',
      'BAR' => 'BAR',
    ], DocCommentEnum::toValues());
  }

  public function test_same_enum_members_are_equals()
  {
    $this->assertTrue(
      DocCommentEnum::A() === DocCommentEnum::A()
    );
  }

  public function test_different_enum_members_are_not_equals()
  {
    $this->assertFalse(
      DocCommentEnum::A() === DocCommentEnum::B()
    );
  }

  public function test_enum_member_string_represent()
  {
    $this->assertEquals(
      '5',
      (string)DocCommentEnum::A()
    );
  }

  public function test_enum_only_scalar_type()
  {
    $this->expectException(TypeError::class);
    WrongTypeEnum::A();
  }

  public function test_enum_can_initialize_by_value()
  {
    $enumMember = DocCommentEnum::fromValue('BAR');
    $this->assertTrue(
      $enumMember instanceof DocCommentEnum
       && $enumMember->value === 'BAR'
    );
  }

  public function test_enum_can_initialize_by_name()
  {
    $enumMember = DocCommentEnum::fromName('BAR');
    $this->assertTrue(
      $enumMember instanceof DocCommentEnum
      && $enumMember->value === 'BAR'
    );
  }

  public function test_all_method_return_all_instances()
  {
    $this->assertEquals(
      [
        ConstEnum::FOO(),
        ConstEnum::BAR(),
      ],
      ConstEnum::all()
    );
  }

  public function test_writing_to_enum_properties_throws_exception()
  {
    $this->expectException(WritingToReadOnlyPropertyEnumException::class);
    $enumMember = DocCommentEnum::BAR();
    $enumMember->value = 'New value';
  }

  public function test_calling_to_underlared_members_throws_exception()
  {
    $this->expectException(\BadMethodCallException::class);
    DocCommentEnum::Underclared();
  }

  public function test_calling_to_underclared_properties_throws_exception()
  {
    $this->expectException(UndefinedPropertyEnumException::class);
    $enumMember = DocCommentEnum::A();
    $enumMember->vaLue;
  }
}
