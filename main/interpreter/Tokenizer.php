<?php
namespace main\interpreter;

# Multiple inheritance simulation with StateChecker
class Tokenizer extends Lexer
{
  const T_NEWLINE    = 2;
  const T_COMMENT    = 3;
  const T_STRING     = 4;
  const T_IDENTIFIER = 5;
  const T_INTEGER    = 6;
  const T_COLON      = 7;
  const T_BEGIN      = 8;
  const T_END        = 9;
  const T_LBRACK     = 10;
  const T_RBRACK     = 11;
  const T_PERIOD     = 12;
  const T_DECLARAT   = 13;
  const T_DECLSTRING = 14;
  const T_DEFVAR     = 15;
  const T_ASSIGN     = 16;
  const T_TRUE       = 17;
  const T_FALSE      = 18;
  const T_AMP        = 19;
  const T_INT        = 20;
  const T_NIL        = 21;
  const T_DECLARE    = 22;
  const T_COMMA      = 23;
  const T_VARIABLE   = 24;

  public static $tokenNames = [
    'n/a', '[EOF]', 'T_NEWLINE', 'T_COMMENT', 'T_STRING', 'T_IDENTIFIER'
    , 'T_INTEGER', 'T_COLON', 'T_BEGIN', 'T_END', 'T_LBRACK', 'T_RBRACK'
    , 'T_PERIOD', 'T_DECLARAT', 'T_DECLSTRING', 'T_DEFVAR', 'T_ASSIGN'
    , 'T_TRUE', 'T_FALSE', 'T_AMP', 'T_INT', 'T_NIL', 'T_DECLARE', 'T_COMMA'
    , 'T_VARIABLE'
  ];

  public function __construct($input) {
    if ( !$input )
      parent::__construct($input);
  }

  public function nextToken()
  {
    $this->consume();
    while ($this->char != self::EOF) {
      switch ($this->char) {
        case " ":
        case "\t":
        case "\r\n":
        case "\n":
        case "\r":
          TokenDefinition::T_WHITESPACE();
          continue;
        case "%":
          TokenDefinition::T_COMMENT();
          continue;
        case "@":
          return TokenDefinition::T_DECLARAT();
        case "{":
          return TokenDefinition::T_DECLSTRING();
        case "$":
          return TokenDefinition::T_DEFVAR();
        case "<":
          return TokenDefinition::T_ASSIGN();
        case '"':
          return TokenDefinition::T_STRING();
        case ",":
          return TokenDefinition::T_COMMA();
        case ".":
          return TokenDefinition::T_PERIOD();
        default:
          if ($this->char != self::EOF) {
            if (preg_match("/\d/", $this->char))
              return TokenDefinition::T_INT();
            if (StateChecker::startsIdentifier())
              return TokenDefinition::T_IDENTIFIER();
          }
          throw new \Exception("Invalid Character: {$this->char} of Ord " . ord($this->char));
      }
    }
    return new Token(self::EOF_TYPE, "[EOF]");
  }

  public function tokenName($token)
  {
    return self::$tokenNames[$token];
  }
}