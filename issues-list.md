
[main/interpreter/TokenReader]
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
==============================