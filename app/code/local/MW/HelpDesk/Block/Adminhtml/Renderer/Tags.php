<?php

class MW_Helpdesk_Block_Adminhtml_Renderer_Tags extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {

    public function render(Varien_Object $row) {
          // load tags
          $result = '';
          $collection = Mage::getModel('helpdesk/tag')->getCollection()
			->addFieldToFilter('ticket_id', array('eq' => $row['ticket_id']));
      	  foreach ($collection as $tag) {
      	  	$param = str_replace(' ', '+', $tag->getName());
	    	$result .= Mage::helper('helpdesk')->__("<b><a href=\"%s\">%s</a></b>", 
    						Mage::helper('adminhtml')->getUrl('adminhtml/hdadmin_ticket/index/tags/', array('tags'=>$param)), 
    						$tag->getName()) . ', ';
		  }
		  rtrim($result, ', ');
    	  return $result;
    }

}