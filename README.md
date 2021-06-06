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
UserRole::all(); // [ UserRole::CLIENT(), UserRole::ADMIN(), UserRole::MANAGER() ]
UserRole::toArray(); // [ 'CLIENT' => 1, 'ADMIN' => 2, 'MANAGER' => 3 ]
UserRole::toValues(); // [1, 2, 3]
UserRole::toNames(); // ['CLIENT', 'ADMIN', 'MANAGER']
UserRole::CLIENT()->id(); // FQCN with enum member name: App\Enums\UserRole::CLIENT
echo UserRole::CLIENT(); // print "1"
echo json_encode(['enumMember' => UserRole::CLIENT()]); // print "{enumMember:1}"
```

## Installation

Install package using composer:
```bash
composer require epalshin/enum
```

## Usage
You can choose one of three ways to define enumeration members (or its combination).

PHPDoc comment:
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

Class constants:
```php
use Palshin\Enum\Enum;

final class UserRole extends Enum
{
  private const CLIENT = 1;
  private const ADMIN = 2;
  private const MANAGER = 3;
}
```

Static method:
```php
use Palshin\Enum\Enum;

final class UserRole extends Enum
{
  public static function values(): array
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

  public static function values(): array
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
In some cases may be useful to add meta information to your
enumeration class. You can do it like this:

```php
use Palshin\Enum\Enum;

/**
 * @method static self CLIENT()
 * @method static self MANAGER()
 * @method static self ADMIN()
*/
class UserRole extends Enum
{
  public static function labels(): array
  {
    return [
      'CLIENT' => 'Client account',
      'MANAGER' => 'Manager account',
      'ADMIN' => 'Administrator account'
    ];
  }

  public static function descriptions(): array
  {
    return [
      'CLIENT' => 'Online store buyer',
      'MANAGER' => 'The person who is responsible for interaction with the client',
      'ADMIN' => 'The person who controls the managers'
    ];
  }
}

UserRole::CLIENT()->label; // Client account
UserRole::MANAGER()->description; // The person who is responsible for interaction with the client
```


