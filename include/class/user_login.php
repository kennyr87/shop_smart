<?php
/**
 * User login class.
 * 
 * Handles user authentication, registration, and other user functionality.
 * 
 * @package ShopSmart
*/
class User_Login {
    
    /**
     * Value of member_id.
     * 
     * @var int
    */
    public $member_id = 0;
    
    /**
     * Member's name,
     * 
     * @var string
    */
    public $name;
    
    /**
     * Constructor.
     * 
     * @param object $member Optional.  Instance of Member to log in.
    */
    public function __construct( Member $member = null ) {
        if ( $member instanceof Member ) {
            $this->sign_on( $member );
        }
    }
    
    /**
     * Logs member in and sets session.
     * 
     * @param object $member Instance of Member to log in.
     * 
     * @return bool|object True on success, error object on failure..
    */
    public function sign_on( Member $member ) {
        
        if ( $this->login_exists() ) {
            $this->logout_member();
        }
        
        if ( $member instanceof Member ) {
            
            $this->member_id = $member->ID;
            $this->name = $member->name;
            
            Session::set_session( array('user_login_obj' => $this ));
        }
        
        if ( $this->member_id ) {
            return true;
        } else {
            return new Error('invalid_login', "Could not log in member: . $member->email" , $member );
        }
    }
    
    /**
     * Log out current member.
     * 
     * @return void
    */
    public function logout_member() {
        $this->member_id = 0;
        $this->name = '';
        Session::destroy();
    }
    
    /**
     * Check to see if login exists.
     * 
     * @return bool True if member_id is set, false otherwise.
    */
    public function login_exists() {
        return ! ( empty( $this->member_id ));
    }
    
    /**
     * Inserts new member into database.
     *      
     * @param array $user_data {
     *      Array of user's data.
     * 
     *      @param string $name Member's name.
     *      @param string $email Member's email.
     *      @param string $password Member's password.
     * }
     * 
     * @return int|Error New member's ID or Error object if there was an error.
    */
    static public function insert_member( $user_data ) {
        global $db;
        
        // Check to see info from form is needed
        if ( empty($user_data) ) {
            if (! empty( $_POST['name']) )  {
                $user_data['name'] = $_POST['name'];
            }
            
            if (! empty($_POST['password']) ) {
                $user_data['password'] = $_POST['password'];
            }
            
            if (! empty($_POST['email']) ) {
                $user_data['email'] = $_POST['email'];
            }
        }
        
        trim_values( $user_data );
        
        //Validate user data
        
        //RegEx for name format
        $invalid_name = "[^-'\040a-z.]";
        
        if (empty($user_data['name'])) {
            return new Error('empty_user_name', 'Cannot sign up with a empty display name.');
        } else if (preg_match("/$invalid_name/i", $user_data['name'])) {
            return new Error('invalid_user_name', 'Display name must contain hyphens, apostrophes, spaces or periods only.');
        } else if (strlen($user_data['name']) > 35) {
            return new Error('invalid_user_name', 'Display name must be 35 characters or less.');
        } else {
            $member_name = $user_data['name'];
        }
        
        if (empty($user_data['email'])) {
            return new Error('empty_user_email', 'Cannot sign up without an email address.');
        } else if (! (filter_var($user_data['email'], FILTER_VALIDATE_EMAIL)) ) {
            return new Error('invalid_user_email', 'Email address is not valid.');
        } else if (strlen($user_data['email']) > 50) {
            return new Error('invalid_user_email', 'Email address must be 50 characters or less.');
        } else if (Member::email_exists($user_data['email'])) {
            return new Error('existing_user_email', 'That email address is already in use.');
        } else {
            $member_email = $user_data['email'];    
        }
        
        if (empty($user_data['password'])) {
            return new Error('empty_password', 'Cannot sign up with an empty password.');
        } else if (strlen($user_data['password']) < 6) {
            return new Error('invalid_password', 'Password must be at least 6 characters.');
        } else {
            $member_pwd = $user_data['password'];    
        }
        
        $success = true;
        
        // Start transaction
        $command = "SET AUTOCOMMIT=0";
        $result = $db->query($command);
        $command = "BEGIN";
        $result = $db->query($command);

        // First, add new member
        $command = "INSERT INTO members VALUES (DEFAULT, '%s', '%s', NOW());";
        $query = $db->prepare($command, $member_name, $member_email);
        
        if (is_string($query)) {
            $member_id = $db->query($query);
        } else {
            $success = false;
        }
        
        if (is_numeric($member_id)) {
            // Add password
            $command = "INSERT INTO logins VALUES (DEFAULT, %d, PASSWORD('%s'), DEFAULT);";
            $query = $db->prepare($command, $member_id, $member_pwd);

            if (is_string($query)) {
                $result = $db->query($query);
            } else {
                $success = false;
            }
            
            if ($result == false) {
                $success = false;
            }
            
        } else {
            $success = false;
        }
        
        // Rollback or committ transaction
        if ($success === false) {
            $command = "ROLLBACK";
            $result = $db->query($command);
            return new Error('invalid_query', 'Could not create new account.');
        } else {
            $command = "COMMIT";
            $result = $db->query($command);
        }
        $command = "SET AUTOCOMMIT=1";
        $result = $db->query($command);   
        return $member_id;
    }
    
