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

  class IntermediateCodeGenerator {
    static $declarations = []
         , $variables    = []
         , $calls        = [];

    public static function bindDeclaration(Declaration $decl) {
      array_push(self :: $declarations, $decl);
    }

    public static function bindVariable(Variable $var) {
      foreach (self :: $variables as $computed) {
        if ($computed->key === $var->key)
          throw new Exception("Variable '{$var->key}' is already defined. Variables are imutable.");
      }
      array_push(self :: $variables, $var);  
    }

    public function bindCall(Call $call) {
      $stack = [];
      $arguments = array_filter(explode("<$>", $call->value));

      for ($i = 0, $arglen = sizeof($arguments); $i < $arglen; $i++) {
        $piece = $arguments[$i];
        if (substr($piece, strlen($piece) - 11) === "<\$variable>")
          # Search in variables virtual table.
          for ($j = 0, $varlen = sizeof(self :: $variables); $j < $varlen; $j++) {
            $var = self :: $variables[$j];
            if ($var->key === substr($piece, 0, strlen($piece) - 11))
              array_push($stack, self :: $variables[$j]->value);
          }
        else array_push($stack, $piece);
      }
      array_push(self :: $calls, [$call->key, $stack]);
    }
  }