<?php
namespace core\interpreter;

final class StateChecker
{
  function startsIdentifier()
  {
    return preg_match("/[a-zA-Z_]/", $this->char);
  }

  function holdsCall()
  {
    if ($this->char != Tokenizer::EOF)
      return preg_match("/[a-zA-Z_\d-]/", $this->char);
  }

  function holdsIdentifier()
  {
    if ($this->char != Tokenizer::EOF)
      return preg_match("/[a-zA-Z_\d]/", $this->char);
  }

  function holdsString()
  {
    if ($this->char != Tokenizer::EOF)
      return $this->char != '"';
  }

  function holdsInt()
  {
    if ($this->char != Tokenizer::EOF)
      return ctype_digit($this->char);
  }
}