    /**
     * Update member in database.
     * 
     * If array not given, use data from form.
     * 
     * @param array $user_data {
     *      Optional array of data to update.
     *      
     *      @param int $member_id Member's ID.
     *      @param string $name Member's name.
     *      @param string $email Member's email.
     *      @param string $password Member's password.
     * }
     * 
     * @return int|Error Number of rows updated or Error object on error.
    */
    public function update_member( $user_data = array() ) {
        global $db;
        
        // Check to see info from form is needed
        if ( empty($user_data) ) {
            if (! empty( $_POST['name']) )  {
                $user_data['name'] = $_POST['name'];
            }
            
            if (! empty($_POST['password']) ) {
                $user_data['password'] = $_POST['password'];
            }
            
            if (! empty($_POST['member_id']) ) {
                $user_data['member_id'] = $_POST['member_id'];
            }
            
            if (! empty($_POST['email']) ) {
                $user_data['email'] = $_POST['email'];
            }
        }
        
        trim_values( $user_data );
        
        $ID = isset( $user_data['member_id'] ) ? intval($user_data['member_id']) : 0;
        
        if (! ($ID) ) {
            return new Error('invalid_member_id', 'Invalid member ID.');
        }
        
        //Get member's original data
        $old_member_data = Member::get_member_data_by('id', $ID);
        
        if (! $old_member_data instanceof stdClass ) {
            return new Error('invalid_member_id', 'Member does not exist.');
        }
        
        $old_member = get_object_vars( $old_member_data );
        
        //Validate user data
        
        //RegEx for name format
        $invalid_name = "[^-'\040a-z.]";
        
        if (! empty($user_data['name']) ) {
            if (preg_match("/$invalid_name/i", $user_data['name'])) {
                return new Error('invalid_user_name', 'Display name must contain hyphens, apostrophes, spaces or periods only.');
            } else if (strlen($user_data['name']) > 35) {
                return new Error('invalid_user_name', 'Display name must be 35 characters or less.');
            }
        }
        
        if (! empty($user_data['email']) ) {
            if (! (filter_var($user_data['email'], FILTER_VALIDATE_EMAIL)) ) {
                return new Error('invalid_user_email', 'Email address is not valid.');
            } else if (strlen($user_data['email']) > 50) {
                return new Error('invalid_user_email', 'Email address must be 50 characters or less.');
            } else if (Member::email_exists($user_data['email'])) {
                return new Error('existing_user_email', 'That email address is already in use.');
            }
        }

        if (! empty($user_data['password']) ) {
            if (strlen($user_data['password']) < 6) {
                return new Error('invalid_password', 'Password must be at least 6 characters.');
            } else {
                $user_data['password'] = self::hash_password( $user_data['password'] );   
            }
        }
        
        $user_data = array_merge( $old_member, $user_data );
        
        $success = true;
        
        // Start transaction
        $command = "SET AUTOCOMMIT=0";
        $result = $db->query($command);
        $command = "BEGIN";
        $result = $db->query($command);

        // First, update member
        $command = "UPDATE members SET name = '%s', email = '%s' WHERE member_id = %d;";
        $query = $db->prepare($command, $user_data['name'], $user_data['email'], $user_data['member_id']);
        
        if ( is_string($query) ) {
            $return_val = $db->query($query);
        } else {
            $success = false;
        }
        
        if ( is_numeric($return_val) ) {
            // Update password
            $command = "UPDATE logins SET password = '%s' WHERE member_id = %d;";
            $query = $db->prepare($command, $user_data['password'], $user_data['member_id']);

            if ( is_string($query) ) {
                $result = $db->query($query);
            } else {
                $success = false;
            }
            
            if ($result == false) {
                $success = false;
            }
            
        } else {
            $success = false;
        }
        
        // Rollback or committ transaction
        if ($success === false) {
            $command = "ROLLBACK";
            $result = $db->query($command);
            return new Error('invalid_query', 'Could not update account.');
        } else {
            $command = "COMMIT";
            $result = $db->query($command);
        }
        $command = "SET AUTOCOMMIT=1";
        $result = $db->query($command);   
        return $return_val;
    }
    
