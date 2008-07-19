<?php
	error_reporting(E_ALL|E_STRICT);
	date_default_timezone_set('Europe/London');

	set_include_path('.' . PATH_SEPARATOR . '../library'
		. PATH_SEPARATOR . get_include_path());

	//If class not found instanciate it automatically
	require_once 'Zend/Loader.php';
	Zend_Loader::registerAutoload();

	// setup controller
	$frontController = Zend_Controller_Front::getInstance();
	$frontController->throwExceptions(true);
	$frontController->setParam('useDefaultControllerAlways', true);
	$frontController->setControllerDirectory('../application/controllers');

	// run!
	$frontController->dispatch(); 