<?php
namespace core\interpreter;

class Declaration
{
  public $key
       , $value;

  public function __construct($key, $value)
  {
    $this->key   = $key;
    $this->value = $value;
    Linker :: bindDeclaration($this);
  }
}