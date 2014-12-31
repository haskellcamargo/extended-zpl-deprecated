<?php

namespace main\core;

class ZPLException extends \Exception
{
	protected $message = 'Unknown exception';
	protected $code = 0;
	protected $file;
	protected $line;

	public function __construct($code, $message = NULL)
	{
		$errosList = Configuration::getErrorsFile();
		$this->code = $code;
		
		if( !is_null($message) )
			$this->message = $message;

		if( is_null($message) && array_key_exists($code, $errosList) )
			$this->message = $errosList[$code];
	}

}
