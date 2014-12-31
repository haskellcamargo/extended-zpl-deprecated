<?php
namespace core\interpreter;

class Variable 
{
  public $key
       , $value;

  public function __construct($key, $value) 
  {
    $this->key   = $key;
    $this->value = $value;
    Linker::bindVariable($this);
  }
}