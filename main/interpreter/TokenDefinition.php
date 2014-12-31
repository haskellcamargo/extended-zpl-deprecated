<?php
namespace main\interpreter;

final class TokenDefinition 
{
  public function T_COMMA()
  {
    $buffer = $this->char;
    $this->consume();
    return new Token(Tokenizer::T_COMMA, $this->char);
  }

  public function T_COMMENT()
  {
    while ($this->char != "\n" && $this->char != EOF_TYPE)
      $this->consume();
  }

  public function T_ASSIGN()
  {
    $buffer = $this->char; # '<'
    $this->consume();
    $buffer .= $this->char; # Expects be '-'
    
    if ($this->char == "-") {
      $this->consume();
      return new Token(Tokenizer::T_ASSIGN, $buffer);
    }

    throw new Exception("Misunderstod Syntax: Assignment operator is '<-', not '{$buffer}'.");
  }

  public function T_DECLARAT() {
    $this->consume(); # Consumes @.
    $buffer = "";

    if (StateChecker::startsIdentifier()) {
      do {
        $buffer .= $this->char;
        $this->consume();
      } while (StateChecker::holdsIdentifier());
      return new Token(Tokenizer::T_DECLARAT, $buffer);
    }
    throw new Exception("Misunderstod Syntax: Identifiers must start with letters or '_'. Not with '{$this->char}'.");
  }

  public function T_DECLSTRING() {
    $this->consume(); # Consumes {
    $buffer = "";

    if ($this->char == ":") {
      $this->consume(); # Consumes :
      CONTINUE_DECLARE:
      do {
        if ($this->char == Tokenizer::EOF)
          throw new Exception("Misunderstod Syntax: Expecting ':}' after declaration string. Got [EOF].");
        $buffer .= $this->char;
        $this->consume();
      } while ($this->char != ":");
      
      $this->consume(); # Consumes :

      if ($this->char == "}") { # Ends T_DECLSTRING
        $this->consume();
        return new Token(Tokenizer::T_DECLSTRING, $buffer); 
      } else {
        $buffer .= ":";
        goto CONTINUE_DECLARE;
      }
    }
    throw new Exception("Misunderstod Syntax: Declaration strings must contain ':' after '{'. Not '{$this->char}'.");
  }

  public function T_IDENTIFIER() {
    $buffer = "";
    do {
      $buffer .= $this->char;
      $this->consume();
    } while (StateChecker::holdsIdentifier());

    switch ($buffer):
      case "True":
        return new Token(Tokenizer::T_TRUE, $buffer);
      case "False":
        return new Token(Tokenizer::T_FALSE, $buffer);
      case "Nil":
        return new Token(Tokenizer::T_NIL, $buffer);
      case "declare":
        return new Token(Tokenizer::T_DECLARE, $buffer);
      case "variable":
        return new Token(Tokenizer::T_VARIABLE, $buffer);
      default:
        return new Token(Tokenizer::T_IDENTIFIER, $buffer);
    endswitch;
  }

  public function T_INT()
  {
    $buffer = "";
    do {
      $buffer .= $this->char;
      $this->consume();
    } while (StateChecker::holdsInt());

    return new Token(Tokenizer::T_INT, (int) $buffer);
  }

  /* # In the moment we'll not be parsing new lines.
  public function T_NEWLINE() {
    # Get the first newline, ignore the rest.
    $buffer = $this->char;
    while ($this->char == "\n" || $this->char == "\r" || $this->char == "\r\n")
      $this->consume();
    return new Token(Tokenizer::T_NEWLINE, "");
  }*/

  public function T_PERIOD()
  {
    $buffer = $this->char;
    $this->consume();
    return new Token(Tokenizer::T_PERIOD, $this->char);
  }

  public function T_STRING()
  {
    $this->consume(); # Consumes opening "
    $buffer = "";
    while (StateChecker::holdsString()) {
      $buffer .= $this->char;
      $this->consume();
    }
    $this->consume(); # Consumes closing "
    return new Token(Tokenizer::T_STRING, $buffer);
  }

  public function T_DEFVAR()
  {
    $this->consume(); # Consumes $.
    $buffer = "";
    if (StateChecker::startsIdentifier()) {
      do {
        $buffer .= $this->char;
        $this->consume();
      } while (StateChecker::holdsIdentifier());
      return new Token(Tokenizer::T_DEFVAR, $buffer);
    }
    throw new Exception("Misunderstod Syntax: Identifiers must start with letters or '_'. Not with '{$this->char}'.");
  }

  public function T_WHITESPACE()
  {
    while (ctype_space($this->char))
      $this->consume();
  }
}