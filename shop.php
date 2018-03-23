<?php
/**
 * Handles updating, creating, and deleting shopping lists.
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
$action = $_GET['action'];
$store_id = $_GET['store'];
$cart_id = $_GET['id'];

#-------------------#
# Main Body         #
#-------------------#

// Make sure user is logged in
if (! $user_login instanceof User_Login ) {
    header( "Location: ./join.php");
}

if (! $user_login->login_exists() ) {
    header( "Location: ./join.php");
}

$member = Member::get_member_by( 'id', $user_login->member_id );

if (! is_member( $member )) {
    $error =  new Error( 'invalid_member_id', 'Invalid login credentials.' );
    $error_msg = $error->get_error_msg();
    View::render_template( 'header-join.php' );
    View::render_template( 'forms/login.php', false, $error_msg );
    View::render_template( 'footer.php' );
}

$header_data = array( 'name' => $member->name );

switch ( $action ) {
    case 'delete':
        
        if (! is_numeric( $cart_id )) {
            Error::error_msg( 'Invalid cart id.' );
        }
        
        $cart = $member->get_cart( $cart_id );
        
        if ( is_cart( $cart )) {
            if ( $cart->delete_cart() ) {
                $msg = 'The list was deleted.';
            } else {
                $msg = 'There was a problem deleting the list.';
            }
            $carts = Cart::get_carts_by_member( $user_login->member_id );
            View::render_template( 'header.php' , $header_data );
            View::render_template( 'views/carts.php', list_carts( $carts ), $msg );
            View::render_template( 'footer.php' );
            exit; 
        }
        break;
    case 'edit':
        if (! is_numeric( $cart_id )) {
            Error::error_msg( 'Invalid cart id.' );
        }
        
        $cart = $member->get_cart( $cart_id );
        
        if ( is_cart( $cart )) {
            $form_data = array( 'member_id' => $member->ID, 
                                'store_id' => $cart->store->store_id,
                                'store_name' => $cart->store->store_name,
                                'cart_name' => $cart->cart_name,
                                'cart_id' => $cart_id,
                                'edit' => true );

            View::render_template( 'header.php' , $header_data );
            View::render_template( 'forms/cart.php', $form_data );
            View::render_template( 'footer.php' );
            exit;
        }
        break;
    case 'create':
        
        if (! is_numeric( $store_id )) {
            Error::error_msg( 'Invalid store id.' );
        }
        
        $store = Store::get_store_by_id( $store_id );
        
        if ( $store instanceof Store ) {
            $form_data = array( 'member_id' => $member->ID, 
                                'store_id' => $store->store_id,
                                'store_name' => $store->store_name );

            View::render_template( 'header.php' , $header_data );
            View::render_template( 'forms/cart.php', $form_data );
            View::render_template( 'footer.php' );
            exit;
        } else {
            $error = new Error( 'invalid_store_id', 
                "Must select a store before creating a new list.");
            $error_msg = $error->get_error_msg( 'invalid_store_id' );
            
            View::render_template( 'header.php', $header_data );
            View::render_template( 'views/stores.php', false, $error_msg );
            View::render_template( 'footer.php' );
            exit;
        }
        break;
    case 'new':
        if (! empty( $_POST )) {
            
            $form_data['member_id'] = form_value( 'member_id', $_POST );
            $form_data['cart_name'] = form_value( 'cart_name', $_POST );
            $form_data['store_id'] = form_value( 'store_id', $_POST );
            $form_data['cart_id'] = form_value( 'cart_id', $_POST );
            
            // Make sure member is logged in member
            
            if (! ( $_POST['member_id'] == $member->ID )) {
                $error =  new Error( 'invalid_member_id', 'You cannot edit that cart.' );
                
                View::render_template( 'header.php', $header_data );
                View::render_template( 'forms/cart.php', false, $error->get_error_msg() );
                View::render_template( 'footer.php' );
                exit;
            }
            
            // Make sure store exists
            
            if (! $store_val = Store::get_store_by_id( $form_data['store_id'] )) {
                $error =  new Error( 'invalid_store_id', 
                    'Must select a store before creating a new list.' );
                    
                View::render_template( 'header.php', $header_data );
                View::render_template( 'views/stores.php', false, $error->get_error_msg() );
                View::render_template( 'footer.php' );
                exit;
            }
            
            $form_data['store_name'] = $store_val->store_name;
            
            // Insert list in database
            
            $cart_val = Cart::insert_cart( $_POST );
            
            if ( Error::is_error( $cart_val )) {
                $error_msg = $cart_val->get_error_msg();
                
                View::render_template( 'header.php', $header_data );
                View::render_template( 'forms/cart.php', $form_data, $error_msg );
                View::render_template( 'footer.php' );
                exit;
            }
            
            $cart = Cart::get_cart_by_id ( $cart_val );
            
            if (! is_cart( $cart )) {
                Error::error_msg( 'Invalid cart.' );
            }
            
            //List items in cart by category
            $cats = Category::get_all_cats();
            
            if ( is_array( $cats )) {
                $list_items = array();
                foreach ( $cats as $cat ) {
                    $list_html = list_cat( $cat, $cart );
                    if (! empty( $list_html )) {
                        array_push( $list_items, $list_html );
                    }
                }
            }
            
            $items = empty( $list_items ) ? 'No items in cart.' : implode( "\n", $list_items );
            
            $data = array( 'cart_id' => $cart->cart_id, 
                           'cart_name' => $cart->cart_name, 
                           'items' => $items );

            // Load cart view templates
            View::render_template( 'header.php', $header_data );
            View::render_template( 'views/cart.php', $data );
            View::render_template( 'footer.php' );
            exit;
        }
        break;
}
// Default view shows all carts
$carts = Cart::get_carts_by_member( $member->ID );

View::render_template( 'header.php', $header_data );
View::render_template( 'views/carts.php', list_carts( $carts ));
View::render_template( 'footer.php' );
?>