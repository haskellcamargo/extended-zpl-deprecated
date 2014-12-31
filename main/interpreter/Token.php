<?php
namespace main\interpreter;

class Token
{
  public $key, $value;

  public function __construct($key, $value)
  {
    $this->key   = $key;
    $this->value = $value;
  }

  public function __toString()
  {
    return "[\"{$this->value}\", " . Tokenizer::$tokenNames[$this->key] . "]";
  }
}