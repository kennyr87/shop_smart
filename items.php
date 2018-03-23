<?php
/**
 * 
 * Handles viewing, creating and editing items.
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
$item_id = $_GET['id'];

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

switch ( $action ) {
    case 'edit':
        
        //Build array of categories
        $cat_objs = Category::get_all_cats();
        
        foreach ( $cat_objs as $cat ) {
            $cat_data[$cat->cat_id] = $cat->cat_name;
        }
        
        //Make sure item exists
        $item = Item::get_item_by_id( $item_id );
        
        if (! is_item( $item )) {
            $error = 'Item does not exist.';
            
            // Build array of items
            $item_data = Item::get_all_items();
            $items = list_all_items( $item_data );

            $data = array( 'items' => $items, 'cat_data' => $cat_data );
            
            View::render_template( 'header.php', $header_data );
            View::render_template( 'views/items.php', $data, $error );
            View::render_template( 'footer.php' );
            exit;
        }
        
        $form_data = array( 'item_id' => $item->item_id, 
                            'company' => $item->company, 
                            'item_name' => $item->item_name,
                            'item_unit' => $item->item_unit, 
                            'item_size' => $item->item_size, 
                            'cat_id' => $item->cat->cat_id, 
                            'cat_name' => $item->cat->cat_name, 
                            'cat_data' => $cat_data, 
                            'update' => true );
        
        View::render_template( 'header.php', $header_data );
        View::render_template( 'forms/item.php', $form_data );
        View::render_template( 'footer.php' );
        exit;
    case 'create':
        //Build array of categories
        $cat_objs = Category::get_all_cats();
        
        foreach ( $cat_objs as $cat ) {
            $cat_data[$cat->cat_id] = $cat->cat_name;
        }
        
        $form_data = array( 'cat_data' => $cat_data );
        
        View::render_template( 'header.php', $header_data );
        View::render_template( 'forms/item.php', $form_data );
        View::render_template( 'footer.php' );
        exit;
    case 'new':
        
        if (! empty( $_POST )) {
            $item_val = Item::insert_item();
            
            // Check for errors
            if ( Error::is_error( $item_val )) {

                //Build array of categories
                $cat_objs = Category::get_all_cats();
                
                foreach ( $cat_objs as $cat ) {
                    $cat_data[$cat->cat_id] = $cat->cat_name;
                }
                
                // Check to see if error is invalid_cat_id
                if ( $item_val->get_error_code() != 'invalid_cat_id' ) {
                    $cat = Category::get_cat_by_id( $_POST['cat_id'] );
                    $cat_id = $cat->cat_id;
                    $cat_name = $cat->cat_name;
                }
                
                $form_data = array( 'item_id' => $_POST['item_id'], 
                                    'company' => $_POST['company'], 
                                    'item_name' => $_POST['item_name'],
                                    'item_unit' => $_POST['item_unit'], 
                                    'item_size' => $_POST['item_size'], 
                                    'cat_id' => $cat_id, 
                                    'cat_name' => $cat_name, 
                                    'cat_data' => $cat_data, 
                                    'update' => true );

                $error = $item_val->get_error_msg();
                
                View::render_template( 'header.php', $header_data );
                View::render_template( 'forms/item.php', $form_data, $error );
                View::render_template( 'footer.php' );
                exit;
            }
            // If item added and no errors, redirect to item view
            $item = Item::get_item_by_id( $_POST['item_id'] );
            header( "Location: ./items.php?action=view&id=" . $item->item_id );
            exit;
        }
        break;
    case 'view';
        
        // Make sure item exists
        $item = Item::get_item_by_id( $item_id );
        
        if ( is_item( $item )) {
            
            //Build item description
            $item_name = empty( $item->company ) ? $item->item_name : sprintf( "%s by %s", $item->item_name, $item->company );
            
            $description = sprintf( "%s - %s %s <br/> %s", $item_name, 
                                    $item->item_size, $item->item_unit, 
                                    $item->cat->cat_name );
            
            //Get city, state
            $carts = Cart::get_carts_by_member( $user_login->member_id );
            
            if ( is_cart( $cart = $carts[0] )) {
                //Build item prices
                $city = $cart->store->address->city;
                $state = $cart->store->address->state;
                $item_price_data = Item::get_item_price_by_city( $item->item_id, $city, $state );
            }
            
            $item_prices = list_item_prices( $item_price_data );
            
            // Load item view
            $data = array( 'item_name' => $item->item_name, 
                           'item_id' => $item->item_id,
                           'description' => $description, 
                           'item_prices' => $item_prices );

            View::render_template( 'header.php', $header_data );
            View::render_template( 'views/item.php', $data );
            View::render_template( 'footer.php' );
            exit;
        }
        break;
}

//Default view shows all items

//Build array of categories
$cat_objs = Category::get_all_cats();

foreach ( $cat_objs as $cat ) {
    $cat_data[$cat->cat_id] = $cat->cat_name;
}

// Build array of items
$item_data = Item::get_all_items();
$items = list_all_items( $item_data );

$data = array( 'items' => $items, 'cat_data' => $cat_data );

View::render_template( 'header.php', $header_data );
View::render_template( 'views/items.php', $data );
View::render_template( 'footer.php' );
?>
