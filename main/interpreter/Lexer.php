<?php
namespace main\interpreter;

abstract class Lexer
{
    const EOF      = -1;
    const EOF_TYPE =  1;
    public $input
           , $position = 0
           , $char;

    public function __construct($input) 
    {
      $this->input = $input;
      $this->char  = $input[$this->position];
    }

    public function consume() 
    {
      $this->position++;
      if ($this->position >= strlen($this->input))
        $this->char = self :: EOF;
      else
        $this->char = $this->input[$this->position];
    }

    public abstract function nextToken();
    public abstract function tokenName($type);
}