<?php
namespace main\core;

class Configuration
{
	const LANGUAGE_FIELD 	= 'language';
	const ERRORS_FILE_FIELD = 'errors';
	const ERRORS_FILE_DEFAULT = 'assets/ErrorsList.ini';
	private $configuration;
	private static $language;
	private static $errorsFile = [];

	public function __construct($configuration)
	{
		$this->configuration = $configuration;
	}

	private function loadField($field = NULL)
	{
		if( !isset($this->configuration[$field])  || empty($this->configuration[$field]) ) {
			throw new ZPLException(0, vsprintf('%s field is required', $field));
		}

		return $this->configuration[$field];
	} 

	public function setLanguage()
	{
		$language = $this->loadField(self::LANGUAGE_FIELD);
		self::$language = $language;
		return $this;
	}

	public function setErrorsFile()
	{
		if( !is_null(self::$language) ) {
			$errors = $this->loadField(self::ERRORS_FILE_FIELD);
			$errorsFile = vsprintf($errors, self::$language);
		}

		if( is_null(self::$language) ) {
			$errorsFile = self::ERRORS_FILE_DEFAULT;
		}

		if( !file_exists($errorsFile) && $errorsFile != self::ERRORS_FILE_DEFAULT ) {
			$errorsFile = self::ERRORS_FILE_DEFAULT;
		}

		if( !file_exists($errorsFile) ) {
			throw new ZPLException(000, vsprintf('Cannot locate file: %s', $errorsFile));
		}
		
		self::$errorsFile = parse_ini_file($errorsFile);
		
		return $this;
	}


	public static function getLanguage()
	{
		return self::$language;
	}
	
	public static function getErrorsFile()
	{
		return self::$errorsFile;
	}
}