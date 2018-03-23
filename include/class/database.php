<?php
/**
 * Database connection interface.
 * 
 * Sets up database connection and provides functionality for running queries.
 * 
 * @package ShopSmart
*/
class Database {

    /**
     * The DB user name.
     * 
     * @var string
    */
    private $user;
    
    /**
     * User's password.
     * 
     * @var string
    */
    private $pwd;
    
    /**
     * MySQL connection object.
     * 
     * @var object
    */
    private $dbo;
    
    /**
     * Results of last query made
     * 
     * @var array|bool
    */
    private $last_result;
    
    /**
    * MySQL result set or boolean.
    *
    * @var mixed
    */
    private $result;
    
    /**
     * Number of row returned from last query.
     * 
     * @var int
    */
    private $num_rows = 0;
    
    /**
     * Assigns DB user name and password and connects to DB.
     * 
     * @param string $user DB user name.
     * @param string $pwd User's password.
    */
    public function __construct( $user, $pwd ) {
        $this->user = $user;
        $this->pwd = $pwd;
        
        $this->db_connect();
    }
    
    /**
     * Connects to DB.
     * 
     * @return bool True with a successful connection, false on failure.
    */
    private function db_connect() {
        $db = @new mysqli( HOST, $this->user, $this->pwd, DB, PORT, SOCKET );
        
        if ( $db->connect_error ) {
            return false;
        } else {
            $this->dbo = $db;
            return true;
        }
    }
    
    /**
     * Escapes string using mysqli_real_escape_string().
     * 
     * @param string $string
     * 
     * @return string
    */
    private function real_escape( &$string ) {
        $string = mysqli_real_escape_string( $this->dbo, $string );
    }
    
    /**
     * Prepare query for safe execution.
     * 
     * @param string $command Query statement with sprintf()-like placeholders.
     * @param array $args Array of variables to substitute into query placeholders.
     * @param mixed $args, ... Variables to substitute into query placeholders.
     * 
     * @return string|false String on success and false othewise.
    */
    public function prepare( $command, $args ) {
        $args = func_get_args();
        array_shift( $args );
        
        // If args were passed as array, set args to that array
        if ( is_array( $args[0] )) {
            $args = $args[0];
        }
        
        trim_values( $args );
        array_walk( $args, array( $this, 'real_escape' ));
        return vsprintf ( $command, $args );
    }
    
    /**
     * Perform a query using current database connection.
     * 
     * @param string $command Database query.
     * @return int|bool Number of rows affected/selected or true on success, false on error.
    */
    public function query ( $command ) {
        $db = $this->dbo;
        
        //Flush results from last query
        $this->flush_results();
        
        if ( $this->result = $db->query( $command )) {
            if ( preg_match('/^\s*(select)\s/i', $command )) {
                if ( $this->result instanceof mysqli_result ) {
                    $num_rows = 0;
                	while ( $row = @mysqli_fetch_object( $this->result )) {
                        $this->last_result[$num_rows] = $row;
                        $num_rows++;
                    }
                }
                $this->num_rows = $result->num_rows;
                $return_val = $this->num_rows;
            } else if ( preg_match('/^\s*(insert)\s/i', $command )) {
                $return_val = $db->insert_id;
            } else if ( preg_match('/^\s*(update)\s/i', $command )) {
                
                preg_match_all ( '/(\S[^:]+): (\d+)/', $db->info, $matches ); 
                $info = array_combine ( $matches[1], $matches[2] );
                
                    if ( $info['Changed'] == 0 ) {
                        $return_val = $info['Rows matched'];
                    } else {
                        $return_val =$info['Changed'];
                    }
            } else {
                $return_val = true;
            }
        } else {
            $return_val = false;
        }
                
        return $return_val;
    }
    
    /**
     * Remove stored query results.
     * 
     * @return void
    */
    private function flush_results() {
        $this->last_result = array();
        $this->num_rows = 0;
        
        if ( $this->result instanceof mysqli_result ) {
            $this->result->free();
        }
        $this->result = null;
    }
    
    /**
     * Get row from database.
     * 
     * @param string $query Command to execute.
     * @param string $output Optional data type to return: 'OBJECT', 'ARRAY_A', or 'ARRAY_N'.
     *      Result object, associative array, or numeric array, respectively.
     * @param int $row Optional row number to return.
     * 
     * @return mixed Database result in format specified by $output, false on failure.
    */
    public function get_row( $query, $output = 'OBJECT', $row = 0 ) {
        if (! ( empty( $query ))) {
            $this->query( $query );
        } else {
            return false;
        }
        
        if (! ( isset ( $this->last_result[$row] ))) {
            return false;
        }
        
        if ( $output === 'ARRAY_A ') {
            return get_object_vars( $this->last_result[$row] );
        } else if ( $output === 'ARRAY_N' ) {
            // Column names are keys
            return array_values( get_object_vars( $this->last_result[$row] ));
        } else {
            return $this->last_result[$row];
        }
    }
    
    /**
     * Get entire result set from database.
     * 
     * @param string $query Command to execute.
     * @param string $output Optional data type to return: 'OBJECT', 'ARRAY_A', or 'ARRAY_N'.
     *      Numeric array of result objects, associative arrays, or numeric arrays, respectively.
     * 
     * @return mixed Database result in format specified by $output, false on failure.
    */
    public function get_results( $query, $output = 'OBJECT' ) {
        if (! (empty( $query ))) {
            $this->query( $query );
        } else {
            return false;
        }
        
        if (! (isset( $this->last_result ))) {
            return false;
        }
        
        if ( $output === 'ARRAY_A' || $output === 'ARRAY_N' ) {
            $return_array = array();
            
            foreach( $this->last_result as $row ) {
                if ( $output === 'ARRAY_A' ) {
                    // Column names are keys
                    array_push( $return_array, get_object_vars( $row ));
                } else {
                    array_push( $return_array, array_values( get_object_vars( $row )));
                }
            }
            return $return_array;
        } else {
            return $this->last_result;
        }
    }
}
?>