<?php

namespace Palshin\Enum\Tests;

use Palshin\Enum\Exceptions\UndefinedPropertyEnumException;
use Palshin\Enum\Exceptions\WritingToReadOnlyPropertyEnumException;
use Palshin\Enum\Tests\Enums\AutoIncrementEnum;
use Palshin\Enum\Tests\Enums\ConstEnum;
use Palshin\Enum\Tests\Enums\DocCommentEnum;
use Palshin\Enum\Tests\Enums\FullEnum;
use Palshin\Enum\Tests\Enums\ValueEnum;
use Palshin\Enum\Tests\Enums\WrongTypeEnum;
use PHPUnit\Framework\TestCase;
use TypeError;

class EnumTest extends TestCase
{
  public function test_const_enum_can_create(): void
  {
    $instance = ConstEnum::FOO();
    $this->assertInstanceOf(ConstEnum::class, $instance);
  }

  public function test_const_enum_can_return_value(): void
  {
    $this->assertEquals(1, ConstEnum::FOO()->value);
  }

  public function test_const_enum_can_return_all_values(): void
  {
    $values = ConstEnum::toArray();
    $this->assertEquals([
      'FOO' => 1,
      'BAR' => 2,
    ], $values);
  }

  public function test_value_enum_can_create(): void
  {
    $instance = ValueEnum::FOO();
    $this->assertInstanceOf(ValueEnum::class, $instance);
  }

  public function test_autoincrement_enum_can_autoincrement(): void
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
    ], AutoIncrementEnum::toArray());
  }

  public function test_doc_comment_enum_can_initialize(): void
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
    ], DocCommentEnum::toArray());
  }

  public function test_same_enum_members_are_equals(): void
  {
    $this->assertTrue(
      DocCommentEnum::A() === DocCommentEnum::A()
    );
  }

  public function test_different_enum_members_are_not_equals(): void
  {
    $this->assertFalse(
      DocCommentEnum::A() === DocCommentEnum::B()
    );
  }

  public function test_enum_member_string_represent(): void
  {
    $this->assertEquals(
      (string)DocCommentEnum::A()->value,
      (string)DocCommentEnum::A()
    );
  }

  public function test_enum_only_scalar_type(): void
  {
    $this->expectException(TypeError::class);
    WrongTypeEnum::A();
  }

  public function test_enum_can_initialize_by_value(): void
  {
    $enumMember = DocCommentEnum::fromValue('BAR');
    $this->assertTrue(
      $enumMember instanceof DocCommentEnum
       && $enumMember->value === 'BAR'
    );
  }

  public function test_enum_can_initialize_by_name(): void
  {
    $enumMember = DocCommentEnum::fromName('BAR');
    $this->assertTrue(
      $enumMember instanceof DocCommentEnum
      && $enumMember->value === 'BAR'
    );
  }

  public function test_all_method_return_all_instances(): void
  {
    $this->assertEquals(
      [
        ConstEnum::FOO(),
        ConstEnum::BAR(),
      ],
      ConstEnum::all()
    );
  }


  public function test_writing_to_enum_properties_throws_exception(): void
  {
    $this->expectException(WritingToReadOnlyPropertyEnumException::class);
    $enumMember = DocCommentEnum::BAR();
    $enumMember->value = 'New value';
  }


  public function test_calling_to_undeclared_members_throws_exception(): void
  {
    $this->expectException(\BadMethodCallException::class);
    DocCommentEnum::Underclared();
  }

  public function test_calling_to_undeclared_properties_throws_exception(): void
  {
    $this->expectException(UndefinedPropertyEnumException::class);
    $enumMember = DocCommentEnum::A();
    $enumMember->vaLue;
  }

  public function test_convert_to_json(): void
  {
    $this->assertJsonStringEqualsJsonString(
      '{"BAR":"BAR"}',
      json_encode(['BAR' => DocCommentEnum::BAR()])
    );
  }

  public function test_values_func_override_doc_comment(): void
  {
    $this->assertEquals('FOO', FullEnum::A()->value);
  }

  public function test_can_access_name(): void
  {
    $this->assertEquals(
      'A',
      FullEnum::A()->name
    );
  }

  public function test_can_access_value(): void
  {
    $this->assertEquals(
      'BAR',
      FullEnum::B()->value
    );
  }

  public function test_can_access_label(): void
  {
    $this->assertEquals(
      'A Label',
      FullEnum::A()->label
    );
  }

  public function test_can_access_description(): void
  {
    $this->assertEquals(
      'A description',
      FullEnum::A()->description
    );
  }
}
