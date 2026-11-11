<?php
  //Mulai session
  session_name('almuna_session');
  ob_start();
  if (session_status() === PHP_SESSION_NONE) {
    session_start([
        'cookie_httponly' => true,
        'cookie_samesite' => 'Strict'
    ]);
  }
  //require_once("core/ErrorHandler.php");
  //set_exception_handler("ErrorHandler::exceptionHandler");
  
  require_once __DIR__ . '/Core/Env.php';
  Env::load(__DIR__ . '/../.env');

  spl_autoload_register(function($class) {
	  if(file_exists(APP_PATH . "core/".$class.".php")) 
	    require_once APP_PATH . "core/$class.php";
  });

  //Load Function Helper
  require_once APP_PATH . 'core/Functions.php';
