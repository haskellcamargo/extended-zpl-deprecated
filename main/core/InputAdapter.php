<?php
namespace main\core;

class InputAdapter
{
	const CLI = 'cli';
	private $acceptCommands = [
		  'file'
		, 'run'
		, 'compile'
	];

	private $inputs = [];

	public function __construct($runnableMode, $argv)
	{
		$this->argv = $argv;
		$this->setInputs($runnableMode, $argv);
	}

	private function setInputs($runnableMode, $argv)
	{
		if(empty($argv) || is_null($argv))
			throw new ZPLException('0002');
			
		if($runnableMode == self::CLI) {
			$inputs = $this->cliFilter($argv);
		} else {
			$inputs = $this->webFilter($argv);
		}

		foreach($inputs as $command => $value) {
			if(in_array($command, $this->acceptCommands) && !in_array($command, $this->inputs)) {
				$this->inputs[$command] = $value;
			}
		}

		if(empty($this->inputs) || !isset($this->inputs['file']) || count($this->inputs) != 2)
			throw new ZPLException('0002');
	}

	private function cliFilter($argv)
	{
		if(isset($argv[0])) array_shift($argv);

		if($argv[0] != 'file' || !isset($argv[1]))
			throw new ZPLException('0004');
		
		if(!isset($argv[2]))
			throw new ZPLException('0005');
			
		$input['file'] = $argv[1];
		$input[$argv[2]] = (isset($argv[3])) ? $argv[3] : NULL;

		return $input;
	}

	private function webFilter($argv)
	{
		return $argv;
	}

	public function get()
	{
		return $this->inputs;
	}
}