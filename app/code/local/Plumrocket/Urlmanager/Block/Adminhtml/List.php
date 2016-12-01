<?php  

/*

Plumrocket Inc.

NOTICE OF LICENSE

This source file is subject to the End-user License Agreement
that is available through the world-wide-web at this URL:
http://wiki.plumrocket.net/wiki/EULA
If you are unable to obtain it through the world-wide-web, please
send an email to support@plumrocket.com so we can send you a copy immediately.font-size:14px

@package	Plumrocket_Url_Manager-v1.2.x
@copyright	Copyright (c) 2013 Plumrocket Inc. (http://www.plumrocket.com)
@license	http://wiki.plumrocket.net/wiki/EULA  End-user License Agreement
 
*/

class Plumrocket_Urlmanager_Block_Adminhtml_List extends Mage_Adminhtml_Block_Widget_Grid_Container  {
	
	public function __construct()
    {
        parent::__construct();
		
        $this->_controller = 'adminhtml_list';
        $this->_blockGroup = 'urlmanager';
        $this->_headerText = Mage::helper('urlmanager')->__('Manage Rules');
        
		$this->_updateButton('add', 'label', 'Add rule');
    }
    
    
    protected function _prepareLayout()
   	{
       $this->setChild('grid',
           $this->getLayout()->createBlock( $this->_blockGroup.'/' . $this->_controller . '_grid',
           $this->_controller . '.grid')->setSaveParametersInSession(true) );
       return parent::_prepareLayout();
   	}

}