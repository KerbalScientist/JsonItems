<?php

namespace JsonItems;

/**
 * Позволяет записывать и читать из json-файла большой массив небольших объектов,
 *  не загружая весь файл в память. Полученный файл - json, сжатый с помощью gzip.
 * У файла свой формат, но его можно читать с помощью любого JSON-парсера.
 *
 * @author anton
 */
class File {
  protected $handle;
  protected $header = '["JsonItemsV1"';

  public static function reader($path, $assoc = false) {
    return new Reader($path, $assoc);
  }

  public static function writer($path) {
    return new Writer($path);
  }
}
