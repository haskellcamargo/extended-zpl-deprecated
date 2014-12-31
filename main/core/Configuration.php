<?php
namespace main\core;

class Configuration
{
	const LANGUAGE_FIELD 		= 'LANGUAGE';
	const ERRORS_FILE_FIELD 	= 'ERRORS';
	const ENVIRONMENT_FIELD 	= 'ENVIRONMENT';
	const ZPLFILES_FIELD 		= 'ZPL_FILES';
	const COMPILE_DIR_FIELD		= 'COMPILE_DIR';
	const ERRORS_FILE_DEFAULT 	= 'assets/ErrorsList.ini';
	
	private $configuration;
	
	private static $language;
	private static $environment;
	private static $runnableMode;
	private static $zplFiles;
	private static $compileDir;

	private static $errorsFile = [];
	private static $commands = [];

	public function __construct($configuration)
	{
		$this->configuration = $configuration;
		$this->setEnvironment()
			 ->setZPLFiles()
			 ->setCompileDir();
	}

	private function loadField($field = NULL)
	{
		if( !isset($this->configuration[$field])  || empty($this->configuration[$field]) ) {
			throw new ZPLException(0, vsprintf('%s field is required', $field));
		}

		return $this->configuration[$field];
	} 

	private function setEnvironment()
	{
		self::$environment = $this->loadField(self::ENVIRONMENT_FIELD);
		return $this;
	}

	private function setZPLFiles()
	{
		self::$zplFiles = $this->loadField(self::ZPLFILES_FIELD);
		return $this;
	}

	private function setCompileDir()
	{
		self::$compileDir = $this->loadField(self::COMPILE_DIR_FIELD);
		return $this;
	}

	public function setLanguage()
	{
		self::$language = $this->loadField(self::LANGUAGE_FIELD);
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

	public function setRunnableMode($mode)
	{
		self::$runnableMode = $mode;
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

	public static function getEnvironment()
	{
		return self::$environment;
	}

	public static function getRunnableMode()
	{
		return self::$runnableMode;
	}

	public static function getZPLFiles()
	{
		return self::$zplFiles;
	}

	public static function getCompileDir()
	{
		return self::$compileDir;
	}
}