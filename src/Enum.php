<?php
declare(strict_types=1);

namespace Palshin\Enum;

use BadMethodCallException;
use InvalidArgumentException;
use JsonSerializable;
use Palshin\Enum\Exceptions\UndefinedPropertyEnumException;
use Palshin\Enum\Exceptions\WritingToReadOnlyPropertyEnumException;
use ReflectionClass;
use Stringable;

/**
 * @property-read int|float|string $value
 * @property-read string $name
 * @property-read string $label
 * @property-read string|null $description
 */
abstract class Enum implements JsonSerializable, Stringable
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
   * @psalm-return array<empty, empty>
   */
  public static function labels(): array
  {
    return [];
  }

  /**
   * @psalm-return array<empty, empty>
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
  public static function __callStatic(string $name, array $arguments): static
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
   * Return enum values implementing by class constants.
   *
   * @psalm-return array<string, mixed>
   */
  private static function getEnumMembersFromClassConstants(): array
  {
    $reflection = new ReflectionClass(static::class);

    return $reflection->getConstants();
  }

  /**
   * Return enum values implementing by values function.
   *
   * @psalm-return array<string, float|int|string>
   */
  private static function getEnumMembersFromValuesFunc(): array
  {
    return self::getAutoIncrementedValues(
      static::values()
    );
  }

  /**
   * @psalm-return array<string,int|float|string>
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
   * @psalm-return array<empty, empty>
   */
  public static function values(): array
  {
    return [];
  }

  /**
   * @psalm-return array<string, float|int|string>
   */
  private static function getEnumMembersFromDocComments(): array
  {
    $values = [];
    $pattern = '/@method[\s]+static[\s]+self[\s]([\w]+)\(\)[\s]+(=|)[\s]+([a-zA-Z0-9_.]+|)/';
    $reflection = new ReflectionClass(static::class);
    $docComment = $reflection->getDocComment();
    if (!$docComment) {
      return [];
    }

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

  public static function fromValue(int|float|string $value): static
  {
    $name = array_search($value, self::toArray());

    if (!$name) {
      $enumClass = static::class;
      throw new InvalidArgumentException(
        "No found members for enum $enumClass which have value $value"
      );
    }

    return self::fromName($name);
  }

  /**
   * @psalm-return array<string, float|int|string>
   */
  public static function toArray(): array
  {
    $array = [];
    foreach (self::enumMembers() as $enumMember) {
      $array[$enumMember->name] = $enumMember->value;
    }

    return $array;
  }

  public static function fromName(string $name): static
  {
    return static::$name();
  }

  /**
   * Return array of enum values.
   *
   * @psalm-return array<int|float|string>
   */
  public static function toValues(): array
  {
    return array_values(static::toArray());
  }

  /**
   * @return string[]
   */
  public static function toNames(): array
  {
    return array_keys(static::toArray());
  }

  /**
   * Return all enum members.
   *
   * @return static[]
   * @psalm-return list<static>
   */
  public static function all(): array
  {
    return array_values(self::enumMembers());
  }

  /**
   * @param string $name
   * @return null|float|int|string
   * @throws UndefinedPropertyEnumException
   */
  public function __get(string $name): string|int|float|null
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
   * Prevent creating protected properties in successors.
   *
   * @throws WritingToReadOnlyPropertyEnumException
   */
  public function __set(string $name, mixed $value): void
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
