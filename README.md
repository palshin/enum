# Enums for PHP

This package offers strongly typed enums for PHP
with some nice distinctive features.

Quick functionality example:

```php
use Palshin\Enum\Enum;

/**
 * @method static self CLIENT() = 1
 * @method static self ADMIN()
 * @method static self MANAGER()
*/
final class UserRole extends Enum
{
}

UserRole::MANAGER() instanceof UserRole; // true
UserRole::MANAGER() === UserRole::MANAGER(); // true
UserRole::MANAGER()->value; // 3
UserRole::all(); // [ UserRole::Client(), UserRole::ADMIN(), UserRole::MANAGER() ]
UserRole::toValues(); // [ 'CLIENT' => 1, 'ADMIN' => 2, 'MANAGER' => 3 ]
```

## Installation

Install package using composer:
```bash
composer require palshin/enum
```

## Usage
You can choose one of three ways to define enumeration members (or its combination).

First:
```php
use Palshin\Enum\Enum;

/**
 * @method static self CLIENT() = 1
 * @method static self ADMIN()
 * @method static self MANAGER()
*/
final class UserRole extends Enum
{
}
```

Second:
```php
use Palshin\Enum\Enum;

final class UserRole extends Enum
{
  private const CLIENT = 1;
  private const ADMIN = 2;
  private const MANAGER = 3;
}
```

And third:
```php
use Palshin\Enum\Enum;

final class UserRole extends Enum
{
  protected static function values(): array
  {
    return [
      'CLIENT' => 1,
      'ADMIN' => 2,
      'MANAGER' => 3,
    ]; 
  }
}
```

Also you can combine few (or even all) methods in your declaration:

```php
use Palshin\Enum\Enum;

/**
 * @method static self CLIENT()
*/
final class UserRole extends Enum
{
  private const ADMIN = 2;

  protected static function values(): array
  {
    return [
      'MANAGER' => 3,
    ]; 
  }
}
```
> :exclamation: **If you decided to combine ways to declare enumeration members,**
> be careful with members intersection: name of enum member is unique for
> enum class so different values of same member can lead to unobvious errors
> in your code. The priority of names is: constant declaration, 
> functional declaration and PHPDoc commentary declaration. Names are case sensitive.

Personally I prefer the way with PHPDoc comment 
but I think if you want keep access to enumeration values 
without class wrapping and
get the IDE's autocompletion benefits
you can do something like this:
```php
use Palshin\Enum\Enum;

/**
 * @method static self CLIENT()
 * @method static self ADMIN()
 * @method static self MANAGER()
*/
final class UserRole extends Enum
{
  const CLIENT = 1;
  const ADMIN = 2;
  const MANAGER = 3;
}

UserRole::CLIENT === 1; // true
UserRole::CLIENT() instanceof UserRole; // true
```

