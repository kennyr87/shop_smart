<?php
/**
 * Base class.
 * 
 * @package ShopSmart
*/
abstract class Base {

    /**
     * Stores data.
     * 
     * @var object $store Result object.
    */
    protected $data = null;
    
    /**
     * Initalize object's propeties.
     * 
     * @param object $data MySQL result set.
     * 
     * @return void
    */
    abstract public function init( stdClass $data );
    
    /**
     * Get object's data.
     * 
     * @return object Result set.
    */
    public function get_data() {
        return $this->data;
    }
    
    /**
     * Clear values of objects properties.
     * 
     * @return void
    */
    protected function flush_object() {
        
        if ( $this->data instanceof stdClass ) {
            $this->data = null;
        }
        
        foreach ($this as $key => $value ) {
            unset( $this->$key );
        }
    }
}
?>