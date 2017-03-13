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
  protected $header = '["JsonItemsV2",';
  protected $boundary = ',"===JsonItemsV2=see0faequ7jeijuW6eev===",';

  public static function reader($path, $assoc = false) {
    return new Reader($path, $assoc);
  }

  public static function writer($path) {
    return new Writer($path);
  }

  protected function checkJsonError() {
    if (($error_code = json_last_error()) !== JSON_ERROR_NONE) {
      throw new JsonItemsException("Json error: "
          . json_last_error_msg() . " (code: $error_code)");
    }
  }
}

class JsonItemsException extends \Exception { }
