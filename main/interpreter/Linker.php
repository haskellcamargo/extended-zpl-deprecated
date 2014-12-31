<?php
namespace core\interpreter;

class Linker {
  static $declarations = []
       , $variables    = []
       , $calls        = [];

  public static function bindDeclaration(Declaration $decl)
  {
    array_push(self::$declarations, $decl);
  }

  public static function bindVariable(Variable $var)
  {
    foreach (self::$variables as $computed) {
      if ($computed->key === $var->key)
        throw new Exception("Variable '{$var->key}' is already defined. Variables are imutable.");
    }
    array_push(self::$variables, $var);  
  }

  public function bindCall(Call $call)
  {
    $stack = [];
    $arguments = array_filter(explode("<$>", $call->value));

    for ($i = 0, $arglen = sizeof($arguments); $i < $arglen; $i++) {
      $piece = $arguments[$i];
      if (substr($piece, strlen($piece) - 11) === "<\$variable>")
        # Search in variables virtual table.
        for ($j = 0, $varlen = sizeof(self::$variables); $j < $varlen; $j++) {
          $var = self::$variables[$j];
          if ($var->key === ($v = substr($piece, 0, strlen($piece) - 11)))
            array_push($stack, self::$variables[$j]->value);
          else
            throw new Exception("Undefined variable '{$v}'.");
            
        }
      else array_push($stack, $piece);
    }
    array_push(self::$calls, [$call->key, $stack]);
  }
}