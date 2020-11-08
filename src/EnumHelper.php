<?php

namespace Palshin\Enum;

class EnumHelper
{
  public static function incrementValuesAfterFirst(array $array): array
  {
    if (empty($array)) {
      return [];
    }
    $result = [];
    $initialKey = array_key_first($array);
    $initialValue = $array[$initialKey];
    $initialKey = is_int($initialKey) ? $initialValue : $initialKey;
    $result[$initialKey] = $initialValue;
    $i = 1;
    foreach (array_slice($array, 1) as $key => $value) {
      $resultKey = is_int($key) ? $value : $key;
      if (! is_int($initialValue)) {
        $result[$resultKey] = $value;
      } else {
        $result[$resultKey] = is_int($key) ? $initialValue + $i++ : $value;
      }
    }

    return $result;
  }

  /**
   * Split array to chunks by function
   *
   * @param array $array
   * @param callable $func
   * @return array
   */
  public static function chunksBy(array $array, callable $func): array
  {
    $chunks = [];
    $chunk = [];
    foreach ($array as $key => $value) {
      if ($func($value, $key) && count($chunk) !== 0) {
        $chunks[] = $chunk;
        $chunk = [];
      }
      $chunk[$key] = $value;
    }
    $chunks[] = $chunk;

    return $chunks;
  }
}
