<?php
namespace core\interpreter;

class TokenReader extends Parser {
  private $acc
        , $ax;  # Internal registers.

  public function __construct(Lexer $source) 
  {
    parent::__construct($source);
  }

  public function stmt()
  {
    if ($this->lookahead->key === Tokenizer::T_DECLARE)
      $this->optDeclare();
    else if ($this->lookahead->key === Tokenizer::T_VARIABLE)
      $this->optVariable();
    else if ($this->lookahead->key === Tokenizer::T_DO)
      $this->optDo();
  }

  public function optDo()
  {
    $this->match(Tokenizer::T_DO);
    $this->calls();
    $this->match(Tokenizer::T_PERIOD);
    $this->match(Tokenizer::EOF_TYPE);
    (new CodeGenerator(Linker::$declarations
                     , Linker::$calls));
  }

  public function calls()
  {
    $this->call();
    while ($this->lookahead->key === Tokenizer::T_COMMA) {
      $this->match(Tokenizer::T_COMMA);
      $this->call();
    }
  }

  public function call()
  {
    if ($this->lookahead->key === Tokenizer::T_SEPARATOR) {
      $this->acc = "field-separator";
      $this->ax  = "<$>"; # As much as it takes no args.
      $this->match(Tokenizer::T_SEPARATOR);
      (new Call($this->acc, $this->ax));
    }
    else {
      $this->acc = $this->lookahead->value; # Replaces function signature.
      $this->ax  = Null; # Empties $ax register.
      $this->match(Tokenizer::T_CALL);
      $this->match(Tokenizer::T_LBRACK);
      $this->arguments();
      $this->match(Tokenizer::T_RBRACK);
      (new Call($this->acc, $this->ax));
    }
  }

  public function arguments()
  {
    goto args;

    parseMoreArgs:
    if ($this->lookahead->key === Tokenizer::T_COMMA) {
      $this->match(Tokenizer::T_COMMA);
      $this->arguments();
    }

    args:
    if ($this->lookahead->key === Tokenizer::T_STRING) {
      $this->ax .= $this->lookahead->value . "<$>";
      $this->match(Tokenizer::T_STRING);
      goto parseMoreArgs;
    }

    else if ($this->lookahead->key === Tokenizer::T_INT) {
      $this->ax .= $this->lookahead->value . "<$>";
      $this->match(Tokenizer::T_INT);
      goto parseMoreArgs;
    }

    else if ($this->lookahead->key === Tokenizer::T_TRUE) {
      $this->ax .= $this->lookahead->value . "<$>";
      $this->match(Tokenizer::T_TRUE);
      goto parseMoreArgs;
    }

    else if ($this->lookahead->key === Tokenizer::T_FALSE) {
      $this->ax .= $this->lookahead->value . "<$>";
      $this->match(Tokenizer::T_FALSE);
      goto parseMoreArgs;
    }

    else if ($this->lookahead->key === Tokenizer::T_NIL) {
      $this->ax .= $this->lookahead->value . "<$>";
      $this->match(Tokenizer::T_NIL);
      goto parseMoreArgs;
    }

    else if ($this->lookahead->key === Tokenizer::T_IDENTIFIER) {
      $this->ax .= $this->lookahead->value . "<$>";
      $this->match(Tokenizer::T_IDENTIFIER);
      goto parseMoreArgs;
    }

    else if ($this->lookahead->key === Tokenizer::T_DEFVAR) {
      $this->ax .= $this->lookahead->value . "<\$variable><$>";
      $this->match(Tokenizer::T_DEFVAR);
      goto parseMoreArgs;
    }
  }

  public function optDeclare()
  {
    $this->match(Tokenizer::T_DECLARE);
    $this->declarations();
    $this->match(Tokenizer::T_PERIOD);
    $this->stmt();
  }

  public function declarations()
  {
    $this->declaration();
    while ($this->lookahead->key === Tokenizer::T_COMMA) {
      $this->match(Tokenizer::T_COMMA);
      $this->declaration();
    }
  }

  public function declaration()
  {
    $key   = $this->lookahead->value; $this->match(Tokenizer::T_DECLARAT);
    $value = $this->lookahead->value; $this->match(Tokenizer::T_DECLSTRING);
    (new Declaration($key, $value));
  }

  public function optVariable()
  {
    $this->match(Tokenizer::T_VARIABLE);
    $this->varDefs();
    $this->match(Tokenizer::T_PERIOD);
    $this->stmt();
  }

  public function varDefs()
  {
    $this->varDef();
    while ($this->lookahead->key === Tokenizer::T_COMMA) {
      $this->match(Tokenizer::T_COMMA);
      $this->varDef();
    }
  }

  public function varDef()
  {
    $key    = $this->lookahead->value; $this->match(Tokenizer::T_DEFVAR);
    $this->match(Tokenizer::T_ASSIGN);
    $value  = $this->lookahead->value; $this->expr();
    (new Variable($key, $value));
  }

  public function expr()
  {
    $this->oblLiteral();
  }

  // public function comparison()
 {
  //   if ($this->lookahead->key === Tokenizer::T_EQUAL) {
  //     $this->match(Tokenizer::T_EQUAL);
  //     $this->expr();
  //   }
  //   else if ($this->lookahead->key === Tokenizer::T_DIFF) {
  //     $this->match(Tokenizer::T_DIFF);
  //     $this->expr();
  //   }
  // }

  // public function varReference()
  // {
  //   if ($this->lookahead->key === Tokenizer::T_DEFVAR)
  //     $this->match(Tokenizer::T_DEFVAR);
  // }

  public function oblLiteral()
 {
    if ($this->lookahead->key === Tokenizer::T_STRING)
      $this->match(Tokenizer::T_STRING);
    else if ($this->lookahead->key === Tokenizer::T_INT)
      $this->match(Tokenizer::T_INT);
    else if ($this->lookahead->key === Tokenizer::T_TRUE)
      $this->match(Tokenizer::T_TRUE);
    else if ($this->lookahead->key === Tokenizer::T_FALSE)
      $this->match(Tokenizer::T_FALSE);
    else if ($this->lookahead->key === Tokenizer::T_NIL)
      $this->match(Tokenizer::T_NIL);
    else
      throw new Exception("Expressions accept just scalar values.");
  }
}
