<?php
/**
 * 
 * Index page.
 * 
 * @package ShopSmart
*/

#-------------------#
# CONSTANTS         #
#-------------------#

/**
 * Define user for database.
 * 
 * @var string
*/
define ( DB_USER, 'member');

#-------------------#
# LOAD CONFIG       #
#-------------------#

require_once "./conf/config.php";

#-------------------#
# User Variables    #
#-------------------#

$user_login = Session::get_session('user_login_obj');

#-------------------#
# Main Body         #
#-------------------#

// Check to see if user is logged in
if ( $user_login instanceof User_Login ) {
    if ( $user_login->login_exists() ) {
        header( "Location: ./shop.php");
        exit;
    }
}

//Load join template
View::render_template( 'header-join.php' );
View::render_template( 'forms/join.php' );
View::render_template( 'footer.php' );
?>