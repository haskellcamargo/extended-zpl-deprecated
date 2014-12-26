declare
  @author {:Marcelo Camargo:},
  @date   {:2014/12/25:}.

variable
  $string  <- "Hello World!",
  $integer <- 1234567890,
  $boolean <- True,
  $falseV  <- False,
  $thatNil <- Nil,
  $age     <- 18,
  $year    <- 2015,
  $compare <- $boolean /= $falseV,
  $birth   <- ($age - $year + 12) * 7 / 2.

begin
  :field-origin [20, 10]
  :font-prop    [D, False, 90, 50]
  :field-data   [$say].
end.