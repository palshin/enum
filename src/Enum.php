<?php

namespace Palshin\Enum;

use BadMethodCallException;
use JsonSerializable;
use Palshin\Enum\Exceptions\UndefinedPropertyEnumException;
use Palshin\Enum\Exceptions\WritingToReadOnlyPropertyEnumException;
use ReflectionClass;
use ReflectionException;

/**
 * Class Enum
 * @package Palshin\Enum
 * @property-read int|float|string $value
 * @property-read string $name
 * @property-read string $label
 * @property-read string|null $description
 */
abstract class Enum implements JsonSerializable
{
  /**
   * @var array<string,array<string,static>>
   */
  protected static array $enumMembers = [];

  /**
   * @var int|float|string
   */
  protected int|float|string $value;

  /**
   * @var string
   */
  private string $name;

  /**
   * @var string
   */
  private string $label;

  /**
   * @var string|null
   */
  private ?string $description;

  /**
   * Enum constructor.
   * @param string $name
   * @param int|float|string $value
   */
  private function __construct(string $name, int|float|string $value)
  {
    $this->name = $name;
    $this->value = $value;
    $this->label = static::labels()[$name] ?? $name;
    $this->description = static::descriptions()[$name] ?? null;
  }

  /**
   * @return array<string,string>
   */
  public static function labels(): array
  {
    return [];
  }

  /**
   * @return array<string,string>
   */
  public static function descriptions(): array
  {
    return [];
  }

  /**
   * @param string $name
   * @param array $arguments
   * @return static
   */
  public static function __callStatic(string $name, array $arguments): self
  {
    $enumMembers = self::enumMembers();
    if (isset($enumMembers[$name])) {
      return $enumMembers[$name];
    }

    $enumClass = static::class;

    throw new BadMethodCallException(
      "No found members for enum $enumClass which have name $name"
    );
  }

  /**
   * @return static[]
   */
  private static function enumMembers(): array
  {
    if (empty(self::$enumMembers[static::class])) {
      $enumValues = self::getEnumMembersFromClassConstants()
        + self::getEnumMembersFromValuesFunc()
        + self::getEnumMembersFromDocComments();

      self::$enumMembers[static::class] = [];
      foreach ($enumValues as $name => $value) {
        self::$enumMembers[static::class][$name] = new static($name, $value);
      }
    }

    return self::$enumMembers[static::class];
  }

  /**
   * Return enum values implementing by class constants
   *
   * @return array
   */
  private static function getEnumMembersFromClassConstants(): array
  {
    $reflection = new ReflectionClass(static::class);

    return $reflection->getConstants();
  }

  /**
   * Return enum values implementing by values function
   *
   * @return array
   */
  private static function getEnumMembersFromValuesFunc(): array
  {
    return self::getAutoIncrementedValues(
      static::values()
    );
  }

  /**
   * @param array $values
   * @return array<string,int|float|string>
   */
  private static function getAutoIncrementedValues(array $values): array
  {
    $incrementedValues = [];
    $initialValue = null;
    foreach ($values as $key => $value) {
      if (is_int($key)) {
        $incrementedValues[$value] = $initialValue === null ? $value : ++$initialValue;
      } else {
        $incrementedValues[$key] = $value;
        $initialValue = is_int($value) ? $value : null;
      }
    }

    return $incrementedValues;
  }

  /**
   * @return array<string,int|float|bool|string>
   */
  public static function values(): array
  {
    return [];
  }

  private static function getEnumMembersFromDocComments(): array
  {
    $values = [];
    $pattern = '/@method[\s]+static[\s]+self[\s]([\w]+)\(\)[\s]+(=|)[\s]+([a-zA-Z0-9_.]+|)/';
    $reflection = new ReflectionClass(static::class);
    $docComment = $reflection->getDocComment();
    $matches = [];
    preg_match_all($pattern, $docComment, $matches);
    foreach ($matches[1] as $index => $name) {
      $value = $matches[3][$index];
      if (!empty($value)) {
        $values[$name] = is_numeric($value) ? $value + 0 : $value;
      } else {
        $values[] = $name;
      }
    }

    return self::getAutoIncrementedValues($values);
  }

  /**
   * @param int|float|string $value
   * @return static
   * @throws ReflectionException
   */
  public static function fromValue(int|float|string $value): self
  {
    $name = array_search($value, self::toArray());

    return self::fromName($name);
  }

  /**
   * Return array of enum values
   *
   * @psalm-return array<int|float|string>
   * @return array
   */
  public static function toValues(): array
  {
    return array_values(static::toArray());
  }

  public static function toArray(): array
  {
    $array = [];
    foreach (self::enumMembers() as $enumMember) {
      $array[$enumMember->name] = $enumMember->value;
    }

    return $array;
  }

  /**
   * @param string $name
   * @return static
   */
  public static function fromName(string $name): self
  {
    return static::$name();
  }

  /**
   * @return string[]
   */
  public static function toNames(): array
  {
    return array_keys(static::toArray());
  }

  /**
   * Return all enum members
   *
   * @return array
   */
  public static function all(): array
  {
    return array_values(self::enumMembers());
  }

  /**
   * @param string $name
   * @return float|int|string|null
   */
  public function __get(string $name): float|int|string|null
  {
    if ($name === 'name') {
      return $this->name;
    }
    if ($name === 'value') {
      return $this->value;
    }
    if ($name === 'label') {
      return $this->label;
    }
    if ($name === 'description') {
      return $this->description;
    }

    throw new UndefinedPropertyEnumException($name, static::class);
  }

  /**
   * Prevent creating protected properties in successors
   *
   * @param $name
   * @param $value
   * @throws WritingToReadOnlyPropertyEnumException
   */
  public function __set($name, $value): void
  {
    if (in_array($name, ['name', 'value', 'label', 'description'])) {
      throw new WritingToReadOnlyPropertyEnumException($name, static::class);
    }
  }

  public function id(): string
  {
    $enumClass = static::class;

    return "$enumClass::{$this->name}";
  }

  public function __toString(): string
  {
    return (string)$this->value;
  }

  public function jsonSerialize(): int|string|float
  {
    return $this->value;
  }
}
