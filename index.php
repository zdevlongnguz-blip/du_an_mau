<?php 

session_name('con-cho-nay');
session_start();


spl_autoload_register(function ($class) {    
    $fileName = "$class.php";

    $fileModel              = PATH_MODEL . $fileName;
    $fileController         = PATH_CONTROLLER . $fileName;
    $fileService            = PATH_SERVICE . $fileName;

    if (is_readable($fileModel)) {
        require_once $fileModel;
    } 
    else if (is_readable($fileController)) {
        require_once $fileController;
    }else if (is_readable($fileService)) {
        require_once $fileService;
    }
});

require_once './vendor/autoload.php';
require_once './configs/env.php';
require_once './configs/helper.php';


set_exception_handler('handleGlobalError');
set_error_handler(function ($errno, $errstr, $errfile, $errline) {
    throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
});


require_once './routes/index.php';
