<?php

namespace JsonItems;

/**
 * Записывает файл JsonItems.
 *
 * @author anton
 */
class Writer extends File {
  public function __construct($path) {
    $this->handle = fopen("compress.zlib://$path", 'w');
    fwrite($this->handle, $this->header . "\n");
  }

  /**
   * Записывает в файл массив значений.
   * @param array $items
   */
  public function write($items) {
    foreach ($items as $item) {
      $json = json_encode($item, JSON_UNESCAPED_UNICODE);
      $length = strlen($json);
      fwrite($this->handle, ",$length,\n$json\n");
    }
  }

  public function close() {
    fwrite($this->handle, ']');
    fclose($this->handle);
  }


  public function __destruct() {
    $this->close();
  }

}
