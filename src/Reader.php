<?php

namespace JsonItems;

/**
 * Читает значения из файла JsonItems.
 *
 * @author anton
 */
class Reader extends File implements \Iterator {

  protected $current;
  protected $valid = false;
  protected $path;
  protected $key = 0;
  protected $assoc;

  public function __construct($path, $assoc = false) {
    $this->path = $path;
    $this->assoc = $assoc;
    $this->open($path);
  }

  protected function open($path) {
    $this->close();
    $this->handle = fopen("compress.zlib://$path", 'r');
    if (!$this->handle) {
      throw new JsonItemsException("Cannot open file '$path' for reading.");
    }
    $header = trim(fgets($this->handle));
    if ($header !== $this->header) {
      throw new JsonItemsException('Wrong file header.');
    }
    $this->valid = true;
    $this->next();
  }

  public function readNextItem() {
    if (!$this->handle) {
      $this->valid = false;
      return;
    }
    $json_item = "";
    while (($line = fgets($this->handle)) && trim($line, " \t\n\r") !== $this->boundary) {
      $json_item .= $line;
    }
    if ($line === FALSE) {
      // Последний элемент - нужно удалить закрывающую скобку массива и закрыть файл.
      $json_item = preg_replace('/\]\s*$/', '', $json_item);
      $this->close();
    }
    $result = json_decode($json_item, $this->assoc);
    $this->checkJsonError();
    return $result;
  }

  public function current() {
    return $this->current;
  }

  public function key() {
    return $this->key;
  }

  public function next() {
    $this->current = $this->readNextItem();
    return $this->current;
  }

  public function rewind() {
    $this->open($this->path);
  }

  public function valid() {
    return $this->valid;
  }

  public function __destruct() {
    $this->close();
  }

  public function close() {
    if ($this->handle) {
      fclose($this->handle);
    }
    $this->handle = false;
  }

}
