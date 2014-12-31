<?php
namespace core\interpreter;

abstract class Parser
{
  public $source, $lookahead;

  public function __construct(Tokenizer $source)
  {
    $this->source = $source;
    $this->consume();
  }

  public function match()
  {
    $args = func_get_args();
    foreach ($args as $x)
      if ($this->lookahead->key == $x)
      {
        $this->consume();
        return;
      }

    $expecting = "";
    foreach ($args as $x)
      $expecting .= $this->source->tokenName($x) . " or ";

    throw new Exception("Expecting token " .
      substr($expecting, 0, strlen($expecting) - 4) . ". Instead got " .
      $this->source->tokenName($this->lookahead->key));
  }

  public function consume()
  {
    $this->lookahead = $this->source->nextToken();
  }

  public function listDeclarations()
  {
    foreach (Linker::$declarations as $decl)
      echo "{$decl->key} = {$decl->value}\n";
  }

  public function listVariables()
  {
    foreach (Linker::$variables as $var)
      echo "{$var->key} = {$var->value}\n";
  }

  public function listCalls()
  {
    foreach (Linker::$calls as $call)
      var_dump($call);
  }
}
