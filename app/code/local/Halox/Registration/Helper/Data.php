<?php

class Halox_Registration_Helper_Data extends Mage_Core_Helper_Abstract
{
   /**
     * Retrieve customer logout url
     *
     * @return string
     */
    public function getLogoutUrl()
    {
        return $this->_getUrl('registration/account/logout');
    }
   
  
}
