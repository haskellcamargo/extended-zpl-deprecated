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

  /**
   * TODO:
   * CREATE PARSING FOR EXPRESSIONS BY FOLLOWING THE GRAMMAR:
   * HOLY SHIT! I ALWAYS FALL IN AN INFINITE RECURSION WHEN I
   * TRY TO PARSE AN ARITHMETIC EXPRESSION!
   * WHAT THE HELL IS HAPPENING WITH ME TODAY!?
   *
   * expr   -> expr + term
   *         | expr - term
   *         | term
   * term   -> term * factor
   *         | term / factor
   *         | factor
   * factor -> lit
   *         | varDef
   *         | comparison
   *         | ( expr )
   * lit    -> T_INT
   *         | T_STRING
   *         | T_TRUE
   *         | T_FALSE
   *         | T_NIL
   */

  require_once 'Parser.php';

  class TokenReader extends Parser {
    public function __construct(Lexer $source) {
      parent :: __construct($source);
    }

    public function stmt() {
      if ($this->lookahead->key === Tokenizer :: T_DECLARE)
        $this->optDeclare();
      else if ($this->lookahead->key === Tokenizer :: T_VARIABLE)
        $this->optVariable();
      else if ($this->lookahead->key === Tokenizer :: T_DO)
        $this->optDo();
    }

    public function optDo() {
      $this->match(Tokenizer :: T_DO);
      $this->calls();
      $this->match(Tokenizer :: T_PERIOD);
      $this->match(Tokenizer :: EOF_TYPE);
    }

    public function calls() {
      $this->call();
      while ($this->lookahead->key === Tokenizer :: T_COMMA) {
        $this->match(Tokenizer :: T_COMMA);
        $this->call();
      }
    }

    public function call() {
      if ($this->lookahead->key === Tokenizer :: T_SEPARATOR)
        $this->match(Tokenizer :: T_SEPARATOR);
      else {
        $this->match(Tokenizer :: T_CALL);
        $this->match(Tokenizer :: T_LBRACK);
        $this->arguments();
        $this->match(Tokenizer :: T_RBRACK);
      }
    }

    public function arguments() {
      if ($this->lookahead->key === Tokenizer :: T_STRING) {
        $this->match(Tokenizer :: T_STRING);
        if ($this->lookahead->key === Tokenizer :: T_COMMA) {
          $this->match(Tokenizer :: T_COMMA);
          $this->arguments();
        }
      }
  
      else if ($this->lookahead->key === Tokenizer :: T_INT) {
        $this->match(Tokenizer :: T_INT);
        if ($this->lookahead->key === Tokenizer :: T_COMMA) {
          $this->match(Tokenizer :: T_COMMA);
          $this->arguments();
        }
      }

      else if ($this->lookahead->key === Tokenizer :: T_TRUE) {
        $this->match(Tokenizer :: T_TRUE);
        if ($this->lookahead->key === Tokenizer :: T_COMMA) {
          $this->match(Tokenizer :: T_COMMA);
          $this->arguments();
        }
      }

      else if ($this->lookahead->key === Tokenizer :: T_FALSE) {
        $this->match(Tokenizer :: T_FALSE);
        if ($this->lookahead->key === Tokenizer :: T_COMMA) {
          $this->match(Tokenizer :: T_COMMA);
          $this->arguments();
        }
      }

      else if ($this->lookahead->key === Tokenizer :: T_NIL) {
        $this->match(Tokenizer :: T_NIL);
        if ($this->lookahead->key === Tokenizer :: T_COMMA) {
          $this->match(Tokenizer :: T_COMMA);
          $this->arguments();
        }
      }

      else if ($this->lookahead->key === Tokenizer :: T_IDENTIFIER) {
        $this->match(Tokenizer :: T_IDENTIFIER);
        if ($this->lookahead->key === Tokenizer :: T_COMMA) {
          $this->match(Tokenizer :: T_COMMA);
          $this->arguments();
        }
      }

      else if ($this->lookahead->key === Tokenizer :: T_DEFVAR) {
        $this->match(Tokenizer :: T_DEFVAR);
        if ($this->lookahead->key === Tokenizer :: T_COMMA) {
          $this->match(Tokenizer :: T_COMMA);
          $this->arguments();
        }
      }
    }

    public function optDeclare() {
      $this->match(Tokenizer :: T_DECLARE);
      $this->declarations();
      $this->match(Tokenizer :: T_PERIOD);
      $this->stmt();
    }

    public function declarations() {
      $this->declaration();
      while ($this->lookahead->key === Tokenizer :: T_COMMA) {
        $this->match(Tokenizer :: T_COMMA);
        $this->declaration();
      }
    }

    public function declaration() {
      $key   = $this->lookahead->value; $this->match(Tokenizer :: T_DECLARAT);
      $value = $this->lookahead->value; $this->match(Tokenizer :: T_DECLSTRING);
      (new Declaration($key, $value));
    }

    public function optVariable() {
      $this->match(Tokenizer :: T_VARIABLE);
      $this->varDefs();
      $this->match(Tokenizer :: T_PERIOD);
      $this->stmt();
    }

    public function varDefs() {
      $this->varDef();
      while ($this->lookahead->key === Tokenizer :: T_COMMA) {
        $this->match(Tokenizer :: T_COMMA);
        $this->varDef();
      }
    }

    public function varDef() {
      $this->match(Tokenizer :: T_DEFVAR);
      $this->match(Tokenizer :: T_ASSIGN);
      $this->expr();
    }

    public function expr() {
      $this->oblLiteral();
    }

    public function comparison() {
      if ($this->lookahead->key === Tokenizer :: T_EQUAL) {
        $this->match(Tokenizer :: T_EQUAL);
        $this->expr();
      }
      else if ($this->lookahead->key === Tokenizer :: T_DIFF) {
        $this->match(Tokenizer :: T_DIFF);
        $this->expr();
      }
    }

    public function varReference() {
      if ($this->lookahead->key === Tokenizer :: T_DEFVAR)
        $this->match(Tokenizer :: T_DEFVAR);
    }

    public function oblLiteral() {
      if ($this->lookahead->key === Tokenizer :: T_STRING)
        $this->match(Tokenizer :: T_STRING);
      else if ($this->lookahead->key === Tokenizer :: T_INT)
        $this->match(Tokenizer :: T_INT);
      else if ($this->lookahead->key === Tokenizer :: T_TRUE)
        $this->match(Tokenizer :: T_TRUE);
      else if ($this->lookahead->key === Tokenizer :: T_FALSE)
        $this->match(Tokenizer :: T_FALSE);
      else if ($this->lookahead->key === Tokenizer :: T_NIL)
        $this->match(Tokenizer :: T_NIL);
      else
        throw new Exception("Blank isn't a literal.");
    }
  }
