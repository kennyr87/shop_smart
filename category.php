<?php
/**
 * 
 * Handles adding and viewing categories.
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
$cat_id = $_GET['id'];

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
        //Make sure cat is real cat
        
        if (! is_numeric( $cat_id )) {
            Error::error_msg( 'Invalid category id.' );
        }
        
        $cat = Category::get_cat_by_id( $cat_id );
        
        if ( Category::is_cat( $cat )) {
            $form_data = array( 'cat_id' => $cat_id, 'cat_name' => $cat->cat_name, 
                                'update' => true);
                                
            // Load category forms template
            View::render_template( 'header.php', $header_data );
            View::render_template( 'forms/category.php', $form_data );
            View::render_template( 'footer.php' );
            exit;
        }
        break;
    case 'add':
        //Load category forms template
        View::render_template( 'header.php', $header_data );
        View::render_template( 'forms/category.php');
        View::render_template( 'footer.php' );
        exit;
    case 'new':
        if (! empty($_POST )) {
            //Try adding category
            $cat_val = Category::insert_cat();
            
            // Check for errors
            if ( Error::is_error( $cat_val )) {
                $error = $cat_val->get_error_msg();
                View::render_template( 'header.php', $header_data );
                View::render_template( 'forms/category', $_POST, $error );
                View::render_template( 'footer.php' );
                exit;
            }
            //Redirect to views template
            header( "Location: ./category.php?action=view&id=$cat_val");
            exit;
        }
        break;
    case 'view':
        // Make sure cat_id is real cateogory
        $cat_obj = Category::get_cat_by_id( $cat_id );
        
        if ( is_cat( $cat_obj )) {
            // Get items in category
            $item_data = Item::get_items_by_cat( $cat_id );
            $items = list_all_items( $item_data );
            
            //Build array of categories
            $cat_objs = Category::get_all_cats();
            
            foreach ( $cat_objs as $cat ) {
                $cat_data[$cat->cat_id] = $cat->cat_name;
            }
            
            $data = array( 'items' => $items, 'cat_data' => $cat_data, 
                           'cat_name' => $cat_obj->cat_name, 
                           'cat_id' => $cat_obj->cat_id );
            
            View::render_template( 'header.php', $header_data );
            View::render_template( 'views/items.php', $data );
            View::render_template( 'footer.php' );
            exit;
        }
        break;
}
//Build array of categories
$cat_objs = Category::get_all_cats();

foreach ( $cat_objs as $cat ) {
    $cat_data[$cat->cat_id] = $cat->cat_name;
}

// Get all items
$item_data = Item::get_all_items();
$items = list_all_items( $item_data );

// Default view loads all items
$data = array( 'items' => $items, 'cat_data' => $cat_data );

View::render_template( 'header.php', $header_data );
View::render_template( 'views/items.php', $data );
View::render_template( 'footer.php' );
?>
