<?php

class MW_Helpdesk_Block_Adminhtml_Renderer_Quicknote extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {

    public function render(Varien_Object $row) {
          // load tags
        $result = '';
        $collection = Mage::getModel('helpdesk/ticket')->load($row['ticket_id']);
      	$quicknote = $collection->getQuicknote();
      	//lay 3 tu trong quick note de hien thi
//      $len = 0; $sub = '';
//    	foreach (explode(' ', $param) as $word) {
//		    $part = (($sub != '') ? ' ' : '') . $word;
//		    $sub .= $part;
//		    $len ++;
//		    if ($len == 3) break;
//	    }
		/* css */
		//background-color: black; color:white;
		//<div class=arrow>▲</div>
		
      	$note = $collection->getNote();
      	
      	if($quicknote != ''){
			  $result .= "
			  	<style>
			  		.tooltip{
					  text-align: justify;
					  color: black;
					  display: none;
					  position: absolute;
					  width: 290px;
					}
					.tooltip .text{
					  background: lightyellow;
					  display: block;
					  border: 2px solid gray; 
					  border-width: 1px 2px 2px 1px;
					  margin-top: -15px;
					  padding:5px;
					  border-radius: 3px 3px 3px 3px;
					}
					.tooltip .arrow {margin-bottom: 20px; margin-top: -17px; margin-left: 0px;}
					.special {text-decoration:none;}
					a:hover {text-decoration:none;}
			  	</style>
			  		
		  		<a class='special' title = ' '>" . $quicknote . "</a>
		  		";
			  
		  		if($note != ''){
		    		$result .= "
		    		<div class='tooltip'>
		    			<div class=arrow></div>
					 	<div class=text>" . $note . " </div>
					</div>
					";
		  		}
      	}
      	
      	$return = Mage::helper('helpdesk')->__($result);
    	return $return;
   }
}