<?php

spl_autoload_register(function($instance) 
{
	if(file_exists($filePath = __DIR__ . DIRECTORY_SEPARATOR . str_replace('\\', DIRECTORY_SEPARATOR, $instance) . '.php'))
        require_once ($filePath);
});