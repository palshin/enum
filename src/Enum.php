<?php

namespace Palshin\Enum;

use BadMethodCallException;
use JsonSerializable;
use Palshin\Enum\Exceptions\UndefinedPropertyEnumException;
use Palshin\Enum\Exceptions\WritingToReadOnlyPropertyEnumException;
use ReflectionClass;
use ReflectionException;
use TypeError;

/**
 * Class Enum
 * @package Palshin\Enum
 * @property-read int|float|bool|string $value
 * @property-read string $name
 * @property-read string $label
 * @property-read string|null $description
 */
abstract class Enum implements JsonSerializable
{
  /**
   * @var array<string,array<string,static>>
   */
  private static $enumMembers = [];

  /**
   * @var int|float|bool|string
   */
  private $value;

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
   * @param int|float|bool|string $value
   */
  private function __construct(string $name, $value)
  {
    self::checkValue($value);
    $this->name = $name;
    $this->value = $value;
    $this->label = static::labels()[$name] ?? $name;
    $this->description = static::descriptions()[$name] ?? null;
  }

  /**
   * Return array of enum values
   *
   * @return array
   * @throws ReflectionException
   */
  public static function toValues(): array
  {
    return array_map(
      fn ($enumMember) => $enumMember->value,
      self::enumMembers()
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
   * @return array<string,int|float|string|bool>
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
   * @return array<string|int,int|float|bool|string>
   */
  protected static function values(): array
  {
    return [];
  }

  /**
   * @return array<string,string>
   */
  protected static function labels(): array
  {
    return [];
  }

  /**
   * @return array<string,string>
   */
  protected static function descriptions(): array
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
      if (! empty($value)) {
        $values[$name] = is_numeric($value) ? $value + 0 : $value;
      } else {
        $values[] = $name;
      }
    }

    return self::getAutoIncrementedValues($values);
  }

  /**
   * Check possible values of enum members
   *
   * @param $value
   * @return bool
   */
  private static function checkValue($value): bool
  {
    if (is_scalar($value)) {
      return true;
    }
    $enumClass = static::class;

    throw new TypeError("Only primitive values are allowed for enum $enumClass members");
  }

  /**
   * @param string $name
   * @param array $arguments
   * @return static
   * @throws ReflectionException
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
   * @param int|float|bool|string $value
   * @return static
   * @throws ReflectionException
   */
  public static function fromValue($value): self
  {
    $name = array_search($value, self::toValues());

    return self::fromName($name);
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
   * @param string $name
   * @return bool|float|int|string|null
   */
  public function __get(string $name)
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
   * @return null
   */
  public function __set($name, $value)
  {
    if (in_array($name, ['name', 'value', 'label', 'description'])) {
      throw new WritingToReadOnlyPropertyEnumException($name, static::class);
    }
  }

  /**
   * Return all enum members
   *
   * @return static[]
   * @throws ReflectionException
   */
  public static function all(): array
  {
    return array_values(self::enumMembers());
  }

  /**
   * @return string
   */
  public function __toString(): string
  {
    $enumClass = static::class;

    return "$enumClass::{$this->name}";
  }

  /**
   * @return bool|float|int|string
   */
  public function jsonSerialize()
  {
    return $this->value;
  }
}
