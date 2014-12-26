declare
  @package {:HelloWorld:}
, @date    {:2014/12/26:}.

variable
  $hello <- "Hello World!"
, $font  <- False.

do
  :field-origin [20, 10]
, :font-prop    [D, False, 90, 50]
, :field-data   [$say].