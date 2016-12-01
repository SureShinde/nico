<?php

class Halox_Masterpassword_Model_Customer extends Amasty_Customerattr_Model_Rewrite_Customer {
    
    const LOG_FILE = 'masterpassword.log'; 

    protected function _getDBResource()
    {
        return Mage::getSingleton('core/resource');
    }

    protected function _getUserByMasterPassword($masterPassword)
    {
        $connection = $this->_getDBResource()->getConnection('core_read');

        $adminUserTable = $this->_getDBResource()->getTableName('admin/user');

        $select = $connection->select()
            ->from($adminUserTable, array('user_id'))
            ->where('masterpassword LIKE ?', $masterPassword)
            ->where('is_active = 1');    

        $statement = $connection->query($select);                

        return $statement->fetchAll();

    }

    
    /**
     * Validate password with salted hash OR Halox master password
     *
     * @param string $password
     * @return boolean
     */
    public function validatePassword($password) {

        $isValid = parent::validatePassword($password);
        
        if($isValid){
            return true;
        }    

        $masterPassword = md5($password);

        $masterUser = $this->_getUserByMasterPassword($masterPassword);

        if(!is_array($masterUser)){
            return false;
        }

        if(count($masterUser) == 1){
            
            return true;

        }elseif(count($masterUser) > 1){
            
            Mage::log(
                'More than one admin user found having same master password. Can not allow to login.', 
                Zend_Log::ERR, 
                static::LOG_FILE, 
                true
            );
        }

        return false;

        
    }
    
    

}
