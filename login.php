<?php
/**
 * Script handles logging user in and out.
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
define ( DB_USER, 'login' );

#-------------------#
# LOAD CONFIG       #
#-------------------#

require_once "./conf/config.php";

#-------------------#
# User Variables    #
#-------------------#

$user_login = Session::get_session('user_login_obj');
$action = $_GET['action'];

#-------------------#
# Main Body         #
#-------------------#

switch ( $action ) {
    case 'logout':
        if ( $user_login instanceof User_Login ) {
            $user_login->logout_member();
        }
        break;
    case 'login':
        if (! empty($_POST )) {
            $return_val = User_Login::authenticate_member( $_POST );

            if ( Error::is_error( $return_val )) {
                $error_msg = $return_val->get_error_msg();
                View::render_template( 'header-join.php' );
                View::render_template( 'forms/login.php', $_POST, $error_msg );
                View::render_template( 'footer.php' );
                exit;
            } else if ( is_member( $return_val )) {
                $user_login = new User_Login();
                $verify_sign_on = $user_login->sign_on( $return_val );
            } else {
                Error::error_msg();
            }
            
            // If successful login, redirect to user to shopping lists
            if ( Error::is_error( $verify_sign_on )) {
                $error_msg = $return_val->get_error_msg( 'invalid_login' );
                View::render_template( 'header-join.php' );
                View::render_template( 'forms/login.php', $_POST, $error_msg );
                View::render_template( 'footer.php' );
                exit;
            } else if ( $verify_sign_on === true ) {
                header( "Location: ./shop.php");
                exit;
            } else {
                Error::error_msg();
            }
        }
        break;
    default:
        if ( $user_login instanceof User_Login ) {
            header( "Location: ./shop.php");
            exit;
        }
}
// Defaul view loads login form
View::render_template( 'header-join.php' );
View::render_template( 'forms/login.php' );
View::render_template( 'footer.php' );
?>
