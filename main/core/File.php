<?php
namespace main\core;

class File
{
	private $filePath;
	private $file;

	public function __construct($path, $file)
	{
		$this->filePath = $path . DIRECTORY_SEPARATOR . $file;
	}

	public function open($mode)
	{
		$this->file = fopen($this->filePath, $mode);
		return $this;
	}

	public function get()
	{	
		if(!file_exists($this->filePath) || !is_readable($this->filePath) )
			throw new ZPLException('0003');
		
		return file_get_contents($this->filePath);
	}

	public function write($content = NULL)
	{
		if( is_null($content) || empty($content) || !is_resource($this->file) )
			return FALSE;

		fwrite($this->file, $content);
		fclose($this->file);
		return TRUE;
	}
}