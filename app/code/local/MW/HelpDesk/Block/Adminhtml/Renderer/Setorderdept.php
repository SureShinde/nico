<?php

class MW_Helpdesk_Block_Adminhtml_Renderer_Setorderdept extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract {

    public function render(Varien_Object $row) {
    	$result = '';
    	$id = $row['department_id'];
    	$name = 'department_sort_order_'.$id.'';
    	$value = $row['department_sort_order'];
    	//$result = "<span style='display: block; margin: 0px 0px 0px 8px;'>".$value."</span><input type=".$type." class=".$class." name=".$name." value=".$value."></input>";
		$result = $value."  <input type='text' class='department_sort_order' style='text-align: center;width: 45px;' title=".$id." id=".$name." name=".$name." value=".$value."></input>";
    	return $result;
    }

}