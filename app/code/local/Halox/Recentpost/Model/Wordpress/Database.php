<?php

class Halox_Recentpost_Model_Wordpress_Database extends Mage_Core_Model_Abstract {

    protected $_connection;
    /**
     * Connection with wordpress database.  
     * @return $this->_connection
     */
    public function __construct() {
        try {
            if (isset($this->_connection)) {
                return $this->_connection;
            } else {
                $resource = Mage::getModel('core/resource');
                $this->_connection = $resource->getConnection('recentpost_read');

                return $this->_connection;
            }
        } catch (Exception $e) {

            throw new Exception('There is an error in connection with wordpress database');
        }
    }
    /**
     * Fetch post data from wordpress database.  
     * @return Array() as $results
     */
    public function fetchPost() {
       
        try {
            $results = $this->_connection->fetchAll("SELECT * FROM wp_posts WHERE post_status = 'publish' AND ping_status = 'open' ORDER BY ID DESC LIMIT 3 ");
            return $results;
        } catch (Exception $e) {

            throw new Exception('There is an error in fetching post from the database');
        }
    }
    /**
     * Fetch post meta data from wordpress database.  
     * @return Array() as $results
     */
    public function fetchMetaData($post_id) {
        try {
            $results = $this->_connection->fetchAll("SELECT * FROM wp_postmeta WHERE meta_key = '_wp_attached_file' and post_id = $post_id");

            return $results;
        } catch (Exception $e) {
            throw new Exception('There is an error in fetching post from the database');
        }
    }

}
