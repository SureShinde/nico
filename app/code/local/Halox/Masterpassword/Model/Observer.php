<?php

class Halox_Masterpassword_Model_Observer extends Varien_Object
{

	/**
	 * encrypt master password before saving to DB
	 */
	public function onAdminUserSaveBefore($observer)
	{

		$event = $observer->getEvent();
		$user = $event->getDataObject();

		if($user->getData('masterpassword')){
            $user->setData('masterpassword', md5($user->getData('masterpassword')));
        }

		return $this;
	}

}