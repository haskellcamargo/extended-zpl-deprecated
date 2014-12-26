<?php
  # Copyright (c) 2014 Marcelo Camargo <marcelocamargo@linuxmail.org>
  #
  # Permission is hereby granted, free of charge, to any person
  # obtaining a copy of this software and associated documentation files
  # (the "Software"), to deal in the Software without restriction,
  # including without limitation the rights to use, copy, modify, merge,
  # publish, distribute, sublicense, and/or sell copies of the Software,
  # and to permit persons to whom the Software is furnished to do so,
  # subject to the following conditions:
  #
  # The above copyright notice and this permission notice shall be
  # included in all copies or substantial of portions the Software.
  #
  # THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,
  # EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF
  # MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND
  # NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE
  # LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION
  # OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION
  # WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.

  define("EOF", 1);
  define("EOF_TYPE", -1);

  require_once 'Lexer.php';
  require_once 'StateChecker.php';
  require_once 'TokenDefinition.php';

  # Multiple inheritance simulation with StateChecker
  class Tokenizer extends Lexer {
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
    const T_PLUS       = 25;
    const T_MINUS      = 26;
    const T_DIVISION   = 27;
    const T_TIMES      = 28;
    const T_POW        = 29;
    const T_EQUAL      = 30;
    const T_DIFF       = 31;
    const T_CALL       = 32;
    const T_LPAREN     = 33;
    const T_RPAREN     = 34;
    const T_DO         = 35;
    const T_SEPARATOR  = 36;

    static $tokenNames = [
        'n/a', '[EOF]', 'T_NEWLINE', 'T_COMMENT', 'T_STRING', 'T_IDENTIFIER'
    , 'T_INTEGER', 'T_COLON', 'T_BEGIN', 'T_END', 'T_LBRACK', 'T_RBRACK'
    , 'T_PERIOD', 'T_DECLARAT', 'T_DECLSTRING', 'T_DEFVAR', 'T_ASSIGN'
    , 'T_TRUE', 'T_FALSE', 'T_AMP', 'T_INT', 'T_NIL', 'T_DECLARE', 'T_COMMA'
    , 'T_VARIABLE', 'T_PLUS', 'T_MINUS', 'T_DIVISION', 'T_TIMES', 'T_POW'
    , 'T_EQUAL', 'T_DIFF', 'T_CALL', 'T_LPAREN', 'T_RPAREN', 'T_DO'
    , 'T_SEPARATOR'
    ];

    public function __construct($input) {
        parent :: __construct($input);
    }

    public function nextToken() {
      while ($this->char != self :: EOF) {
        switch ($this->char) {
          case " ":
          case "\t":
          case "\r\n":
          case "\n":
          case "\r":
            TokenDefinition :: T_WHITESPACE();
            continue;
          case "%":
            TokenDefinition :: T_COMMENT();
            continue;
          case "@":
            return TokenDefinition :: T_DECLARAT();
          case "{":
            return TokenDefinition :: T_DECLSTRING();
          case "$":
            return TokenDefinition :: T_DEFVAR();
          case "<":
            return TokenDefinition :: T_ASSIGN();
          case '"':
            return TokenDefinition :: T_STRING();
          case ",":
            return TokenDefinition :: T_COMMA();
          case ".":
            return TokenDefinition :: T_PERIOD();
          #case "+":
          #  return TokenDefinition :: T_PLUS();
          #case "-":
          #  return TokenDefinition :: T_MINUS();
          #case "/":
            return AmbiguitySolver :: SLASH();
          #case "*":
          #  return TokenDefinition :: T_TIMES();
          #case "^":
          #  return TokenDefinition :: T_POW();
          #case "=":
          #  return TokenDefinition :: T_EQUAL();
          case "[":
            return TokenDefinition :: T_LBRACK();
          case "]":
            return TokenDefinition :: T_RBRACK();
          case ":":
            return TokenDefinition :: T_CALL();
          case "/":
            return TokenDefinition :: T_SEPARATOR();
          #case "(":
          #  return TokenDefinition :: T_LPAREN();
          #case ")":
          #  return TokenDefinition :: T_RPAREN();
          default:
            if ($this->char != self :: EOF) {
              if (preg_match("/\d/", $this->char))
                return TokenDefinition :: T_INT();
              if (StateChecker :: startsIdentifier())
                return TokenDefinition :: T_IDENTIFIER();
            }
            throw new Exception("Invalid Character: {$this->char} of Ord " . ord($this->char));
        }
      }
      return new Token(self::EOF_TYPE, "[EOF]");
    }

    public function tokenName($token) {
      return self :: $tokenNames[$token];
    }
  }