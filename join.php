<?php
/**
 * ShopSmart join page.
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
define ( DB_USER, 'login');

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

// Check to see if user is logged in
if ( $user_login instanceof User_Login ) {
    if ( $user_login->login_exists() ) {
        header( "Location: ./shop.php");
    }
}

switch ( $action ) {
    case 'new':
        // Insert new member and log member into site.

        if (! empty( $_POST )) {
            $return_val = User_Login::insert_member( $_POST );
            
            if ( Error::is_error( $return_val )) {
                $error_msg = $return_val->get_error_msg();
                View::render_template( 'header-join.php' );
                View::render_template( 'forms/join.php', $_POST, $error_msg );
                View::render_template( 'footer.php' );
                exit;
            } else if ( is_numeric( $return_val )) {
                
                $member = Member::get_member_by( 'id', $return_val );
                
                if ( $member instanceof Member ) {
                    $user_login = new User_Login();
                    $verify_sign_on = $user_login->sign_on( $member );
                }
                
                // If successful login, redirect to user to shopping lists
                if ( Error::is_error( $verify_sign_on )) {
                    $error_msg = $return_val->get_error_msg( 'invalid_login' );
                    View::render_template( 'header.php' );
                    View::render_template( 'forms/login.php', $_POST, $error_msg );
                    View::render_template( 'footer.php' );
                    exit;
                } else if ( $verify_sign_on === true ) {
                    header( "Location: ./shop.php");
                    exit;
                } else {
                    Error::error_msg();
                }
            } else {
                Error::error_msg();
            }
        }
        break;
}
// Default view loads join form
View::render_template( 'header-join.php' );
View::render_template( 'forms/join.php');
View::render_template( 'footer.php' );
?>