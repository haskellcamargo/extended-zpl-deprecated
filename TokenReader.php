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

  require_once 'Parser.php';
  class TokenReader extends Parser {
    public function __construct(Lexer $source) {
      parent :: __construct($source);
    }

    public function stmt() {
      if ($this->lookahead->key === Tokenizer :: T_DECLARE)
        $this->decl();
      else if ($this->lookahead->key === Tokenizer :: T_VARIABLE)
        $this->variable();
    }

    public function decl() {
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
      $this->match(Tokenizer :: T_DECLARAT);
      $this->match(Tokenizer :: T_DECLSTRING);
    }

    public function variable() {
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
      $this->literal();      # |
      $this->varReference(); # |
      $this->comparison();   # |
      $this->arithmetic();   # |
    }

    public function arithmetic() {
      if ($this->lookahead->key === Tokenizer :: T_PLUS) {
        $this->match(Tokenizer :: T_PLUS);
        $this->term();
      }
      else if ($this->lookahead->key === Tokenizer :: T_MINUS) {
        $this->match(Tokenizer :: T_MINUS);
        $this->term();
      }
    }

    public function term() {
      if ($this->lookahead->key === Tokenizer :: T_TIMES) {
        $this->match(Tokenizer :: T_TIMES);
        $this->factor();
      }
      else if ($this->lookahead->key === Tokenizer :: T_DIVISION) {
        $this->match(Tokenizer :: T_DIVISION);
        $this->factor();
      }
    }

    public function factor() {
      $this->expr();
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

    public function literal() {
      $this->boolean(); # |
      $this->nil();     # |

      if ($this->lookahead->key === Tokenizer :: T_STRING)
        $this->match(Tokenizer :: T_STRING);
      else if ($this->lookahead->key === Tokenizer :: T_INT)
        $this->match(Tokenizer :: T_INT);
    }

    public function boolean() {
      if ($this->lookahead->key === Tokenizer :: T_TRUE)
        $this->match(Tokenizer :: T_TRUE);
      else if ($this->lookahead->key === Tokenizer :: T_FALSE)
        $this->match(Tokenizer :: T_FALSE);
    }

    public function nil() {
      if ($this->lookahead->key === Tokenizer :: T_NIL)
        $this->match(Tokenizer :: T_NIL);
    }
  }