    /**
     * Removes user from site.
     * 
     * Adds flag to logins table.
     * 
     * @param int $id Member's id.
     * 
     * @return bool True on success, false otherwise.
    */
    static public function delete_member($id) {
        global $db;
        
        $id = intval($id);
        $member = new User_Login($id);
        
        if (! ( $member->member_exists() ) ) {
            return false;
        }
        
        $command = "UPDATE logins SET date_deactivated = NOW() WHERE member_id = %d";
        $result = $db->query( $db->prepare($command, $id) );
        
        if ($result) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Authenticate user's login information.
     * 
     * If credentials not given, then function will use credentials from from
     * log in form.
     * 
     * @param array $info {
     *      Optional array of member's login info.
     * 
     *      @param string $email Member's email.
     *      @param string $password Member's password.
     * }
     * 
     * @return Member|Error Member object on success, Error on failure.
    */
    static public function authenticate_member( $info = array() ) {
        global $db;
        
        // Check to see info from form is needed
        if ( empty($info) ) {
            if (! empty( $_POST['email']) )  {
                $info['email'] = $_POST['email'];
            } else if (! empty($_POST['password']) ) {
                $info['password'] = $_POST['password'];
            }
        }
        
        trim_values( $info );
        
        // Validate variables
        if ( empty($info['email']) || empty($info['password']) ) {

            if ( empty($info['email']) ) {
                return new Error('empty_user_email', 'Cannot sign in without an email address.');
            } else if ( empty($info['password']) ) {
                return new Error('empty_password', 'Cannot sign in with an empty password.');
            }
            
        } else if (! filter_var($info['email'], FILTER_VALIDATE_EMAIL) ) {
            return new Error('invalid_user_email', 'Email address is not valid.');
        }
        
        //Check password
        $member = Member::get_member_by( 'email', $info['email'] );
        
        if (! ($member instanceof Member) ) {
            return new Error('invalid_user_email', 'No member with that email.');
        }

        if ( self::authenticate_password( $info['password'], $member )) {
            return $member;
        } else {
            return new Error( 'incorrect_password', sprintf('The password you entered for %s is not valid.', 
            $info['email']) );
        }
    }
    
    /**
     * Authenticate member's password.
     * 
     * @param string $pwd Member's password.
     * @param object $member Member object.
     * @param bool $active Optional.  Flag to only check active accounts.
     * 
     * @return bool True if password mataches, false otherwise.
    */
    static private function authenticate_password($pwd, Member $member, $active = true) {
        global $db;
        
        $member_id = intval( $member->ID );
        
        $command =  <<<SQL
SELECT L.member_id FROM logins AS L
JOIN members AS M ON M.member_id = L.member_id 
WHERE password = '%s' AND email = '%s'
SQL;
        
        if ( $active === true ) {
            $command .= " AND date_deactivated <= 0;";
        }
        
        $pwd = self::hash_password( $pwd );
        $query = $db->prepare( $command, $pwd, $member->email );
        $result = $db->get_row( $query );
        
        if ($result instanceof stdClass) {
            if ( $result->member_id == $member_id ) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }
    
    /**
     * Hash password using MySQL password() function.
     * 
     * @param string $pwd Password to hash.
     * 
     * @return string|bool Hashed password on success, false on failure.
    */
    static private function hash_password($pwd) {
        global $db;
        
        if ( is_string($pwd) ) {
            $command = "SELECT PASSWORD('%s') AS pwd";
            $result = $db->get_row( $db->prepare($command, $pwd) );
            
            if ( $result instanceof stdClass ) {
                $hashed_pwd = $result->pwd;
            }
            
            if ( strlen($hashed_pwd) === 41) {
                return $hashed_pwd;
            }
        }
        return false;
    }
}
?>