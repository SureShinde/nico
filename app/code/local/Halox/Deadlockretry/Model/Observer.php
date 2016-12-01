<?php

class Halox_Deadlockretry_Model_Observer{
  public function setup()
    {        
        Mage::register('deadlock_retry/enable/retry',Mage::getStoreConfig('deadlock_retry/enable/retry', Mage::app()->getStore()));

        if(Mage::registry('deadlock_retry/enable/retry')){
            Mage::register('deadlock_retry/enable/max_retry',Mage::getStoreConfig('deadlock_retry/enable/max_retry', Mage::app()->getStore()));
            Mage::register('deadlock_retry/enable/log',Mage::getStoreConfig('deadlock_retry/enable/log', Mage::app()->getStore()));
        } 
		return $this;
    }
}
