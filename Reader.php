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
    $header = trim(fgets($this->handle));
    if ($header !== $this->header) {
      throw new Exception('Wrong file header.');
    }
    $this->next();
  }

  public function readNextItem() {
    $line = fgets($this->handle);
    if (!$line && $line === ']') {
      return;
    }
    $json_size = (int) trim($line, ", \n");
    if (!$json_size) {
      return;
    }
    $json_item = fread($this->handle, $json_size);
    fgets($this->handle);
    return json_decode($json_item, $this->assoc);
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
