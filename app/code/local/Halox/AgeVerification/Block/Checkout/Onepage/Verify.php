<?php

class Halox_AgeVerification_Block_Checkout_Onepage_Verify extends Mage_Checkout_Block_Onepage_Abstract {

    protected function _construct() {
        $this->getCheckout()->setStepData('verify', array(
            'label' => Mage::helper('ageverification')->__('Age Verification'),
            'is_show' => $this->isShow()
        ));
        parent::_construct();
    }

    /**
     * Retrieve is allow and show block
     *
     * @return bool
     */
    public function isShow() {
        return !$this->getQuote()->isVirtual();
    }

}
