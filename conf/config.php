<?php
/**
 * Initialize session and database connection.
 * 
 * Querying engine is based on code.tutplus.com's class based query engine.
 * 
 * @link http://code.tutsplus.com/tutorials/simple-php-class-based-querying--net-11863
 * 
 * @package ShopSmart
*/ 

// Database
define ( 'HOST', 'localhost' );
define ('SOCKET', '/users/krogers/mysql-php/data/mysql.sock');
define ('PORT', 0);
define ( 'DB', 'shop_smart' );

// File root
define (DOCROOT, '/users/krogers/shopsmart');

// Site title
define ( SITETITLE, 'ShopSmart' );

// Helper functions
require DOCROOT . "/include/functions.php";

/**
 * Autoloader to register.
 * 
 * @param string
*/
function my_autoloader($class) {
    
   $file_name = DOCROOT . "/include/class/" . strtolower($class) . ".php";
   
   if(file_exists($file_name)) {
      require_once $file_name;
   }
}

spl_autoload_register("my_autoloader");

Session::init();

if (DB_USER === 'login') {
    $db = new Database('shopsmart_login', 'login123');
} else if (DB_USER === 'member') {
    $db = new Database('shopsmart_member', 'member123');
} else {
    Error::error_msg('Could not connect to database.');
}
?>