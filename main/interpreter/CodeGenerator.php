<?php
namespace core\interpreter;

class CodeGenerator
{
  private $nonOptimizedCode = "";

  public function __construct($declarations, $callings) 
  {
    $this->nonOptimizedCode .= "^XA\n";

    foreach ($declarations as $decl)
      $this->nonOptimizedCode .= "^FX {$decl->key}: {$decl->value}\n";

    foreach ($callings as $call) {
      if (isset(DispatchTable::$parse[$call[0]]))
        $this->nonOptimizedCode .= DispatchTable::$parse[$call[0]];
      else
        throw new Exception("Call to undefined function '{$call[0]}'.");
        
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

    $this->nonOptimizedCode .= "^XZ\n";

    # Code has been successfully generated.
    file_put_contents("output.zpl", $this->nonOptimizedCode);
    echo "Successfully compiled.";
  }
}