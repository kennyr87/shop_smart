<?php
/**
 * Class to interface with member's table.
 * 
 * Based on WordPress WP_User class.
 * 
 * @see Base
 * @package ShopSmart
*/
class Member extends Base {
    
    /**
     * Member's ID.
     * 
     * @var int
    */
    public $ID = 0;

    /**
     * Member's name.
     * 
     * @var string
    */
    public $name;
    
    /**
     * Member's email.
     * 
     * @var int
    */
    public $email;
    
    /**
     * Member's carts.
     * 
     * Array of cart objects.
     * 
     * @var array
    */
    public $carts;
    
    /**
     * Constructor.
     * 
     * @param int|string $id Optional user id.
     * @param string $email Optional user email.
    */
    public function __construct($id = 0, $email = '') {
        if ($id) {
            $user_data = self::get_member_by('id', $id);
        } else if (! (empty($email)) ) {
            $user_data = self::get_member_by('email', $email);
        }
        
        if ( $user_data instanceof stdClass ) {
            $this->init($user_data);
        }
    }
    
    /**
    * Sets up object's properties.
    *
    * @param object $user_data Result object.
    * 
    * @return void
    */
    public function init( stdClass $user_data ) {
        if ( $this->member_exists() ) {
            $this->flush_object();
        }
        
        $this->data = $user_data;
        $this->ID = intval( $user_data->member_id );
        $this->email = $user_data->email;
        $this->name = $user_data->name;
        
        if ( $carts = Cart::get_carts_by_member( $this->ID )) {
            $this->carts = $carts;
        }
    }
    
    /**
     * Get user data by a given field.
     * 
     * @param string $db_field Field to query against: 'id', 'email'.
     * @param string|int $value Value to search for.
     * 
     * @return object|false MySQL result set or false on failure.
    */
    private static function get_member_data_by( $db_field, $value ) {
        global $db;
        
        if ( empty( $value )) {
            return false;
        }
        
        // Make sure id searched for is integer
        if ($db_field === 'id') {
            if (! ( is_numeric( $value ))) {
                return false;
            } else {
                $value = intval( $value );
            }
        }
        
        switch ($db_field) {
            case 'id':
                $db_field = 'member_id';
                break;
            case 'email':
                $db_field = 'email';
                break;
            default:
                return false;
        }
        
        $command = "SELECT * FROM members WHERE $db_field = '%s';";
        $result = $db->get_row( $db->prepare( $command, $value ));
        
        if ( $result ) {
            return $result;
        } else {
            return false;
        }
    }
    
    /**
     * Get member object by particular field.
     * 
     * @param string $db_field Field to query against: 'id', 'email'.
     * @param string|int $value Value to search for.
     * 
     * @return Member|false Object from Member class, or false on failure.
    */
    static public function get_member_by( $db_field, $value ) {
        if ($user_data = self::get_member_data_by( $db_field, $value )) {
            $member = new Member();
            $member->init( $user_data );
            return $member;
        } else {
            return false;
        }
    }
    
	/**
    * Check to see if member exists.
    *
    * @return bool True if ID is set, false if not.
    */
    public function member_exists() {
        return ! (empty( $this->ID ));
    }
    
    /**
     * Checks whether given email exists.
     * 
     * @param string $email Email address to check.
     * 
     * @return bool|int User's id on success, false on failure.
    */
    public static function email_exists($email) {
        if ($member = self::get_member_by( 'email', $email )) {
            return $member->ID;
        } else {
            return false;
        }
    }
    
    /**
     * Checks to see if member is logged in.
     * 
     * @param object $user Instance of User_Login class.
     * 
     * @return bool True if member is logged in; false otherwise.
    */
    public function is_logged_in( User_Login $user ) {
       if ( $this->ID == $user->member_id ) {
           return true;
       } else {
           return false;
       }
    }
    
    /**
     * Checks to see if cart is one of member's carts.
     * 
     * @param int $cart_id ID of cart.
     * 
     * @return object|bool Cart object or false on if cart does not belong to member.
    */
    public function get_cart( $cart_id ) {
        if ( is_numeric( $cart_id )) {
            foreach ( $this->carts as $cart ) {
                if ( $cart_id == $cart->cart_id ) {
                    return $cart;
                }
            }
        }
        return false;
    }
}
?>