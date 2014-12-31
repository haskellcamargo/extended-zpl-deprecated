Module main.

Declare
	entryPoint => {:main:}
	date       => {:2014/12/27:}.

Const
	hello  => {:Hello World!:}
	size   => 90
	font   => True.

Import
	^GB As graphic.box
	^SN As label.serialize.

Block helloWorld Where
	field.origin(20,10)
	font.config(D, font, size, 50)
	field.data(hello):/

Block main Where
	label.begin
		<Block:helloWorld>
		<Module:footer>
	label.end.