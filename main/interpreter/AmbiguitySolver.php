<?php
namespace core\interpreter;

class AmbiguitySolver
{
  public function SLASH() 
  {
    $this->consume();
    if ($this->char === "=") {
      $this->consume();
      return new Token(Tokenizer :: T_DIFF, "/=");
    }
  # return new Token(Tokenizer :: T_DIVISION, "/");
  }
}