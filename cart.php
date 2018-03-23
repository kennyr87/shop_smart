<?php
/**
 * Handles updating, creating, and deleting items in shopping cart.
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
$cart_id = $_GET['id'];
$item_id = $_GET['item'];

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

$header_data = array( 'name' => $user_login->name );

$member = Member::get_member_by( 'id', $user_login->member_id );

// Make sure cart belongs to user

if (! empty( $cart_id )) {
    
    $cart = $member->get_cart( $cart_id );
    
    if (! is_cart( $cart )) {
        $error = 'You cannot view that list.';
        
        View::render_template( 'header.php', $header_data );
        View::render_template( 'views/carts.php', list_carts( $member->carts ), $error );
        View::render_template( 'footer.php' );
        exit;
    }
}

switch ( $action ) {
    case 'delete':
                
        if (! is_cart( $cart )) {
            Error::error_msg( 'Invalid cart.' );
        }
        
        // Load cart view if error deleting item
        if (! $cart->delete_cart_item( $item_id )) {
            $error = 'There was a problem deleting the item.';
            
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
        
        $items = empty( $list_items ) ? 'THIS LIST IS EMPTY' : implode( "\n", $list_items );
        
        $data = array( 'cart_id' => $cart->cart_id, 
                       'cart_name' => $cart->cart_name, 
                       'items' => $items );

        //Load cart view
        View::render_template( 'header.php', $header_data );
        View::render_template( 'views/cart.php', $data, $error );
        View::render_template( 'footer.php' );
        exit;
        }
        // Redirect to cart if item deleted
        header( "Location: ./cart.php?action=view&id=" . $cart->cart_id );
        exit;
    case 'update':
                
        if (! is_cart( $cart )) {
            Error::error_msg( 'Invalid cart.' );
        }
        
        if ( $item = $cart->is_item_in_cart( $item_id )) {
            $form_data = array( 'cart_name' => $cart->cart_name, 
                                'item_id' => $item->item_id, 
                                'item_name' => $item->item_name, 
                                'cart_id' => $cart->cart_id, 
                                'item_count' => $item->item_count, 
                                'item_price' => $item->item_price,
                                'update' => true);
            
            View::render_template( 'header.php', $header_data );
            View::render_template( 'forms/cart_item.php', $form_data );
            View::render_template( 'footer.php' );
            exit;
        }
        break;
    case 'add':
        
        //If item does not exists, load items template
        $item = Item::get_item_by_id( $item_id );
        
        if (! is_item( $item )) {
            $error = 'Select item to add to list.';
            
            // Build array of items
            $item_data = Item::get_all_items();
            $items = list_all_items( $item_data, $cart_id );

            //Build array of categories
            $cat_objs = Category::get_all_cats();
            
            foreach ( $cat_objs as $cat ) {
                $cat_data[$cat->cat_id] = $cat->cat_name;
            }
            
            $data = array( 'items' => $items, 'cat_data' => $cat_data );
            
            View::render_template( 'header.php', $header_data );
            View::render_template( 'views/items.php', $data, $error );
            View::render_template( 'footer.php' );
            exit;
        }

        if ( Cart::is_cart( $cart )) {
            $cart_id = $cart->cart_id;
            $cart_name = $cart->cart_name;
        }
        
        //Build array of shopping lists
        foreach ( $member->carts as $cart_obj ) {
            $lists[$cart_obj->cart_id] = $cart_obj->cart_name;
        }

        $form_data = array( 'item_id' => $item->item_id, 
                            'item_name' => $item->item_name, 
                            'lists' => $lists,
                            'cart_id' => $cart_id,
                            'cart_name' => $cart_name,
                            'item_count' => 1);

        // Load cart items form
        View::render_template( 'header.php', $header_data );
        View::render_template( 'forms/cart_item.php', $form_data );
        View::render_template( 'footer.php' );
        exit;
    case 'new':
        
        if (! empty( $_POST )) {
            
            $form_data['item_id'] = $_POST['item_id'];
            $form_data['item_name'] = $_POST['item_name'];
            $form_data['item_count'] = $_POST['item_count'];
            $form_data['item_price'] = $_POST['item_price'];
            $form_data['cart_id'] = $_POST['cart_id'];
            
            //Build array of shopping lists
            foreach ( $member->carts as $cart ) {
                $lists[$cart->cart_id] = $cart->cart_name;
            }
            
            $form_data['lists'] = $lists;
            
            // Make sure valid cart is selected
            $cart = $member->get_cart( $_POST['cart_id'] );
            
            if (! is_cart( $cart )) {
                $error = 'Pick a valid cart.';
                
                View::render_template( 'header.php', $header_data );
                View::render_template( 'forms/cart_item.php', $form_data, $error );
                View::render_template( 'footer.php' );
                exit;
            }
            
            //Try to insert item in cart
            $item_val = $cart->insert_cart_item();
            
            // Check for errors
            if ( Error::is_error( $item_val )) {
                
                $error = $item_val->get_error_msg();
                
                // If invalid item id, load items template
                if ( $item_val->get_error_code() === 'invalid_item_id' ) {

                    //Build array of categories
                    $cat_objs = Category::get_all_cats();
                    
                    foreach ( $cat_objs as $cat ) {
                        $cat_data[$cat->cat_id] = $cat->cat_name;
                    }
                    
                    //Build list of items
                    $item_data = Item::get_all_items();
                    $item_list = list_all_items( $item_data );
                    
                    $data = array( 'items' => $item_list, 'cats' => $cat_data );
                    
                    View::render_template( 'header.php', $header_data );
                    View::render_template( 'views/items.php', $data, $error );
                    View::render_template( 'footer.php' );
                    exit;
                }
                
                $form_data['cart_name'] = $cart->cart_name;
                
                View::render_template( 'header.php', $header_data );
                View::render_template( 'forms/cart_item.php', $form_data, $error );
                View::render_template( 'footer.php' );
                exit;
            }
            
            //Load cart view if no errors
            header( "Location: ./cart.php?action=view&id=". $cart->cart_id );
            exit;
        }
        break;
    case 'view';
    
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
        
        $items = empty( $list_items ) ? 'THIS LIST IS EMPTY' : implode( "\n", $list_items );
        
        $data = array( 'cart_id' => $cart->cart_id, 
                       'cart_name' => $cart->cart_name, 
                       'items' => $items );
        
        View::render_template( 'header.php', $header_data );
        View::render_template( 'views/cart.php', $data, $msg );
        View::render_template( 'footer.php' );
        exit;
}
// Default view shows all carts for member
View::render_template( 'header.php', $header_data );
View::render_template( 'views/carts.php', list_carts( $member->carts ));
View::render_template( 'footer.php' );
?>