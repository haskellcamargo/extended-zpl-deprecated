<?php
namespace main\core;

class Core
{
	private $configuration;
	private $input;

	public function __construct(Configuration $configuration, InputAdapter $input)
	{
		$this->configuration = $configuration;
		$this->input = $input->get();
	}

	public function run()
	{
		$zplFile = new File(
				$this->configuration->getZPLFiles(),
				$this->input['file']);

		$zplContent = $zplFile->get();
		var_dump($zplContent);

		if( isset($this->input['run'] ) ) {
			/*
			 * Implement here
			 */
		} else if( isset($this->input['compile'])) {
			/*
			 * Implement here
			 */

			$file = new File(
					$this->configuration->getCompileDir(),
					$this->input['file']);

			$status = $file->open('w+')->write('CONTENT HERE');

		}
	}
}