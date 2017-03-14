<?php

namespace JsonItems;

/**
 * Записывает файл JsonItems.
 *
 * @author anton
 */
class Writer extends File {

  protected $empty = true;
  protected $path;

  public function __construct($path) {
    $this->handle = fopen("compress.zlib://$path", 'w');
    if (!$this->handle) {
      throw new JsonItemsException("Cannot open file '$path' for writing.");
    }
    $this->path = $path;
    fwrite($this->handle, $this->header . "\n");
  }

  /**
   * Записывает в файл массив значений.
   * @param array $items
   */
  public function write($items) {
    $boundary_length = strlen($this->boundary . "\n");
    foreach ($items as $item) {
      $ok = true;
      if ($this->empty) {
        $this->empty = false;
      } else {
        $ok &= (fwrite($this->handle, $this->boundary . "\n") === $boundary_length);
      }
      $json = json_encode($item, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) . "\n";
      $this->checkJsonError();
      $ok &= (fwrite($this->handle, $json) === strlen($json));
      if (!$ok) {
        throw new JsonItemsException("Unable to write file '$this->path'.");
      }
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
