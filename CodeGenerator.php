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

  require_once 'DispatchTable.php';

  class CodeGenerator {
    private $nonOptimizedCode = "";

    public function __construct($declarations, $callings) {
      foreach ($declarations as $decl)
        $this->nonOptimizedCode .= "^FX {$decl->key}: {$decl->value}\n";

      foreach ($callings as $call) {
        if (isset(DispatchTable :: $parse[$call[0]]))
          $this->nonOptimizedCode .=
            DispatchTable :: $parse[$call[0]];
        if (sizeof($call[1]) > 0) {

          # A big workaround to parse ZPL patterns! PLEASE, REMEMBER TO CHANGE THIS SH*T AS SOON
          # AS POSSIBLE! # USE array_reduce to simulate foldl.
          $stack  = [];
          $buffer = "";
          for ($i = 0; $i < sizeof(array_filter($call[1])); $i++) {
            if (preg_match("/[DNY]/", $call[1][$i])) {
              if (!isset($call[1][$i + 1])) {
                array_push($stack, $call[1][$i]);
              }
              else {
                if (preg_match("/[DNY]/", $call[1][$i + 1])) {
                  $buffer = $call[1][$i] . $call[1][$i + 1];
                  $i++; $i++;

                  for ($j = 0; $j < sizeof($call[1][$i]); $j++)
                    if (preg_match("/\d/", $call[1][$i])) {
                      $buffer .= $call[1][$i];
                      $j++;
                    }
                  array_push($stack, $buffer);
                }
              }
            } else {
              array_push($stack, $call[1][$i]);
            }
          }
        }

        if (empty($call[1])) # Empties the stack if there are no params.
          $stack = [];
        $this->nonOptimizedCode .= implode(",", $stack);
        $this->nonOptimizedCode .= "\n";
      }

      # Code has been successfully generated.
      file_put_contents("output.zpl", $this->nonOptimizedCode);
      echo "Successfully compiled.";
    }
  }