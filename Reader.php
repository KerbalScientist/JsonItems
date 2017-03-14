<?php

namespace JsonItems;

/**
 * Читает значения из файла JsonItems.
 *
 * @author anton
 */
class Reader extends File implements \Iterator {

  protected $current;
  protected $path;
  protected $key = 0;
  protected $assoc;

  public function __construct($path, $assoc = false) {
    $this->path = $path;
    $this->assoc = $assoc;
    $this->open($path);
  }

  protected function open($path) {
    $this->handle = fopen("compress.zlib://$path", 'r');
    if (!$this->handle) {
      throw new JsonItemsException("Cannot open file '$path' for reading.");
    }
    $header = trim(fgets($this->handle));
    if ($header !== $this->header) {
      throw new JsonItemsException('Wrong file header.');
    }
    $this->next();
  }

  public function readNextItem() {
    $json_item = "";
    while (($line = fgets($this->handle)) && trim($line, " \t\n\r") !== $this->boundary) {
      $json_item .= $line;
    }
    if (!$line) {
      // Последний элемент - нужно удалить конец массива.
      $json_item = preg_replace('/\]\s*$/', '', $json_item);
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
    return !is_null($this->current);
  }

  public function __destruct() {
    fclose($this->handle);
  }

}
