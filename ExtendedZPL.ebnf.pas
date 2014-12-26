statement = declare 
          | variable;
declare = "declare", { declaration, "," }, declaration, "." ;
variable = "variable", { defvar, "," }, defvar, "." ;

declaration = "@", ident, declstring ;
defvar = "$", ident, expr ;
expr = literal
     | arithmetic 
     | comparasion ;

arithmetic = arithmetic, "+", term
           | arithmetic, "-", term
           | digit ;
term = term, "*", factor
     | term, "/", factor
     | arithmetic ;
factor = "(", factor, ")"
       | factor, "^", term
       | term ;

declstring = "{:", ?.*?, ":}" ;

literal = boolean
        | integer
        | string
        | "Nil";
boolean = "True" | "False" ;
integer = digit, { digit } ;
string = "\"", ?.*?, "\"" ;
digit = "0" | "1" | "2" | "3" | "4" | "5" 
      | "6" | "7" | "8" | "9" ;