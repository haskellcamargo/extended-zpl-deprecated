declare
  @module   {:HelloWorld:}
, @date     {:2014/12/26:}
, @descr    {:Simple hello world program in Extended ZPL:}.

variable
  $hello <- "Hello World!".

do
  :field-origin [20, 10]
, :font-config  [D, N, 90, 50]
, :field-origin [$hello], /.