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

  require_once 'StateChecker.php';
  require_once 'AmbiguitySolver.php';

  final class TokenDefinition {
    function T_COMMA() {
      $this->consume();
      return new Token(Tokenizer :: T_COMMA, ",");
    }

    function T_COMMENT() {
      while ($this->char != "\n" && $this->char != EOF_TYPE)
        $this->consume();
    }

    function T_ASSIGN() {
      $buffer = $this->char; # '<'
      $this->consume();
      $buffer .= $this->char; # Expects be '-'
      if ($this->char == "-") {
        $this->consume();
        return new Token(Tokenizer :: T_ASSIGN, $buffer);
      }
      throw new Exception("Misunderstod Syntax: Assignment operator is '<-', not '{$buffer}'.");
    }

    function T_CALL() {
      $this->consume(); # Consumes :.
      $buffer = "";
      if (StateChecker :: startsIdentifier()) {
        do {
          $buffer .= $this->char;
          $this->consume();
        } while (StateChecker :: holdsCall());
        return new Token(Tokenizer :: T_CALL, $buffer);
      }
      throw new Exception("Function call expects an identifier derivation. Got '{$this->char}' after ':'.");
      
    }

    function T_DECLARAT() {
      $this->consume(); # Consumes @.
      $buffer = "";
      if (StateChecker :: startsIdentifier()) {
        do {
          $buffer .= $this->char;
          $this->consume();
        } while (StateChecker :: holdsIdentifier());
        return new Token(Tokenizer :: T_DECLARAT, $buffer);
      }
      throw new Exception("Misunderstod Syntax: Identifiers must start with letters or '_'. Not with '{$this->char}'.");
    }

    function T_DECLSTRING() {
      $this->consume(); # Consumes {
      $buffer = "";

      if ($this->char == ":") {
        $this->consume(); # Consumes :
        CONTINUE_DECLARE:
        do {
          if ($this->char == Tokenizer :: EOF)
            throw new Exception("Misunderstod Syntax: Expecting ':}' after declaration string. Got [EOF].");
          $buffer .= $this->char;
          $this->consume();
        } while ($this->char != ":");
        
        $this->consume(); # Consumes :

        if ($this->char == "}") { # Ends T_DECLSTRING
          $this->consume();
          return new Token(Tokenizer :: T_DECLSTRING, $buffer); 
        } else {
          $buffer .= ":";
          goto CONTINUE_DECLARE;
        }
      }
      throw new Exception("Misunderstod Syntax: Declaration strings must contain ':' after '{'. Not '{$this->char}'.");
    }

    function T_DEFVAR() {
      $this->consume(); # Consumes $.
      $buffer = "";
      if (StateChecker :: startsIdentifier()) {
        do {
          $buffer .= $this->char;
          $this->consume();
        } while (StateChecker :: holdsIdentifier());
        return new Token(Tokenizer :: T_DEFVAR, $buffer);
      }
      throw new Exception("Misunderstod Syntax: Identifiers must start with letters or '_'. Not with '{$this->char}'.");
    }

    function T_DIVISION() {
      $this->consume();
      return new Token(Tokenizer :: T_DIVISION, "/");
    }

    function T_EQUAL() {
      $this->consume();
      return new Token(Tokenizer :: T_EQUAL, "=");
    }

    function T_IDENTIFIER() {
      $buffer = "";
      do {
        $buffer .= $this->char;
        $this->consume();
      } while (StateChecker :: holdsIdentifier());

      switch ($buffer):
        case "True":
          return new Token(Tokenizer :: T_TRUE, $buffer);
        case "False":
          return new Token(Tokenizer :: T_FALSE, $buffer);
        case "Nil":
          return new Token(Tokenizer :: T_NIL, $buffer);
        case "declare":
          return new Token(Tokenizer :: T_DECLARE, $buffer);
        case "variable":
          return new Token(Tokenizer :: T_VARIABLE, $buffer);
        case "do":
          return new Token(Tokenizer :: T_DO, $buffer);
        default:
          return new Token(Tokenizer :: T_IDENTIFIER, $buffer);
      endswitch;
    }

    function T_INT() {
      $buffer = "";
      do {
        $buffer .= $this->char;
        $this->consume();
      } while (StateChecker :: holdsInt());

      return new Token(Tokenizer :: T_INT, (int) $buffer);
    }

    function T_LBRACK() {
      $this->consume();
      return new Token(Tokenizer :: T_LBRACK, "[");
    }

    function T_LPAREN() {
      $this->consume();
      return new Token(Tokenizer :: T_LPAREN, "(");
    }

    function T_MINUS() {
      $this->consume();
      return new Token(Tokenizer :: T_MINUS, "-");
    }

    /* # In the moment we'll not be parsing new lines.
    function T_NEWLINE() {
      # Get the first newline, ignore the rest.
      $buffer = $this->char;
      while ($this->char == "\n" || $this->char == "\r" || $this->char == "\r\n")
        $this->consume();
      return new Token(Tokenizer :: T_NEWLINE, "");
    }*/

    function T_PERIOD() {
      $this->consume();
      return new Token(Tokenizer :: T_PERIOD, ".");
    }

    function T_PLUS() {
      $this->consume();
      return new Token(Tokenizer :: T_PLUS, "+");
    }

    function T_POW() {
      $this->consume();
      return new Token(Tokenizer :: T_POW, "^");
    }

    function T_RBRACK() {
      $this->consume();
      return new Token(Tokenizer :: T_RBRACK, "]");
    }

    function T_RPAREN() {
      $this->consume();
      return new Token(Tokenizer :: T_RPAREN, ")");
    }

    function T_SEPARATOR() {
      $this->consume();
      return new Token(Tokenizer :: T_SEPARATOR, "/");
    }

    function T_STRING() {
      $this->consume(); # Consumes opening "
      $buffer = "";
      while (StateChecker :: holdsString()) {
        $buffer .= $this->char;
        $this->consume();
      }
      $this->consume(); # Consumes closing "
      return new Token(Tokenizer :: T_STRING, $buffer);
    }

    function T_TIMES() {
      $this->consume();
      return new Token(Tokenizer :: T_TIMES, "*");
    }

    function T_WHITESPACE() {
      while (ctype_space($this->char))
        $this->consume();
    }
  }