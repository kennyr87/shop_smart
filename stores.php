<?php
/**
 * View, update, add stores.
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
$store_id = $_GET['id'];

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
        if (! is_numeric( $store_id )) {
            $store_data = Store::get_all_stores();
            View::render_template( 'header.php', $header_data );
            View::render_template( 'views/stores.php', list_stores( $store_data ));
            View::render_template( 'footer.php' );
            exit;
        } else {
            // Make sure store exists
            $store_val = Store::get_store_by_id( $store_id );
            
            if (! Store::is_store( $store_val )) {
                $store_data = Store::get_all_stores();
                View::render_template( 'header.php', $header_data );
                View::render_template( 'views/stores.php', list_stores( $store_data ));
                View::render_template( 'footer.php' );
                exit;
            } else {
                $form_data = array(
                    'store_id' => $store_val->store_id, 
                    'store_name' => $store_val->store_name, 
                    'tel_number' => $store_val->tel_number, 
                    'address' => $store_val->address->address, 
                    'city' => $store_val->address->city, 
                    'state' => $store_val->address->state, 
                    'zip_code' => $store_val->address->zip_code,
                    'edit' => true
                );
                View::render_template( 'header.php', $header_data );
                View::render_template( 'forms/store.php', $form_data );
                View::render_template( 'footer.php' );
                exit;
            }
        }
        break;
    case 'create':
        View::render_template( 'header.php', $header_data );
        View::render_template( 'forms/store.php' );
        View::render_template( 'footer.php' );
        exit;
    case 'new':
        if (! empty( $_POST )) {
            
            $form_data['store_id'] = form_value( 'store_id', $_POST );
            $form_data['store_name'] = form_value( 'store_name', $_POST );
            $form_data['tel_number'] = form_value( 'tel_number', $_POST );
            $form_data['address'] =form_value( 'address', $_POST );
            $form_data['city'] = form_value( 'city', $_POST );
            $form_data['state'] = form_value( 'state', $_POST );
            $form_data['zip_code'] = form_value( 'zip_code', $_POST );

            // Get address ID
            
            $address_val = Address::insert_address( $_POST );
            
            if ( Error::is_error( $address_val )) {
                $form_data['error_msg'] = $address_val->get_error_msg();
                View::render_template( 'header.php', $header_data );
                View::render_template( 'forms/store.php', $form_data );
                View::render_template( 'footer.php' );
                exit;
            }
            
            $post_data = array_merge( $_POST, array( 'address_id' => intval( $address_val )));
            
            $store_val = Store::insert_store( $post_data );
            
            if ( Error::is_error( $store_val )) {
                $form_data['error_msg'] = $store_val->get_error_msg();
                View::render_template( 'header.php', $header_data );
                View::render_template( 'forms/store.php', $form_data );
                View::render_template( 'footer.php' );
                exit;
            }
            
            if (! is_numeric( $store_val )) {
                Error::error_msg();
            } else {
                $store_data = Store::get_store_by_id( $store_val );
                $data = array( $store_data );
                View::render_template( 'header.php', $header_data );
                View::render_template( "views/stores.php", list_stores( $data ));
                View::render_template( 'footer.php' );
                exit;
            }
        } else {
            View::render_template( 'header.php', $header_data );
            View::render_template( 'forms/store.php' );
            View::render_template( 'footer.php' );
            exit;
        }
        break;
    case 'view':
        // Make sure store exists
        $store_data = Store::get_store_by_id( $store_id );
        if ( Store::is_store( $store_data )) {
            $data = array( $store_data );
            View::render_template( 'header.php', $header_data );
            View::render_template( 'views/stores.php', list_stores( $data ));
            View::render_template( 'footer.php' );
            exit;
        }
    break;
}

//Default views shows all stores
$store_data = Store::get_all_stores();
View::render_template( 'header.php', $header_data );
View::render_template( 'views/stores.php', list_stores( $store_data ));
View::render_template( 'footer.php' );
?>